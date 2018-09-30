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
    public $image =
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
        'changelog' => 'https://kb.wpbakery.com/docs/preface/release-notes/',
    ];

    /**
     * Parse public releases using "release log" block on Code Canyon profile
     */
    protected function loadReleases()
    {
        // profile
        $crawler = new Crawler($this->fetch('changelog'));

        // need to parse changelog block
        $changelog = $crawler->filter('table.confluenceTable');

        // each row is release
        foreach($changelog->filter('tr') as $index => $node)
        {
            // first row are the headers
            if($index == 0)
            {
                continue;
            }

            $first = $changelog->filter('tr')->eq($index)->children()->getNode(0);
            $last = $changelog->filter('tr')->eq($index)->children()->getNode(1);

            // title must have pubdate and version
            $title = $first->textContent;
            $regexp = '/(\d+)\.(\d+)\.(\d+)(.+)ver\s+(.+)/i';
            if(!preg_match($regexp, $title, $match))
            {
                continue;
            }

            // convert release title to version
            $version = $this->parseVersion($match[5]);

            // release object
            $release = new Release($this->title, $version, $this->parseStability($version));
            $release->link = "{$this->sources['profile']}#$version";
            $release->content = $last->ownerDocument->saveHTML($last);

            // pubdate needs to be parsed
            $pubdate = $match[3] . '-' . $match[2] . '-' . $match[1];
            $release->created = $this->parseDate($pubdate);

            $this->addRelease($release);
        }
    }
}
