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
        $parser = Parser::getInstance('akismet');
        $releases = $parser->getReleases(false);

        $this->assertTrue
        (
            isset($releases['3.1.2']) &&
            preg_match('/security/i', $releases['3.1.2']->title) &&

            isset($releases['3.1.5']) &&
            preg_match('/security/i', $releases['3.1.5']->title)
        );
    }
}
