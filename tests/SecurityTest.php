<?php

use WordPressPluginFeed\Parsers\Parser;

/**
 * Security detection tests
 */
class SecurityTest extends PHPUnit_Framework_TestCase
{
    /**
     * Security detection in JetPack: versions 2.9.3
     */
    public function testSecurityJetPack()
    {
        $version = '4.1.0-stable';
        $parser = Parser::getInstance('woocommerce');
        $releases = $parser->getReleases(false);

        $this->assertTrue(isset($releases[$version]) && preg_match('/security/i', $releases[$version]->title));
    }
}
