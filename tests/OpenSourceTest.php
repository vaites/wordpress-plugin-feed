<?php

use WordPressPluginFeed\Parsers\Parser;

/**
 * Tests for open source plugins with external changelog
 */
class OpenSourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Open source plugin 1: BuddyPress
     */
    public function testBuddyPress()
    {
        $parser = Parser::getInstance('buddypress');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Open source plugin 2: Google XML Sitemaps
     */
    public function testGoogleXMLSitemaps()
    {
        $parser = Parser::getInstance('google-sitemap-generator');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }
}
