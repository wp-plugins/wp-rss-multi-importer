<?php

add_action( 'admin_init', 'upgrade_db' ); // Used starting in version 2.22...afterwards, version is being stored in db

function upgrade_db() { //beginning of upgrade function

	$option_settings       = get_option( 'rss_import_options' ); //get shortcode settings (has the plugin version)
	$items_options         = get_option( 'rss_import_items' ); // get rss feeds
	$categoryoptions       = get_option( 'rss_import_categories_images' ); //category images
	$post_options          = get_option( 'rss_post_options' ); //autoPost options
	$wprssmi_admin_options = get_option( 'rss_admin_options' ); // admin settings
	$catOptions            = get_option( 'rss_import_categories' ); //categories

//	$wprssmi_admin_options['old_plugin_version'] = $option_settings['plugin_version'];
	$old_version = '';
	if ( isset( $option_settings['plugin_version'] ) ) {
		$old_version = $option_settings['plugin_version'];
	}
	$wprssmi_admin_options['plugin_version'] = number_format( WP_RSS_MULTI_VERSION, 2 );

	update_option( 'rss_admin_options', $wprssmi_admin_options ); //put the current version in the database


	//indicate that new installs and beta testers are already activated

	if ( empty( $items_options ) || ( isset( $wprssmi_admin_options['activate'] ) && $wprssmi_admin_options['activate'] == 1 ) ) {
		$wprssmi_admin_options['activate'] = 1;
	}
	else {
		$wprssmi_admin_options['activate'] = 0;
	}
	update_option( 'rss_admin_options', $wprssmi_admin_options ); //update the database


	//  ADD THIS BEFORE LAUNCH AND MAKE VERSION 3.00
	if ( floatval( $old_version ) == 2.68 ) {
		rssmi_activate_now();
	}


	if ( ! empty( $items_options ) && empty( $option_settings ) ) { // this transfers data to new table if upgrading
		add_option( 'rss_import_options', $items_options, '', '' );
	}

	if ( ! empty( $option_settings ) ) { //only if not a new install
		if ( ! isset( $option_settings['template'] ) || $option_settings['template'] === '' ) {

			foreach ( $option_settings as $key => $value ) {
				$template_settings[$key] = $value;
			}
			$template_settings['template'] = 'default.php';
			update_option( 'rss_import_options', $template_settings );
		}

	}


	if ( empty( $post_options ) ) {

		$post_settings = array(
			'active'      => 0,
			'post_status' => 'publish',
			'maxperfetch' => 5,
			'maxfeed'     => 5,
			'maximgwidth' => 150,
			'category'    => 0
		);

		update_option( 'rss_post_options', $post_settings );
	}

	//this is for adding multiple categories to the feed to post feature (version 2.47)


	if ( ! empty( $post_options ) ) {

		if ( ! isset( $post_options['categoryid']['plugcatid'] ) || $post_options['categoryid']['plugcatid'] === '' ) {


			foreach ( $post_options as $key => $value ) {
				$post_settings[$key] = $value;
			}

			$post_settings['categoryid']['plugcatid'][1] = '';
			if ( isset( $post_options['category'] ) ) {
				$post_settings['categoryid']['plugcatid'][1] = $post_options['category'];
			}

			$post_settings['categoryid']['wpcatid'][1]  = '';
			if ( isset( $post_options['wpcategory'] ) ) {
				$post_settings['categoryid']['wpcatid'][1]   = $post_options['wpcategory'];
			}
			update_option( 'rss_post_options', $post_settings );
		}
	}


	if (
		! empty( $post_options ) &&
		isset( $post_options['categoryid'] ) &&
		! is_array( $post_options['categoryid']['wpcatid'][1] )
	) {

		foreach ( $post_options as $key => $value ) {
			if ( $key != 'categoryid' ) {
				$post_settings[$key] = $value;
			}
		}

		$catsize        = count( $catOptions );
		$postoptionsize = $catsize / 2;

		for ( $q = 1; $q <= $postoptionsize; $q ++ ) {
			$post_settings['categoryid']['plugcatid'][$q]  = $post_options['categoryid']['plugcatid'][$q];
			$post_settings['categoryid']['wpcatid'][$q][1] = $post_options['categoryid']['wpcatid'][$q];
		}

		update_option( 'rss_post_options', $post_settings );
	}


	if ( ! empty( $categoryoptions ) && ! is_array( $categoryoptions[1] ) ) {

		foreach ( $categoryoptions as $key => $value ) {
			$cat_settings[$key]['imageURL'] = $value;
			$cat_settings[$key]['tags']     = '';
		}
		update_option( 'rss_import_categories_images', $cat_settings );


	}

	//for resetting the admin message
	if ( isset( $plugin_version ) && $plugin_version < 2.40 ) {
		$wprssmi_admin_options['dismiss_slug'] = 'false';
		//update_option( 'wprssmi_admin_options', $post_settings );
	}


	if ( empty( $option_settings ) ) {

		$option_default_settings = array(
			'sortbydate'      => 0,
			'pag'             => 0,
			'targetWindow'    => 0,
			'maxfeed'         => 4,
			'sourcename'      => 'Source',
			'showcategory'    => 0,
			'noFollow'        => 0,
			'showdesc'        => 1,
			'descnum'         => 50,
			'floatType'       => 1,
			'adjustImageSize' => 1,
			'showsocial'      => 0
		);
		update_option( 'rss_import_options', $option_default_settings );

	}


} //  end of upgrade function


function rssmi_get_wp_categories( $catid ) {
	$option_post_items = get_option( 'rss_post_options' );
	if ( ! empty( $option_post_items['categoryid'] ) ) {
		$catkey  = array_search( $catid, $option_post_items['categoryid']['plugcatid'] );
		$wpcatid = $option_post_items['categoryid']['wpcatid'][$catkey];
	}
	else {
		$wpcatid = 0;
	}
	return $wpcatid;
}


// insert date format 3.00
$rssmi_global_options = get_option( 'rssmi_global_options' );
if ( empty( $rssmi_global_options ) ) {
	$date_default_settings = array(
		'date_format'     => 'D, M d, Y',
		'fetch_schedule'  => 5,
		'expiration'      => 7,
		'single_feed_max' => 20
	);

	update_option( 'rssmi_global_options', $date_default_settings );

}


function rssmi_activate_upgrade() {
//this upgrades for 2.70 - only if upgrading
//2.68 is beta version, so don't upgrade database for beta users

	$option_items          = get_option( 'rss_import_items' );
	$wprssmi_admin_options = get_option( 'rss_admin_options' );

//if (!empty($option_items) && floatval($old_version)<WP_RSS_MULTI_VERSION and floatval($old_version)<2.68){

	if ( ! isset( $wprssmi_admin_options['activate'] ) || $wprssmi_admin_options['activate'] == 0 ) {


		$post_options = get_option( 'rss_post_options' );
		$bloguserid   = $post_options['bloguserid'];
		if ( is_null( $bloguserid ) ) {
			$bloguserid = 1;
		}
		$option_values = array_values( $option_items );
		remove_action( 'save_post', 'rssmi_save_custom_fields' );
		remove_action( 'wp_insert_post', 'rssmi_fetch_feed_items' );
		for ( $i = 0; $i <= count( $option_items ) - 1; $i ++ ) {
			$feed_item   = array(
				'post_title'   => $option_values[$i],
				'post_content' => '',
				'post_status'  => 'publish',
				'post_type'    => 'rssmi_feed'
			);
			$inserted_ID = wp_insert_post( $feed_item );
			$i           = $i + 1;
			update_post_meta( $inserted_ID, "rssmi_url", $option_values[$i] );
			$i = $i + 1;
			update_post_meta( $inserted_ID, "rssmi_cat", $option_values[$i] );
			update_post_meta( $inserted_ID, "rssmi_user", $bloguserid );
			rssmi_fetch_feed_items( $inserted_ID );
			unset( $feed_item );

		}
		add_action( 'save_post', 'rssmi_save_custom_fields' );
		add_action( 'wp_insert_post', 'rssmi_fetch_feed_items' );
//	delete_option('rss_import_items');
		//  set activate to 1
		$wprssmi_admin_options['activate'] = 1;
		update_option( 'rss_admin_options', $wprssmi_admin_options );
	}
}


function rssmi_check_didUpgrade() {
	$option_items          = get_option( 'rss_import_items' );
	$wprssmi_admin_options = get_option( 'rss_admin_options' );


	if ( ( isset( $wprssmi_admin_options['activate'] ) && $wprssmi_admin_options['activate'] == 1 ) || empty( $option_items ) ) {
		$isActivated = 1;
	}
	else {
		$isActivated = 0;
	}
	return $isActivated;

}

function rssmi_activate_now() {
	$wprssmi_admin_options             = get_option( 'rss_admin_options' );
	$wprssmi_admin_options['activate'] = 1;
	update_option( 'rss_admin_options', $wprssmi_admin_options );

}

//UPGRADE NOTICES
function rssmi_admin_warnings() {

	if ( current_user_can( 'activate_plugins' ) ) {


		if ( rssmi_check_didUpgrade() == 0 ) { //restrict this to the plugin pages!!!


			//	if ((isset( $_GET['post_type'] )) && (strpos("rssmi_feed",$_GET['post_type'] ,0)!==false) || ( isset( $_GET['page'] )) && ((strpos("wprssmi",$_GET['page'] ,0)!==false) || (strpos('wprssmi',$_GET['page'] )!==false ) || (strpos('wprssmi_options',$_GET['page'] )!==false ) || (strpos('wprssmi_options2',$_GET['page'] )!==false ) || (strpos('wprssmi_options3',$_GET['page'] )!==false ) || (strpos('wprssmi_options4',$_GET['page'] )!==false ) || (strpos('wprssmi_options9',$_GET['page'] )!==false )  || (strpos('wprssmi_options5',$_GET['page'] )!==false ) || (strpos('wprssmi_options8',$_GET['page'] )!==false )|| (strpos('wprssmi_options7',$_GET['page'] )!==false ))) {
			if ( rssmi_is_plugin_page() == 1 ) {


				?>
				<div id="upgrade_message" class="error">
					<p><?php echo sprintf( __( 'Thank you for upgrading to the new version of Multi-Importer.  <br>To activate the new features you need to upgrade the database for the plugin.  <br>No problem.  Just go to the <a href="%s">Start Here page</a> and follow the simple directions. Sorry for the extra step! ' ), 'admin.php?page=wprssmi' ); ?></p>
				</div>
			<?php
			}
		}
	}


}

add_action( 'admin_notices', 'rssmi_admin_warnings', 100 );


?>