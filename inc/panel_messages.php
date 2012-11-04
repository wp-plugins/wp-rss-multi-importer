<?php



function wp_section_text() {
?>

<div class="postbox">
	<h3><label for="title"><?php _e("Usage Details", 'wp-rss-multi-importer')?></label></h3>
	<div class="inside"><H4><?php _e("Step 1:", 'wp-rss-multi-importer')?></H4>
		<p><?php _e("Enter a name and the full URL (with http://) for each of your feeds. The name will be used to identify which feed produced the link (see the Attribution Label option below). Click Save Settings.", 'wp-rss-multi-importer')?></p>
		
		<H4><?php _e("Step 2 (how to present the feeds):", 'wp-rss-multi-importer')?></H4><p><?php _e("First, decide whether you want to have the feeds go into blog posts, so people can comment on them.  If so, you can go directly to the Feed to Post Options, set them and you are done.", 'wp-rss-multi-importer')?></p>
		
		<p><?php _e("If you'd rather have the feeds show up in a more typical way (and use templates to customize how they look), go to the tab called", 'wp-rss-multi-importer')?> <a href="/wp-admin/options-general.php?page=wp_rss_multi_importer_admin&tab=setting_options"><?php _e("Setting Options", 'wp-rss-multi-importer')?></a>, <?php _e("choose options and click Save Settings.", 'wp-rss-multi-importer')?></p>
		
		<H4><?php _e("Step 3 (only relevant if you're not using the Feed to Post feature):", 'wp-rss-multi-importer')?></H4><p><?php _e("Put this shortcode, [wp_rss_multi_importer], on the page you wish to have the feed.", 'wp-rss-multi-importer')?></p>
		

<p>You can also assign each feed to a category. Go to the Category Options tab, enter as many categories as you like.</p><p>Then you can restrict what shows up on a given page by using this shortcode, like [wp_rss_multi_importer category="2"] (or [wp_rss_multi_importer category="1,2"] to have two categories) on the page you wish to have only show feeds from those categories.</p>

</div></div>

<?php
}


function wp_rss_multi_importer_template_page(){
   ?>	
	   <div class="wrap">
	<div id="poststuff">
<div class="postbox"><h3><label for="title"><?php _e("How to Use Templates", 'wp-rss-multi-importer')?></label></h3>

<div class="inside"><p>Many people have asked about styling their own RSS feed layouts on their sites.  While I've tried to provide many ways to do this, the first way is for me to construct various templates..which I've done and are now available in the pull-down menu on the Options Settings panel.  If you don't want to mess with other templates, just use the default template (called DEFAULT).</p>
	
<p>Some kind users have shown me other nice layouts, and so I've included those in the templates folder and are available in the pull down menu.  You might give those a try.</p>

<p><a href="http://templates.allenweiss.com" target="_blank">Go here to see what various templates look like</a>.</p>

<p>First, if you know some CSS you can change the styles of all the templates..and then save the CSS for use when the next update of the plugin happens.</p>

<p>To change the CSS, you can either FTP to your server, or an easier way is to edit the plugin's files...<a href="http://www.youtube.com/watch?v=H5HXzIBPD80" target="_blank">watch this video to see how to do this</a></p>


<p>Even more, if you know a bit of PHP coding, you can go in and make your own template.  All the templates are in the folder called Templates. I've included a file (called example.txt) that shows the foundation php code you must use with all templates.  Look through the other templates and you'll see other options you can include.</p>

<p>Also, now if you are using a template that you've changed or customized, you can save it.  Just choose a name for the template below, like (My great template), and hit save.  Then, when the plugin gets it's next update (that overwrites all the files) you can come back to this page, hit Restore, and your template will be available for use again.</p>
	
	
<p>Thank you.<br>Allen</p>
	
	</div></div></div></div>
<h3>Save Your Template</h3>
<?php
$options = get_option( 'rss_import_options' ); 
$thistemplate=$options['template'];
save_template_function($thistemplate);

}



function wp_rss_multi_importer_style_tags(){
   ?>	
	   <div class="wrap">
	<div id="poststuff">


<div class="postbox"><h3><label for="title"><?php _e("Shortcode Parameters", 'wp-rss-multi-importer')?></label></h3><div class="inside"><h2><?php _e("Customize some of the ways the feeds are presented on your page by using shortcode parameters.  Here are some examples:", 'wp-rss-multi-importer')?></h2>


<table class="widefat">
<tr><th><?php _e("FEATURE CHANGE", 'wp-rss-multi-importer')?></th><th><?php _e("PARAMETER", 'wp-rss-multi-importer')?></th><th><?php _e("DEFAULT", 'wp-rss-multi-importer')?></th><th><?php _e("EXAMPLE", 'wp-rss-multi-importer')?></th></tr>
<tr class="alternate"><td ><?php _e("Headline font size", 'wp-rss-multi-importer')?></td><td>hdsize</td><td>16px</td><td>[wp_rss_multi_importer hdsize="18px"]</td></tr>	
<tr><td ><?php _e("Headline bold weight", 'wp-rss-multi-importer')?></td><td>hdweight</td><td>400</td><td>[wp_rss_multi_importer hdweight="500"]</td></tr>		
<tr class="alternate"><td ><?php _e("Style of the Today and Earlier tags", 'wp-rss-multi-importer')?></td><td>testyle</td><td>color: #000000; font-weight: bold;margin: 0 0 0.8125em;</td><td>[wp_rss_multi_importer testyle="color:#cccccc"]</td></tr>	
<tr ><td><?php _e("If using excerpt, symbol or word you want to indicate More..", 'wp-rss-multi-importer')?></td><td>morestyle</td><td>[...]</td><td>[wp_rss_multi_importer morestyle="more >>"]</td></tr>	
<tr class="alternate"><td ><?php _e("Change the width of the maximum image size", 'wp-rss-multi-importer')?></td><td>maximgwidth</td><td>150</td><td>[wp_rss_multi_importer maximgwidth="160"]</td></tr>	
<tr ><td ><?php _e("Change the style of the date", 'wp-rss-multi-importer')?></td><td>datestyle</td><td>font-style:italic;</td><td>[wp_rss_multi_importer datestyle="font-style:bold;"]</td></tr>	
<tr class="alternate"><td ><?php _e("Change how images float on a page", 'wp-rss-multi-importer')?></td><td>floattype</td><td>set by default to whatever is set in the admin options</td><td>[wp_rss_multi_importer floattype="right"]</td></tr>	
<tr ><td ><?php _e("Change whether the date shows or not", 'wp-rss-multi-importer')?></td><td>showdate</td><td>set to 0 to suppress the date</td><td>[wp_rss_multi_importer showdate="0"]</td></tr>	
<tr class="alternate"><td ><?php _e("Change whether the attribution shows or not (e.g., news source)", 'wp-rss-multi-importer')?></td><td>showgroup</td><td>set to 0 to suppress the source affiliation</td><td>[wp_rss_multi_importer showgroup="0"]</td></tr>	
<tr class="alternate"><td ><?php _e("Specify the cache time (to override global setting)", 'wp-rss-multi-importer')?></td><td>cachetime</td><td>set in settings option</td><td>[wp_rss_multi_importer cachetime="20"]</td></tr>

<tr ><td ><?php _e("Specific the number of posts per feed instead of using the general number in the settings panel<", 'wp-rss-multi-importer')?>/td><td>thisfeed</td><td>set to a number, as in thisfeed="4"</td><td>[wp_rss_multi_importer thisfeed="5"]</td></tr>	
<tr ><td ><?php _e("Change the float of the elements in the feed", 'wp-rss-multi-importer')?></td><td>floattype</td><td>by default, no float set</td><td>[wp_rss_multi_importer floattype="left"]</td></tr>	
<tr ><td ><?php _e("Use this if bringing in a Pinterest feed..to display correctly", 'wp-rss-multi-importer')?></td><td>pinterest</td><td>pinterest=0</td><td>[wp_rss_multi_importer pinterest="1"]</td></tr>

	
</table>

<p><?php _e("You can use combinations of parameters, too.  So, if you'd like to change the headline font size to 18px and make it a heavier bold and change the more in the excerpt to >>, just do this:   [wp_rss_multi_importer hdsize=\"18px\" hdweight=\"500\" morestyle=\">>\"] ", 'wp-rss-multi-importer')?></p>
<p><?php _e("If setting the style of the Today and Earlier tags, you need to enter the entire inline css - so be careful.", 'wp-rss-multi-importer')?></p>
</div></div></div></div>
	
<?php
}




function wp_rss_multi_importer_more_page(){
   ?>	
	   <div class="wrap">
	<div id="poststuff">

		<div class="postbox">
			<h3><label for="title"><?php _e("How Does This Work?", 'wp-rss-multi-importer')?></label></h3>
			<div class="inside">
<p><?php _e("This plugin does several different things.  Mainly, it brings in RSS feeds and aggregates and sorts them and puts them on your web site.  You can assign categories to the feeds.", 'wp-rss-multi-importer')?></p>
<p>I<?php _e("You can put them on your web site in 3 different ways. First, you can present them using shortcode using one of 8 different templates (see the Settings Option for this).  Second, the plugin can take the feeds and put them directly into your blog posts, so people can comment on them (see the Feed to Post Options for this).  Third, it provides you with a feed to give your readers (see the Export Feed Options for this).", 'wp-rss-multi-importer')?></p>
<p><?php _e("Finally, you don't have to choose one way or another to present the feeds.  You can do all 3 at the same time.", 'wp-rss-multi-importer')?></p>


</div></div>
<div class="postbox">
	<h3><label for="title"><?php _e("Help Us Help You", 'wp-rss-multi-importer')?></label></h3>
	<div class="inside">
		
		<p>In an attempt to increase the functionality of this plugin, let me know if you have any feature requests by <a href="http://www.allenweiss.com/wp_plugin" target="_blank">going here.</a> where you can also get support.</p>
	
<p>If you'd like to support the development and maintenance of this plugin, you can do so by <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=M6GC7V8BARAJL" target="_blank">donating here.</a></p>

<p>If you find this plugin helpful, let others know by <a href="http://wordpress.org/extend/plugins/wp-rss-multi-importer/" target="_blank">rating it here</a>.  That way, it will help others determine whether or not they should try out the plugin.  Thank you.<br>Allen</p>

</div></div>

</div></div>	
<?php

}