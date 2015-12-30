<?php

use WordPressPluginFeed\Parsers\Parser;

/**
 * Tests for top standard plugins
 */
class StandardTest extends PHPUnit_Framework_TestCase
{
    /**
     * Standard plugin
     *
     * @dataProvider    pluginProvider
     * @param   string  $plugin
     */
    public function testStandard($plugin)
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
            array('akismet'), array('wp-super-cache'), array('contact-form-7'), array('wordfence'),
            array('better-wp-security'), array('jetpack'), array('woocommerce'), array('wordpress-seo'),
            array('updraftplus'), array('w3-total-cache'), array('seo-ultimate'), array('newsletter'),
            array('mailchimp-for-wp'), array('user-role-editor'), array('wysija-newsletters'), array('ninja-forms'),
            array('shortcodes-ultimate'), array('sucuri-scanner'), array('php-code-widget'), array('cookie-law-info'),
            array('tinymce-advanced'), array('nextgen-gallery'), array('google-analytics-for-wordpress'),
            array('siteorigin-panels'), array('wpclef'), array('broken-link-checker'), array('backwpup'),
            array('wptouch'), array('the-events-calendar'), array('advanced-custom-fields'), array('tinymce-advanced'),
            array('duplicate-post'), array('disable-comments'), array('ml-slider'), array('iwp-client'),
            array('the-definitive-url-sanitizer')
        );
    }
}
