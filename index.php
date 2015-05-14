<?php

include "vendor/autoload.php";
include "src/WordPressPluginFeed.php";

$plugin = filter_input(INPUT_GET, 'plugin');
$stability = filter_input(INPUT_GET, 'stability');

if(isset(WordPressPluginFeed::$proprietary[$plugin]))
{
    $class = WordPressPluginFeed::$proprietary[$plugin];
    
    include "src/proprietary/$class.php";
    $instance = new $class($plugin, $stability);
}
else
{
    $instance = new WordPressPluginFeed($plugin, $stability);
}
    
$instance->generate();