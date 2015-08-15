<?php

use WordPressPluginFeed\WordPressPluginFeed;

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
        $feed = new WordPressPluginFeed('unknown-plugin');
        $releases = $feed->getReleases();

        $this->assertEquals(0, count($releases));
    }
}
