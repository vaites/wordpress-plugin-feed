<?php

use WordPressPluginFeed\Parsers\Parser;
use WordPressPluginFeed\Generators\Generator;

/**
 * Configuration tests
 */
class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * Limit configuration test
     */
    public function testLimitConfiguration()
    {
        putenv('OUTPUT_LIMIT=10');

        $parser = new Parser('jetpack');
        $releases = $parser->getReleases();

        $this->assertEquals(10, count($releases));
    }

    public function testFormatConfiguration()
    {
        putenv('OUTPUT_FORMAT=rss');

        $parser = new Parser('jetpack');
        $generator = Generator::getInstance();

        $output = $generator->generate($parser, false);

        $this->assertTrue((bool) preg_match('/<rss version="2.0"/', $output));
    }
}
