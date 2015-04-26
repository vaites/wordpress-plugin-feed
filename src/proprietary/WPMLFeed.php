<?php

use Carbon\Carbon;
use Zend\Feed\Reader\Reader;

/**
 * Slider Revolution custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class WPMLFeed extends WordPressPluginFeed
{
    /**
     * Plugin title
     *
     * @var string
     */
    protected $title = 'The WordPress Multilingual Plugin';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    protected $description = 'WPML makes it easy to build multilingual sites'
            . 'and run them. It’s powerful enough for corporate sites, '
            . 'yet simple for blogs.';

    /**
     * Plugin image
     * 
     * @var string
     */
    protected $image = 
    [
        'uri' => 'https://d2salfytceyqoe.cloudfront.net/wp-content'
            . '/uploads/2010/09/wpml_logo.png',
        'height' => 265,
        'width' => 101
    ];
    
    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = 
    [
        'profile'   => 'https://wpml.org/category/changelog/feed/atom/',
    ];
    
    /**
     * Parse public releases using feed from official blog
     */    
    protected function loadReleases()
    {
        // fetch 5 pages of feed
        for($p = 0; $p < 5; $p++)
        {
            $query = "?paged=$p";
            $changelog = Reader::importString($this->fetch('profile', $query));

            // each entry can be a release
            foreach($changelog as $entry)
            {
                // WPML releases starts with "WPML"
                if(!preg_match('/^WPML\s+\d+\./i', $entry->getTitle()))
                {
                    continue;
                }

                // convert release title to version
                $version = preg_replace('/^WPML\s+/i', '', $entry->getTitle());
                $version = preg_replace('/^v(ersion\s*)?/i','', trim($version));
                $version = preg_replace('/\s+(.+)$/', '', trim($version));
                
                // avoid betas
                if(preg_match('/b\d+/i', $version))
                {
                    continue;
                }
                elseif(preg_match('/beta/i', $entry->getLink()))
                {
                    continue;
                }

                // creation time
                $created = $entry->getDateCreated()->getTimestamp();

                // release object
                $release = new stdClass();
                $release->link = $entry->getLink();
                $release->title = "{$this->title} $version";
                $release->description = $entry->getDescription();
                $release->created = Carbon::createFromTimestamp($created);
                $release->content = $entry->getContent();

                $this->releases[$version] = $release;
            }
        }
    }
}
