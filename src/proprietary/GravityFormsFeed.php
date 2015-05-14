<?php

use Carbon\Carbon;
use Zend\Feed\Reader\Reader;

/**
 * Slider Revolution custom parser
 *
 * @author David Martínez <contacto@davidmartinez.net>
 */
class GravityFormsFeed extends WordPressPluginFeed
{
    /**
     * Plugin title
     *
     * @var string
     */
    protected $title = 'Gravity Forms';
    
    /**
     * Plugin short description
     *
     * @var string
     */
    protected $description = 'Gravity Forms for WordPress is a full featured contact form plugin that features a drag and drop interface, advanced notification routing, lead capture, conditional logic fields, multi-page forms, pricing calculations and the ability to create posts from external forms.';

    /**
     * Plugin image
     * 
     * @var string
     */
    protected $image = 
    [
        'uri' => 'http://gravityforms.s3.amazonaws.com/logos/gravityforms_logo_100.png',
        'height' => 100,
        'width' => 116
    ];
    
    /**
     * Source URLs 
     *
     * @var array
     */    
    protected $sources = 
    [
        'profile'   => 'http://www.gravityhelp.com/feed/atom/',
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
                $regexp = '/^Gravity Forms v(\d|\.)+ Released/i';
                if(!preg_match($regexp, $entry->getTitle()))
                {
                    continue;
                }

                // convert release title to version
                $version = $this->parseVersion($entry->getTitle());
                
                // creation time
                $created = $entry->getDateCreated()->getTimestamp();

                // release object
                $release = new stdClass();
                $release->link = $entry->getLink();
                $release->title = "{$this->title} $version";
                $release->description = $entry->getDescription();
                $release->stability = $this->parseStability($entry->getTitle());
                $release->created = Carbon::createFromTimestamp($created);
                $release->content = $entry->getContent();

                $this->releases[$version] = $release;
            }
        }
    }
}
