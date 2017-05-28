<?php namespace WordPressPluginFeed\Generators;

use Exception;
use stdClass;

use WordPressPluginFeed\Parsers\Parser;

/**
 * Base generator
 *
 * @package WordPressPluginFeed\Generators
 */
abstract class Generator
{
    /**
     * Parser instance
     *
     * @var Parser
     */
    protected $parser;

    /**
     * List of supported formats
     *
     * @var array
     */
    protected static $aliases = array
    (
        'atom'  => 'Formats\\AtomGenerator',
        'rss'   => 'Formats\\RSSGenerator',
        'json'  => 'Formats\\JSONGenerator',
        'xml'   => 'Formats\\XMLGenerator',
        'yaml'  => 'Formats\\YAMLGenerator'
    );

    /**
     * Set release list on construct
     *
     * @param   Parser   $parser
     */
    public function __construct(Parser $parser = null)
    {
        if(!is_null($parser))
        {
            $this->setParser($parser);
        }
    }

    /**
     * Get a generator class instance based on format
     * Default format defined in .env file (OUTPUT_FORMAT)
     *
     * @param   string  $format
     * @param   Parser  $parser
     * @return  \WordPressPluginFeed\Generators\Generator
     * @throws  \Exception
     */
    public static function getInstance($format = null, Parser $parser = null)
    {
        if(is_null($format))
        {
            $format = getenv('OUTPUT_FORMAT') ?: key(static::$aliases);
        }

        if(!isset(static::$aliases[$format]))
        {
            throw new Exception("Format not supported");
        }

        $class = "WordPressPluginFeed\\Generators\\" . self::$aliases[$format];

        return new $class($parser);
    }

    /**
     * Get parser
     *
     * @return  Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Set parser
     *
     * @param   Parser  $parser
     * @return  $this
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;

        return $this;
    }

    /**
     * Serialize parsed data to a basic class with plugin info and release list
     *
     * @param   string  $mode   array or object
     * @param   int     $limit
     * @return  stdClass
     */
    protected function serialize($mode = 'array', $limit = null)
    {
        $time = is_null($this->parser->modified) ? time() : $this->parser->modified->timestamp;

        $data = new stdClass();
        $data->title = $this->parser->title;
        $data->name = $this->parser->plugin;
        $data->link = $this->parser->link;
        $data->modified = $time;
        $data->description = $this->parser->description;

        if(!empty($this->parser->image['uri']))
        {
            $data->image = new stdClass();
            $data->image->height = $this->parser->image['height'];
            $data->image->link = $this->parser->link;
            $data->image->title = $this->parser->title;
            $data->image->uri = sprintf($this->parser->image['uri'], $this->parser->plugin);
            $data->image->width = $this->parser->image['width'];

            if($mode == 'array')
            {
                $data->image = (array) $data->image;
            }
        }

        $data->releases = array();
        foreach($this->parser->getReleases($limit) as $release)
        {
            $item = new stdClass();;
            $item->id = sha1($release->title);
            $item->title = $release->title;
            $item->version = $release->version;
            $item->stability = $release->stability;
            $item->security = $release->security;
            $item->link = $release->link;
            $item->author = null;
            $item->categories = $release->categories;
            $item->description = $release->content;
            $item->modified = $release->created->timestamp;
            $item->created = $release->created->timestamp;

            if(is_string($release->author))
            {
                $item->author = array
                (
                    'name' => $release->author,
                    'uri' => "https://profiles.wordpress.org/{$release->author}/"
                );
            }
            elseif(is_array($release->author))
            {
                $item->author = $release->author;
            }

            if($mode == 'array')
            {
                $data->releases[] = (array) $item;
            }
            else
            {
                $data->releases[] = $item;
            }
        }

        return ($mode == 'array') ? (array) $data : $data;
    }

    /**
     * Generate and returns output, printing if specified
     *
     * @param   Parser  $parser
     * @param   int     $limit
     * @param   boolean $echo
     * @return  string
     **/
    abstract public function generate(Parser $parser = null, $limit = null, $echo = true);
}
