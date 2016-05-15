<?php

use WordPressPluginFeed\Parsers\Parser;

/**
 * Tests for open source plugins with external changelog
 */
class OpenSourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Open source plugin test
     *
     * @dataProvider    pluginProvider
     * @param   string  $plugin
     */
    public function testOpenSource($plugin)
    {
        $parser = Parser::getInstance($plugin);
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases), $parser->getLastError());
    }

    /**
     * Plugin provider
     *
     * @return  array
     */
    public function pluginProvider()
    {
        return array
        (
            array('buddypress'), array('google-sitemap-generator'), array('versionpress'), array('woocommerce')
        );
    }
}
