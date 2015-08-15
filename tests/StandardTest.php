<?php

use WordPressPluginFeed\WordPressPluginFeed;

/**
 * Tests for top standard plugins
 */
class StandardTest extends PHPUnit_Framework_TestCase
{
    /**
     * Standard plugin 1: Akismet
     */
    public function testAkismet()
    {
        $feed = new WordPressPluginFeed('akismet');
        $releases = $feed->getReleases();
        
        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 2: WP Super Cache
     */
    public function testWPSuperCache()
    {
        $feed = new WordPressPluginFeed('wp-super-cache');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 3: Contact Form 7
     */
    public function testContactForm7()
    {
        $feed = new WordPressPluginFeed('contact-form-7');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 4: Wordfence Security
     */
    public function testWordfence()
    {
        $feed = new WordPressPluginFeed('wordfence');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 5: iThemes Security
     */
    public function testBetterWPSecurity()
    {
        $feed = new WordPressPluginFeed('better-wp-security');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 6: WooCommerce
     */
    public function testWooCommerce()
    {
        $feed = new WordPressPluginFeed('woocommerce');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 7: JetPack
     */
    public function testJetPack()
    {
        $feed = new WordPressPluginFeed('jetpack');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }
}
