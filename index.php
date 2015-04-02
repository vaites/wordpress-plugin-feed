<?php

include "vendor/autoload.php";
include "src/WordPressPluginFeed.php";

$plugin = filter_input(INPUT_GET, 'plugin');
$instance = new WordPressPluginFeed($plugin);
$instance->generate();