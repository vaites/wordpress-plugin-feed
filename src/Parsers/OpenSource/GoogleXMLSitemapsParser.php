<?php namespace WordPressPluginFeed\Parsers\OpenSource;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * BuddyPress custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class GoogleXMLSitemapsParser extends Parser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'Google (XML) Sitemap Generator';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'This plugin generates a XML-Sitemap compliant sitemap of your WordPress blog. This format is supported by Ask.com, Google, YAHOO and MSN Search.';

    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'profile'   => 'http://www.arnebrachhold.de/projects/wordpress-plugins/google-xml-sitemaps-generator/changelog/',
        'tags'      => 'https://plugins.trac.wordpress.org/browser/%s/tags?order=date&desc=1'
    );
    
    /**
     * Parse public releases using changelog page on authors web
     */    
    protected function loadReleases()
    {
        // tags need to be loaded before parse releases
        $this->loadTags();

        // profile
        $crawler = new Crawler($this->fetch('profile'));

        // need to parse changelog block
        $changelog = $crawler->filter('.storycontent')->children();

        // each p is a release
        foreach($changelog->filter('p') as $index=>$node)
        {
            // first paragraph is a small description
            if($index == 0)
            {
                continue;
            }

            // convert release title to version
            $version = $this->parseVersion($node->textContent);

            // tag must exist in Subversion
            if(!isset($this->tags[$version]))
            {
                continue;
            }

            // release object
            $release = new Release($this->title, $version);
            $release->link = $this->sources['profile'];
            $release->stability = $this->parseStability($node->textContent);
            $release->created = $this->tags[$version]->created;

            // nodes that follows p are the details
            $details = $changelog->filter('p')->eq($index)->nextAll();
            foreach($details as $n=>$node)
            {
                $tag = $node->tagName;
                if($tag != 'p')
                {
                    $release->content .= "<$tag>" . $details->eq($n)->html() . "</$tag>" . PHP_EOL;
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
