<?php

/**
 * Returns the saved or default date format
 *
 * @return string
 */
function rssmi_get_default_date_format() {

	$rssmi_global_options = get_option( 'rssmi_global_options' );

	if ( ! empty( $rssmi_global_options ) ) {
		return $rssmi_global_options['date_format'];
	}
	else {
		return "D, M d, Y";
	}
}

/**
 * Update the last DB update option in the database to now
 */
function rssmi_update_feed_time() {

	$wprssmi_admin_options                   = get_option( 'rss_admin_options' ); // admin settings
	$wprssmi_admin_options['last_db_update'] = time();
	update_option( 'rss_admin_options', $wprssmi_admin_options ); //put the current version in the database

}

/**
 * Displays the last time the feed was updated and controls to update now
 *
 * @return string
 */
function rssmi_show_last_feed_update() {

	$wprssmi_admin_options = get_option( 'rss_admin_options' ); // admin settings
	$last_db_update        = $wprssmi_admin_options['last_db_update'];

	return "
	<h3>Last Update of the Feed Database: <em>" .
		get_date_from_gmt( date( 'Y-m-d H:i:s', $last_db_update ), 'M j, Y @ g:i a' ) . "; " .
		human_time_diff( $last_db_update, time() ) . " ago</em></h3>
	<p><button type='button' name='getFeedsNow' id='getFeeds-Now' class='button button-primary' value=''>Update the feed Database</button></p>

	<div id='gfnote'>
		<em>(note: this could take several minutes)</em>
	</div>
	<div id='rssmi-ajax-loader-center'></div>
	<p>Think there is a scheduling problem? <a href='http://www.wprssimporter.com/faqs/the-cron-scheduler-isnt-working-whats-happening/' target='_blank'>Read this</a>.</p>";

}

/**
 * Determines item freshness
 *
 * @param $date
 *
 * @return int
 */
function rssmi_is_not_fresh( $date ) {
	$rssmi_global_options = get_option( 'rssmi_global_options' );
	$days                 = round( abs( strtotime( $date ) - strtotime( 'now' ) ) / 86400 );
	if ( isset( $rssmi_global_options['item_freshness'] ) && $rssmi_global_options['item_freshness']>0 ) {
		$day_cutoff = $rssmi_global_options['item_freshness'];

		if ( $days >= $day_cutoff ) {
			return 1;
		}
		else {
			return 0;
		}
	}
	else {
		return 0;
	}
}
