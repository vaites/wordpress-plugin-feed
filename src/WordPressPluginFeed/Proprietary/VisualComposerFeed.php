<?php namespace WordPressPluginFeed\Proprietary;

use stdClass;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\WordPressPluginFeed;

/**
 * Visual Composer custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class VisualComposerFeed extends WordPressPluginFeed
{
    /**
     * Plugin title
     *
     * @var string
     */
    protected $title = 'Visual composer';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    protected $description = 'Visual Composer is Drag and Drop Frontend and Backend Page Builder Plugin for WordPress';
    
    /**
     * Plugin image
     * 
     * @var string
     */
    protected $image = 
    [
        'uri' => 'https://thumb-cc.s3.envato.com/files/140080840/th-4.6.png',
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
        'profile'   => 'http://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431',
    ];
    
    /**
     * Parse public releases using "release log" block on Code Canyon profile
     */    
    protected function loadReleases()
    {
        // profile
        $crawler = new Crawler($this->fetch('profile'));
        
        // need to parse changelog block
        $changelog = $crawler->filter('#item-description__updates')
                    ->nextAll()->filter('pre')->eq(0);

        // each release has a title with date and version followed by changes
        foreach(explode("\n\n", $changelog->text()) as $block)
        {
            $block = explode("\n", $block);

            // title must have pubdate and version
            $title = array_shift($block);
            $regexp = '/(\d+)\.(\d+)\.(\d+) - ver (.+)/i';
            if(!preg_match($regexp, $title, $match) || empty($block))
            {
                continue;
            }

            // convert release title to version
            $version = $this->parseVersion($match[4]);

            // release object
            $release = new stdClass();
            $release->link = "{$this->sources['profile']}#item-description__updates";
            $release->title = "{$this->title} $version";
            $release->description = false;
            $release->stability = $this->parseStability($version);
            $release->content = implode("<br />\n", $block);

            // pubdate needs to be parsed
            $pubdate = $match[3] . '-' . $match[2] . '-' . $match[1];
            $release->created = Carbon::parse($pubdate);

            $this->releases[$version] = $release;
        }
    }
}
