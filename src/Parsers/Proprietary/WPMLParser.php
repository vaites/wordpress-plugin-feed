<?php namespace WordPressPluginFeed\Parsers\Proprietary;

use WordPressPluginFeed\Parsers\Generic\FeedParser;

/**
 * The WordPress Multilingual Plugin custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class WPMLParser extends FeedParser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'The WordPress Multilingual Plugin';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'WPML makes it easy to build multilingual sites and run them. It’s powerful enough for corporate sites, yet simple for blogs.';

    /**
     * Plugin image
     * 
     * @var string
     */
    public $image = array
    (
        'uri' => 'https://d2salfytceyqoe.cloudfront.net/wp-content/uploads/2010/09/wpml_logo.png',
        'height' => 265,
        'width' => 101
    );
    
    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'profile'   => 'https://wpml.org/category/changelog/feed/'
    );

    /**
     * Regular expression to detect releases
     *
     * @var string
     */
    protected $regexp = '/^WPML\s+(\d+)\.(\d+)(\.(\d+))?\s+/i';
}
