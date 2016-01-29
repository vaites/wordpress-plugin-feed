[![Current release](https://img.shields.io/github/release/vaites/wordpress-plugin-feed.svg)](https://github.com/vaites/wordpress-plugin-feed/releases/latest)
[![Build status](https://travis-ci.org/vaites/wordpress-plugin-feed.svg?branch=master)](https://travis-ci.org/vaites/wordpress-plugin-feed)
[![Code coverage](https://img.shields.io/codecov/c/github/vaites/wordpress-plugin-feed.svg)](https://codecov.io/github/vaites/wordpress-plugin-feed)
[![Dependecies](https://img.shields.io/gemnasium/vaites/wordpress-plugin-feed.svg)](https://gemnasium.com/vaites/wordpress-plugin-feed)

WordPress Plugin Feed
=====================

![Example](https://raw.githubusercontent.com/vaites/wordpress-plugin-feed/master/example.png)

WordPress developers should be informed of the updates of the plugins that use. Automattic provides feeds that do not give the necessary information:

* [Akismet feed](https://wordpress.org/plugins/rss/topic/akismet)
* [Akismet development log](https://plugins.trac.wordpress.org/log/akismet?limit=100&mode=stop_on_copy&format=rss)

WordPress Feed Plugin provides detailed feeds for plugin releases, avoiding the need to review the WordPress control panel or visit the profile of each plugin.

Features:
* Full human readable changelog
* Accurate release date (based on Subversion commits)
* Link to Subversion commit list between releases
* Highlighted security updates
  * Link to known vulnerabilities on [CVE Details](http://www.cvedetails.com) and [WPScan Vulnerability Database](https://wpvulndb.com)
* [Semantic Versioning](http://semver.org/) syntax
* Multiple output formats (Atom, RSS, JSON and XML)
* Support for (less accurate) changelog of plugins with external information:
  * [All-In-One SEO Pack](http://semperplugins.com/plugins/all-in-one-seo-pack-pro-version/)
  * [BuddyPress](https://buddypress.org/)
  * [Gravity Forms](http://www.gravityforms.com/)
  * [Google XML Sitemaps](http://www.arnebrachhold.de/projects/wordpress-plugins/google-xml-sitemaps-generator/)
  * [Slider Revolution](http://www.themepunch.com/portfolio/slider-revolution-wordpress-plugin/)
  * [The WordPress Multilingual Plugin (WPML)](http://wpml.org)
  * [UberMenu](http://wpmegamenu.com/)
  * [Ultimade Addons for Visual Composer](http://vc.wpbakery.com/addons/ultimate-addon-visual-composer/)
  * [Visual Composer](http://vc.wpbakery.com/)
  * [Yoast SEO Premium](https://yoast.com/wordpress/plugins/seo-premium/)
* Compatible with PHP 5.3 or greater

Because this tool parses HTML of different websites, result cannot be 100% accurate and can fail after a change in the code. So issues and pull requests are welcome...

Installation
------------

Just download the release package and place the code on any web server, or clone the repository:

    git clone https://github.com/vaites/wordpress-plugin-feed
    cd wordpress-plugin-feed
    composer update

Remember to run `composer update` after each update.

Usage
-----

Use GET parameters:

    http://your/web/server/wordpress-plugin-feed/index.php?plugin=PLUGIN

Or use the command line interface:

    ./cli.php generate --plugin=PLUGIN > feed.xml

Replace *PLUGIN* with plugin name, the same as WordPress uses in plugin URL 
(like *better-wp-security* for [iThemes Security](https://wordpress.org/plugins/better-wp-security))

Options
-------

[PHP Dotenv](https://github.com/vlucas/phpdotenv), GET or CLI parameters are used to define configuration:
* **Limit**: number of releases on output (default 25)
  * GET: `limit=10`
  * CLI: `--limit="10"`
  * ENV: `OUTPUT_LIMIT="10"`
* **Format**: atom, rss, json or xml (default atom)
  * GET: `format=rss`
  * CLI: `--format="rss""`
  * ENV: `OUTPUT_FORMAT="rss"`
* **Filter**: terms to match against title and content (default none)
  * GET: `filter=security`
  * CLI: `--filter="security""`
  * ENV: `OUTPUT_FILTER="security"`
* **Stability**: one o more stability options (any, stable, alpha, beta, rc) separated by commas (default any)
  * GET: `stability=stable,rc`
  * CLI: `--stability="stable,rc"`
  * ENV: `RELEASE_STABILITY="stable,rc"`
  
Other options are only configurable with environment variables:
* CACHE_TTL: cache life in seconds

There's an *.env.example* file that you can copy to *.env*.

TO-DO
-----
* PHAR Package instead of ZIPs
* Add database persistence
* Add tags based on contents
* Enhance search&replace (HTML safe)