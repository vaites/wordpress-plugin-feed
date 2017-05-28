<?php

use WordPressPluginFeed\Clients\CLIClient;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Command line interface test
 */
class CLITest extends PHPUnit_Framework_TestCase
{
    /**
     * CLI with format test
     */
    public function testCLI()
    {
        $application = new Application();
        $application->add(new CLIClient());

        $command = $application->find('generate');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array
        (
            'command'   => $command->getName(),
            '--plugin'  => 'jetpack',
            '--format'  => 'rss'
        ));

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/<rss version="2.0"/', $output);
    }
}
