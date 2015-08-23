<?php include 'vendor/autoload.php';

use WordPressPluginFeed\Clients\WebClient;

$application = new WebClient();
$application->run();