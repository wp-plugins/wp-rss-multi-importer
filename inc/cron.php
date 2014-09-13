<?php

/**
 * Create cron events
 */
function wp_rss_multi_activation() {

	if ( ! wp_next_scheduled( 'wp_rss_multi_event' ) ) {
		wp_schedule_event( time(), 'hourly', 'wp_rss_multi_event' );
	}

	if ( ! wp_next_scheduled( 'wp_rss_multi_event_delete_custom_posts' ) ) {
		wp_schedule_event( time(), 'hourly', 'wp_rss_multi_event_delete_custom_posts' );
	}

}

add_action( 'init', 'wp_rss_multi_activation' );


/**
 * Setup additional cron schedule for Auto Post
 */
function rssmi_schedule_autopost() {

	$post_schedule_options = get_option( 'rss_post_options' );
	if ( isset( $post_schedule_options['active'] ) && $post_schedule_options['active'] == 1 ) {

		if ( isset( $post_schedule_options['fetch_schedule'] ) ) {
			$periodnumber = $post_schedule_options['fetch_schedule'];
		}
		else {
			$periodnumber = 1;
		}

		switch ( $periodnumber ) {
			case 1:
				$display_period = 'hourly';
				break;
			case 2:
				$display_period = 'tenminutes';
				break;
			case 3:
				$display_period = 'fifteenminutes';
				break;
			case 4:
				$display_period = 'twentyminutes';
				break;
			case 5:
				$display_period = 'thirtyminutes';
				break;
			case 6:
				$display_period = 'twohours';
				break;
			case 7:
				$display_period = 'fourhours';
				break;
			case 12:
				$display_period = 'twicedaily';
				break;
			case 24:
				$display_period = 'daily';
				break;
			case 168:
				$display_period = 'weekly';
				break;
			default:
				$display_period = 'weekly';
		}


		if ( ! wp_next_scheduled( 'wp_rss_multi_event_feedtopost' ) ) {
			wp_schedule_event( time(), $display_period, 'wp_rss_multi_event_feedtopost' );
		}

		add_action( 'wp_rss_multi_event_feedtopost', 'wp_rss_multi_cron_feedtopost' );

	}
	else {
		wp_rss_multi_deactivation( 2 );
	}
}

add_action( 'init', 'rssmi_schedule_autopost' );


/**
 * Setup additional cron schedule for Feed Imports
 */
function rssmi_schedule_import_feeds() {

	$rssmi_global_options = get_option( 'rssmi_global_options' );

	if ( isset( $rssmi_global_options['fetch_schedule'] ) ) {
		$feedperiodnumber = $rssmi_global_options['fetch_schedule'];
	}
	else {
		$feedperiodnumber = 1;
	}

	$feed_display_period = '';
	switch ( $feedperiodnumber ) {
		case 1:
			$feed_display_period = 'hourly';
			break;
		case 2:
			$feed_display_period = 'tenminutes';
			break;
		case 3:
			$feed_display_period = 'fifteenminutes';
			break;
		case 4:
			$feed_display_period = 'twentyminutes';
			break;
		case 5:
			$feed_display_period = 'thirtyminutes';
			break;
		case 6:
			$feed_display_period = 'twohours';
			break;
		case 7:
			$feed_display_period = 'fourhours';
			break;
		case 12:
			$feed_display_period = 'twicedaily';
			break;
		case 24:
			$feed_display_period = 'daily';
			break;
		case 168:
			$feed_display_period = 'weekly';
			break;
	}

	$current_schedule = wp_get_schedule( 'wp_rss_multi_event_importfeeds' );

	if ( $current_schedule != $feed_display_period ) {
		wp_clear_scheduled_hook( 'wp_rss_multi_event_importfeeds' );
	}

	if ( ! wp_next_scheduled( 'wp_rss_multi_event_importfeeds' ) ) {
		wp_schedule_event( time(), $feed_display_period, 'wp_rss_multi_event_importfeeds' );
	}

	add_action( 'wp_rss_multi_event_importfeeds', 'wp_rss_multi_cron_importfeeds' );
}

add_action( 'init', 'rssmi_schedule_import_feeds' );


/**
 * Add a weekly schedule to cron
 *
 * @param $schedules
 *
 * @return mixed
 */
function cron_add_wprssmi_schedule( $schedules ) {

	$period              = 168 * 3600;
	$schedules['weekly'] = array(
		'interval' => $period,
		'display'  => __( 'Once Weekly' )
	);
	return $schedules;
}

add_filter( 'cron_schedules', 'cron_add_wprssmi_schedule' );


/**
 * Add a 10 min schedule to cron
 *
 * @param $schedules
 *
 * @return mixed
 */
function cron_add_wprssmi_schedule_10( $schedules ) {

	$period                  = 600;
	$schedules['tenminutes'] = array(
		'interval' => $period,
		'display'  => __( 'Once Every 10 Minutes' )
	);
	return $schedules;
}

add_filter( 'cron_schedules', 'cron_add_wprssmi_schedule_10' );


/**
 * Add a 15 min schedule to cron
 *
 * @param $schedules
 *
 * @return mixed
 */
function cron_add_wprssmi_schedule_15( $schedules ) {

	$period                      = 900;
	$schedules['fifteenminutes'] = array(
		'interval' => $period,
		'display'  => __( 'Once Every 15 Minutes' )
	);
	return $schedules;
}

add_filter( 'cron_schedules', 'cron_add_wprssmi_schedule_15' );


/**
 * Add a 20 min schedule to cron
 *
 * @param $schedules
 *
 * @return mixed
 */
function cron_add_wprssmi_schedule_20( $schedules ) {

	$period                     = 1200;
	$schedules['twentyminutes'] = array(
		'interval' => $period,
		'display'  => __( 'Once Every 20 Minutes' )
	);
	return $schedules;
}

add_filter( 'cron_schedules', 'cron_add_wprssmi_schedule_20' );


/**
 * Add a 30 min schedule to cron
 *
 * @param $schedules
 *
 * @return mixed
 */
function cron_add_wprssmi_schedule_30( $schedules ) {

	$period                     = 1800;
	$schedules['thirtyminutes'] = array(
		'interval' => $period,
		'display'  => __( 'Once Every 30 Minutes' )
	);
	return $schedules;
}

add_filter( 'cron_schedules', 'cron_add_wprssmi_schedule_30' );


/**
 * Add a 2 hourly schedule to cron
 *
 * @param $schedules
 *
 * @return mixed
 */
function cron_add_wprssmi_schedule_120( $schedules ) {

	$period                = 7200;
	$schedules['twohours'] = array(
		'interval' => $period,
		'display'  => __( 'Once Every 2 Hours' )
	);
	return $schedules;
}

add_filter( 'cron_schedules', 'cron_add_wprssmi_schedule_120' );


/**
 * Aadd a 4 hourly schedule to cron
 *
 * @param $schedules
 *
 * @return mixed
 */
function cron_add_wprssmi_schedule_240( $schedules ) {

	$period                 = 14400;
	$schedules['fourhours'] = array(
		'interval' => $period,
		'display'  => __( 'Once Every 4 Hours' )
	);
	return $schedules;
}

add_filter( 'cron_schedules', 'cron_add_wprssmi_schedule_240' );


/**
 * Call the find_db_transients function during the wp_rss_multi_event cron schedule
 */
function wp_rss_multi_cron() {
	find_db_transients();
}

add_action( 'wp_rss_multi_event', 'wp_rss_multi_cron' );


/**
 * Find all WP RSS MI transients
 */
function find_db_transients() {

	global $wpdb;

	$expired = $wpdb->get_col( "
		SELECT option_name
		FROM {$wpdb->options}
		WHERE option_name
		LIKE '_transient_wprssmi_%';
	" );

	if ( ! $expired ) {
		return;
	}

	foreach ( $expired as $transient ) {
		$key = str_replace( '_transient_wprssmi_', '', $transient );
		wp_rss_multi_importer_shortcode( array( 'category' => $key ) );
	}
}


/**
 *  Delete feed items
 */
function wp_rss_multi_delete_custom_posts() {
	rssmi_delete_custom_posts();
}

add_action( 'wp_rss_multi_event_delete_custom_posts', 'wp_rss_multi_delete_custom_posts' );


/**
 * Fetch AutoPost items from database
 */
function wp_rss_multi_cron_feedtopost() {
	rssmi_import_feed_post();

}


/**
 * Import feed items
 */
function wp_rss_multi_cron_importfeeds() {
	rssmi_fetch_all_feed_items();

}



function wp_rss_multi_deactivation( $hook_event ) {
	if ( $hook_event == 1 ) {
		wp_clear_scheduled_hook( 'wp_rss_multi_event_feedtopost' );
	}
}