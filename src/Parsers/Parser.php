<?php namespace WordPressPluginFeed\Parsers;

use ErrorException;
use Exception;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;
use Zend\Cache\StorageFactory;
use Zend\Http\Client;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Tag;

/**
 * Main class that parses WordPress.org plugin profiles
 * 
 * @author  David Martínez <contacto@davidmartinez.net>
 */
class Parser
{
    /**
     * List of plugins with specific update log (plugin => class)
     *
     * @var array
     */
    protected static $aliases = array
    (
        'buddypress'                  => 'OpenSource\\BuddyPressParser',
        'google-sitemap-generator'    => 'OpenSource\\GoogleXMLSitemapsParser',

        'all-in-one-seo-pack'         => 'Proprietary\\AllInOneSEOPackParser',
        'gravityforms'                => 'Proprietary\\GravityFormsParser',
        'revslider'                   => 'Proprietary\\RevolutionSliderParser',
        'js-composer'                 => 'Proprietary\\VisualComposerParser',
        'sitepress-multilingual-cms'  => 'Proprietary\\WPMLParser',
        'ubermenu'                    => 'Proprietary\\UberMenuParser',
        'ultimate-vc-addons'          => 'Proprietary\\UltimateVCAddonsParser',
        'yoast-wordpress-seo-premium' => 'Proprietary\\YoastSEOPremiumParser'
    );

    /**
     * Plugin name
     *
     * @var string
     */
    public $plugin = null;
    
    /**
     * Stability filter
     *
     * @var string
     */
    public $stability = 'stable';

    /**
     * Terms to match against title
     *
     * @var string
     */
    public $filter = false;
    
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = null;
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = null;
    
    /**
     * Plugin image
     * 
     * @var array
     */
    public $image = array
    (
        'uri' => "http://ps.w.org/%s/assets/icon-128x128.png",
        'height' => 128,
        'width' => 128        
    );

    /**
     * Plugin URL at WordPress.org
     *
     * @var string
     */
    public $link = null;
    
    /**
     * Feed URL
     *
     * @var string
     */
    public $feed_link = null;
    
    /**
     * Last release date
     *
     * @var \Carbon\Carbon
     */
    public $modified = null;
    
    /**
     * Release list
     *
     * @var \WordPressPluginFeed\Release[]
     */
    protected $releases = array();
    
    /**
     * Subversion tag list 
     * 
     * @var array
     */
    protected $tags = array();
    
    /**
     * WPScan Vulnerability Database
     *
     * @var array
     */
    protected $vulnerabilities = array();

    /**
     * Source URLs 
     *
     * @var array
     */
    protected $sources = array
    (
        'profile'   => 'https://wordpress.org/plugins/%s/changelog/',
        'tags'      => 'https://plugins.trac.wordpress.org/browser/%s/tags?order=date&desc=1'
    );

    /**
     * CLI call
     *
     * @var bool
     */
    protected $cli = false;

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
     * List of exceptions thrown during process
     *
     * @var \Exception[]
     */
    protected $exceptions = array();

    /**
     * Show errors and exceptions
     *
     * @var bool
     */
    protected $debug = true;
    
    /**
     * Load plugin data
     * 
     * @param   string  $plugin
     * @param   string  $stability
     * @param   string  $filter
     * @param   bool    $debug
     */
    public function __construct($plugin, $stability = null, $filter = null, $debug = null)
    {
        $this->plugin = $plugin;

        // error handler
        set_error_handler(array($this, 'error'));

        // load .env
        $env_path = dirname(dirname(dirname(__FILE__)));

        if(file_exists("$env_path/.env"))
        {
            $dotenv = new \Dotenv\Dotenv($env_path);
            $dotenv->load();
        }

        // error handler only for web calls
        if(php_sapi_name() == 'cli')
        {
            $this->cli = true;
        }

        // feed link
        if($this->cli == false)
        {
            $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
            $request = filter_input(INPUT_SERVER, 'REQUEST_URI');
            $this->feed_link = "http://{$host}{$request}";
        }
        // Atom feeds require a link or "self" keyword
        else
        {
            $this->feed_link = "self";
        }

        // default stability if not set
        if(empty($stability))
        {
            $stability = getenv('RELEASE_STABILITY') ?: 'any';
        }
        
        // stability filter
        if($stability == 'any')
        {
            $this->stability = false;
        }
        else
        {
            $this->stability = '/(' . str_replace(',', '|', $stability) . ')/';
        }

        // text filter
        if(!empty($filter))
        {
            $this->filter = '/(' . preg_replace('/\s+/', '|', preg_quote($filter)) . ')/i';
        }
        elseif(getenv('OUTPUT_FILTER'))
        {
            $this->filter = '/(' . preg_replace('/\s+/', '|', preg_quote(getenv('OUTPUT_FILTER'))) . ')/i';
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
            'sslcapath' => '/etc/ssl/certs',
            'timeout' => 30,
            'useragent' => 'Mozilla/5.0 (X11; Fedora; Linux x86_64; rv:43.0) Gecko/20100101 Firefox/43.0'
        ));

        // use cURL if exists
        if(function_exists('curl_init'))
        {
            $this->http->setOptions(array
            (
                'adapter' => 'Zend\Http\Client\Adapter\Curl',
                'curloptions' => array
                (
                    CURLOPT_CONNECTTIMEOUT => 30,
                    CURLOPT_TIMEOUT => 30
                )
            ));
        }

        // cache instance
        $this->cache = StorageFactory::factory(array
        (
            'adapter' => array
            (
                'name' => 'filesystem', 
                'options' => array
                (
                    'cache_dir' => getenv('CACHE_DIR') ?: dirname(dirname(__DIR__)) . '/cache',
                    'ttl' => getenv('CACHE_TTL') ?: 3600
                )
            ),
            'plugins' => array
            (
                'exception_handler' => array('throw_exceptions' => false)
            )
        ));

        // debug mode
        $this->debug = is_null($debug) ? !$this->cli : (bool) $debug;

        // load releases after class config
        try
        {
            $this->loadVulnerabilities();
            $this->loadReleases();

            if(empty($this->releases))
            {
                throw new Exception('No releases detected');
            }
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
        if($this->cache instanceof \Zend\Cache\Storage\Adapter\Filesystem)
        {
            $this->cache->clearExpired();
        }
    }

    /**
     * Get a parser class instance based on plugin name
     *
     * @param   string  $plugin
     * @param   string  $stability
     * @param   string  $filter
     * @param   bool    $debug
     * @return  \WordPressPluginFeed\Parsers\Parser
     */
    public static function getInstance($plugin, $stability = null, $filter = null, $debug = null)
    {
        if(isset(self::$aliases[$plugin]))
        {
            $class = 'WordPressPluginFeed\\Parsers\\' . self::$aliases[$plugin];
        }
        else
        {
            $class = 'WordPressPluginFeed\\Parsers\\Parser';
        }

        return new $class($plugin, $stability, $filter, $debug);
    }
    
    /**
     * Fetch a source (results are cached)
     * 
     * @link    http://framework.zend.com/manual/2.4/en/modules/zend.http.client.html
     * @param   string  $type   profile, tags or image
     * @param   strign  $append query string or other parameters
     * @return  string
     * @throws  \Exception
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
                else
                {
                    $message = "Error fetching {$source} (" . $response->getReasonPhrase() . ")";

                    throw new Exception($message, $response->getStatusCode());
                }
            }        
        }
        
        return $code;
    }

    /**
     * Add a release to list
     *
     * @param   $release
     */
    public function addRelease(Release $release)
    {
        if(isset($this->vulnerabilities[$release->version]))
        {
            $release->vulnerabilities = $this->vulnerabilities[$release->version];
        }

        $this->releases[$release->version] = $release;
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
                $tag = new Tag();
                $tag->name = trim($row->filter('.name')->text());  
                $tag->revision = trim($row->filter('.rev a')->first()->text());
                $tag->description = trim($row->filter('.change')->text());
                $tag->author = trim($row->filter('.author')->text());
                $tag->created = Carbon::parse($time);

                // fixes to tag name
                $tag->name = preg_replace('/^v/', '', $tag->name);
                
                $this->tags[$tag->name] = $tag;
            }
        }
    }

    /**
     * Get known vulnerabilities from WPScan Vulnerability Database
     *
     * @link    https://wpvulndb.com/api
     */
    public function loadVulnerabilities()
    {
        $this->sources['vulnerabilities'] = 'https://wpvulndb.com/api/v2/plugins/' . $this->plugin;

        try
        {
            $response = @json_decode($this->fetch('vulnerabilities'));

            foreach($response->{$this->plugin}->vulnerabilities as $vulnerability)
            {
                if(!isset($this->vulnerabilities[$vulnerability->fixed_in]))
                {
                    $this->vulnerabilities[$vulnerability->fixed_in] = array();
                }

                $this->vulnerabilities[$vulnerability->fixed_in][] = $vulnerability;
            }
        }
        catch(Exception $exception)
        {
            // not all plugins have  profile at wpvulndb.com
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
            
            /* @var $tag Tag */
            $tag =& $this->tags[$version];

            // release object
            $release = new Release($this->title, $version);
            $release->description = $tag->description;
            $release->author = $tag->author;
            $release->stability = $this->parseStability($node->textContent);
            $release->created = $tag->created;
            $release->content = '';

            // nodes that follows h4 are the details
            $details = $changelog->filter('h4')->eq($index)->nextAll();
            foreach($details as $n=>$node)
            {
                $tagname = $node->tagName;
                if($tagname != 'h4')
                {
                    $release->content .= "<$tagname>" . $details->eq($n)->html() . "</{$tagname}>" . PHP_EOL;
                }
                else
                {
                    break;
                }
            }

            // use tag description if no content is detected
            if(empty($release->content))
            {
                $release->content = $tag->description;
            }

            $this->addRelease($release);
        }
        
        // with zero releases, generate release data from Trac
        if(empty($this->releases))
        {
            foreach($this->tags as $tag)
            {
                $version = $tag->name;

                $release = new Release($this->title, $version);
                $release->description = $tag->description;
                $release->author = $tag->author;
                $release->stability = $this->parseStability($tag->name);
                $release->created = $tag->created;
                $release->content = "Commit message: " . $tag->description;

                $this->addRelease($release);
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
            
            $this->addRelease($release);
        }

        // if profile page doesn't have a <meta name="thumbnail">, plugin doesn't have custom image
        if($crawler->filter('meta[name=thumbnail]')->count() == 0)
        {
            $this->image['uri'] = null;
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
        $string = preg_replace('/^v(er)?(sion\s*)?/i', '', trim($string));

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
     * Get the parsed releases applying filters
     * 
     * @return  \WordPressPluginFeed\Release[]
     */
    public function getReleases($limit = null)
    {
        $releases = array();

        if(is_null($limit))
        {
            $limit = getenv('OUTPUT_LIMIT') ?: 25;
        }

        $count = 0;
        foreach($this->releases as $release)
        {
            if($this->stability !== false && !preg_match($this->stability, $release->stability))
            {
                continue;
            }

            if($this->filter !== false && !preg_match($this->filter, $release->title . $release->content))
            {
                continue;
            }

            $release->filter($this->filter);
            $releases[$release->version] = $release;

            $count++;
            if($limit > 0 && $count >= $limit)
            {
                break;
            }
        }

        return $releases;
    }

    /**
     * Get the last error as a string
     *
     * @return string
     */
    public function getLastError()
    {
        $error = '';

        $exception = end($this->exceptions);

        if(!empty($exception) && $this->cli === false)
        {
            header('HTTP/1.1 500');
            $error .= "<h1>Error " . $exception->getCode() . "</h1>";
            $error .= "<p><strong>Plugin:</strong> {$this->plugin}<br />";
            $error .= "<strong>Message:</strong> " . $exception->getMessage() . "<br />";
            $error .= "<strong>File:</strong> " . $exception->getFile() . " (" . $exception->getLine() . ")</p>";
        }
        elseif(!empty($exception))
        {
            $error .= "Error " . $exception->getCode() . "\n";
            $error .= "Plugin: {$this->plugin}\n";
            $error .= "Message: " . $exception->getMessage() . "\n";
            $error .= "File: " . $exception->getFile() . " (" . $exception->getLine() . ")\n";
        }

        return $error;
    }
    
    /**
     * Error handler
     *
     * @codeCoverageIgnore
     * @param   int     $errno
     * @param   string  $errstr
     * @param   string  $errfile
     * @param   int     $errline
     */
    public function error($errno, $errstr, $errfile, $errline)
    {
        $this->exception(new ErrorException($errstr, $errno, 1, $errfile, $errline));
    }

    /**
     * Exception handler
     *
     * @codeCoverageIgnore
     * @param Exception $exception
     */
    public function exception(Exception $exception)
    {
        $this->exceptions[] = $exception;

        if($this->debug && $this->cli === false)
        {
            header('HTTP/1.1 500');
            die($this->getLastError());
        }
        elseif($this->debug)
        {
            echo $this->getLastError();
        }
    }
}
