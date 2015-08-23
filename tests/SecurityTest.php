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
        $parser = Parser::getInstance('jetpack');
        $releases = $parser->getReleases(false);

        $this->assertTrue
        (
            isset($releases['2.9.3']) &&
            preg_match('/security/i', $releases['2.9.3']->title) &&

            isset($releases['3.5.3']) &&
            preg_match('/security/i', $releases['3.5.3']->title)
        );
    }
}
