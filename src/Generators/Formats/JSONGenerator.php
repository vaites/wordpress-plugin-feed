<?php namespace WordPressPluginFeed\Generators\Formats;

use stdClass;

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
     * Generates the feed
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

        $time = is_null($this->parser->modified) ? time() : $this->parser->modified->timestamp;

        $json = new stdClass();
        $json->title = $this->parser->title;
        $json->link = $this->parser->link;
        $json->modified = $time;
        $json->description = $this->parser->description;

        if(!empty($this->parser->image['uri']))
        {
            $json->image = array
            (
                'height' => $this->parser->image['height'],
                'link' => $this->parser->link,
                'title' => $this->parser->title,
                'uri' => sprintf($this->parser->image['uri'], $this->parser->plugin),
                'width' => $this->parser->image['width']
            );
        }

        $json->releases = array();
        foreach($this->parser->getReleases($limit) as $release)
        {
            // stability filter
            if($this->parser->stability != false)
            {
                if(!preg_match($this->parser->stability, $release->stability))
                {
                    continue;
                }
            }

            // feed entry
            $entry = new stdClass();;
            $entry->id = sha1($release->title);
            $entry->title = $release->title;
            $entry->version = $release->version;
            $entry->stability = $release->stability;
            $entry->security = $release->security;
            $entry->link = $release->link;
            $entry->description = $release->content;
            $entry->modified = $release->created->timestamp;
            $entry->created = $release->created->timestamp;

            // entry author
            if(is_string($release->author))
            {
                $entry->author = array
                (
                    'name' => $release->author,
                    'uri' => "https://profiles.wordpress.org/{$release->author}/"
                );
            }
            elseif(is_array($release->author))
            {
                $entry->author = $release->author;
            }

            $json->releases[] = $entry;
        }

        $output = json_encode($json, defined('JSON_PRETTY_PRINT') ? JSON_PRETTY_PRINT : 0);

        if($echo)
        {
            header('Content-Type: application/json;charset=utf-8');
            echo $output;
        }

        return $output;
    }
}