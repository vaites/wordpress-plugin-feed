<?php

namespace WordPressPluginFeed\Parsers\Proprietary;

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
    public $image =
    [
        'uri' => 'http://ps.w.org/wordpress-seo/assets/icon-128x128.png',
        'height' => 128,
        'width' => 128
    ];

    /**
     * Source URLs
     *
     * @var array
     */
    protected $sources =
    [
        'changelog' => 'https://yoast.com/wordpress/plugins/seo/change-log/',
    ];

    /**
     * Parse public releases using changelog page on Yoast.com
     */
    protected function loadReleases()
    {
        // load Yoast SEO (free) releases
        $free = Parser::getInstance('wordpress-seo', $this->stability)->getReleases();

        // profile
        $this->http->setOptions(['curloptions' => [CURLOPT_SSL_VERIFYPEER => false]]);
        $crawler = new Crawler($this->fetch('changelog'));

        // need to parse changelog block
        $changelog = $crawler->filter('section.content')->children();

        // each h2 is a release
        foreach($changelog->filter('h2') as $index => $node)
        {
            // convert release title to version
            $version = $this->parseVersion($node->textContent);

            // get the ID to build link
            $id = $changelog->filter('h2')->eq($index)->attr('id');

            // release object
            $release = new Release($this->title, $version, $this->parseStability($node->textContent));
            $release->link = "{$this->sources['profile']}#{$id}";

            // add changelog from free version if exists
            if(isset($free[$version]))
            {
                $release->content = preg_replace('/<\/?body>/', '', $free[$version]->content);
                $release->content .= "<p>Yoast SEO Premium changes:</p>\n";
            }

            // nodes that follows h2 are the details
            $details = $changelog->filter('h2')->eq($index)->nextAll();
            foreach($details as $n => $node)
            {
                $tagname = $node->tagName;
                if($tagname != 'h2')
                {
                    $release->content .= "<$tagname>" . $details->eq($n)->html() . "</$tagname>" . PHP_EOL;
                }
                else
                {
                    break;
                }
            }

            // pubdate needs to be parsed and can be stripped
            $release->created = $this->parseDate($changelog->filter('h2')->eq($index)->nextAll()->first()->text());
            $release->content = preg_replace("/<p>\s*<small>\s*(.+)\s*<\/small>\s*<\/p>/i", '', $release->content);

            $this->addRelease($release);
        }
    }
}
