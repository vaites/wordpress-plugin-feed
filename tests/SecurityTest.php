<?php

use WordPressPluginFeed\WordPressPluginFeed;

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
        $feed = new WordPressPluginFeed('jetpack');
        $releases = $feed->getReleases();

        $this->assertTrue
        (
            isset($releases['2.9.3']) &&
            preg_match('/security/i', $releases['2.9.3']->title) &&

            isset($releases['3.5.3']) &&
            preg_match('/security/i', $releases['3.5.3']->title)
        );
    }
}
