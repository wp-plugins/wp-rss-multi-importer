=== WP RSS Multi Importer ===
Contributors: allenweiss
Tags: rss, feeds, aggregation, aggregator, import
Requires at least: 2.9
Tested up to: 3.4.1
Stable tag: 2.1
Imports and merges multiple RSS Feeds. Options including sorting feeds by date, limit feeds/page and by category, and include excerpts with images. 

== Description ==

WP RSS Multi Importer helps you create a feed reader on your Wordpress site.  The plugin gives you a lot of flexibility by adding and deleting specific feeds, sorting by date, categorizing your feeds, limiting posts per feed and more.  This works well for news items as well as events (which are sorted differently than news items).

You can add any number of feeds through an administration panel, the plugin will then pull all the feeds from these sites, merge them and sort them by date.  You can easily delete a specific feed and add excerpts from news feeds.  Also, you can open up the links in the feed into either a Lightbox, a  new window, or in the home window.  Finally, you can add a bunch of feeds, assign them to categories and then output only feeds you want based on the category.  This allows you to have one input of RSS feeds, and yet put them on different pages of your web site.

The output will be organized like this:

* Title
* Excerpt (if selected)
* Date
* Source (if selected)

and sorted by date ascending or descending.  The Source attribution can be changed to Club, Sponsor or no attribution (e.g., Source: LA Times, or Club: Consulting Club).

The plugin uses SimplePie for the feed operations. The actual feeds are not stored in your databases but only cached for faster response times.
You call the function by using a shortcode or output the results using a widget.

= Credit = 
Allen Weiss  http://www.allenweiss.com/wp_plugin

== Installation ==

1. Upload the `wp-rss-multi-importer` folder to the `/wp-content/plugins/` directory
2. Activate the WP RSS Multi Importer plugin through the 'Plugins' menu in WordPress
3. Configure the plugin by going to the `RSS Multi Importer` submenu that appears in your `Settings` admin menu.
3. Use the shortcode in your posts or pages: `[wp_rss_multi_importer]` or use the widget.
4. Limit which feeds get shown on a page by using a parameter in the shortcode, like: [wp_rss_multi_importer category="#"] or choose the category in the widget.

The parameters are all set in the setting tab and are:

* number of posts per feed 
* number of posts on a page
* separate out Today from Earlier posts
* sort by date (ascending or descending) 
* output feed name as (Source, Sponsor, or Club)
* excerpt (if yes, number of words to show - 50, 100, 200, 300)
* where the links should open (in a Lightbox, a new window, or the current window)

Other parameters can be customized in the shortcode..right now this includes:

* Headline font size
* Headline bold weight
* Style of the Today and Earlier tags
* If using excerpt, symbol or word you want to indicate More..


== Frequently Asked Questions ==
= How can I output the feeds in my theme? =

Use the shortcode in your posts and pages:
[wp_rss_multi_importer]
Make sure the shortcode is entered when the input is set to HTML (versus Visual)

If you want to limit the feeds to those in a given category, make sure to first
assign the feed to a category, then use this shortcode on your page or post:
[wp_rss_multi_importer category="#"]

Use the widget.  If your theme allows for widgets, you'll find the RSS Multi Importer Widget there.
Configure your feeds in the administration panel, then choose the category, number of posts, sorting method
in the widget admin.

== Screenshots ==

1. The output of this plugin on the frontend.

2. Admin administration panel.

== Change Log ==

= Version 2.1 =
* Added a widget option for displaying feeds, better image formatting, separate Today from Earlier posts.
= Version 2.01 =
* Fixed bug that caused some users to have problems when they haven't added any categories.
= Version 2.0 =
* Added ability to assign feeds to a category and output feeds from a given a given category.  Limit posts on a page.  Uninstall now works for multiuser sites.  Solved problem for some users where the LightBox option was conflicting with other plugins that also relied on Lightbox or Colorbox.
= Version 1.1 =
* Added ability to determine where the links should open (Lightbox, new window, current window)
= Version 1.0 =
* Fixed problem where showing text before the shortcode rendered after the shortcode
= Version 0.7 =
* Fixed problem with showing excerpts withe foreign characters
= Version 0.6 =
* Fixed bugs in Lightbox and eliminated error message
= Version 0.5 =
* Added ability to include short descriptions - excerpts (if they exist in the RSS feed)
