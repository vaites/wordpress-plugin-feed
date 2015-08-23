<?php

use WordPressPluginFeed\Clients\CLIClient;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class CLITest extends PHPUnit_Framework_TestCase
{
    public function testCLI()
    {
        $application = new Application();
        $application->add(new CLIClient());

        $command = $application->find('generate');

        $commandTester = new CommandTester($command);
        $commandTester->execute(array
        (
            'command'   => $command->getName(),
            '--plugin'  => 'akismet',
            '--format'  => 'rss'
        ));

        $output = $commandTester->getDisplay();

        $this->assertRegExp('/<rss version="2.0"/', $output);
    }
}