<?php

namespace WordPressPluginFeed\Parsers\Proprietary;

use WordPressPluginFeed\Parsers\Generic\FeedParser;

/**
 * Gravity Forms custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class GravityFormsParser extends FeedParser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'Gravity Forms';

    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'Gravity Forms for WordPress is a full featured contact form plugin that features a drag and drop interface, advanced notification routing, lead capture, conditional logic fields, multi-page forms, pricing calculations and the ability to create posts from external forms.';

    /**
     * Plugin image
     *
     * @var string
     */
    public $image =
    [
        'uri' => 'http://gravityforms.s3.amazonaws.com/logos/gravityforms_logo_100.png',
        'height' => 100,
        'width' => 116
    ];

    /**
     * Source URLs
     *
     * @var array
     */
    protected $sources =
    [
        'changelog' => 'https://www.gravityforms.com/feed/?s=release',
        'profile' => false
    ];

    /**
     * Regular expression to detect releases
     *
     * @var string
     */
    protected $regexp = '/^Gravity Forms v(.+) Release/i';

    /**
     * Number of pages to request
     *
     * @var int
     */
    protected $pages = 5;
}
