<?php namespace WordPressPluginFeed\Parsers\OpenSource;

use WordPressPluginFeed\Parsers\Parser;

/**
 * Yoast SEO custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class YoastSEOParser extends Parser
{
    /**
     * Other source URLs
     *
     * @var array
     */
    protected $sources = array
    (
        'readme' => 'https://plugins.trac.wordpress.org/export/%d/wordpress-seo/trunk/readme.txt'
    );

    /**
     * Load detailed changelog from readme.txt
     */
    public function loadReleases()
    {
        parent::loadReleases();

        $changelog = $this->parseReadme();

        foreach($this->releases as $version=>$release)
        {
            $major_version = preg_replace('/-stable/i', '', $version);
            $minor_version = $major_version . '.0';

            if(isset($changelog[$major_version]))
            {
                $this->releases[$version]->content = $changelog[$major_version];
            }
            elseif(isset($changelog[$minor_version]))
            {
                $this->releases[$version]->content = $changelog[$minor_version];
            }
        }
    }

    /**
     * Parse the readme.txt file
     *
     * @return  array
     */
    protected function parseReadme()
    {
        $changelog = array();

        $latest_tag = current($this->tags);

        $this->sources['readme'] = sprintf($this->sources['readme'], $latest_tag->revision);

        $readme = $this->fetch('readme');
        $readme = preg_replace('/^(.+)== Changelog ==/Usi', '', $readme);

        $blocks = preg_split('/=\s*(.+)\s*=/Usi', $readme, -1, PREG_SPLIT_DELIM_CAPTURE);

        foreach($blocks as $index=>$block)
        {
            $version = $this->parseVersion(trim($block));
            if(mb_strlen($block) < 8 && $version && isset($blocks[$index + 1]))
            {
                $changelog[$version] = nl2br(trim($blocks[++$index]));
            }
        }

        return $changelog;
    }
}
