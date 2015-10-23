<?php namespace WordPressPluginFeed;

use HTMLPurifier;
use HTMLPurifier_Config;

use Symfony\Component\DomCrawler\Crawler;

class Release
{
    /**
     * Plugin name and version
     *
     * @var string
     */
    public $title;

    /**
     * Changelog excerpt
     *
     * @var string
     */
    public $description;

    /**
     * Release author
     *
     * @var array|string
     */
    public $author;

    /**
     * Stability
     *
     * @var string
     */
    public $stability;

    /**
     * Permalink to release info
     *
     * @var string
     */
    public $link;

    /**
     * Changelog
     *
     * @var string
     */
    public $content;

    /**
     * Publish date
     *
     * @var \Carbon\Carbon
     */
    public $created;

    /**
     * Keywords that activate "Security release" message
     *
     * @var array
     */
    protected static $keywords = array
    (
        'safe', 'trusted', 'security', 'vulnerability', 'leak', 'attack',
        'CSRF', 'SQLi', 'XSS', 'LFI', 'RFI', 'CVE-'
    );

    /**
     * HTMLPurifier instance
     *
     * @var \HTMLPurifier
     */
    protected $purifier = null;

    /**
     * Instantiate dependencies
     */
    public function __construct()
    {
        // HTMLPurifier instance
        $this->purifier = new HTMLPurifier(HTMLPurifier_Config::create(array
        (
            'Attr.AllowedFrameTargets' => array('_blank')
        )));
    }

    /**
     * Filter a release adding type, warnings and other stuff
     *
     * @return  self
     */
    public function filter()
    {
        // add release type
        if($this->stability != 'stable')
        {
            $this->title .= '-' . $this->stability;
        }

        // purify HTML
        $this->content = $this->purifier->purify($this->content);
        $this->content = html_entity_decode($this->content);

        // create a DOM crawler to modify HTML
        $crawler = new Crawler($this->content);

        // add target="_blank" to all links
        foreach($crawler->filter('a') as $index=>$node)
        {
            $node->setAttribute('target', '_blank');
        }
        $this->content = utf8_decode($crawler->html());

        // detect security keywords
        $content = strip_tags($this->content);
        $keywords = implode('|', self::$keywords);

        if(preg_match_all("/(^|\W)($keywords)(\W|$)/i", $content, $match))
        {
            foreach(array_unique($match[2]) as $keyword)
            {
                $highlight[$keyword] = $keyword;
            }
        }

        // detect Common Vulnerabilities and Exposures
        if(preg_match('/CVE-(\d{4})-(\d{4})/i', $this->content, $match))
        {
            $link = '<a href="http://www.cvedetails.com/cve/%s/" '
                . 'target="_blank">%s</a>';

            $highlight[$match[0]] = sprintf($link, $match[0], $match[0]);
        }

        // add warning to title and highlight security keywords
        if(!empty($highlight))
        {
            $this->title .= ' (Security release)';

            foreach($highlight as $search=>$replace)
            {
                $this->content = preg_replace
                (
                    "/$search/",
                    '<strong style="color:red">' . $replace . '</strong>',
                    $this->content
                );
            }
        }

        return $this;
    }
}