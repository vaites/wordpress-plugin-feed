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

Because this tool parses HTML of different websites, result cannot be 100% accurate and can fail after a change in the code. So pull requests are welcome...

Usage
-----

Just place the code on any web server that supports PHP and run `composer update`:

    git clone https://github.com/vaites/wordpress-plugin-feed
    cd wordpress-plugin-feed
    composer update

Then, add to your favorite feed reader:

    http://your/web/server/wordpress-plugin-feed/index.php?plugin=PLUGIN

Replace *PLUGIN* with the name of the plugin you want to track, the same as WordPress uses in plugin URL (like *better-wp-security* for [iThemes Security](https://wordpress.org/plugins/better-wp-security)).