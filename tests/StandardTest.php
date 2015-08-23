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
        $parser = Parser::getInstance('akismet');
        $releases = $parser->getReleases();
        
        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 2: WP Super Cache
     */
    public function testWPSuperCache()
    {
        $parser = Parser::getInstance('wp-super-cache');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 3: Contact Form 7
     */
    public function testContactForm7()
    {
        $parser = Parser::getInstance('contact-form-7');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 4: Wordfence Security
     */
    public function testWordfence()
    {
        $parser = Parser::getInstance('wordfence');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 5: iThemes Security
     */
    public function testBetterWPSecurity()
    {
        $parser = Parser::getInstance('better-wp-security');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 6: WooCommerce
     */
    public function testWooCommerce()
    {
        $parser = Parser::getInstance('woocommerce');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 7: JetPack
     */
    public function testJetPack()
    {
        $parser = Parser::getInstance('jetpack');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 8: Yoast SEO
     */
    public function testYoastSEO()
    {
        $parser = Parser::getInstance('wordpress-seo');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 9: W3 Total Cache
     */
    public function testW3TotalCache()
    {
        $parser = Parser::getInstance('w3-total-cache');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 10: UpdraftPlus
     */
    public function testUpdraftPlus()
    {
        $parser = Parser::getInstance('updraftplus');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 11: Shortcodes Ultimate
     */
    public function testShortcodesUltimate()
    {
        $parser = Parser::getInstance('shortcodes-ultimate');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 12: MailChimp for WordPress
     */
    public function testMailChimpForWordPress()
    {
        $parser = Parser::getInstance('mailchimp-for-wp');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 13: User Role Editor
     */
    public function testUserRoleEditor()
    {
        $parser = Parser::getInstance('user-role-editor');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 14: MailPoet Newsletters
     */
    public function testMailPoetNewsletters()
    {
        $parser = Parser::getInstance('wysija-newsletters');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 15: SEO Ultimate
     */
    public function testSEOUltimate()
    {
        $parser = Parser::getInstance('seo-ultimate');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 16: Ninja Forms
     */
    public function testNinjaForms()
    {
        $parser = Parser::getInstance('ninja-forms');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 17: Sucuri Security
     */
    public function testSucuriSecurity()
    {
        $parser = Parser::getInstance('sucuri-scanner');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 18: PHP Code Widget
     */
    public function testPHPCodeWidget()
    {
        $parser = Parser::getInstance('php-code-widget');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 19: Newsletter
     */
    public function testNewsletter()
    {
        $parser = Parser::getInstance('newsletter');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 20: Cookie Law Info
     */
    public function testCookieLawInfo()
    {
        $parser = Parser::getInstance('cookie-law-info');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 21: TinyMCE Advanced
     */
    public function testTinyMCEAdvanced()
    {
        $parser = Parser::getInstance('tinymce-advanced');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 22: NextGEN Gallery
     */
    public function testNextGENGallery()
    {
        $parser = Parser::getInstance('nextgen-gallery');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }

    /**
     * Standard plugin 23: Google Analytics by Yoast
     */
    public function testGoogleAnalyticsByYoast()
    {
        $parser = Parser::getInstance('google-analytics-for-wordpress');
        $releases = $parser->getReleases();

        $this->assertGreaterThan(0, count($releases));
    }
}
