<?php

use WordPressPluginFeed\Parsers\OpenSource\BuddyPressParser;

/**
 * Tests for open source plugins with external changelog
 */
class OpenSourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Proprietary plugin 1: BuddyPress
     */
    public function testBuddyPress()
    {
        $parser = new BuddyPressParser('buddypress');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }
}
