<?php namespace WordPressPluginFeed\Parsers\Proprietary;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * The WordPress Multilingual Plugin custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class WPMLParser extends Parser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'The WordPress Multilingual Plugin';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'WPML makes it easy to build multilingual sites and run them. It’s powerful enough for corporate sites,  yet simple for blogs.';

    /**
     * Plugin image
     * 
     * @var string
     */
    public $image = array
    (
        'uri' => 'https://d2salfytceyqoe.cloudfront.net/wp-content/uploads/2010/09/wpml_logo.png',
        'height' => 265,
        'width' => 101
    );
    
    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'profile'   => 'https://wpml.org/category/changelog/'
    );
    
    /**
     * Parse public releases using changelog category from official blog
     */    
    protected function loadReleases()
    {
        // fetch 5 pages of feed
        for($p = 0; $p < 5; $p++)
        {
            // profile
            $crawler = new Crawler($this->fetch('profile', "?paged=$p"));

            // need to parse changelog block
            $changelog = $crawler->filter('.post > h2');

            // each h2 is a release
            foreach($changelog as $index=>$node)
            {
                // title must start with WPML and version
                $regexp = '/^WPML\s+(\d+)\.(\d+)(\.(\d+))?\s+/i';
                if(!preg_match($regexp, $node->textContent))
                {
                    continue;
                }

                // convert release title to version
                $version = $this->parseVersion($node->textContent);

                // release object
                $release = new Release();
                $release->link = $changelog->eq($index)->filter('a')->attr('href');
                $release->title = "{$this->title} $version";
                $release->description = false;
                $release->stability = $this->parseStability($node->textContent);
                $release->created = time();
                $release->content = $crawler->filter('.post .entry')->eq($index)->html();

                // pubdate needs to be parsed
                $release->created = Carbon::parse(preg_replace('/\s+by(.+)/', '',
                    trim($crawler->filter('.post > small')->eq($index)->text())));

                $this->releases[$version] = $release;
            }
        }
    }
}
