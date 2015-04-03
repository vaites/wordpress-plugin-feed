<?php

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;
use Zend\Cache\StorageFactory;
use Zend\Feed\Writer\Feed;

class WordPressPluginFeed 
{
    /**
     * Plugin name
     *
     * @var string
     */
    protected $plugin = null;
    
    /**
     * Plugin title
     *
     * @var string
     */
    protected $title = null;
    
    /**
     * Plugin URL at WordPress.org
     *
     * @var string
     */
    protected $link = null;
    
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
     * Cache handler instance
     *
     * @var \Zend\Cache\StorageFactory
     */
    protected $cache = null;
    
    /**
     * 
     * 
     * @param   string  $plugin
     */
    public function __construct($plugin) 
    {
        set_error_handler([$this, 'error']);
        
        $host = filter_input(INPUT_SERVER, 'HTTP_HOST');
        $request = filter_input(INPUT_SERVER, 'REQUEST_URI');
        
        $this->plugin = $plugin;
        $this->link = "http://$host/$request";
        
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
        
        try
        {
            $this->loadTags();
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
     * Get HTML code from changelog tab
     * 
     * Results are cached
     * 
     * @return  string
     */
    public function fetch($type = 'profile')
    {
        if($type == 'profile')
        {
            $url = 'https://wordpress.org/plugins/%s/changelog/';
        }
        elseif($type == 'tags')
        {
            $url = "https://plugins.trac.wordpress.org/browser/%s/tags?desc=1";
        }
        
        $source = sprintf($url, $this->plugin);
        $key = sha1($source);
        
        $code = $this->cache->getItem($key, $success);
        if($success == false)
        {
            $stream = fopen($source, 'r');
            
            if(is_resource($stream))
            {
                stream_set_timeout($stream, 60);
                $code = stream_get_contents($stream);
                fclose($stream);

                if(!empty($code))
                {
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
                $tag->release = false; // betas and other tags aren't releases
                
                $this->tags[$tag->name] = $tag;
            }
        }
    }
    
    /**
     * Parse public releases using "changelog" tab on profile
     */
    protected function loadReleases()
    {
        // profile 
        $crawler = new Crawler($this->fetch('profile'));

        // plugin title (used for feed title)
        $this->title = $crawler->filter('#plugin-title h2')->text();

        // need to parse changelog block
        $changelog = $crawler->filter('.block.changelog .block-content');
        
        // each h4 is a release
        foreach($changelog->filter('h4') as $index=>$node)
        {
            // convert release title to version
            $version = $node->textContent;
            $version = preg_replace('/^v(ersion\s*)?/i', '', trim($version));
            $version = preg_replace('/\s+(.+)$/', '', trim($version));
            
            // version must exist in tag list
            if(!isset($this->tags[$version]))
            {
                continue;
            }
            
            // tag instance
            $tag =& $this->tags[$version];
            $tag->release = true;

            // release object
            $release = new stdClass();
            $release->title = "{$this->title} $version";
            $release->description = $tag->description;
            $release->created = $tag->created;
            $release->content = '';

            // nodes that follows h4 are the details
            $details = $changelog->filter('h4')->eq($index)->nextAll();
            foreach($details as $index=>$node)
            {
                if($node->tagName != 'h4')
                {
                    $release->content .= $details->eq($index)->html() . PHP_EOL;
                }
                else
                {
                    break;
                }
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
            $previous = current($this->tags);
            if(!empty($previous))
            {
                $release->link .= '&stop_rev=' . $previous->revision;
            }
            
            // add warning to title if detail has "security"
            if(preg_match('/(security|CSRF|XSS)/i', $release->content))
            {
                $release->title .= ' (Security update)';
            }
            
            $this->releases[$version] = $release;
        }
    }
    
    /**
     * Generates the feed
     * 
     * @param   string  $format     feed format (atom or rss)
     */
    public function generate($format = 'atom')
    {

        $feed = new Feed();
        $feed->setTitle($this->title);
        $feed->setLink("https://wordpress.org/plugins/{$this->plugin}/");
        $feed->setFeedLink($this->link, 'atom');
        $feed->setDateModified(time());
        $feed->addHub('http://pubsubhubbub.appspot.com/');
        
        foreach($this->releases as $release)
        {
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
     * Error handler
     * 
     * @param   int     $errno
     * @param   string  $errstr
     */
    protected function error($errno, $errstr)
    {
        throw new Exception($errstr, $errno);
    }

    /**
     * Exception handler
     * 
     * @param Exception $exception
     */
    protected function exception(Exception $exception)
    {
        $this->title = 'WordPress Plugin Feed';
        
        $error = new stdClass();
        $error->title = "Error " . $exception->getCode();
        $error->link = $this->link;
        $error->created = Carbon::now();
        $error->content = $exception->getMessage();
        
        $this->releases = [$error];        
    }
}