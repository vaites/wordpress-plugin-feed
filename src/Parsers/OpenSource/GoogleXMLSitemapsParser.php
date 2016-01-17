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
     * Plugin title
     *
     * @var string
     */
    public $title = 'Google (XML) Sitemap Generator';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'This plugin generates a XML-Sitemap compliant sitemap of your WordPress blog. This format is supported by Ask.com, Google, YAHOO and MSN Search.';

    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'profile'   => 'http://www.arnebrachhold.de/projects/wordpress-plugins/google-xml-sitemaps-generator/changelog/',
        'tags'      => 'https://plugins.trac.wordpress.org/browser/%s/tags?order=date&desc=1'
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
    protected $tagMustExist = true;
}
