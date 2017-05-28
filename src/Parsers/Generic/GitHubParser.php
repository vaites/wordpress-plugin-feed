<?php namespace WordPressPluginFeed\Parsers\Generic;

use Carbon\Carbon;
use Zend\Feed\Reader\Reader;

use WordPressPluginFeed\Tag;

/**
 * GitHub parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class GitHubParser extends FeedParser
{
    /**
     * GitHub repository (user/repo)
     *
     * @var string
     */
    protected $repository = null;

    /**
     * Number of pages to request
     *
     * @var int
     */
    protected $pages = 1;

    /**
     * Regular expression to detect releases
     *
     * @var string
     */
    protected $regexp = false;

    /**
     * GitHubParser constructor: adds changelog feed
     *
     * @param string      $plugin
     * @param null|string $stability
     * @param null|string $filter
     * @param null|string $categories
     * @param bool|null   $debug
     */
    public function __construct($plugin, $stability, $filter, $categories, $debug)
    {
        $this->sources['profile'] = false;
        $this->sources['changelog'] = "https://github.com/{$this->repository}/releases.atom";
        $this->sources['tags'] = "https://github.com/{$this->repository}/tags.atom";

        parent::__construct($plugin, $stability, $filter, $categories, $debug);
    }

    /**
     * Load GitHub tags before loading releases and fixes relative links
     */
    public function loadReleases()
    {
        $this->loadTags();

        parent::loadReleases();

        foreach($this->releases as $version=>$release)
        {
            $this->releases[$version]->link = "https://github.com/{$release->link}";
        }
    }

    /**
     * Load GitHub tags
     *
     * @throws \Exception
     */
    public function loadTags()
    {
        $tags = Reader::importString($this->fetch('tags', null, $cached));

        foreach($tags as $entry)
        {
            $tag = new Tag();
            $tag->name = $entry->getTitle();
            $tag->revision = false;
            $tag->description = $entry->getDescription();
            $tag->author = $entry->getAuthor() ? current($entry->getAuthor()) : null;
            $tag->created = Carbon::instance($entry->getDateModified());

            $this->addTag($tag);
        }
    }
}
