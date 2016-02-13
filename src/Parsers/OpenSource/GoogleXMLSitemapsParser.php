<?php namespace WordPressPluginFeed\Parsers\OpenSource;

use WordPressPluginFeed\Parsers\Generic\GenericParser;

/**
 * BuddyPress custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class GoogleXMLSitemapsParser extends GenericParser
{
    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'changelog' => 'http://www.arnebrachhold.de/projects/wordpress-plugins/google-xml-sitemaps-generator/changelog/',
    );

    /**
     * Release list container selector
     *
     * @var string
     */
    protected $container = '.storycontent';

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
