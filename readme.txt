=== WP RSS Multi Importer ===
Contributors: allenweiss
Tags: rss, feeds, aggregation, aggregator, import
Requires at least: 2.9
Tested up to: 3.3.1
Stable tag: 0.5
Imports and merges multiple RSS Feeds using SimplePie. Options including sorting feeds by date, limit feeds, include feed name and excerpts.

== Description ==

WP RSS Multi Importer helps you create a feed reader on your Wordpress site.  The plugin extends WP RSS Aggregator, and allows more flexibility by adding and deleting specific feeds, sorting by date, limiting posts per feed.  This works well for news items as well as events (which are sorted differently than news items). You can add any number of feeds through an administration panel, the plugin will then pull all the feeds from these sites, merge them and sort them by date.  You can easily delete a specific feed and add excerpts from news feeds.

The output will be organized like this:

Title
Date
Source

and sorted by date ascending or descending.  The Source attribution can be changed to Club, Sponsor or no attribution (e.g., Source: LA Times, or Club: Consulting Club).

The plugin uses SimplePie for the feed operations. The actual feeds are not stored in your databases but only cached for faster response times.
You call the function by using a shortcode.

= Demo =
The plugin can be seen in use on a students site for the Marshall School of Business where the RSS feeds come from campusgroups.com.
http://students.marshall.usc.edu/undergrad/student-organizations/calendar-of-events/
= Credit = 
Allen Weiss, extending the plugin (WP RSS Aggregator) developed by Jean Galea.  http://www.allenweiss.com/wp_plugin

== Installation ==

1. Upload the `wp-rss-multi-importer` folder to the `/wp-content/plugins/` directory
2. Activate the WP RSS Multi Importer plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the `RSS Multi Importer` submenu that appears in your `Settings` admin menu.
3. Use the shortcode in your posts or pages: `[wp_rss_multi_importer]`

The parameters are all set in the setting tab and are:

* number of posts per feed 
* sort by date (ascending or descending) 
* output feed name as (Source, Sponsor, or Club)


== Frequently Asked Questions ==
= How can I output the feeds in my theme? =

Use the shortcode in your posts and pages:
[wp_rss_multi_importer]

== Screenshots ==

1. The output of this plugin on the frontend.

2. Admin administration panel.

== Changelog ==

= Version 0.5 =
* Added ability to include excerpts from news feeds or other feeds that have a description tag..

