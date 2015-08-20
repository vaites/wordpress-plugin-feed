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
        $parser = new Parser('unknown-plugin');
        $releases = $parser->getReleases();

        $this->assertEquals(0, count($releases));
    }
}
