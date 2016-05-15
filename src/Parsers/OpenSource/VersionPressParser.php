<?php namespace WordPressPluginFeed\Parsers\OpenSource;

use Exception;

use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Parsers\Generic\GitHubParser;

/**
 * VersionPress custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class VersionPressParser extends GitHubParser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'VersionPress';

    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'VersionPress is a free and open source version control plugin for WordPress built on Git.';

    /**
     * Plugin image
     *
     * @var string
     */
    public $image = array
    (
        'uri' => 'https://versionpress.net/wp-content/themes/versionpress.net/img/icon.png',
        'height' => 54,
        'width' => 53
    );

    /**
     * GitHub repository (user/repo)
     *
     * @var string
     */
    protected $repository = 'versionpress/versionpress';

    /**
     * Load detailed changelog from official release notes
     */
    public function loadReleases()
    {
        parent::loadReleases();

        foreach($this->releases as $version=>$release)
        {
            $version_fixed = preg_replace('/-stable/i', '', $version);
            $source = "release-$version_fixed";

            $this->sources[$source] = "http://docs.versionpress.net/en/release-notes/$version_fixed";

            try
            {
                $crawler = new Crawler($this->fetch($source));

                $content = '';

                $details = $crawler->filter('.markdown-body.main-content h1')->nextAll();
                foreach($details as $n=>$node)
                {
                    $tagname = $node->tagName;
                    $content .= "<$tagname>" . $details->eq($n)->html() . "</$tagname>" . PHP_EOL;
                }

                $this->releases[$version]->content = $content;
            }
            catch(Exception $e)
            {
                // we have GitHub release contents
            }
        }
    }
}
