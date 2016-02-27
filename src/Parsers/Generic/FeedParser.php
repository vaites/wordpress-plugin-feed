<?php namespace WordPressPluginFeed\Parsers\Generic;

use Carbon\Carbon;
use Zend\Feed\Reader\Reader;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * Generic feed parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class FeedParser extends Parser
{
    /**
     * Number of pages to request
     *
     * @var int
     */
    protected $pages = 5;

    /**
     * Seconds to sleep between calls
     *
     * @var int
     */
    protected $sleep = 1;

    /**
     * Regular expression to detect releases
     *
     * @var string
     */
    protected $regexp = false;

    /**
     * Parse public releases using a feed
     */
    protected function loadReleases()
    {
        // fetch 5 pages of feed
        for($p = 0; $p < $this->pages; $p++)
        {
            $query = "?paged=$p";
            $source = isset($this->sources['changelog']) ? 'changelog' : 'profile';
            $changelog = Reader::importString($this->fetch($source, $query, $cached));

            // each entry can be a release
            foreach($changelog as $entry)
            {
                // title must match regexp
                if(!preg_match($this->regexp, $entry->getTitle()))
                {
                    continue;
                }

                // convert release title to version
                $version = $this->parseVersion($entry->getTitle());
                if($version !== false)
                {
                    // creation time
                    $created = $entry->getDateCreated()->getTimestamp();

                    // release object
                    $release = new Release($this->title, $version);
                    $release->link = $entry->getLink();
                    $release->description = $entry->getDescription();
                    $release->stability = $this->parseStability($entry->getTitle());
                    $release->created = Carbon::createFromTimestamp($created);
                    $release->content = $entry->getContent();

                    $this->addRelease($release);
                }
            }

            if($this->sleep > 0 && $cached == false)
            {
                sleep($this->sleep);
            }
        }
    }
}