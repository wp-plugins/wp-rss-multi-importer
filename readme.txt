=== WP RSS Multi Importer ===
Contributors: allenweiss
Tags: rss, feeds, aggregation, aggregator, import
Requires at least: 2.9
Tested up to: 3.4.1
Stable tag: 1.1
Imports and merges multiple RSS Feeds using SimplePie. Options including sorting feeds by date, limit feeds, include feed name and excerpts.

== Description ==

WP RSS Multi Importer helps you create a feed reader on your Wordpress site.  The plugin extends WP RSS Aggregator, and allows more flexibility by adding and deleting specific feeds, sorting by date, limiting posts per feed.  This works well for news items as well as events (which are sorted differently than news items). You can add any number of feeds through an administration panel, the plugin will then pull all the feeds from these sites, merge them and sort them by date.  You can easily delete a specific feed and add excerpts from news feeds.

The output will be organized like this:

* Title
* Excerpt (if selected)
* Date
* Source (if selected)

and sorted by date ascending or descending.  The Source attribution can be changed to Club, Sponsor or no attribution (e.g., Source: LA Times, or Club: Consulting Club).

The plugin uses SimplePie for the feed operations. The actual feeds are not stored in your databases but only cached for faster response times.
You call the function by using a shortcode.

= Credit = 
Allen Weiss  http://www.allenweiss.com/wp_plugin

== Installation ==

1. Upload the `wp-rss-multi-importer` folder to the `/wp-content/plugins/` directory
2. Activate the WP RSS Multi Importer plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the `RSS Multi Importer` submenu that appears in your `Settings` admin menu.
3. Use the shortcode in your posts or pages: `[wp_rss_multi_importer]`

The parameters are all set in the setting tab and are:

* number of posts per feed 
* sort by date (ascending or descending) 
* output feed name as (Source, Sponsor, or Club)
* excerpt (if yes, number of characters to show - 50, 100, 200, 300)
* where the links should open (in a Lightbox, a new window, the current window)


== Frequently Asked Questions ==
= How can I output the feeds in my theme? =

Use the shortcode in your posts and pages:
[wp_rss_multi_importer]

== Screenshots ==

1. The output of this plugin on the frontend.

2. Admin administration panel.

== Change Log ==

= Version 1.1 =
* Added ability determine where the links should open (Lightbox, new window, current window)
= Version 1.0 =
* Fixed problem where showing text before the shortcode rendered after the shortcode
= Version 0.7 =
* Fixed problem with showing excerpts withe foreign characters
= Version 0.6 =
* Fixed bugs in Lightbox and eliminated error message
= Version 0.5 =
* Added ability to include short descriptions - excerpts (if they exist in the RSS feed)
