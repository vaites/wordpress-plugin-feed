<?php namespace WordPressPluginFeed\Generators\Formats;

use Zend\Feed\Writer\Feed;

use WordPressPluginFeed\Parsers\Parser;
use WordPressPluginFeed\Generators\Generator;


/**
 * Feed generator
 *
 * @package WordPressPluginFeed\Generators\Formats
 */
class AtomGenerator extends Generator
{
    /**
     * Feed format
     *
     * @var string
     */
    protected $format = 'atom';

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

        $time = is_null($this->parser->modified) ? time() : $this->parser->modified->timestamp;

        $feed = new Feed();
        $feed->setTitle($this->parser->title);
        $feed->setLink($this->parser->link);
        $feed->setDateModified($time);
        $feed->addHub('http://pubsubhubbub.appspot.com/');
        $feed->setFeedLink($this->parser->feed_link, 'atom');

        if(!is_null($this->parser->description))
        {
            $feed->setDescription($this->parser->description);
        }

        if(!empty($this->parser->image['uri']))
        {
            $feed->setImage(array
            (
                'height' => $this->parser->image['height'],
                'link' => $feed->getLink(),
                'title' => $this->parser->title,
                'uri' => sprintf($this->parser->image['uri'], $this->parser->plugin),
                'width' => $this->parser->image['width']
            ));
        }

        foreach($this->parser->getReleases($limit) as $release)
        {
            // feed entry
            $entry = $feed->createEntry();
            $entry->setId(sha1($release->title));
            $entry->setTitle($release->title);
            $entry->setLink($release->link);
            $entry->setDateModified($release->created->timestamp);
            $entry->setDateCreated($release->created->timestamp);
            $entry->setDescription($release->content);

            foreach($release->categories as $category)
            {
                $entry->addCategory(array('term' => $category));
            }

            // entry author
            if(is_string($release->author))
            {
                $entry->addAuthor(array
                (
                    'name' => $release->author,
                    'uri' => "https://profiles.wordpress.org/{$release->author}/"
                ));
            }
            elseif(is_array($release->author))
            {
                $entry->addAuthor($release->author);
            }

            $feed->addEntry($entry);
        }

        $output = $feed->export($this->format);

        if($echo)
        {
            header('Content-Type: text/xml;charset=utf-8');
            echo $output;
        }

        return $output;
    }
}