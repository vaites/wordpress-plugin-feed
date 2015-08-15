<?php include 'vendor/autoload.php';

WordPressPluginFeed\WordPressPluginFeed::getInstance
(
    filter_input(INPUT_GET, 'plugin'),
    filter_input(INPUT_GET, 'stability')
)->generate();