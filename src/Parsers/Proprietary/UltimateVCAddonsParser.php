<?php

namespace WordPressPluginFeed\Parsers\Proprietary;

use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * Ultimate Addons for Visual Composer custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class UltimateVCAddonsParser extends Parser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'Ultimate Addons for Visual Composer';

    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'This plugin adds several premium elements in your Visual Composer on top of the built-in ones given by WPBakery.';

    /**
     * Plugin image
     *
     * @var string
     */
    public $image =
    [
        'uri' => 'https://thumb-cc.s3.envato.com/files/86787603/80x80.png',
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
        'changelog' => 'https://ultimate.brainstormforce.com/changelog/',
    ];

    /**
     * Parse public releases using "release log" block on Code Canyon profile
     */
    protected function loadReleases()
    {
        // profile
        $crawler = new Crawler($this->fetch('changelog'));

        // need to parse changelog block
        $changelog = $crawler->filter('main');

        // each p is a release
        foreach($changelog->filter('article') as $index => $node)
        {
            $title = $changelog->filter('header h2')->eq($index)->text();

            // title must have version
            if(preg_match('/Version\s+(.+)/i', $title, $match))
            {
                // convert release title to version
                $version = $this->parseVersion($title);

                // release object
                $release = new Release($this->title, $version, $this->parseStability($version));
                $release->link = $this->sources['changelog'].'?p='.str_replace('post-', '', $node->getAttribute('id'));

                // contents are in a div
                $release->content =  $changelog->filter('.bsf-entry-content')->eq($index)->html();

                // pubdate needs to be parsed
                $release->created = $this->parseDate($changelog->filter('.changelog-publish-date')->eq($index)->text());

                $this->addRelease($release);
            }
        }
    }
}
