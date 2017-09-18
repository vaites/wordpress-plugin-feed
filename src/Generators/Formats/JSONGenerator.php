<?php

namespace WordPressPluginFeed\Generators\Formats;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use WordPressPluginFeed\Parsers\Parser;
use WordPressPluginFeed\Generators\Generator;

/**
 * JSON generator
 *
 * @package WordPressPluginFeed\Generators\Formats
 */
class JSONGenerator extends Generator
{
    /**
     * Generate the feed
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
        $serializer = new Serializer([new ObjectNormalizer()], [new JsonEncoder()]);
        $context = ['json_encode_options' => defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0];
        $output = $serializer->serialize($data, 'json', $context);

        if($echo)
        {
            header('Content-Disposition: inline; filename="' . $parser->title . '.json"');
            header('Content-Type: application/json;charset=utf-8');
            echo $output;
        }

        return $output;
    }
}
