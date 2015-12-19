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
        putenv('OUTPUT_LIMIT=5');

        $parser = Parser::getInstance('akismet');
        $releases = $parser->getReleases();

        $this->assertCount(5, $releases, $parser->getLastError());
    }

    /**
     * Atom Format configuration test
     */
    public function testAtomFormatConfiguration()
    {
        putenv('OUTPUT_FORMAT=atom');

        $parser = Parser::getInstance('jetpack');
        $generator = Generator::getInstance();

        $output = $generator->generate($parser, null, false);

        $this->assertContains('<feed xmlns="http://www.w3.org/2005/Atom"', $output, $parser->getLastError());
    }

    /**
     * RSS Format configuration test
     */
    public function testRSSFormatConfiguration()
    {
        putenv('OUTPUT_FORMAT=rss');

        $parser = Parser::getInstance('jetpack');
        $generator = Generator::getInstance();

        $output = $generator->generate($parser, null, false);

        $this->assertContains('<rss version="2.0"', $output, $parser->getLastError());
    }

    /**
     * JSON Format configuration test
     */
    public function testJSONFormatConfiguration()
    {
        putenv('OUTPUT_FORMAT=json');

        $parser = Parser::getInstance('jetpack');
        $generator = Generator::getInstance();

        $output = @json_decode($generator->generate($parser, null, false));

        $this->assertEquals($output->name, 'jetpack', $parser->getLastError());
    }

    /**
     * XML Format configuration test
     */
    public function testXMLFormatConfiguration()
    {
        putenv('OUTPUT_FORMAT=xml');

        $parser = Parser::getInstance('jetpack');
        $generator = Generator::getInstance();

        $output = $generator->generate($parser, null, false);

        $this->assertContains('<?xml version="1.0"?>', $output, $parser->getLastError());
    }
}
