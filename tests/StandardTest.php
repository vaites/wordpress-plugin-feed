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
     * @param   string $plugin
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
        return
        [
            ['akismet'], ['wp-super-cache'], ['contact-form-7'], ['wordfence'],
            ['better-wp-security'], ['jetpack'], ['woocommerce'], ['wordpress-seo'],
            ['updraftplus'], ['w3-total-cache'], ['seo-ultimate'], ['breadcrumb-navxt'],
            ['mailchimp-for-wp'], ['user-role-editor'], ['wysija-newsletters'], ['ninja-forms'],
            ['shortcodes-ultimate'], ['sucuri-scanner'], ['php-code-widget'], ['cookie-law-info'],
            ['tinymce-advanced'], ['nextgen-gallery'], ['siteorigin-panels'],
            ['backwpup'], ['wptouch'], ['the-events-calendar'], ['advanced-custom-fields'],
            ['duplicate-post'], ['disable-comments'], ['ml-slider'], ['iwp-client'],
            ['regenerate-thumbnails'], ['wp-pagenavi'], ['the-definitive-url-sanitizer'],
            ['wordpress-importer'], ['limit-login-attempts'], ['google-analytics-for-wordpress'],
            ['siteorigin-panels'], ['so-widgets-bundle'], ['wp-multibyte-patch'],
        ];
    }
}
