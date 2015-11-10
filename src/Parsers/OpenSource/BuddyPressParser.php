<?php namespace WordPressPluginFeed\Parsers\OpenSource;

use Carbon\Carbon;
use Zend\Feed\Reader\Reader;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * BuddyPress custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class BuddyPressParser extends Parser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'BuddyPress';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'BuddyPress helps you run any kind of social network on your WordPress, with member profiles, activity streams, user groups, messaging, and more.';

    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'profile'   => 'https://buddypress.org/blog/feed/atom/',
    );
    
    /**
     * Parse public releases using feed from official blog
     */    
    protected function loadReleases()
    {
        // fetch 5 pages of feed
        for($p = 0; $p < 5; $p++)
        {
            $query = "?paged=$p";
            $changelog = Reader::importString($this->fetch('profile', $query));

            // each entry can be a release
            foreach($changelog as $entry)
            {
                // BuddyPress releases starts with "BuddyPress"
                $regexp = '/^BuddyPress\s+(\d|\.)/i';
                if(!preg_match($regexp, $entry->getTitle()))
                {
                    continue;
                }
                
                // convert release title to version
                $version = $this->parseVersion($entry->getTitle());
                
                // creation time
                $created = $entry->getDateCreated()->getTimestamp();

                // release object
                $release = new Release();
                $release->version = $version;
                $release->link = $entry->getLink();
                $release->title = "{$this->title} $version";
                $release->description = $entry->getDescription();
                $release->stability = $this->parseStability($entry->getTitle());
                $release->created = Carbon::createFromTimestamp($created);
                $release->content = $entry->getContent();

                $this->addRelease($release);
            }
        }
    }
}
