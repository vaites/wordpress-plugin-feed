<?php

namespace WordPressPluginFeed\Parsers\OpenSource;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * WooCommerce custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class WooCommerceParser extends Parser
{
    /**
     * Source URLs
     *
     * @var array
     */
    protected $sources =
    [
        'changelog' => 'https://raw.githubusercontent.com/woothemes/woocommerce/master/CHANGELOG.txt',
    ];

    /**
     * Parse public releases using changelog page on GitHub
     *
     * @link https://github.com/woothemes/woocommerce
     */
    protected function loadReleases()
    {
        $this->loadTags();

        $changelog = preg_replace("/^(.+)\n/", '', $this->fetch('changelog'));

        if(preg_match_all("/= (.+) - (.+) =\n(.+)\n(\n|$)/Us", $changelog, $match))
        {
            $count = count($match[0]);
            for($r = 0; $r < $count; $r++)
            {
                $version = $match[1][$r];

                if(!isset($this->tags[$version]))
                {
                    continue;
                }
                else
                {
                    $tag =& $this->tags[$version];
                }

                // release object
                $release = new Release($this->title, $version, $this->parseStability($version));
                $release->description = $tag->description;
                $release->author = $tag->author;
                $release->content = '<ul>' . preg_replace('/\*(.+)/', '<li>$1</li>', $match[3][$r]) . '</ul>';
                $release->created = $tag->created;

                $this->addRelease($release);
            }
        }
    }
}
