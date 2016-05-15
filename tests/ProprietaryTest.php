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
     * @param   string  $plugin
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
        return array
        (
            array('gravityforms'), array('revslider'), array('ultimate-vc-addons'),array('ubermenu'),
            array('sitepress-multilingual-cms'), array('js-composer'), array('all-in-one-seo-pack'),
            array('yoast-wordpress-seo-premium'), array('affiliatewp')
        );
    }
}
