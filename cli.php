#!/usr/bin/env php
<?php include 'vendor/autoload.php';

use WordPressPluginFeed\Clients\CLIClient;
use Symfony\Component\Console\Application;

$application = new Application();
$application->add(new CLIClient());
$application->run();