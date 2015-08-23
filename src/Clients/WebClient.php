<?php namespace WordPressPluginFeed\Clients;

use WordPressPluginFeed\Parsers\Parser;
use WordPressPluginFeed\Generators\Generator;

/**
 * Basic web client
 *
 * @package WordPressPluginFeed\Clients
 */
class WebClient
{
    public function run()
    {
        $plugin     = filter_input(1, 'plugin',    FILTER_SANITIZE_STRING);
        $stability  = filter_input(1, 'stability', FILTER_SANITIZE_STRING);
        $format     = filter_input(1, 'format',    FILTER_SANITIZE_STRING);
        $limit      = filter_input(1, 'limit',     FILTER_SANITIZE_NUMBER_INT);

        $parser = Parser::getInstance($plugin, $stability);
        $generator = Generator::getInstance($format);
        $generator->generate($parser, $limit);
    }
}