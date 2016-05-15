<?php namespace WordPressPluginFeed\Parsers\Proprietary;

use WordPressPluginFeed\Parsers\Generic\FeedParser;

/**
 * AffiliateWP custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class AffiliateWPParser extends FeedParser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'AffiliateWP';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'The best affiliate marketing plugin for WordPress';

    /**
     * Plugin image
     * 
     * @var string
     */
    public $image = array
    (
        'uri' => 'https://7386-presscdn-0-40-pagely.netdna-ssl.com/wp-content/themes/affiliatewp-master/images/favicon-152.png',
        'height' => 152,
        'width' => 152
    );
    
    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'changelog' => 'https://affiliatewp.com/feed/'
    );

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
    protected $regexp = '/^Version\s+(\d+)\.(\d+)\s+released/i';
}
