<?php

use WordPressPluginFeed\OpenSource\BuddyPressFeed;

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
        $feed = new BuddyPressFeed('buddypress');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }
}
