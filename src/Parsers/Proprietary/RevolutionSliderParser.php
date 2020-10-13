<?php

namespace WordPressPluginFeed\Parsers\Proprietary;

use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * Slider Revolution custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class RevolutionSliderParser extends Parser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'Revolution Slider';

    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'Create a responsive(mobile friendly) or fullwidth slider with must-see-effects and meanwhile keep or build your SEO optimization (all content always readable for search engines)';

    /**
     * Plugin image
     *
     * @var string
     */
    public $image =
    [
        'uri' => 'https://thumb-cc.s3.envato.com/files/144560542/smallicon.png',
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
        'changelog' => 'https://www.sliderrevolution.com/documentation/changelog',
    ];

    /**
     * Parse public releases using "release log" block on Code Canyon profile
     */
    protected function loadReleases()
    {
        // profile
        $crawler = new Crawler($this->fetch('changelog'));

        // need to parse changelog block
        $changelog = $crawler->filter('main .entry-content')->children();

        // each h3 is a release
        foreach($changelog->filter('.wp-block-group__inner-container > h3') as $index => $node)
        {
            $version = $this->parseVersion($node->textContent);

            // title must have pubdate
            if(preg_match('/(.+) \((.+)\)/i', $node->textContent, $pubdate))
            {
                // release object
                $release = new Release($this->title, $version, $this->parseStability($node->textContent));
                $release->link = "{$this->sources['changelog']}#{$version}";

                // nodes that follows h3 are the details
                $details = $changelog->filter('.wp-block-group__inner-container > h3')->eq($index)->nextAll();
                foreach($details as $n => $node)
                {
                    $tagname = $node->tagName;
                    if($node->getAttribute('class') != 'wp-block-group__inner-container')
                    {
                        $release->content .= "<$tagname>" . $details->eq($n)->html() . "</$tagname>" . PHP_EOL;
                    }
                    else
                    {
                        break;
                    }
                }

                // pubdate needs to be parsed
                $release->created = $this->parseDate($pubdate[2]);

                $this->addRelease($release);
            }
        }
    }
}
