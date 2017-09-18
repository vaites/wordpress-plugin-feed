<?php

use WordPressPluginFeed\Parsers\Parser;
use WordPressPluginFeed\Generators\Generator;

/**
 * Configuration tests
 */
class ConfigurationTest extends PHPUnit_Framework_TestCase
{
    /**
     * .env configuration test
     */
    public function testDotEnvConfiguration()
    {
        $env_path = dirname(dirname(__FILE__));

        if(!file_exists("$env_path/.env"))
        {
            file_put_contents("$env_path/.env", 'OUTPUT_LIMIT="0"');
        }

        $parser = Parser::getInstance('jetpack');

        $this->assertEquals(0, getenv('OUTPUT_LIMIT'));
    }

    /**
     * Categories configuration test
     */
    public function testCategoriesConfiguration()
    {
        putenv('OUTPUT_FORMAT=json');

        $parser = Parser::getInstance('jetpack', 'stable', null, 'example.com');
        $generator = Generator::getInstance();

        $output = @json_decode($generator->generate($parser, null, false));

        $this->assertEquals($output->releases[0]->categories, ['example.com'], $parser->getLastError());
    }

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

        $this->stringStartsWith('<feed xmlns="http://www.w3.org/2005/Atom"', $output, $parser->getLastError());
    }

    /**
     * Atom Format output test
     */
    public function testAtomFormatOutput()
    {
        putenv('OUTPUT_FORMAT=atom');

        $parser = Parser::getInstance('jetpack');
        $generator = Generator::getInstance();

        ob_start();
        $generator->generate($parser);
        $output = ob_get_clean();

        $this->stringStartsWith('<feed xmlns="http://www.w3.org/2005/Atom"', $output, $parser->getLastError());
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

        $this->stringStartsWith('<rss version="2.0"', $output, $parser->getLastError());
    }

    /**
     * RSS Format output test
     */
    public function testRSSFormatOutput()
    {
        putenv('OUTPUT_FORMAT=rss');

        $parser = Parser::getInstance('jetpack');
        $generator = Generator::getInstance();

        ob_start();
        $generator->generate($parser);
        $output = ob_get_clean();

        $this->stringStartsWith('<rss version="2.0"', $output, $parser->getLastError());
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

        $this->stringStartsWith('<?xml version="1.0"?>', $output, $parser->getLastError());
    }

    /**
     * XML Format output test
     */
    public function testXMLFormatOutput()
    {
        putenv('OUTPUT_FORMAT=xml');

        $parser = Parser::getInstance('jetpack');
        $generator = Generator::getInstance();

        ob_start();
        $generator->generate($parser);
        $output = ob_get_clean();

        $this->stringStartsWith('<?xml version="1.0"?>', $output, $parser->getLastError());
    }

    /**
     * YAML Format configuration test
     */
    public function testYAMLFormatConfiguration()
    {
        putenv('OUTPUT_FORMAT=yaml');

        $parser = Parser::getInstance('jetpack');
        $generator = Generator::getInstance();

        $output = $generator->generate($parser, null, false);

        $this->stringStartsWith('title: Jetpack', $output, $parser->getLastError());
    }

    /**
     * YAML Format configuration test
     */
    public function testYAMLFormatOutput()
    {
        putenv('OUTPUT_FORMAT=yaml');

        $parser = Parser::getInstance('jetpack');
        $generator = Generator::getInstance();

        ob_start();
        $generator->generate($parser);
        $output = ob_get_clean();

        $this->stringStartsWith('title: Jetpack', $output, $parser->getLastError());
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
     * JSON Format output test
     */
    public function testJSONFormatOutput()
    {
        putenv('OUTPUT_FORMAT=json');

        $parser = Parser::getInstance('jetpack');
        $generator = Generator::getInstance();

        ob_start();
        $generator->generate($parser);
        $output = @json_decode(ob_get_clean());

        $this->assertEquals($output->name, 'jetpack', $parser->getLastError());
    }
}
