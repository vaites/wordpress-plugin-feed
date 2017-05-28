<?php namespace WordPressPluginFeed\Generators\Formats;

use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

use WordPressPluginFeed\Parsers\Parser;
use WordPressPluginFeed\Generators\Generator;

/**
 * XML generator
 *
 * @package WordPressPluginFeed\Generators\Formats
 */
class XMLGenerator extends Generator
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
        $serializer = new Serializer(array(new ObjectNormalizer()), array(new XmlEncoder()));
        $context = array('xml_root_node_name' => 'plugin');
        $output = $serializer->serialize($data, 'xml', $context);

        if($echo)
        {
            header('Content-Disposition: inline; filename="' . $parser->title . '.xml"');
            header('Content-Type: application/xml;charset=utf-8');
            echo $output;
        }

        return $output;
    }
}
