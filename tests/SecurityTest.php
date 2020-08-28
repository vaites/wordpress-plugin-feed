<?php

use WordPressPluginFeed\Parsers\Parser;

/**
 * Security detection tests
 */
class SecurityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Security detection in WooCoomerce: versions 4.2.1
     */
    public function testSecurityRelease()
    {
        $version = '4.2.1-stable';
        $parser = Parser::getInstance('woocommerce');
        $releases = $parser->getReleases(false);

        $this->assertTrue(isset($releases[$version]) && preg_match('/security/i', $releases[$version]->title));
    }
}
