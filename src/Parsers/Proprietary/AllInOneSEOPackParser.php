<?php namespace WordPressPluginFeed\Parsers\Proprietary;

use WordPressPluginFeed\Parsers\Generic\GenericParser;

/**
 * All in One SEO Pack custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class AllInOneSEOPackParser extends GenericParser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'All in One SEO Pack';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'All in One SEO Pack is a WordPress SEO plugin to automatically optimize your WordPress blog for Search Engines such as Google.';
    
    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'profile'   => 'http://semperfiwebdesign.com/blog/all-in-one-seo-pack/all-in-one-seo-pack-release-history/',
        'tags'      => 'https://plugins.trac.wordpress.org/browser/%s/tags?order=date&desc=1'
    );

    /**
     * Release list container selector
     *
     * @var string
     */
    protected $container = '.entry-content';

    /**
     * Block separator selector
     *
     * @var null
     */
    protected $block = 'p';

    /**
     * For open source plugins, tag must exist on SubVersion
     *
     * @var bool
     */
    protected $useTags = true;
}
