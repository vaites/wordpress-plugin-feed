<?php

use WordPressPluginFeed\Parsers\Proprietary\AllInOneSEOPackParser;
use WordPressPluginFeed\Parsers\Proprietary\GravityFormsParser;
use WordPressPluginFeed\Parsers\Proprietary\RevolutionSliderParser;
use WordPressPluginFeed\Parsers\Proprietary\UberMenuParser;
use WordPressPluginFeed\Parsers\Proprietary\UltimateVCAddonsParser;
use WordPressPluginFeed\Parsers\Proprietary\VisualComposerParser;
use WordPressPluginFeed\Parsers\Proprietary\WPMLParser;

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
        $parser = new AllInOneSEOPackParser('all-in-one-seo-pack');
        $releases = $parser->getReleases();
        
        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 2: Gravity Forms
     */
    public function testGravityForms()
    {
        $parser = new GravityFormsParser('gravityforms');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 3: Revolution Slider
     */
    public function testRevolutionSlider()
    {
        $parser = new RevolutionSliderParser('revslider');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 4: The WordPress Multilingual Plugin
     */
    public function testWPML()
    {
        $parser = new WPMLParser('sitepress-multilingual-cms');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 5: Visual Composer
     */
    public function testVisualComposer()
    {
        $parser = new VisualComposerParser('js-composer');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 6: Ultimate Addons for Visual Composer
     */
    public function testUltimateVCAddons()
    {
        $parser = new UltimateVCAddonsParser('ultimate-vc-addons');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 7: UberMenu
     */
    public function testUberMenu()
    {
        $parser = new UberMenuParser('ubermenu');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }
}
