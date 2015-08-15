<?php namespace WordPressPluginFeed\Proprietary;

use stdClass;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\WordPressPluginFeed;

/**
 * All in One SEO Pack custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class AllInOneSEOPackFeed extends WordPressPluginFeed
{
    /**
     * Plugin title
     *
     * @var string
     */
    protected $title = 'All in One SEO Pack';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    protected $description = 'All in One SEO Pack is a WordPress SEO plugin to automatically optimize your WordPress blog for Search Engines such as Google.';
    
    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = 
    [
        'profile'   => 'http://semperfiwebdesign.com/blog/all-in-one-seo-pack/all-in-one-seo-pack-release-history/',
        'tags'      => 'https://plugins.trac.wordpress.org/browser/%s/tags?order=date&desc=1'
    ];
    
    /**
     * Parse public releases using changelog of developer web
     */    
    protected function loadReleases()
    {
        // tags need to be loaded before parse releases
        $this->loadTags();
        
        // profile 
        $crawler = new Crawler($this->fetch('profile'));
        
        // need to parse changelog block
        $changelog = $crawler->filter('.entry-content')->children();
        
        // each h3 is a release
        foreach($changelog->filter('p') as $index=>$node)
        {
            // convert release title to version
            $version = $this->parseVersion($node->textContent);
            
            // version must exist in tag list
            if(!isset($this->tags[$version]))
            {
                continue;
            }
            
            // release object
            $release = new stdClass();
            $release->link = $this->sources['profile'];
            $release->title = "{$this->title} $version";
            $release->description = false;
            $release->stability = $this->parseStability($node->textContent);
            $release->created = $this->tags[$version]->created;
            $release->content = '';

            // ul that follows p+strong are the details
            $details = $changelog->filter('p')->eq($index)->nextAll();
            foreach($details as $index=>$node)
            {
                if($node->tagName != 'p')
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
    }
}
