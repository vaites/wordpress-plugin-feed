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
class YoastSEOPremiumParser extends Parser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'Yoast SEO Premium';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'The Yoast SEO Premium plugin (formerly known as WordPress SEO by Yoast Premium) adds several features to the Yoast SEO plugin';
    
    /**
     * Plugin image
     * 
     * @var string
     */
    public $image = array
    (
        'uri' => 'http://ps.w.org/wordpress-seo/assets/icon-128x128.png',
        'height' => 128,
        'width' => 128
    );

    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'profile'   => 'https://yoast.com/wordpress/plugins/seo-premium/change-log/',
    );
    
    /**
     * Parse public releases using changelog page on Yoast.com
     */    
    protected function loadReleases()
    {
        // profile
        $crawler = new Crawler($this->fetch('profile'));

        // need to parse changelog block
        $changelog = $crawler->filter('.entry-content')->children();

        // each h2 is a release
        foreach($changelog->filter('h2') as $index=>$node)
        {
            // convert release title to version
            $version = $this->parseVersion($node->textContent);

            // get the ID to build link
            $id = $changelog->filter('h2')->eq($index)->attr('id');

            // release object
            $release = new Release();
            $release->link = "{$this->sources['profile']}#{$id}";
            $release->title = "{$this->title} $version";
            $release->description = false;
            $release->stability = $this->parseStability($node->textContent);
            $release->created = time();
            $release->content = '';

            // nodes that follows h2 are the details
            $details = $changelog->filter('h2')->eq($index)->nextAll();
            foreach($details as $index=>$node)
            {
                if($node->tagName != 'h2')
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
            $release->created = Carbon::parse($changelog->filter('h2')
                    ->eq($index)->nextAll()->first()->text());

            $this->releases[$version] = $release;
        }
    }
}