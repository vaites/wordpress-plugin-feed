<?php

namespace WordPressPluginFeed\Parsers\Proprietary;

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
    protected $sources =
    [
        'changelog' => 'https://semperplugins.com/all-in-one-seo-pack-changelog/',
    ];

    /**
     * Release list container selector
     *
     * @var string
     */
    protected $container = '.post-entry';

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
