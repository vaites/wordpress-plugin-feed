<?php

use Symfony\Component\Process\Process;

/**
 * Web interface test
 *
 * @requires PHP 5.4
 * @requires ! HHVM
 */
class WebTest extends PHPUnit_Framework_TestCase
{
    /**
     * Built-in server instance
     *
     * @var \Symfony\Component\Process\Process;
     */
    protected $server = null;

    /**
     * Launch PHP built-in server
     */
    public function setUp()
    {
        if(defined('HHVM_VERSION'))
        {
            $this->markTestSkipped();
        }
        else
        {
            $this->server = new Process('php -S localhost:18473');
            $this->server->setWorkingDirectory(dirname(__DIR__));
            $this->server->start();

            sleep(2);
        }
    }

    /**
     * Stop PHP built-in server
     */
    public function tearDown()
    {
        sleep(2);

        $this->server->stop(3, SIGKILL);
    }

    /**
     * Web with format test
     */
    public function testWeb()
    {
        $output = file_get_contents('http://localhost:18473/index.php?plugin=jetpack&format=rss');

        $this->assertRegExp('/<rss version="2.0"/', $output);
    }
}
