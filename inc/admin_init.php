<?php

/**
 * Admin init to add settings, styles, scripts
 */
function wp_rss_multi_importer_start() {

	register_setting( 'wp_rss_multi_importer_options', 'rss_import_items' );
	register_setting( 'wp_rss_multi_importer_categories', 'rss_import_categories' );
	register_setting( 'wp_rss_multi_importer_item_options', 'rss_import_options' );
	register_setting( 'wp_rss_multi_importer_template_item', 'rss_template_item' );
	register_setting( 'wp_rss_multi_importer_feed_options', 'rss_feed_options' );
	register_setting( 'wp_rss_multi_importer_post_options', 'rss_post_options' );
	register_setting( 'wp_rss_multi_importer_admin_options', 'rss_admin_options' );
	register_setting( 'wp_rss_multi_importer_categories_images', 'rss_import_categories_images' );
	register_setting( 'wp_rss_multi_importer_global_options', 'rssmi_global_options', 'rssmi_sanitize_global' );

	add_settings_section( 'wp_rss_multi_importer_main', '', 'wp_section_text', 'wprssimport' );

	wp_enqueue_style( 'farbtastic' );
	wp_enqueue_script( 'farbtastic' );

}

add_action( 'admin_init', 'wp_rss_multi_importer_start' );


/**
 * Init function for the front-end auto-created posts
 */
function wp_rss_multi_importer_post_to_feed() {

	$post_options = get_option( 'rss_post_options' );

	if ( empty( $post_options ) ) {
		return;
	}

	// Load colorbox scripts on the front end if the option is set
	if (
		empty( $post_options['targetWindow'] ) &&
		( isset( $post_options['active'] ) && $post_options['active'] == 1 )
	) {
		add_action( 'wp_footer', 'colorbox_scripts' );
	}

	// Add noindex meta tags if the option is set
	if ( isset( $post_options['noindex'] ) && $post_options['noindex'] == 1 ) {
		add_action( 'wp_head', 'rssmi_noindex_function' );
	}

	// Add canonical link if the option is set
	if ( isset( $post_options['addcanonical'] ) && $post_options['addcanonical'] == 1 ) {
		remove_action( 'wp_head', 'rel_canonical' );
		add_action( 'wp_head', 'rssmi_canonical_function' );
	}
}

add_action( 'init', 'wp_rss_multi_importer_post_to_feed' );


/**
 * Is the current user on mobile?
 * TODO: update this from http://detectmobilebrowsers.com/
 *
 * @return int
 */
function rssmi_isMobile() {
	return preg_match( "/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"] );
}


/**
 * Set the global $isMobileDevice variable
 * TODO: stop using global variable and combine with the above
 *
 * @return int
 */
function isMobileForWordPress() {

	global $isMobileDevice;

	$isMobileDevice = rssmi_isMobile() ? 1 : 0;

	return $isMobileDevice;
}

add_action( 'init', 'isMobileForWordPress', 1 );


/**
 * Make sure SimplePie is loaded
 * TODO: why is this needed?
 */
function startSimplePie() {
	if ( ! class_exists( 'SimplePie' ) ) {
		require_once( ABSPATH . WPINC . '/class-simplepie.php' );
	}

	class SimplePie_RSSMI extends SimplePie {
	}
}

add_action( 'init', 'startSimplePie' );


/**
 * Setup all settings pages
 * TODO: Delete commented out pages here if not used
 * TODO: Update the menu icon to fit the new admin theme
 */
function wp_rss_multi_importer_menu() {

	$menuColor   = rssmi_check_didUpgrade() == 1 ? "#2ea2cc" : "#F00";
	$parent_slug = 'wprssmi';

	// Main menu page
	add_menu_page( __( 'Overview' ), __( 'Multi Importer' ), 'manage_options', $parent_slug, 'wp_rss_multi_importer_intro_page', WP_RSS_MULTI_IMAGES . "RSSadmin16.png", '150' );

	// All submenu options pages
	add_submenu_page( $parent_slug, 'Start Here', '<span style="color:' . $menuColor . '">' . 'Start Here' . '</span>', 'manage_options', $parent_slug, 'wp_rss_multi_importer_intro_page', '', '' );

	add_submenu_page( $parent_slug, 'Feed List', 'Feed List', 'manage_options', 'edit.php?post_type=rssmi_feed', '' );
	
	add_submenu_page( $parent_slug, 'Add a Feed', 'Add a Feed', 'edit_posts', 'post-new.php?post_type=rssmi_feed', '' );

	add_submenu_page( $parent_slug, 'Upload Feeds', 'Upload Feeds', 'manage_options', 'wprssmi_options8', 'wp_rss_multi_importer_upload_page' );

	add_submenu_page( $parent_slug, 'Categories', 'Categories', 'manage_options', 'wprssmi_options', 'rssmi_category_pages', '' );
	
	add_submenu_page( $parent_slug, 'Feed Items', 'Feed Items', 'edit_posts', 'edit.php?post_type=rssmi_feed_item', '' );

	add_submenu_page( $parent_slug, 'Global Settings', 'Global Settings', 'manage_options', 'wprssmi_options5', 'wp_rss_multi_importer_dateformat_page' );

	add_submenu_page( $parent_slug, 'AutoPost', 'AutoPost', 'manage_options', 'wprssmi_options3', 'wp_rss_multi_importer_display_autopost' );

	add_submenu_page( $parent_slug, 'Shortcode', 'Shortcode', 'manage_options', 'wprssmi_options2', 'wp_rss_multi_importer_display_shortcode' );

	add_submenu_page( $parent_slug, 'Export', 'Export Feeds', 'manage_options', 'wprssmi_options7', 'wp_rss_multi_importer_feed_page' );

	add_submenu_page( $parent_slug, 'Diagnostics', 'Diagnostics', 'manage_options', 'wprssmi_options9', 'wp_rss_multi_importer_diagnostics' );

	// All submenu Feed Items pages



	//	add_submenu_page( 'wprssmi', 'Settings', '<span style="color:#2ea2cc">'.'Settings'.'</span>', 'manage_options', 'wprssmi_options2', 'wp_rss_multi_importer_display' );

	//	add_submenu_page( 'wprssmi', 'Shortcode Parameters', 'Shortcode Parameters', 'manage_options', 'wprssmi_options6', 'wp_rss_multi_importer_style_tags' );

	//	add_submenu_page( 'wprssmi', 'Shortcode Settings', 'Shortcode Settings', 'manage_options', 'wprssmi_options2', 'wp_rss_multi_importer_options_page' );

	//	add_submenu_page( 'wprssmi', 'Manage AutoPosts', 'Manage AutoPosts', 'manage_options', 'wprssmi_options4', 'rssmi_posts_list' );

	//	add_submenu_page('wprssmi', 'Start Here','<span style="color:#2ea2cc">'.'Start Here'.'</span>', 'manage_options', 'wprssmi', 'wp_rss_multi_importer_intro_page', '', '');


}

add_action( 'admin_menu', 'wp_rss_multi_importer_menu' );


/**
 * Output Feed category options page
 */
function rssmi_category_pages() {
	wp_rss_multi_importer_category_page();
	wp_rss_multi_importer_category_images_page();
}


/**
 * Output Manage Auto Post page
 */
function rssmi_posts_list() {
	global $myListTable;
	my_add_menu_items();
	rssmi_add_options();
	my_render_list_page();
	$myListTable->admin_header();
}


/**
 * Output Auto Post settings page tabs
 *
 * @param string $active_tab
 */
function wp_rss_multi_importer_display_autopost( $active_tab = '' ) {

	// Set default active tab if URL parameter is not set
	if ( empty( $active_tab ) ) {
		if ( isset( $_GET['tab'] ) ) {
			$active_tab = $_GET['tab'];
		} else {
			$active_tab = 'feed_to_post_options';
		}
	}

	?>

	<div class="wrap">

		<div id="icon-themes" class="icon32"></div>
		<h2><?php _e( "Multi-Importer", 'wp-rss-multi-importer' ) ?></h2>

		<!-- AutoPost menu  -->

		<h2 class="nav-tab-wrapper">
			<a href="?page=wprssmi_options3&tab=feed_to_post_options" class="nav-tab <?php echo $active_tab == 'feed_to_post_options' ? 'nav-tab-active' : ''; ?>"><?php _e( "AutoPost Settings", 'wp-rss-multi-importer' ) ?></a>
			<a href="?page=wprssmi_options3&tab=manage_autoposts" class="nav-tab <?php echo $active_tab == 'manage_autoposts' ? 'nav-tab-active' : ''; ?>"><?php _e( "Manage AutoPosts", 'wp-rss-multi-importer' ) ?></a>
		</h2>

		<?php
		switch ( $active_tab ) {
			case 'feed_to_post_options':
				wp_rss_multi_importer_post_page();
				break;
			case 'manage_autoposts':
				rssmi_posts_list();
				break;
			case 'category_options':
				wp_rss_multi_importer_category_page();
				wp_rss_multi_importer_category_images_page();
				break;
			case 'more_options':
				wp_rss_multi_importer_dateformat_page();
				break;
			default:
				wp_rss_multi_importer_options_page();
		}
		?>
	</div>
	<?php
}


/**
 * Output shortcode options page
 *
 * @param string $active_tab
 */
function wp_rss_multi_importer_display_shortcode( $active_tab = '' ) {

	// Set default active tab if URL parameter is not set
	if ( empty( $active_tab ) ) {
		if ( isset( $_GET['tab'] ) ) {
			$active_tab = $_GET['tab'];
		}
		else {
			$active_tab = 'setting_options';
		}
	}

	?>

	<div class="wrap">

		<div id="icon-themes" class="icon32"></div>
		<h2><?php _e( "Multi-Importer", 'wp-rss-multi-importer' ) ?></h2>

		<!-- AutoPost menu  -->

		<h2 class="nav-tab-wrapper">
			<a href="?page=wprssmi_options2&tab=setting_options" class="nav-tab <?php echo $active_tab == 'setting_options' ? 'nav-tab-active' : ''; ?>"><?php _e( "Shortcode Settings", 'wp-rss-multi-importer' ) ?></a>
			<a href="?page=wprssmi_options2&tab=shortcode_parameters" class="nav-tab <?php echo $active_tab == 'shortcode_parameters' ? 'nav-tab-active' : ''; ?>"><?php _e( "Shortcode Parameters", 'wp-rss-multi-importer' ) ?></a>

			<a href="?page=wprssmi_options2&tab=save_template" class="nav-tab <?php echo $active_tab == 'save_template' ? 'nav-tab-active' : ''; ?>"><?php _e( "Save Template and CSS", 'wp-rss-multi-importer' ) ?></a>
		</h2>

		<?php
		switch ( $active_tab ) {
			case 'setting_options':
				wp_rss_multi_importer_options_page();
				break;
			case 'shortcode_parameters':
				wp_rss_multi_importer_style_tags();
				break;
			case 'save_template':
				wp_rss_multi_importer_template_page();
				break;
			default:
				wp_rss_multi_importer_options_page();
		}
		?>
	</div>
	<?php
}