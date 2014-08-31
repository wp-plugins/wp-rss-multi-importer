<?php


function rssmi_get_default_date_format(){
	
	$rssmi_global_options = get_option( 'rssmi_global_options' ); 
	if (!empty($rssmi_global_options)){
		$date_format=$rssmi_global_options['date_format'];	
	}else{
		$date_format="D, M d, Y";	
	}
	return $date_format;
}



function rssmi_update_feed_time(){
	
	
	
		$wprssmi_admin_options = get_option( 'rss_admin_options' );  // admin settings
	    $wprssmi_admin_options['last_db_update'] = strtotime("now");
		update_option( 'rss_admin_options', $wprssmi_admin_options ); //put the current version in the database
		
		
}

function rssmi_show_last_feed_update(){
	
	
	$wprssmi_admin_options = get_option( 'rss_admin_options' );  // admin settings
	$last_db_update=$wprssmi_admin_options['last_db_update'];


	return "<br><div style='font-size:14px;'><strong>Last Update of the Feed Database on</strong> ".get_date_from_gmt(date('Y-m-d H:i:s',$last_db_update),'M j, Y @ g:i a  ').":          ".rssmi_long_ago($last_db_update)." ago<button type='button' name='getFeedsNow' id='getFeeds-Now' class='button-fetch-green' value=''>CLICK TO UPDATE THE FEED DATABASE NOW</button>&nbsp;&nbsp;&nbsp;&nbsp;(note: this could take several minutes)<div id='gfnote' style='margin-left:520px;'></div><div id='rssmi-ajax-loader-center'></div></div><p>Think there is a scheduling problem, <a href='http://www.wprssimporter.com/faqs/the-cron-scheduler-isnt-working-whats-happening/' target='_blank'>read this</a></p>";
	


}


function rssmi_long_ago ($time)
{

    $time = time() - $time; // to get the time since that moment

    $tokens = array (
        31536000 => 'year',
        2592000 => 'month',
        604800 => 'week',
        86400 => 'day',
        3600 => 'hour',
        60 => 'minute',
        1 => 'second'
    );

    foreach ($tokens as $unit => $text) {
        if ($time < $unit) continue;
        $numberOfUnits = floor($time / $unit);
        return $numberOfUnits.' '.$text.(($numberOfUnits>1)?'s':'');
    }

}



function rssmi_is_not_fresh($date){
	$rssmi_global_options = get_option( 'rssmi_global_options' ); 
	$days= round(abs(strtotime($date)-strtotime('now'))/86400);
	if (isset($rssmi_global_options['item_freshness'])){
		$day_cutoff= $rssmi_global_options['item_freshness'];	
		
			if ($days>=$day_cutoff){
				return 1;
			}else{
				return 0;
			}
	}else{
		return 0;
	}
	
}



?>