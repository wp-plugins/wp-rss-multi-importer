<?php
add_action('wp_ajax_checkfeeds_all', 'wp_rss_multi_importer_checkfeeds');

function wp_rss_multi_importer_checkfeeds(){
		$rssmi_global_options = get_option('rssmi_global_options'); 
		$noDirectFetch=(isset($rssmi_global_options['noForcedFeed']) ? $rssmi_global_options['noForcedFeed'] : 0);
		
		 global $wpdb;

		$query = "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'rssmi_feed'";
		$myUrlList = array();
		$ids = $wpdb->get_results($query);

	    foreach($ids as $id) {                   
	               array_push($myUrlList,get_post_meta( $id->ID, 'rssmi_url', true ));
		}


		if(!empty($myUrlList)){
		//  Check invalid Feed URLS
	
			$badURL=0;
			   foreach($myUrlList as $rssURL){

						 if( ! empty( $rssURL ) ) {    

							if ($noDirectFetch==1){
								$feed = fetch_feed($rssURL);
							}else{
								 $feed = wp_rss_fetchFeed( $rssURL,20,true,0 ); 
							}
					
					
					
					           if ( $feed->error()) {

									if ($badURL==0){
										_e('<div style="border-bottom:1px solid black;padding-bottom:20px"><h2>These RSS Feed URL are either invalid or not making a connection to their servers at the time of this test:</h2> (If after checking, you find the feed is valid then  <a href="http://www.wprssimporter.com/faqs/im-told-the-feed-isnt-valid-or-working/" target=\"_blank\">go here to learn more about what might be wrong</a>.)','wp-rss-multi-importer');
										$badURL=1;
									}

					               _e('<h3> ' . $rssURL . '</h3>','wp-rss-multi-importer'); 


					           _e("<strong>Error: Invalid feed URL or feed is not being served at the time of this test.</strong> - Validate this feed source URL by <a href=\"http://validator.w3.org/feed/check.cgi?url=".$rssURL."\" target=\"_blank\">clicking here</a>.",'wp-rss-multi-importer');
					       }
					if($badURL==1){echo "</div>";}
					}



	}
	if($badURL==0){_e("<h3>GOOD NEWS: There were no errors due to feeds not connecting to external servers.  Make sure to update the feeds by clicking the green button below.</h3>",'wp-rss-multi-importer');}
die();

}






}

function wp_rss_multi_importer_diagnostics(){
	?>
		<h2><?php  _e("Multi-Importer", 'wp-rss-multi-importer')?></h2>
		       <div class="wrap">
		 <h2>Diagnostics</h2>
		
		
		
		
		
	<div id="poststuff">
		<div class="postbox">
		<div class="inside">
						
<?php


 	$post_options = get_option('rss_post_options');  
  
_e('<p>A quick check of basic diagnostics (filters, cron scheduler, php settings, text settings) are now being run and will let you know if there are any obvious problems below. <a href="http://www.wprssimporter.com/faqs" target=\"_blank\">Go here to see all FAQs about typical problems and how they can be solved.</a></p>','wp-rss-multi-importer' );


	
	//  Check FOPEN for Images
		if (ini_get('allow_url_fopen')==0){
		_e('<h3>Your server is not configured to accept images from outside sources.  Please contact your web host to set allow_url_fopen to ON.  You might be able to do this for yourself if your host gives you a way to edit the php.ini file.</h3>','wp-rss-multi-importer');	
		}
		
		//  Check FSOCKOPEN for Images
		if(!function_exists('fsockopen')) {
		_e('<h3>Your server is not configured to for fsockopen.  Without this being configured, you may have problems connecting to another server to get an RSS feed. Please contact your web host to set fsockopen.  You might be able to do this for yourself if your host gives you a way to edit the php.ini file.</h3>','wp-rss-multi-importer');	
		}

		
		
//  Check the Cron Scheduler		

echo '<h3>Cron Schedule</h3>';
		
		$cronSchedule=get_option('cron');
	//	var_dump($cronSchedule);
		
			foreach ( $cronSchedule as $timestamp => $cronhooks ) {
				foreach ( (array) $cronhooks as $hook => $events ) {
					foreach ( (array) $events as $key => $event ) {		
						if (stripos($hook,'wp_rss_multi_event_feedtopost')!== false){
						//echo  $hook.' is scheduled for every '.friendly_event_schedule_name($event[ 'schedule' ]).'<br>' ;
						_e('<p style="margin-left:30px"> The AutoPost is successfully scheduled for this time period:  '.friendly_event_schedule_name($event[ 'schedule' ]).'. </p>','wp-rss-multi-importer') ;
					}
						
					if (stripos($hook,'wp_rss_multi_event_importfeeds')!== false){
					_e('<p style="margin-left:30px"> The database is successfully scheduled for this time period:  '.friendly_event_schedule_name($event[ 'schedule' ]).'. </p>','wp-rss-multi-importer') ;
				}			
					
					}
				}
			}
			if (!isset($post_options['active']) || $post_options['active']==0){	
					_e('<p style="margin-left:30px">The AutoPost is not active, so there is no schedule.</p>','wp-rss-multi-importer') ;
				}else{
echo '<p><strong>IMPORTANT:</strong> If the period for AutoPost is less than the period for the database, you will not be getting new AutoPost feeds until the database is updated.  It is better to have the AutoPost set for a longer interval than than the database.</p>';
}
echo '<p>If there are cron schedules, but things do not seem to be working, <a href="http://www.wprssimporter.com/faqs/the-cron-scheduler-isnt-working-whats-happening/" target="_blank">read this to learn what is going on.</a>	</p>';	


	
	
	// check for Word Filters
	
	$options = get_option('rss_import_categories' );
	$option_category = get_option('rss_import_categories_images'); 
	if ( !empty($options) ) {
		$size = count($options);
		for ( $i=1; $i<=$size; $i++ ) {   

				if( $i % 2== 0 ) continue;
				   $key = key( $options );

	$j = cat_get_id_number($key);
		$myCatIDs[] = array("CatID"=>$j);

		next( $options );	
		next( $options );
		}
	}

	if (!empty($myCatIDs)){
	foreach($myCatIDs as $myCatID){
	
	$catID=$myCatID['CatID'];

	if(!empty($option_category)){
		$filterString=(isset($option_category[$catID]['filterwords']) ? $option_category[$catID]['filterwords'] : null);	
		$exclude=(isset($option_category[$catID]['exclude']) ? $option_category[$catID]['exclude'] : null);		
		$filterWords=explode(',', $filterString);
		if (!is_null($filterWords) && !empty($filterWords) && is_array($filterWords)){
			foreach($filterWords as $filterWord){
					if ($filterWord!=''){	
							$msg=1;
							break;	
						}else{
							$msg=0;
						}
					}
			}
		}
	
		if ($msg==1){
			_e("<h3>Category Word Filters are On</h3><p style='margin-left:30px'>This could be a reason you are getting no new posts.</p>", 'wp-rss-multi-importer');
		}
	}
}

//  Check for 0 for amount of text

$text_options = get_option('rss_import_options');
$post_options = get_option('rss_post_options'); 

if (isset($post_options['active']) && $post_options['active']==1){	
	if ($post_options['descnum']==0){
		_e("<h3>You appear to be running the Feed to Post and chosen to have no text</h3><p style='margin-left:30px'>This is the most likely reason you have no text in your posts.  If you want text to show, set that in the Feed to Post admin page.</p>",'wp-rss-multi-importer');
	}
	}elseif ($text_options['descnum']==0){
			_e("<h3>Notice: You appear to be using the Shortcode and chosen to have no text</h3><p style='margin-left:30px'>This is the most likely reason you have no text in your posts.  If you want text to show and are using the Shortcode, set that in the Shortcode admin page.</p>",'wp-rss-multi-importer');
	} 
	
	// Check for feed numbers
	
	$feed_options = get_option('rss_import_items'); 
	$numFeeds= count($feed_options)/3;
	
	if (isset($post_options['active']) && $post_options['active']==1){	
		if ($post_options['maxfeed']*$numFeeds>$post_options['maxperfetch']){
			_e("<h3>You appear to be running the Feed to Post and configured the number of feeds incorrectly.</h3><p style='margin-left:30px'>This is the most likely reason you have problems.  <a href=\"http://www.wprssimporter.com/faqs/how-does-the-number-of-entries-per-feed-and-page-or-fetch-work//\" target=\"_blank\">GO HERE TO SEE HOW TO SET THIS OPTION</a></p>",'wp-rss-multi-importer');
		}
	}elseif ($text_options['maxfeed']*$numFeeds>$text_options['maxperPage']){
				_e("<h3>Notice: You appear to be using the Shortcode and configured the feeds incorrectly.</h3><p style='margin-left:30px'>This is the most likely reason you have problems.  <a href=\"http://www.wprssimporter.com/faqs/how-does-the-number-of-entries-per-feed-and-page-or-fetch-work//\" target=\"_blank\">GO HERE TO SEE HOW TO SET THIS OPTION</a></p>",'wp-rss-multi-importer');
		}
	
		?>
		<h3>Check Your Feeds</h3><br>
		<button type="button"  name="checkfeedsall" id="checkfeeds-all" value="" ><?php _e("CLICK TO CHECK ALL FEEDS NOW", 'wp-rss-multi-importer')?></button> <p>This could take a few minutes, so click once and then be patient!</p><div id="checkfeeds_note"></div><div id="rssmi-ajax-loader-center"></div>
	
		</div></div></div>
		
		<div id="poststuff">
			<div class="postbox">
			<div class="inside">

			<?php echo 		rssmi_show_last_feed_update();?>
		</div></div></div>
		
		
		
		</div>
		
		<?php

		

}

function friendly_event_schedule_name($scheduleName){

	if (stripos($scheduleName,'minutes')>0){
		$scheduleName = str_replace('minutes',' minutes',$scheduleName);
	}
	if (stripos($scheduleName,'hours')>0){
		$scheduleName = str_replace('hours',' hours',$scheduleName);
	}
	if (stripos($scheduleName,'daily')>0){
		$scheduleName = str_replace('daily',' daily',$scheduleName);
	}
	
	if (stripos($scheduleName,'hourly')>0){
		$scheduleName = str_replace(' hourly ',' hour',$scheduleName);
	}
	
	
	return $scheduleName;
}

?>