<?php namespace WordPressPluginFeed\Parsers\Proprietary;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * UberMenu custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class UberMenuParser extends Parser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'UberMenu';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'UberMenu™ is a user-friendly, highly customizable, responsive Mega Menu WordPress plugin. It works out of the box with the WordPress 3 Menu System, making it simple to get started but powerful enough to create highly customized and creative mega menu configurations.';
    
    /**
     * Plugin image
     * 
     * @var string
     */
    public $image = array
    (
        'uri' => 'https://thumb-cc.s3.envato.com/files/100231922/ubermenu-3.0.thumb.jpg',
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
        'profile'   => 'http://codecanyon.net/item/ubermenu-wordpress-mega-menu-plugin/154703',
    );
    
    /**
     * Parse public releases using "release log" block on Code Canyon profile
     */    
    protected function loadReleases()
    {
        // profile
        $crawler = new Crawler($this->fetch('profile'));
        
        // need to parse changelog block
        $changelog = $crawler->filter('#item-description__changelog')
                    ->nextAll()->filter('pre')->eq(0);

        // each release has a title with date and version followed by changes
        foreach(preg_split("/(-|=){10,}/", $changelog->text()) as $block)
        {
            $block = explode("\n", trim($block));

            // title must have pubdate and version
            $title = array_shift($block);
            $regexp = '/v([\d|\.]+)\s\(?(.+)\)?/i';
            if(!preg_match($regexp, $title, $match) || empty($block))
            {
                continue;
            }

            // convert release title to version
            $version = $this->parseVersion($match[1]);

            // get de ID to build the link
            $id = 'item-description__changelog';

            // release object
            $release = new Release();
            $release->link = "{$this->sources['profile']}#$id";
            $release->title = "{$this->title} $version";
            $release->description = false;
            $release->stability = $this->parseStability($version);
            $release->content = implode("<br />\n", $block);

            // pubdate needs to be parsed
            $pubdate = strtotime($match[2]);
            $release->created = Carbon::createFromTimestamp($pubdate);

            $this->releases[$version] = $release;
        }
    }
}
