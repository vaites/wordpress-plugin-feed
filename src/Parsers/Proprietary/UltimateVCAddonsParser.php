<?php namespace WordPressPluginFeed\Parsers\Proprietary;

use Carbon\Carbon;
use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Release;
use WordPressPluginFeed\Parsers\Parser;

/**
 * Ultimate Addons for Visual Composer custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class UltimateVCAddonsParser extends Parser
{
    /**
     * Plugin title
     *
     * @var string
     */
    public $title = 'Ultimate Addons for Visual Composer';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    public $description = 'This plugin adds several premium elements in your Visual Composer on top of the built-in ones given by WPBakery.';
    
    /**
     * Plugin image
     * 
     * @var string
     */
    public $image = array
    (
        'uri' => 'https://thumb-cc.s3.envato.com/files/86787603/80x80.png',
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
        'changelog' => 'http://codecanyon.net/item/ultimate-addons-for-visual-composer/6892199',
    );
    
    /**
     * Parse public releases using "release log" block on Code Canyon profile
     */    
    protected function loadReleases()
    {
        // profile
        $crawler = new Crawler($this->fetch('changelog'));
        
        // need to parse changelog block
        $changelog = $crawler->filter('#item-description__changelog')->nextAll();

        // each p is a release
        foreach($changelog->filter('p') as $index=>$node)
        {
            // title must have pubdate and version
            if(preg_match('/(\w+)\s+(\d+),\s+(\d+).+Version\s+(.+)/i', $node->textContent, $match))
            {
                // convert release title to version
                $version = $this->parseVersion($match[4]);

                // get de ID to build the link
                $id = 'item-description__changelog';

                // release object
                $release = new Release($this->title, $version, $this->parseStability($version));
                $release->link = "{$this->sources['profile']}#$id";

                // pre that follows p are the details
                $details = $changelog->filter('p')->eq($index)->nextAll();
                foreach($details as $n=>$node)
                {
                    if($node->tagName == 'pre')
                    {
                        $text = $details->eq($n)->text();
                        $release->content .= str_replace("\n", "<br />\n", $text);
                    }
                    else
                    {
                        break;
                    }
                }

                // pubdate needs to be parsed
                $pubdate = $match[1] . ' ' . $match[2] . ', ' . $match[3];
                $release->created = Carbon::parse($pubdate);

                $this->addRelease($release);
            }
        }
    }
}
