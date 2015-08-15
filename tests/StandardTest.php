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

    /**
     * Standard plugin 8: Yoast SEO
     */
    public function testYoastSEO()
    {
        $feed = new WordPressPluginFeed('wordpress-seo');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 9: W3 Total Cache
     */
    public function testW3TotalCache()
    {
        $feed = new WordPressPluginFeed('w3-total-cache');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 10: UpdraftPlus
     */
    public function testUpdraftPlus()
    {
        $feed = new WordPressPluginFeed('updraftplus');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 11: Shortcodes Ultimate
     */
    public function testShortcodesUltimate()
    {
        $feed = new WordPressPluginFeed('shortcodes-ultimate');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 12: MailChimp for WordPress
     */
    public function testMailChimpForWordPress()
    {
        $feed = new WordPressPluginFeed('mailchimp-for-wp');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 13: User Role Editor
     */
    public function testUserRoleEditor()
    {
        $feed = new WordPressPluginFeed('user-role-editor');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 14: MailPoet Newsletters
     */
    public function testMailPoetNewsletters()
    {
        $feed = new WordPressPluginFeed('wysija-newsletters');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 15: SEO Ultimate
     */
    public function testSEOUltimate()
    {
        $feed = new WordPressPluginFeed('seo-ultimate');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 16: Ninja Forms
     */
    public function testNinjaForms()
    {
        $feed = new WordPressPluginFeed('ninja-forms');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 17: Sucuri Security
     */
    public function testSucuriSecurity()
    {
        $feed = new WordPressPluginFeed('sucuri-scanner');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 18: PHP Code Widget
     */
    public function testPHPCodeWidget()
    {
        $feed = new WordPressPluginFeed('php-code-widget');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 19: Newsletter
     */
    public function testNewsletter()
    {
        $feed = new WordPressPluginFeed('newsletter');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 20: Cookie Law Info
     */
    public function testCookieLawInfo()
    {
        $feed = new WordPressPluginFeed('cookie-law-info');
        $releases = $feed->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }
}
