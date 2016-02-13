<?php namespace WordPressPluginFeed\Parsers\Generic;

use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * Generic parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class GenericParser extends Parser
{
    /**
     * Release list container selector
     *
     * @var string
     */
    protected $container = null;

    /**
     * Block separator selector that contains version number
     *
     * @var null
     */
    protected $block = null;

    /**
     * For open source plugins, tag must exist on SubVersion
     *
     * @var bool
     */
    protected $useTags = false;

    /**
     * Parse public releases using changelog page on authors web
     */
    protected function loadReleases()
    {
        // tags need to be loaded before parse releases
        if($this->useTags == true)
        {
            $this->loadTags();
        }

        // get profile source
        $source = isset($this->sources['changelog']) ? 'changelog' : 'profile';
        $crawler = new Crawler($this->fetch($source));

        // changelog is inside container
        $changelog = $crawler->filter($this->container)->children();

        // process each block inside container
        foreach($changelog->filter($this->block) as $index=>$node)
        {
            // convert release title to version
            $version = $this->parseVersion($node->textContent);

            // tag must exist in Subversion?
            if($this->useTags == true && !isset($this->tags[$version]))
            {
                continue;
            }

            // release object
            $release = new Release($this->title, $version);
            $release->link = $this->sources['profile'];
            $release->stability = $this->parseStability($node->textContent);

            // creation date based on tag
            if($this->useTags == true && isset($this->tags[$version]))
            {
                $release->created = $this->tags[$version]->created;
            }

            // nodes that follows block separator are the details
            $details = $changelog->filter($this->block)->eq($index)->nextAll();
            foreach($details as $n=>$node)
            {
                $tagname = $node->tagName;

                if($tagname != $this->block)
                {
                    $release->content .= "<$tagname>" . $details->eq($n)->html() . "</$tagname>" . PHP_EOL;
                }
                else
                {
                    break;
                }
            }

            $this->addRelease($release);
        }
    }
}
