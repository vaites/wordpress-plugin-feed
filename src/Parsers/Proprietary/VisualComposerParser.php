<?php namespace WordPressPluginFeed\Parsers\Proprietary;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * Visual Composer custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class VisualComposerParser extends Parser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'Visual composer';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'Visual Composer for WordPress is drag and drop frontend and backend page builder plugin that will save you tons of time working on the site content.';
    
    /**
     * Plugin image
     * 
     * @var string
     */
    public $image = array
    (
        'uri' => 'https://thumb-cc.s3.envato.com/files/140080840/th-4.6.png',
        'height' => 80,
        'width' => 80
    );

    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'profile'   => 'http://codecanyon.net/item/visual-composer-page-builder-for-wordpress/242431',
    );
    
    /**
     * Parse public releases using "release log" block on Code Canyon profile
     */    
    protected function loadReleases()
    {
        // profile
        $crawler = new Crawler($this->fetch('profile'));
        
        // need to parse changelog block
        $changelog = $crawler->filter('#item-description__updates')->nextAll()->filter('pre')->eq(0);

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

            // get de ID to build the link
            $id = 'item-description__updates';

            // release object
            $release = new Release($this->title, $version);
            $release->link = "{$this->sources['profile']}#$id";
            $release->stability = $this->parseStability($version);
            $release->content = implode("<br />\n", $block);

            // pubdate needs to be parsed
            $pubdate = $match[3] . '-' . $match[2] . '-' . $match[1];
            $release->created = Carbon::parse($pubdate);

            $this->addRelease($release);
        }
    }
}
