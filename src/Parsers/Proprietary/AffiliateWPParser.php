<?php

namespace WordPressPluginFeed\Parsers\Proprietary;

use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Generic\FeedParser;

/**
 * AffiliateWP custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class AffiliateWPParser extends FeedParser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'AffiliateWP';

    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'The best affiliate marketing plugin for WordPress';

    /**
     * Plugin image
     *
     * @var string
     */
    public $image =
    [
        'uri' => 'https://7386-presscdn-0-40-pagely.netdna-ssl.com/wp-content/themes/affiliatewp-master/images/favicon-152.png',
        'height' => 152,
        'width' => 152
    ];

    /**
     * Source URLs
     *
     * @var array
     */
    protected $sources =
    [
        'changelog' => 'https://affiliatewp.com/changelog/'
    ];

    /**
     * Number of pages to request
     *
     * @var int
     */
    protected $pages = 1;

    /**
     * Parse public releases using official page
     */
    protected function loadReleases()
    {
        // profile
        $crawler = new Crawler($this->fetch('changelog'));

        // need to parse changelog block
        $changelog = $crawler->filter('article .entry-content')->children();

        // each h3 is a release
        foreach($changelog->filter('h3') as $index => $node)
        {
            // title must have pubdate
            if(preg_match_all('/Version\s+(.+),(.+),(.+)/i', $node->textContent, $match))
            {
                // convert release title to version
                $version = $this->parseVersion($match[1][0]);

                // release object
                $release = new Release($this->title, $version, $this->parseStability($node->textContent));
                $release->link = $this->sources['changelog'];

                // nodes that follows h3 are the details
                $details = $changelog->filter('h3')->eq($index)->nextAll();
                foreach($details as $n => $node)
                {
                    $tagname = $node->tagName;
                    if($tagname != 'h3')
                    {
                        $release->content .= "<$tagname>" . $details->eq($n)->html() . "</$tagname>" . PHP_EOL;
                    }
                    else
                    {
                        break;
                    }
                }

                // pubdate needs to be parsed
                $release->created = $this->parseDate($match[2][0] . ', ' . $match[3][0]);

                $this->addRelease($release);
            }
        }
    }
}
