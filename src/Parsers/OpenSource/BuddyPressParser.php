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
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'changelog' => 'https://buddypress.org/blog/feed/atom/',
    );

    /**
     * Regular expression to detect releases
     *
     * @var string
     */
    protected $regexp = '/^BuddyPress\s+(\d|\.)/i';
}
