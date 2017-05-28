<?php namespace WordPressPluginFeed;

class Tag
{
    /**
     * Subversion tag name
     *
     * @var string
     */
    public $name;

    /**
     * Subversion revision
     *
     * @var int
     */
    public $revision;

    /**
     * Subversion commit message
     *
     * @var string
     */
    public $description;

    /**
     * Subversion commit author
     *
     * @var
     */
    public $author;

    /**
     * Publish date
     *
     * @var \Carbon\Carbon
     */
    public $created;
}
