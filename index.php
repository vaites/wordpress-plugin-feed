<?php include 'vendor/autoload.php';

$format = filter_input(INPUT_GET, 'format', FILTER_SANITIZE_STRING);
$plugin = filter_input(INPUT_GET, 'plugin', FILTER_SANITIZE_STRING);
$stability = filter_input(INPUT_GET, 'stability', FILTER_SANITIZE_STRING);

$parser = WordPressPluginFeed\Parsers\Parser::getInstance($plugin, $stability);
$generator = WordPressPluginFeed\Generators\Generator::getInstance($format);
$generator->generate($parser);