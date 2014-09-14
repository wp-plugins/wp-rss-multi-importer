<?php
/*  Plugin Name: RSS Multi Importer
  Plugin URI: http://www.wprssimporter.com/
  Description: All-in-one solution for importing & merging multiple feeds. Make blog posts or display on a page, excerpts w/ images, 13 templates, categorize and more. 
  Version: 3.13
  Author: Allen Weiss
  Author URI: http://www.wprssimporter.com/
  License: GPL2  - most WordPress plugins are released under GPL2 license terms
*/


/* Set the version number of the plugin. */
define( 'WP_RSS_MULTI_VERSION', 3.13 );

/* Set constant path to the plugin directory. */
define( 'WP_RSS_MULTI_PATH', plugin_dir_path( __FILE__ ) );

/* Set constant url to the plugin directory. */
define( 'WP_RSS_MULTI_URL', plugin_dir_url( __FILE__ ) );

/* Set the constant path to the plugin's includes directory. */
define( 'WP_RSS_MULTI_INC', WP_RSS_MULTI_PATH . trailingslashit( 'inc' ), true );

/* Set the constant path to the plugin's utils directory. */
define( 'WP_RSS_MULTI_UTILS', WP_RSS_MULTI_PATH . trailingslashit( 'utils' ), true );

/* Set the constant path to the plugin's template directory. */
define( 'WP_RSS_MULTI_TEMPLATES', WP_RSS_MULTI_PATH . trailingslashit( 'templates' ), true );

/* Set the constant path to the plugin's scripts directory. */
define( 'WP_RSS_MULTI_SCRIPTS', WP_RSS_MULTI_URL . trailingslashit( 'scripts' ), true );

/* Set the constant path to the plugin's css directory. */
define( 'WP_RSS_MULTI_CSS', WP_RSS_MULTI_URL . trailingslashit( 'css' ), true );

/* Set the constant path to the plugin's image directory. */
define( 'WP_RSS_MULTI_IMAGES', WP_RSS_MULTI_URL . trailingslashit( 'images' ), true );

/* Load the template functions file. */
require_once( WP_RSS_MULTI_INC . 'template_functions.php' );

/* Load the messages file. */
require_once( WP_RSS_MULTI_INC . 'panel_messages.php' );

/* Load the database functions file. */
require_once( WP_RSS_MULTI_INC . 'db_functions.php' );

/* Load the excerpt functions file. */
require_once( WP_RSS_MULTI_INC . 'excerpt_functions.php' );

/* Load the cron file. */
require_once( WP_RSS_MULTI_INC . 'cron.php' );

/* Load the options file. */
require_once( WP_RSS_MULTI_INC . 'options.php' );

/* Load the widget functions file. */
require_once( WP_RSS_MULTI_INC . 'rss_multi_importer_widget.php' );

/* Load the upgrade file. */
require_once( WP_RSS_MULTI_INC . 'upgrade.php' );

/* Load the admin functions file. */
require_once( WP_RSS_MULTI_INC . 'admin_functions.php' );

require_once( WP_RSS_MULTI_INC . 'import_feeds.php' );

/* Load the scripts files. */
require_once( WP_RSS_MULTI_INC . 'scripts.php' );

/* Load the feed files. */
require_once( WP_RSS_MULTI_INC . 'rss_feed.php' );

/* Load the import posts files. */
require_once( WP_RSS_MULTI_INC . 'import_posts.php' );

/* Load the custom posts files. */
require_once( WP_RSS_MULTI_INC . 'custom_posts.php' );

/* Load the feed to post list files. */
require_once( WP_RSS_MULTI_INC . 'ftp_list_table.php' );

/* Load the admin_init files. */
require_once( WP_RSS_MULTI_INC . 'admin_init.php' );

/* Load the upload feed files. */
require_once( WP_RSS_MULTI_INC . 'textbox-to-db.php' );

/* Load the diagnostics  files. */
require_once( WP_RSS_MULTI_INC . 'diagnostics.php' );

/* Load the export  files. */
require_once( WP_RSS_MULTI_INC . 'export.php' );

/* Load the global settings files. */
require_once( WP_RSS_MULTI_INC . 'global_settings.php' );


register_activation_hook( __FILE__, 'wp_rss_multi_importer_activate' );

register_deactivation_hook(__FILE__, 'wp_rss_multi_deactivation_event');

function wp_rss_multi_deactivation_event(){
	wp_clear_scheduled_hook('wp_rss_multi_event_importfeeds');
	wp_clear_scheduled_hook('wp_rss_multi_event_feedtopost');
	wp_clear_scheduled_hook('wp_rss_multi_event_delete_custom_posts');
	wp_clear_scheduled_hook('wp_rss_multi_event');
}


function rssmi_plugin_update_info() {
	if ( rssmi_remoteFileExists( "http://www.wprssimporter.com/a/plugin-updates.txt" ) === True ) {
		$info = wp_remote_fopen( "http://www.wprssimporter.com/a/plugin-updates.txt" );
		echo '<br />' . strip_tags( $info, "<br><a><b><i><span>" );
	}
}

add_action( 'in_plugin_update_message-' . plugin_basename( __FILE__ ), 'rssmi_plugin_update_info' );


/**
 *  Shortcode setup and call (shortcode is [wp_rss_multi_importer]) with options
 */

add_shortcode( 'wp_rss_multi_importer', 'wp_rss_multi_importer_shortcode' );


function wp_rss_mi_lang_init() {
	load_plugin_textdomain( 'wp-rss-multi-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' ); // load the language files
}

add_action( 'plugins_loaded', 'wp_rss_mi_lang_init' );


function wp_rss_fetchFeed( $url, $timeout = 10, $forceFeed = false, $showVideo = 0 ) {

	$feed = new SimplePie_RSSMI();
	$feed->set_feed_url( $url );
	$feed->force_feed( $forceFeed );
	$feed->set_autodiscovery_level( SIMPLEPIE_LOCATOR_ALL );
	if ( $showVideo == 1 ) {
		$strip_htmltags = $feed->strip_htmltags;
		array_splice( $strip_htmltags, array_search( 'iframe', $strip_htmltags ), 1 );
		$feed->strip_htmltags( $strip_htmltags );
	}
	$feed->enable_cache( false );
	$feed->set_timeout( $timeout );
	$feed->init();
	$feed->handle_content_type();

	return $feed;
}


//  MAIN SHORTCODE OUTPUT FUNCTION


function wp_rss_multi_importer_shortcode( $atts = array() ) {


	add_action( 'wp_footer', 'rssmi_footer_scripts' );

	if ( ! function_exists( "wprssmi_hourly_feed" ) ) {
		function wprssmi_hourly_feed() {
			return 0;
		}
	}
	add_filter( 'wp_feed_cache_transient_lifetime', 'wprssmi_hourly_feed' );


	$siteurl         = get_site_url();
	$cat_options_url = $siteurl . '/wp-admin/options-general.php?page=wp_rss_multi_importer_admin&tab=category_options/';
	$images_url      = $siteurl . '/wp-content/plugins/' . basename( dirname( __FILE__ ) ) . '/images';

	global $fopenIsSet;
	$fopenIsSet = ini_get( 'allow_url_fopen' );

	$parms = shortcode_atts( array( //Get shortcode parameters
		'category'      => 0,
		'hdsize'        => '16px',
		'hdweight'      => 400,
		'anchorcolor'   => '',
		'testyle'       => 'color: #000000; font-weight: bold;margin: 0 0 0.8125em;',
		'maximgwidth'   => 150,
		'datestyle'     => 'font-style:italic;',
		'floattype'     => '',
		'showdate'      => 1,
		'showgroup'     => 1,
		'thisfeed'      => '',
		'timer'         => 0,
		'dumpthis'      => 0,
		'cachetime'     => NULL,
		'pinterest'     => 0,
		'maxperpage'    => 0,
		'excerptlength' => NULL,
		'noimage'       => 0,
		'sortorder'     => NULL,
		'defaultimage'  => NULL,
		'nofollow'      => NULL,
		'showdesc'      => NULL,
		'mytemplate'    => '',
		'showmore'      => NULL,
		'sourcename'    => '',
		'authorprep'    => 'by',
		'windowstyle'   => NULL,
		'morestyle'     => '[...]'
	), $atts );

	$showThisDesc = $parms['showdesc'];
	$defaultImage = $parms['defaultimage'];
	$sortOrder    = $parms['sortorder'];
	$authorPrep   = $parms['authorprep'];
	$anchorcolor  = $parms['anchorcolor'];
	$datestyle    = $parms['datestyle'];
	$hdsize       = $parms['hdsize'];
	$thisCat      = $parms['category'];
	$parmfloat    = $parms['floattype'];
	$catArray     = explode( ",", $thisCat );
	$showdate     = $parms['showdate'];
	$showgroup    = $parms['showgroup'];
	$pshowmore    = $parms['showmore'];
	$hdweight     = $parms['hdweight'];
	$testyle      = $parms['testyle'];
	global $morestyle;
	$morestyle = $parms['morestyle'];
	global $maximgwidth;
	$maximgwidth            = $parms['maximgwidth'];
	$thisfeed               = $parms['thisfeed']; // max posts per feed
	$timerstop              = $parms['timer'];
	$dumpthis               = $parms['dumpthis']; //diagnostic parameter
	$cachename              = 'wprssmi_' . $thisCat;
	$cachetime              = $parms['cachetime'];
	$pnofollow              = $parms['nofollow'];
	$pinterest              = $parms['pinterest'];
	$parmmaxperpage         = $parms['maxperpage'];
	$noimage                = $parms['noimage'];
	$mytemplate             = $parms['mytemplate'];
	$windowstyle            = $parms['windowstyle'];
	$excerptlength          = $parms['excerptlength'];
	$parsourcename          = $parms['sourcename'];
	$readable               = '';
	$options                = get_option( 'rss_import_options', 'option not found' );
	$option_items           = get_option( 'rss_import_items', 'option not found' );
	$option_category_images = get_option( 'rss_import_categories_images', 'option not found' );
	$rssmi_global_options   = get_option( 'rssmi_global_options' );
	$suppress_warnings      = ( isset( $rssmi_global_options['suppress_warnings'] ) ? $rssmi_global_options['suppress_warnings'] : 0 );

	if ( $option_items == false ) return _e( "You need to set up the WP RSS Multi Importer Plugin before any results will show here.  Just go into the <a href='/wp-admin/options-general.php?page=wp_rss_multi_importer_admin'>settings panel</a> and put in some RSS feeds", 'wp-rss-multi-importer' );


	if ( ! empty( $option_items ) ) {

//GET PARAMETERS  
		global $RSSdefaultImage;
		$RSSdefaultImage = $options['RSSdefaultImage']; // 0- process normally, 1=use default for category, 2=replace when no image available
		$size            = count( $option_items );
		$sortDir         = $options['sortbydate']; // 1 is ascending
		$stripAll        = ( isset( $options['stripAll'] ) ? $options['stripAll'] : null );
		$stripSome       = ( isset( $options['stripSome'] ) ? $options['stripSome'] : null );
		$todaybefore     = $options['todaybefore'];
		$adjustImageSize = ( isset( $options['adjustImageSize'] ) ? $options['adjustImageSize'] : null );
		$showDesc        = $options['showdesc']; // 1 is show
		$descNum         = $options['descnum'];
		$maxperPage      = $options['maxperPage'];
		$showcategory    = ( isset( $options['showcategory'] ) ? $options['showcategory'] : null );
		$maxposts        = $options['maxfeed'];
		$showsocial      = $options['showsocial'];
		$targetWindow    = $options['targetWindow']; // 0=LB, 1=same, 2=new
		$floatType       = $options['floatType'];
		$noFollow        = $options['noFollow'];
		$showmore        = ( isset( $options['showmore'] ) ? $options['showmore'] : null );
		$cb              = ( isset( $options['cb'] ) ? $options['cb'] : null ); // 1 if colorbox should not be loaded
		$pag             = $options['pag']; // 1 if pagination 2 or 3 if load more
		$perPage         = $options['perPage'];
		$addAuthor       = ( isset( $options['addAuthor'] ) ? $options['addAuthor'] : null );
		$warnmsg         = ( isset( $options['warnmsg'] ) ? $options['warnmsg'] : null );
		$directFetch     = ( isset( $options['directFetch'] ) ? $options['directFetch'] : 0 );
		$forceFeed       = ( isset( $options['forceFeed'] ) ? $options['forceFeed'] : false );
		$forceFeed       = ( $forceFeed == 1 ? True : False );
		$timeout         = ( isset( $options['timeout'] ) ? $options['timeout'] : 10 );
		if ( ! isset( $timeout ) ) {
			$timeout = 10;
		}
		if ( ! isset( $directFetch ) ) {
			$directFetch = 0;
		}
		if ( ! is_null( $defaultImage ) ) {
			$RSSdefaultImage = $defaultImage;
		}
		if ( ! is_null( $windowstyle ) ) {
			$targetWindow = $windowstyle;
		}
		if ( ! is_null( $showThisDesc ) ) {
			$showDesc = $showThisDesc;
		}
		if ( ! is_null( $sortOrder ) ) {
			$sortDir = $sortOrder;
		}
		if ( ! is_null( $pshowmore ) ) {
			$showmore = $pshowmore;
		}
		if ( ! is_null( $excerptlength ) ) {
			$descNum = $excerptlength;
		}
		if ( ! is_null( $pnofollow ) ) {
			$noFollow = $pnofollow;
		}
		if ( ! isset( $maxposts ) ) {
			$maxposts = 1;
		}
		if ( empty( $options['sourcename'] ) ) {
			$attribution = '';
		}
		else {
			$attribution = $options['sourcename'] . ' ';
		}
		if ( $parsourcename != '' ) $attribution = $parsourcename;
		if ( $floatType == '1' ) {
			$float = "left";
		}
		else {
			$float = "none";
		}


		if ( $parmfloat != '' ) $float = $parmfloat;
		if ( $parmmaxperpage != 0 ) $maxperPage = $parmmaxperpage;
		if ( $noimage == 1 ) $stripAll = 1;
		if ( $thisfeed != '' ) $maxposts = $thisfeed;

		if ( $pinterest == 1 ) {
			$divfloat = "left";
		}
		else {
			$divfloat = '';
		}


		if ( is_null( $cb ) && $targetWindow == 0 ) {
			add_action( 'wp_footer', 'colorbox_scripts' ); // load colorbox only if not indicated as conflict
		}

		$template = $options['template'];
		if ( $mytemplate != '' ) $template = $mytemplate;


//	END PARAMETERS


//  GET ALL THE FEEDS	

		global $wpdb;
		$myarray = array();

		/*
		if ($thisCat==0){
			$feedQuery="SELECT * FROM wp_posts inner join wp_postmeta ON wp_posts.id=wp_postmeta.post_id where `post_type`='rssmi_feed' AND `post_status`='publish' AND `meta_key`='rssmi_url'";
		}else{
			$feedQuery="SELECT * FROM wp_posts inner join wp_postmeta ON wp_posts.id=wp_postmeta.post_id where `post_type`='rssmi_feed' AND `post_status`='publish' AND `meta_key`='rssmi_cat' AND `meta_value` in ($thisCat) ";
			}
		*/

		if ( $thisCat == 0 ) {
			$feedQuery = "SELECT * FROM $wpdb->posts as a inner join $wpdb->postmeta as b ON a.id=b.post_id where post_type='rssmi_feed' AND post_status='publish' AND meta_key='rssmi_url'";
		}
		else {
			$feedQuery = "SELECT * FROM $wpdb->posts as a inner join $wpdb->postmeta as b ON a.id=b.post_id where post_type='rssmi_feed' AND post_status='publish' AND meta_key='rssmi_cat' AND meta_value in ($thisCat) ";
		}


//$feedQuery="SELECT ID FROM $wpdb->posts WHERE  post_type ='rssmi_feed' AND post_status='publish'";

		$feed_array = $wpdb->get_results( $feedQuery );

		if ( $suppress_warnings == 0 && empty( $feed_array ) ) {

			return _e( "There is a problem - it appears you are using categories and no feeds have been put into those categories.", 'wp-rss-multi-importer' );

			return;
		}


//  ****  TAKE EACH FEED AND GET THE ITEMS FROM THAT FEED
		foreach ( $feed_array as $feed ) {

			$feedlimit      = 0;
			$rssmi_cat      = get_post_meta( $feed->ID, 'rssmi_cat', true );
			$rssmi_source   = get_the_title( $feed->ID );
			$catSourceArray = array(
				"myGroup" => $rssmi_source,
				"mycatid" => $rssmi_cat
			);


//  *** SORT THESE DATE AND THEN ADD TO THE FINAL ARRAY

//$rssmi_sql = "SELECT a.post_id,b.meta_key,b.meta_value FROM $wpdb->postmeta as a inner join $wpdb->postmeta as b on a.post_id=b.post_id WHERE a.meta_value =$feed->ID and b.meta_key='rssmi_item_date' order by b.meta_value desc"; 

			$rssmi_sql = "SELECT a.post_id,b.meta_key,b.meta_value FROM $wpdb->postmeta as a inner join $wpdb->postmeta as b on a.post_id=b.post_id WHERE a.meta_value =$feed->ID and b.meta_key='rssmi_item_date' order by b.meta_value ";

			if ( $sortDir == 0 ) {
				$rssmi_sql .= "desc";
			}
			elseif ( $sortDir == 1 ) {
				$rssmi_sql .= "asc";
			}


			$desc_array = $wpdb->get_results( $rssmi_sql );


			foreach ( $desc_array as $arrayItem ) {
				$feedlimit = $feedlimit + 1;
				if ( $feedlimit > $maxposts ) continue;
				$post_ID   = $arrayItem->post_id;
				$desc      = get_post_meta( $post_ID, 'rssmi_item_description', true );
				$arrayItem = array_merge( (array) $desc[0], $catSourceArray ); //  add the source and category ID
				if ( include_post( $rssmi_cat, $arrayItem['mydesc'], $arrayItem['mytitle'] ) == 0 ) {
					continue;
				} // FILTER
				array_push( $myarray, $arrayItem ); //combine into final array

			}

		}


//  CHECK $myarray BEFORE DOING ANYTHING ELSE //

		if ( $dumpthis == 1 ) {
			echo "<br><strong>Array</strong><br>";
			var_dump( $myarray );
			return;
		}
		if ( ! isset( $myarray ) || empty( $myarray ) ) {
			if ( $suppress_warnings == 0 && current_user_can( 'edit_post' ) ) {

				return _e( "There is a problem with the feeds you entered. Go to our <a href='http://www.wprssimporter.com/faqs/im-told-the-feed-isnt-valid-or-working/'>support page</a> to see how to solve this.", 'wp-rss-multi-importer' );
			}
			return;
		}

		global $isMobileDevice;
		if ( isset( $isMobileDevice ) && $isMobileDevice == 1 ) { //open mobile device windows in new tab
			$targetWindow = 2;

		}


//$myarrary sorted by mystrdate

		foreach ( $myarray as $key => $row ) {
			$dates[$key] = $row["mystrdate"];
		}


//SORT, DEPENDING ON SETTINGS

		if ( $sortDir == 1 ) {
			array_multisort( $dates, SORT_ASC, $myarray );
		}
		elseif ( $sortDir == 0 ) {
			array_multisort( $dates, SORT_DESC, $myarray );
		}


//echo $targetWindow;

// HOW THE LINK OPENS

		if ( $targetWindow == 0 ) {
			$openWindow = 'class="colorbox"';
		}
		elseif ( $targetWindow == 1 ) {
			$openWindow = 'target="_self"';
		}
		else {
			$openWindow = 'target="_blank"';
		}

		$total      = - 1;
		$todayStamp = 0;
		$idnum      = rand( 1, 500 );

//for pagination
		$currentPage = ( isset( $_REQUEST['pg'] ) ? trim( $_REQUEST['pg'] ) : 0 );
		$currentURL  = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
		$currentURL  = str_replace( '&pg=' . $currentPage, '', $currentURL );
		$currentURL  = str_replace( '?pg=' . $currentPage, '', $currentURL );

		if ( strpos( $currentURL, '?' ) == 0 ) {
			$currentURL = $currentURL . '?';
		}
		else {
			$currentURL = $currentURL . '&';
		}


//pagination controls and parameters


		if ( ! isset( $perPage ) ) {
			$perPage = 5;
		}

		$numPages = ceil( count( $myarray ) / $perPage );
		if ( ! $currentPage || $currentPage > $numPages )
			$currentPage = 0;
		$start = $currentPage * $perPage;
		$end   = ( $currentPage * $perPage ) + $perPage;


		if ( $pag == 1 || $pag == 2 || $pag == 3 ) { //set up pagination array and put into myarray
			foreach ( $myarray AS $key => $val ) {
				if ( $key >= $start && $key < $end )
					$pagedData[] = $myarray[$key];
			}

			$myarray = $pagedData;
		}
		//end set up pagination array and put into myarray


//  templates checked and added here

		if ( ! isset( $template ) || $template == '' ) {
			return _e( "One more step...go into the <a href='/wp-admin/options-general.php?page=wp_rss_multi_importer_admin&tab=setting_options'>Settings Panel and choose a Template.</a>", 'wp-rss-multi-importer' );
		}


		require( WP_RSS_MULTI_TEMPLATES . $template );


	}

	if ( $pag == 2 || $pag == 3 ) {

		add_action( 'rssmi_load_more_data', 'rssmi_pbd_alp_init', 10, 5 );
		if ( strpos( $currentURL, 'http' ) == 0 ) {
			$nextPost = 'http://' . $currentURL . 'pg=' . ( $currentPage + 1 );
		}
		else {
			$nextPost = $currentURL . 'pg=' . ( $currentPage + 1 );
		}
		do_action( 'rssmi_load_more_data', $numPages, $currentPage, $nextPost, WP_RSS_MULTI_IMAGES, $pag );
	}


	//pagination controls at bottom

	if ( ( $pag == 1 || $pag == 2 || $pag == 3 ) && $numPages > 1 ) {

		$readable .= '<div class="rssmi_pagination"><ul>';

		for ( $q = 0; $q < $numPages; $q ++ ) {
			//	if($currentPage>0 && $q==0){$readable .='<li class="prev"><a href="http://'.$currentURL.'pg=' . ($currentPage-1) . '">Prev</a></li>';}
			if ( $currentPage > 0 && $q == 0 ) {
				$readable .= '<li class="prev"><a href="http://' . $currentURL . 'pg=' . ( $currentPage - 1 ) . '">' . __( "Prev", 'wp-rss-multi-importer' ) . '</a></li>';
			}

			if ( $currentPage <> $q ) {
				$readable .= '<li><a href="http://' . $currentURL . 'pg=' . ( $q ) . '"> ' . __( $q + 1, 'wp-rss-multi-importer' ) . '</a></li>';
			}
			else {
				$readable .= '<li class="active"><a href="#">' . ( $q + 1 ) . '</a></li>';
			}
			if ( $q == $numPages - 1 && $currentPage <> $numPages - 1 ) {
				$readable .= '<li class="next"><a href="http://' . $currentURL . 'pg=' . ( $currentPage + 1 ) . '">' . __( "Next", 'wp-rss-multi-importer' ) . '</a></li>';
			}
		}
		$readable .= '</ul></div>';

	}
	//end pagination controls at bottom


	return $readable;


}


?>