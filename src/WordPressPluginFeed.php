<?php

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;
use Zend\Cache\StorageFactory;
use Zend\Feed\Writer\Feed;
use Zend\Http\Client;

/**
 * Main class that parses WordPress.org plugin profiles
 * 
 * @author  David Martínez <contacto@davidmartinez.net>
 */
class WordPressPluginFeed 
{
    /**
     * List of proprietary plugins with specific update log:
     * 
     *      plugin-name     => ClassName
     *
     * @var array
     */
    public static $proprietary = 
    [
        'all-in-one-seo-pack'           => 'AllInOneSEOPackFeed',
        'buddypress'                    => 'BuddyPressFeed',
        'gravityforms'                  => 'GravityFormsFeed',
        'revslider'                     => 'RevolutionSliderFeed',
        'sitepress-multilingual-cms'    => 'WPMLFeed'
    ];
    
    /**
     * Plugin name
     *
     * @var string
     */
    protected $plugin = null;
    
    /**
     * Stability filter
     *
     * @var string
     */
    protected $stability = 'stable';
    
    /**
     * Plugin title
     *
     * @var string
     */
    protected $title = null;
    
    /**
     * Plugin short description
     *
     * @var string
     */
    protected $description = null;
    
    /**
     * Plugin image
     * 
     * @var array
     */
    protected $image = 
    [
        'uri' => "http://ps.w.org/%s/assets/icon-128x128.png",
        'height' => 128,
        'width' => 128        
    ];

    /**
     * Plugin URL at WordPress.org
     *
     * @var string
     */
    protected $link = null;
    
    /**
     * Feed URL
     *
     * @var string
     */
    protected $feed_link = null;
    
    /**
     * Last release date
     *
     * @var \Carbon\Carbon
     */
    protected $modified = null;
    
    /**
     * Release list
     *
     * @var array
     */
    protected $releases = [];
    
    /**
     * Subversion tag list 
     * 
     * @var array
     */
    protected $tags = [];
    
    /**
     * Source URLs 
     *
     * @var array
     */
    protected $sources = 
    [
        'profile'   => 'https://wordpress.org/plugins/%s/changelog/',
        'tags'      => 'https://plugins.trac.wordpress.org/browser/%s/tags?order=date&desc=1'
    ];

    /**
     * HTTP client instance
     *
     * @var \Zend\Http\Client
     */
    protected $http = null;
    
    /**
     * Cache handler instance
     *
     * @var \Zend\Cache\StorageFactory
     */
    protected $cache = null;
    
    /**
     * HTMLPurifier instance
     *
     * @var \HTMLPurifier
     */
    protected $purifier = null;
    
    /**
     * Load plugin data
     * 
     * @param   string  $plugin
     * @param   string  $stability
     */
    public function __construct($plugin, $stability = null) 
    {
        $this->plugin = $plugin;
        
        // error handler only for web calls
        if(php_sapi_name() != "cli") 
        {
            set_error_handler([$this, 'error']);
        }

        // feed link
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
        $request = filter_input(INPUT_SERVER, 'REQUEST_URI');
        $this->feed_link = "http://{$host}{$request}";
        
        // stability
        if(empty($stability) || $stability == 'any')
        {
            $this->stability = false;
        }
        else
        {
            $this->stability = '/(' . str_replace(',', '|', $stability) . ')/';
        }
        
        // external link if not defined
        if(empty($this->link))
        {
            $this->link = "https://wordpress.org/plugins/$plugin/";
        }
        
        // Zend HTTP Client instance
        $this->http = new Client();
        $this->http->setOptions(array
        (
            'timeout' => 30
        ));
        
        // use cURL if exists
        if(function_exists('curl_init'))
        {
            $this->http->setOptions(array
            (
                'adapter' => 'Zend\Http\Client\Adapter\Curl'
            ));
        }
        
        // cache instance
        $this->cache = StorageFactory::factory(
        [
            'adapter' => [
                'name' => 'filesystem', 
                'options' => [
                    'cache_dir' => dirname(dirname(__FILE__)) . '/cache',
                    'ttl' => 3600
                ]
            ],
            'plugins' => [
                'exception_handler' => ['throw_exceptions' => false]
            ]
        ]);
        
        // HTMLPurifier instance
        $this->purifier = new HTMLPurifier(HTMLPurifier_Config::create(array
        (
            'Attr.AllowedFrameTargets' => array('_blank')
        )));
        
        // load releases after class config
        try
        {
            $this->loadReleases();
        }
        catch(Exception $exception)
        {
            $this->exception($exception);
        }
    }
    
    /**
     * Clear expired cache after work is done
     */
    public function __destruct() 
    {
        $this->cache->clearExpired();
    }
    
    /**
     * Get HTML code from changelog tab (results are cached)
     * 
     * @link    http://framework.zend.com/manual/2.4/en/modules/zend.http.client.html
     * @param   string  $type   profile, tags or image
     * @param   strign  $append query string or other parameters
     * @return  string
     */
    public function fetch($type = 'profile', $append = null)
    {
        $code = false;
        
        if(isset($this->sources[$type]) && $this->sources[$type])
        {
            $uri = $this->sources[$type] . $append;
            $source = sprintf($uri, $this->plugin);
            $key = sha1($source);

            $code = $this->cache->getItem($key, $success);
            if($success == false)
            {
                $response = $this->http->setUri($source)->send();
                
                if($response->isSuccess())
                {
                    $code = $response->getBody();
                    
                    $this->cache->setItem($key, $code);
                }
            }        
        }
        
        return $code;
    }
    
    /**
     * Parse Subversion tags using Trac browser
     */
    public function loadTags()
    {
        // tag list from Trac repository browser
        $crawler = new Crawler($this->fetch('tags'));
        
        // each table row is a tag
        $rows = $crawler->filter('#dirlist tr');
        foreach($rows as $index=>$node)
        {
            $row = $rows->eq($index);
            if($row->filter('a.dir')->count())
            {
                // created datetime obtained from "age" link
                $time = $row->filter('a.timeline')->attr('title');
                $time = trim(preg_replace('/See timeline at/', '', $time));
                
                // tag object
                $tag = new stdClass();
                $tag->name = trim($row->filter('.name')->text());  
                $tag->revision = trim($row->filter('.rev a')->first()->text());
                $tag->description = trim($row->filter('.change')->text());
                $tag->created = Carbon::parse($time);
                
                // fixes to tag name
                $tag->name = preg_replace('/^v/', '', $tag->name);
                
                $this->tags[$tag->name] = $tag;
            }
        }
    }
    
    /**
     * Parse public releases using "changelog" tab on profile
     */
    protected function loadReleases()
    {
        // tags need to be loaded before parse releases
        $this->loadTags();
        
        // profile 
        $crawler = new Crawler($this->fetch('profile'));

        // plugin title (used for feed title)
        $this->title = $crawler->filter('#plugin-title h2')->text();
        $this->title = preg_replace('/\s*(:|\s+\-|\|)(.+)/', '', $this->title);
        $this->title = preg_replace('/\s+\((.+)\)$/', '', $this->title);
        
        // short description
        $this->description = $crawler->filter('.shortdesc')->text();

        // need to parse changelog block
        $changelog = $crawler->filter('.block.changelog .block-content');
        
        // each h4 is a release
        foreach($changelog->filter('h4') as $index=>$node)
        {
            // convert release title to version
            $version = $this->parseVersion($node->textContent);
            
            // version must exist in tag list
            if(!isset($this->tags[$version]))
            {
                continue;
            }
            
            // tag instance
            $tag =& $this->tags[$version];

            // release object
            $release = new stdClass();
            $release->title = "{$this->title} $version";
            $release->description = $tag->description;
            $release->stability = $this->parseStability($node->textContent);
            $release->created = $tag->created;
            $release->content = '';

            // nodes that follows h4 are the details
            $details = $changelog->filter('h4')->eq($index)->nextAll();
            foreach($details as $index=>$node)
            {
                if($node->tagName != 'h4')
                {
                    $release->content .= "<{$node->tagName}>" . 
                                         $details->eq($index)->html() .
                                         "</{$node->tagName}>" . PHP_EOL;
                }
                else
                {
                    break;
                }
            }
            
            $this->releases[$version] = $release;
        }
        
        // with zero releases, generate release data from Trac
        if(empty($this->releases))
        {
            foreach($this->tags as $tag)
            {
                $version = $tag->name;
                
                $release = new stdClass();
                $release->title = "{$this->title} $version";
                $release->description = $tag->description;
                $release->stability = $this->parseStability($tag->name);
                $release->created = $tag->created;
                $release->content = "Commit message: " . $tag->description;

                $this->releases[$version] = $release;
            }
            
            reset($this->tags);            
        }
        
        // add extra info to detected releases
        foreach($this->releases as $version=>$release)
        {            
            // tag instance
            $tag =& $this->tags[$version];
            
            // sets the feed modification time
            if(is_null($this->modified))
            {
                $this->modified = $tag->created;
            }
            
            // link to Track browser listing commits between since previous tag
            $release->link = 'https://plugins.trac.wordpress.org/log/'
                    . $this->plugin . '/trunk?action=stop_on_copy'
                    . '&mode=stop_on_copy&rev=' . $tag->revision 
                    . '&limit=100&sfp_email=&sfph_mail=';                       
            
            // move pointer to previous release
            while(current($this->tags) && key($this->tags) != $version)
            {
                next($this->tags);
            }
            
            // add previous release revision to limit commit list
            $previous = next($this->tags);
            if(!empty($previous))
            {
                $release->link .= '&stop_rev=' . $previous->revision;
            }
            
            $this->releases[$version] = $release;
        }
    }
    
    /**
     * Parses a string containing version to extract it
     * 
     * @param   string  $string
     * @return  string
     */
    protected function parseVersion($string)
    {
        $version = false;
        
        $string = preg_replace("/^{$this->title}\s+/i", '', $string);
        $string = preg_replace('/^v(ersion\s*)?/i', '', trim($string));
        
        if(preg_match('/(\d|\.)+/', $string, $match))     
        {
            $version = $match[0];
        }                

        return $version;
    }
    
    /**
     * Parses a string containing version to extract its type (alpha, beta...)
     * 
     * @param   string  $string
     * @return  strign
     */
    protected function parseStability($string)
    {
        $stability = 'stable';
        
        $versions = array
        (
            'alpha' => "/(alpha)(\s*\d+)?/i",
            'beta' => "/(beta)(\s*\d+)?/i",
            'rc' => "/(rc|release\s+candidate)(\s*\d+)?/i",
        );
        
        foreach($versions as $version=>$regexp)
        {
            if(preg_match($regexp, $string, $match))
            {
                $stability = $version;
                
                if(!empty($match[2]))
                {
                    $stability .= '.' . trim($match[2]);
                }
            }
        }
        
        return $stability;
    }
    
    /**
     * Filter a release adding type, warnings and other stuff
     * 
     * @param   stdClass    $release
     * @return  stdClass
     */
    public function filterRelease($release)
    {
        $highlight = array();
        
        // add release type
        if($release->stability != 'stable')
        {
            $release->title .= '-' . $release->stability;
        }
        
        // detect security keywordks
        $keywords = 'safe|security|vulnerability|CSRF|SQLi|XSS';
        if(preg_match_all("/($keywords)/i", $release->content, $match))
        {
            foreach(array_unique($match[1]) as $keyword)
            {
                $highlight[$keyword] = $keyword;
            }
        }
        
        // detect Common Vulnerabilities and Exposures
        if(preg_match('/CVE-(\d{4})-(\d{4})/i', $release->content, $match))
        {
            $link = '<a href="http://www.cvedetails.com/cve/%s/" '
                  . 'target="_blank">%s</a>';
            
            $highlight[$match[0]] = sprintf($link, $match[0], $match[0]);
        }
        
        // add warning to title and highlight security keywords
        if(!empty($highlight))
        {
            $release->title .= ' (Security release)';
            
            foreach($highlight as $search=>$replace)
            {
                $release->content = preg_replace
                (
                    "/$search/", 
                    '<strong><code>' . $replace . '</code></strong>', 
                    $release->content
                );
            }
        }        
        
        // purify HTML
        $release->content = $this->purifier->purify($release->content);

        return $release;
    }

    /**
     * Generates the feed
     * 
     * @param   string  $format     feed format (atom or rss)
     */
    public function generate($format = 'atom')
    {
        $plugin = $this->plugin;
        $time = is_null($this->modified) ? time() : $this->modified->timestamp;
        
        $feed = new Feed();
        $feed->setTitle($this->title);
        $feed->setLink($this->link);
        $feed->setFeedLink($this->feed_link, 'atom');
        $feed->setDateModified($time);
        $feed->addHub('http://pubsubhubbub.appspot.com/');
        
        if(!is_null($this->description))
        {
            $feed->setDescription($this->description);
        }
        
        if(!empty($this->image['uri']))
        {
            $feed->setImage(
            [
                'height' => $this->image['height'],
                'link' => $feed->getLink(),
                'title' => $this->title,
                'uri' => sprintf($this->image['uri'], $this->plugin),
                'width' => $this->image['width']
            ]);
        }
        
        foreach($this->releases as $release)
        {
            // stability filter
            if($this->stability != false)
            {
                if(!preg_match($this->stability, $release->stability))
                {
                    continue;
                }
            }
            
            // filters
            $release = $this->filterRelease($release);
            
            // feed entry
            $entry = $feed->createEntry();
            $entry->setTitle($release->title);
            $entry->setLink($release->link);
            $entry->setDateModified($release->created->timestamp);
            $entry->setDateCreated($release->created->timestamp);
            $entry->setContent($release->content);
            
            $feed->addEntry($entry);
        }

        header('Content-Type: text/xml;charset=utf-8');
        echo $feed->export($format);        
    }
    
    /**
     * Get the parsed releases
     * 
     * @return  array
     */
    public function getReleases()
    {
        return $this->releases;
    }
    
    /**
     * Error handler
     * 
     * @param   int     $errno
     * @param   string  $errstr
     */
    public function error($errno, $errstr, $errfile, $errline, $errcontext)
    {
        header('HTTP/1.1 500');
        echo "<h1>Error $errno</h1>";
        echo "<p><strong>Plugin:</strong> {$this->plugin}<br />";
        echo "<strong>Message:</strong> $errstr<br />";
        echo "<strong>File:</strong> $errfile ($errline)</p>";
        exit;
    }

    /**
     * Exception handler
     * 
     * @param Exception $exception
     */
    public function exception(Exception $exception)
    {
        $this->error
        (
            $exception->getCode(), 
            $exception->getMessage(), 
            $exception->getFile(), 
            $exception->getLine(), 
            $exception->getTrace()
        );
    }
}
