<?php namespace WordPressPluginFeed\Generators\Formats;

use Symfony\Component\Yaml\Dumper;

use WordPressPluginFeed\Parsers\Parser;
use WordPressPluginFeed\Generators\Generator;

/**
 * YAML generator
 *
 * @package WordPressPluginFeed\Generators\Formats
 */
class YAMLGenerator extends Generator
{
    /**
     * Generate the XML
     *
     * @param   Parser  $parser
     * @param   int     $limit
     * @param   boolean $echo
     * @return  string
     */
    public function generate(Parser $parser = null, $limit = null, $echo = true)
    {
        if(!is_null($parser))
        {
            $this->setParser($parser);
        }

        $data = $this->serialize('array', $limit);
        $dumper = new Dumper();
        $output = $dumper->dump($data, 10, 1);

        if($echo)
        {
            header('Content-Type: application/x-yaml;charset=utf-8');
            echo $output;
        }

        return $output;
    }
}