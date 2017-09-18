<?php

use WordPressPluginFeed\Parsers\Parser;

/**
 * Tests for open proprietary plugins
 */
class ProprietaryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Proprietary plugin test
     *
     * @dataProvider    pluginProvider
     * @param   string $plugin
     */
    public function testProprietary($plugin)
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
        return
        [
            ['gravityforms'], ['revslider'], ['ultimate-vc-addons'], ['ubermenu'],
            ['sitepress-multilingual-cms'], ['js-composer'], ['all-in-one-seo-pack'],
            ['yoast-wordpress-seo-premium'], ['affiliatewp']
        ];
    }
}
