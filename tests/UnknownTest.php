<?php

use WordPressPluginFeed\Parsers\Parser;

/**
 * Error tests
 */
class UnknownTest extends PHPUnit_Framework_TestCase
{
    /**
     * Unknown plugin
     */
    public function testUnknownPlugin()
    {
        $parser = Parser::getInstance('unknown-plugin', null, false);
        $releases = $parser->getReleases();

        $this->assertEquals(0, count($releases));
    }
}
