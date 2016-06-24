<?php namespace WordPressPluginFeed;

use Carbon\Carbon;
use HTMLPurifier;
use HTMLPurifier_Config;

use Symfony\Component\DomCrawler\Crawler;

use WordPressPluginFeed\Parsers\Parser;

class Release
{
    /**
     * Unique identifier (version + stability)
     *
     * @var string
     */
    public $id;

    /**
     * Version
     *
     * @var string
     */
    public $version;

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
     * User defined categories
     *
     * @var array
     */
    public $categories = array();

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
     * List of fixed vulnerabilities
     *
     * @var array
     */
    public $vulnerabilities;

    /**
     * Is a security release?
     */
    public $security = false;

    /**
     * Keywords that activate "Security release" message
     *
     * @var array
     */
    protected static $keywords = array
    (
        'safe', 'trusted', 'security', 'insecure', 'vulnerability', 'leak', 'attack', 'malware', 'malicious',
        'CSRF', 'SSRF', 'SQLi', 'SQL.+injection', 'XSS', 'XXE', 'LFI', 'RFI', 'MITM', 'CVE-',
    );

    /**
     * HTMLPurifier instance
     *
     * @var \HTMLPurifier
     */
    protected static $purifier = null;

    /**
     * Parser instance
     *
     * @var \WordPressPluginFeed\Parsers\Parser
     */
    protected $parser = null;

    /**
     * Instantiate dependencies
     *
     * @param   string  $title
     * @param   string  $version
     */
    public function __construct($title, $version, $stability)
    {
        // set mandatory properties
        $this->id = "$version-$stability";
        $this->title = "$title $version";
        $this->version = $version;
        $this->stability = $stability;
        $this->created = Carbon::now();

        // HTMLPurifier instance
        if(empty(self::$purifier))
        {
            self::$purifier = new HTMLPurifier(HTMLPurifier_Config::create(array
            (
                'Attr.AllowedFrameTargets' => array('_blank')
            )));
        }
    }

    /**
     * Filter a release adding type, warnings and other stuff
     *
     * @param   string  $filter
     * @return  self
     */
    public function filter($filter = null)
    {
        // add release type
        if($this->stability != 'stable')
        {
            $this->title .= '-' . $this->stability;
        }

        // replace known strings
        $this->content = preg_replace('/^Commit message:\s+/i', '', $this->content);

        // purify HTML
        $this->content = self::$purifier->purify($this->content);
        $this->content = html_entity_decode($this->content);

        // create a DOM crawler to modify HTML
        $crawler = new Crawler($this->content);

        // add target="_blank" to all links
        foreach($crawler->filter('a') as $index=>$node)
        {
            $node->setAttribute('target', '_blank');
        }
        $this->content = utf8_decode($crawler->html());

        // base for highlights
        $content = strip_tags($this->content);
        $content = preg_replace('/' . preg_quote($this->parser->title) . '/i', '', $content);
        $keywords = implode('|', self::$keywords);
        $highlight = array();

        // detect filtered words
        if(!empty($filter) && preg_match_all($filter, $content, $match))
        {
            foreach(array_unique($match[1]) as $keyword)
            {
                $highlight[$keyword] = $keyword;
            }
        }

        // detect security keywords
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
            $link = '<a href="http://www.cvedetails.com/cve/%s/" target="_blank">%s</a>';

            $highlight[$match[0]] = sprintf($link, $match[0], $match[0]);
        }

        // add warning to title and highlight security keywords
        if(!empty($highlight))
        {
            $this->title .= ' (Security release)';

            // highlight security keywords
            foreach($highlight as $search=>$replace)
            {
                $this->content = preg_replace
                (
                    "/$search/",
                    '<strong style="color:red">' . $replace . '</strong>',
                    $this->content
                );
            }

            // set as security release
            $this->security = true;
        }

        // add warning to title and links to WPScan Vulnerability Database
        if(!empty($this->vulnerabilities))
        {
            if(empty($highlight))
            {
                $this->title .= ' (Security release)';
            }

            // add vulnerability list at the end of content
            $this->content .= "\n\n<p>Fixed vulnerabilities:</p>\n<ul>";
            foreach($this->vulnerabilities as $vulnerability)
            {
                $this->content .= "<li><a href=\"https://wpvulndb.com/vulnerabilities/{$vulnerability->id}\">"
                                 . "{$vulnerability->title}</a></li>\n";

            }

            $this->content .= "\n</ul>";

            // set as security release
            $this->security = true;
        }

        return $this;
    }

    /**
     * Sets the parser instance
     *
     * @param   Parser $parser
     * @return  $this
     */
    public function setParser(Parser $parser)
    {
        $this->parser = $parser;

        return $this;
    }
}