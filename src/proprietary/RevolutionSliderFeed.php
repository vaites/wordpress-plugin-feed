<?php

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

/**
 * Slider Revolution custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class RevolutionSliderFeed extends WordPressPluginFeed
{
    /**
     * Plugin title
     *
     * @var string
     */
    protected $title = 'Revolution Slider';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    protected $description = 'Create a responsive(mobile friendly) or fullwidth slider with must-see-effects and meanwhile keep or build your SEO optimization (all content always readable for search engines)';
    
    /**
     * Plugin image
     * 
     * @var string
     */
    protected $image = 
    [
        'uri' => 'https://0.s3.envato.com/files/104347001/smallicon2.png',
        'height' => 80,
        'width' => 80
    ];

    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = 
    [
        'profile'   => 'http://codecanyon.net/item/slider-revolution-responsive-wordpress-plugin/2751380',
    ];
    
    /**
     * Parse public releases using "release log" block on Code Canyon profile
     */    
    protected function loadReleases()
    {
        // profile 
        $crawler = new Crawler($this->fetch('profile'));
        
        // need to parse changelog block
        $changelog = $crawler->filter('img')->reduce(function($node, $index)
        {
            return (bool) preg_match('/tpbanner_updates/', $node->attr('src'));
        })->parents()->nextAll();
        
        // each h3 is a release
        foreach($changelog->filter('h3') as $index=>$node)
        {
            // convert release title to version
            $version = $this->parseVersion($node->textContent);
            
            // title must have pubdate
            if(!preg_match('/(.+) \((.+)\)/i', $node->textContent, $pubdate))
            {
                continue;
            }
            
            # get the ID to build link
            $id = $changelog->filter('h3')->eq($index)->attr('id');
            
            // release object
            $release = new stdClass();
            $release->link = "{$this->sources['profile']}#{$id}";
            $release->title = "{$this->title} $version";
            $release->description = false;
            $release->created = time();
            $release->content = '';

            // nodes that follows h3 are the details
            $details = $changelog->filter('h3')->eq($index)->nextAll();
            foreach($details as $index=>$node)
            {
                if($node->tagName != 'h3')
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
            
            // pubdate needs to be parsed
            $release->created = Carbon::parse($pubdate[2]);
            
            $this->releases[$version] = $release;
        }
    }
}
