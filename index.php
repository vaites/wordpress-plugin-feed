<?php

include "vendor/autoload.php";
include "src/WordPressPluginFeed.php";

$plugin = filter_input(INPUT_GET, 'plugin');

if(isset(WordPressPluginFeed::$proprietary[$plugin]))
{
    $class = WordPressPluginFeed::$proprietary[$plugin];
    
    include "src/proprietary/$class.php";
    $instance = new $class($plugin);
}
else
{
    $instance = new WordPressPluginFeed($plugin);
}
    
$instance->generate();