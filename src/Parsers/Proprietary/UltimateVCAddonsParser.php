<?php namespace WordPressPluginFeed\Parsers\Proprietary;

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
        'changelog' => 'https://changelog.brainstormforce.com/ultimate',
    );
    
    /**
     * Parse public releases using "release log" block on Code Canyon profile
     */    
    protected function loadReleases()
    {
        // profile
        $crawler = new Crawler($this->fetch('changelog'));
        
        // need to parse changelog block
        $changelog = $crawler->filter('#recent-posts-2 ul')->children();

        // each p is a release
        foreach($changelog->filter('li') as $index=>$node)
        {
            // title must have pubdate and version
            if(preg_match('/Version\s+(.+)/i', $node->textContent, $match))
            {
                // convert release title to version
                $version = $this->parseVersion($match[0 ]);

                // release object
                $release = new Release($this->title, $version, $this->parseStability($version));
                $release->link = $changelog->filter('li')->eq($index)->children()->getNode(0)->getAttribute('href');

                // fetch the post
                $this->sources["version-{$release->version}"] = $release->link;
                $detail = new Crawler($this->fetch("version-{$release->version}"));

                // pre that follows p are the details
                $release->content = $detail->filter('.entry-content')->html();

                // pubdate needs to be parsed
                $pubdate = $detail->filter('.entry-footer time')->getNode(0)->getAttribute('datetime');
                $release->created = $this->parseDate($pubdate);

                $this->addRelease($release);

                if($index == 5)
                {
                    break;
                }
            }
        }
    }
}
