<?php namespace WordPressPluginFeed\Generators;

use WordPressPluginFeed\Parsers\Parser;

/**
 * Base generator
 *
 * @package WordPressPluginFeed\Generators
 */
abstract class Generator
{
    /**
     * @var
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
     *
     * @param   string  $format
     * @param   Parser  $parser
     * @return  \WordPressPluginFeed\Generators\Generator
     * @throws  \Exception
     */
    public static function getInstance($format, Parser $parser = null)
    {
        if(!isset(static::$aliases[$format]))
        {
            throw new \Exception("Format not supported");
        }

        $class = "WordPressPluginFeed\\Generators\\" . self::$aliases[$format];

        return new $class($parser);
    }

    /**
     * Get release list
     *
     * @return  Parser
     */
    public function getParser()
    {
        return $this->parser;
    }

    /**
     * Set release list
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
     * Generate and print output
     *
     * @param   Parser  $parser
     **/
    abstract public function generate(Parser $parser = null);
}