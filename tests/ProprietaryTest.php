<?php

use WordPressPluginFeed\Proprietary\AllInOneSEOPackFeed;
use WordPressPluginFeed\Proprietary\GravityFormsFeed;
use WordPressPluginFeed\Proprietary\RevolutionSliderFeed;
use WordPressPluginFeed\Proprietary\UberMenuFeed;
use WordPressPluginFeed\Proprietary\UltimateVCAddonsFeed;
use WordPressPluginFeed\Proprietary\VisualComposerFeed;
use WordPressPluginFeed\Proprietary\WPMLFeed;

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
        $feed = new AllInOneSEOPackFeed('all-in-one-seo-pack');
        $releases = $feed->getReleases();
        
        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 2: Gravity Forms
     */
    public function testGravityForms()
    {
        $feed = new GravityFormsFeed('gravityforms');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 3: Revolution Slider
     */
    public function testRevolutionSlider()
    {
        $feed = new RevolutionSliderFeed('revslider');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 4: The WordPress Multilingual Plugin
     */
    public function testWPML()
    {
        $feed = new WPMLFeed('sitepress-multilingual-cms');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 5: Visual Composer
     */
    public function testVisualComposer()
    {
        $feed = new VisualComposerFeed('js-composer');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 6: Ultimate Addons for Visual Composer
     */
    public function testUltimateVCAddons()
    {
        $feed = new UltimateVCAddonsFeed('ultimate-vc-addons');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Proprietary plugin 7: UberMenu
     */
    public function testUberMenu()
    {
        $feed = new UberMenuFeed('ubermenu');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }
}