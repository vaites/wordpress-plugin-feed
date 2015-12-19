<?php namespace WordPressPluginFeed\Parsers\OpenSource;

use WordPressPluginFeed\Parsers\Generic\FeedParser;

/**
 * BuddyPress custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class BuddyPressParser extends FeedParser
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
     * Regular expression to detect releases
     *
     * @var string
     */
    protected $regexp = '/^BuddyPress\s+(\d|\.)/i';
}
