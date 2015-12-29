<?php

use WordPressPluginFeed\Parsers\Parser;
use WordPressPluginFeed\Generators\Generator;

/**
 * Simple code coverage tests
 */
class CoverageTest extends PHPUnit_Framework_TestCase
{
    /**
     * Parser setter and getter for generators
     */
    public function testParser()
    {
        putenv('RELEASE_STABILITY', 'any');

        $parser = Parser::getInstance('jetpack');
        $generator = Generator::getInstance('atom', $parser);

        $this->assertInstanceOf('\WordPressPluginFeed\Parsers\Parser', $generator->getParser());
    }
}
