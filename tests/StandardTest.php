<?php

use WordPressPluginFeed\Parsers\Parser;

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
        $parser = new Parser('akismet');
        $releases = $parser->getReleases();
        
        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 2: WP Super Cache
     */
    public function testWPSuperCache()
    {
        $parser = new Parser('wp-super-cache');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 3: Contact Form 7
     */
    public function testContactForm7()
    {
        $parser = new Parser('contact-form-7');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 4: Wordfence Security
     */
    public function testWordfence()
    {
        $parser = new Parser('wordfence');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 5: iThemes Security
     */
    public function testBetterWPSecurity()
    {
        $parser = new Parser('better-wp-security');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 6: WooCommerce
     */
    public function testWooCommerce()
    {
        $parser = new Parser('woocommerce');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 7: JetPack
     */
    public function testJetPack()
    {
        $parser = new Parser('jetpack');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 8: Yoast SEO
     */
    public function testYoastSEO()
    {
        $parser = new Parser('wordpress-seo');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 9: W3 Total Cache
     */
    public function testW3TotalCache()
    {
        $parser = new Parser('w3-total-cache');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 10: UpdraftPlus
     */
    public function testUpdraftPlus()
    {
        $parser = new Parser('updraftplus');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 11: Shortcodes Ultimate
     */
    public function testShortcodesUltimate()
    {
        $parser = new Parser('shortcodes-ultimate');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 12: MailChimp for WordPress
     */
    public function testMailChimpForWordPress()
    {
        $parser = new Parser('mailchimp-for-wp');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 13: User Role Editor
     */
    public function testUserRoleEditor()
    {
        $parser = new Parser('user-role-editor');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 14: MailPoet Newsletters
     */
    public function testMailPoetNewsletters()
    {
        $parser = new Parser('wysija-newsletters');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 15: SEO Ultimate
     */
    public function testSEOUltimate()
    {
        $parser = new Parser('seo-ultimate');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 16: Ninja Forms
     */
    public function testNinjaForms()
    {
        $parser = new Parser('ninja-forms');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 17: Sucuri Security
     */
    public function testSucuriSecurity()
    {
        $parser = new Parser('sucuri-scanner');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 18: PHP Code Widget
     */
    public function testPHPCodeWidget()
    {
        $parser = new Parser('php-code-widget');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 19: Newsletter
     */
    public function testNewsletter()
    {
        $parser = new Parser('newsletter');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 20: Cookie Law Info
     */
    public function testCookieLawInfo()
    {
        $parser = new Parser('cookie-law-info');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 21: TinyMCE Advanced
     */
    public function testTinyMCEAdvanced()
    {
        $parser = new Parser('tinymce-advanced');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 22: NextGEN Gallery
     */
    public function testNextGENGallery()
    {
        $parser = new Parser('nextgen-gallery');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 23: Google Analytics by Yoast
     */
    public function testGoogleAnalyticsByYoast()
    {
        $parser = new Parser('google-analytics-for-wordpress');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }
}
