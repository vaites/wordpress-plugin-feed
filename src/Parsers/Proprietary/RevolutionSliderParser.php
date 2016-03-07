<?php namespace WordPressPluginFeed\Parsers\Proprietary;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * Slider Revolution custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class RevolutionSliderParser extends Parser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'Revolution Slider';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'Create a responsive(mobile friendly) or fullwidth slider with must-see-effects and meanwhile keep or build your SEO optimization (all content always readable for search engines)';
    
    /**
     * Plugin image
     * 
     * @var string
     */
    public $image = array
    (
        'uri' => 'https://thumb-cc.s3.envato.com/files/144560542/smallicon.png',
        'height' => 80,
        'width' => 80
    );

    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = array
    (
        'changelog' => 'http://codecanyon.net/item/slider-revolution-responsive-wordpress-plugin/2751380',
    );

    /**
     * Parse public releases using "release log" block on Code Canyon profile
     */
    protected function loadReleases()
    {
        // profile
        $crawler = new Crawler($this->fetch('changelog'));

        // need to parse changelog block
        $changelog = $crawler->filter('#item-description__ressources-credits')->nextAll();

        // each h3 is a release
        foreach($changelog->filter('h3') as $index=>$node)
        {
            // convert release title to version
            $version = $this->parseVersion($node->textContent);

            // title must have pubdate
            if(preg_match('/(.+) \((.+)\)/i', $node->textContent, $pubdate))
            {
                // get the ID to build link
                $id = $changelog->filter('h3')->eq($index)->attr('id');

                // release object
                $release = new Release($this->title, $version, $this->parseStability($node->textContent));
                $release->link = "{$this->sources['changelog']}#{$id}";

                // nodes that follows h3 are the details
                $details = $changelog->filter('h3')->eq($index)->nextAll();
                foreach($details as $n=>$node)
                {
                    $tagname = $node->tagName;
                    if($tagname != 'h3')
                    {
                        $release->content .= "<$tagname>" . $details->eq($n)->html() . "</$tagname>" . PHP_EOL;
                    }
                    else
                    {
                        break;
                    }
                }

                // pubdate needs to be parsed
                $pubdate[2] = preg_replace('/dezember/i', 'december', $pubdate[2]);
                $release->created = Carbon::parse($pubdate[2]);

                $this->addRelease($release);
            }
        }
    }
}
