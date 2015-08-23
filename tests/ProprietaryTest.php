<?php

use WordPressPluginFeed\Parsers\Parser;

/**
 * Tests for open proprietary plugins
 */
class ProprietaryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Proprietary plugin 1: All In One Seo PACK
     */
    public function testAllInOneSEOPack()
    {
        $parser = Parser::getInstance('all-in-one-seo-pack');
        $releases = $parser->getReleases();
        
        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 2: Gravity Forms
     */
    public function testGravityForms()
    {
        $parser = Parser::getInstance('gravityforms');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 3: Revolution Slider
     */
    public function testRevolutionSlider()
    {
        $parser = Parser::getInstance('revslider');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 4: The WordPress Multilingual Plugin
     */
    public function testWPML()
    {
        $parser = Parser::getInstance('sitepress-multilingual-cms');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 5: Visual Composer
     */
    public function testVisualComposer()
    {
        $parser = Parser::getInstance('js-composer');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 6: Ultimate Addons for Visual Composer
     */
    public function testUltimateVCAddons()
    {
        $parser = Parser::getInstance('ultimate-vc-addons');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 7: UberMenu
     */
    public function testUberMenu()
    {
        $parser = Parser::getInstance('ubermenu');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }
}
