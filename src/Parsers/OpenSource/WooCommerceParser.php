<?php namespace WordPressPluginFeed\Parsers\OpenSource;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

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
    protected $sources = array
    (
        'changelog' => 'https://raw.githubusercontent.com/woothemes/woocommerce/master/CHANGELOG.txt',
    );

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
            for($r = 0; $r < count($match[0]); $r++)
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
                $release = new Release($this->title, $version);
                $release->description = $tag->description;
                $release->author = $tag->author;
                $release->stability = $this->parseStability($version);
                $release->content = '<ul>' . preg_replace('/\*(.+)/', '<li>$1</li>', $match[3][$r]) . '</ul>';
                $release->created = $tag->created;

                $this->addRelease($release);

            }
        }
    }
}
