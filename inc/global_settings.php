<?php

function rssmi_lb_filter_callback(){
	$rssmi_global_options = get_option('rssmi_global_options'); 
	$f= $rssmi_global_options['lightbox_filter'];
	if (isset($f) && !empty($f)){
		$filter_array= preg_replace('/\s+/',',', $f);
		$filter_arr = explode(",", $filter_array);
	}

	return $filter_arr;
}



function rssmi_sanitize_global($input){  //gets rid of adding http to urls for lightbox filter
	$input = str_replace('http://', '', $input);	
	return $input;
}







function rssmi_fb_title_filter($url,$title){
	

	
	if (    stripos( $url, 'http://facebook.com' ) === 0
	    ||  stripos( $url, 'http://www.facebook.com' ) === 0
	    ||  stripos( $url, 'https://facebook.com' ) === 0
	    ||  stripos( $url, 'https://www.facebook.com' ) === 0
	) {	
			$array_titles=array("?", "!", ". ", ":");
			
			foreach($array_titles as $at){
				
				if (stripos ($title,$at)!==false){
						$title = current(explode($at, $title)).$at;
						
						break;
			}		
			}
						
}	
	
return $title;	
	
}


















function wp_rss_multi_importer_dateformat_page() {

       ?>
		<div id="icon-themes" class="icon32 icon32-posts-rssmi_feed"></div>
<h2><?php  _e("Multi-Importer", 'wp-rss-multi-importer')?></h2>
       <div class="wrap">
	  <h2><?php _e("Global Settings", 'wp-rss-multi-importer')?></h2>
	<div id="poststuff">

  	<?php settings_errors(); ?>


       <div id="options">
       <form action="options.php" method="post"  >            
       <?php
    	settings_fields('wp_rss_multi_importer_global_options');
		$rssmi_global_options = get_option( 'rssmi_global_options' ); 
       ?>
<div class="postbox">
<div class="inside">
	<h2><?php _e("Scheduling and Expirations", 'wp-rss-multi-importer')?></h2>

	<p><label class='o_textinput' for='fetch_schedule'><?php _e("How often feeds will be updated. <span class='vtip' title='This will determine how up to date the feeds will be for importing into the AutoPost or when displaying your shortcode.'>?</span>", 'wp-rss-multi-importer')?></label>
	<SELECT NAME="rssmi_global_options[fetch_schedule]" id="post_status">
	<OPTION VALUE="2" <?php if($rssmi_global_options['fetch_schedule']=="2"){echo 'selected';} ?>>Every 10 Min.</OPTION>
	<OPTION VALUE="3" <?php if($rssmi_global_options['fetch_schedule']=="3"){echo 'selected';} ?>>Every 15 Min.</OPTION>
	<OPTION VALUE="4" <?php if($rssmi_global_options['fetch_schedule']=="4"){echo 'selected';} ?>>Every 20 Min.</OPTION>
	<OPTION VALUE="5" <?php if($rssmi_global_options['fetch_schedule']=="5"){echo 'selected';} ?>>Every 30 Min.</OPTION>
	<OPTION VALUE="1" <?php if($rssmi_global_options['fetch_schedule']=="1"){echo 'selected';} ?>>Hourly</OPTION>
	<OPTION VALUE="6" <?php if($rssmi_global_options['fetch_schedule']=="6"){echo 'selected';} ?>>Every Two Hours</OPTION>
	<OPTION VALUE="7" <?php if($rssmi_global_options['fetch_schedule']=="7"){echo 'selected';} ?>>Every Four Hours</OPTION>
	<OPTION VALUE="12" <?php if($rssmi_global_options['fetch_schedule']=="12"){echo 'selected';} ?>>Twice Daily</OPTION>
	<OPTION VALUE="24" <?php if($rssmi_global_options['fetch_schedule']=="24"){echo 'selected';} ?>>Daily</OPTION>
	</SELECT></p>
	
	
	
	<p><label class='o_textinput' for='single_feed_max'><?php _e("Maximum number of items to import for each feed.", 'wp-rss-multi-importer')?></label>
	<SELECT NAME="rssmi_global_options[single_feed_max]" id="single_feed_max">
	<OPTION VALUE="0" <?php if($rssmi_global_options['single_feed_max']=="0"){echo 'selected';} ?>>No Limit</OPTION>
	<OPTION VALUE="2" <?php if($rssmi_global_options['single_feed_max']=="2"){echo 'selected';} ?>>2</OPTION>
	<OPTION VALUE="5" <?php if($rssmi_global_options['single_feed_max']=="5"){echo 'selected';} ?>>5</OPTION>
	<OPTION VALUE="10" <?php if($rssmi_global_options['single_feed_max']=="10"){echo 'selected';} ?>>10</OPTION>
	<OPTION VALUE="15" <?php if($rssmi_global_options['single_feed_max']=="15"){echo 'selected';} ?>>15</OPTION>
	<OPTION VALUE="20" <?php if($rssmi_global_options['single_feed_max']=="20"){echo 'selected';} ?>>20</OPTION>
	<OPTION VALUE="40" <?php if($rssmi_global_options['single_feed_max']=="40"){echo 'selected';} ?>>40</OPTION>
	<OPTION VALUE="80" <?php if($rssmi_global_options['single_feed_max']=="80"){echo 'selected';} ?>>80</OPTION>
	<OPTION VALUE="100" <?php if($rssmi_global_options['single_feed_max']=="100"){echo 'selected';} ?>>100</OPTION>
	<OPTION VALUE="150" <?php if($rssmi_global_options['single_feed_max']=="150"){echo 'selected';} ?>>150</OPTION>
	<OPTION VALUE="200" <?php if($rssmi_global_options['single_feed_max']=="200"){echo 'selected';} ?>>200</OPTION>
	</SELECT></p>
	
	
	
	
	
	
	

	<p ><label class='o_textinput' for='expiration'><?php _e("Remove older feed item after how much time (number of days, weeks, etc.) <span class='vtip' title='Use this to delete older items automatically based on how old they are.'>?</span>", 'wp-rss-multi-importer')?></label>
	<SELECT NAME="rssmi_global_options[expiration]" id="expiration">
	<OPTION VALUE="0" <?php if($rssmi_global_options['expiration']==0){echo 'selected';} ?>>Never</OPTION>
	<OPTION VALUE="1" <?php if($rssmi_global_options['expiration']==1){echo 'selected';} ?>>1 Day</OPTION>
	<OPTION VALUE="2" <?php if($rssmi_global_options['expiration']==2){echo 'selected';} ?>>2 Days</OPTION>
	<OPTION VALUE="3" <?php if($rssmi_global_options['expiration']==3){echo 'selected';} ?>>3 Days</OPTION>
	<OPTION VALUE="4" <?php if($rssmi_global_options['expiration']==4){echo 'selected';} ?>>4 Days</OPTION>
	<OPTION VALUE="5" <?php if($rssmi_global_options['expiration']==5){echo 'selected';} ?>>5 Days</OPTION>
	<OPTION VALUE="6" <?php if($rssmi_global_options['expiration']==6){echo 'selected';} ?>>6 Days</OPTION>
	<OPTION VALUE="7" <?php if($rssmi_global_options['expiration']==7){echo 'selected';} ?>>7 Days</OPTION>
	<OPTION VALUE="14" <?php if($rssmi_global_options['expiration']==14){echo 'selected';} ?>>2 Weeks</OPTION>
	<OPTION VALUE="21" <?php if($rssmi_global_options['expiration']==21){echo 'selected';} ?>>3 Weeks</OPTION>
	<OPTION VALUE="28" <?php if($rssmi_global_options['expiration']==28){echo 'selected';} ?>>4 Weeks</OPTION>
	<OPTION VALUE="56" <?php if($rssmi_global_options['expiration']==56){echo 'selected';} ?>>2 Months</OPTION>
	</SELECT></p>
	
	

	<p ><label class='o_textinput' for='item_freshness'><?php _e("Only bring in items newer than how many days old?", 'wp-rss-multi-importer')?></label>
 <input  id='item_freshness' type="text" size='8'  Name="rssmi_global_options[item_freshness]" Value="<?php echo $rssmi_global_options['item_freshness'] ?>">days old (leave blank to place no restriction.)
	</p>

	
	
<hr>
<h2><?php _e("SimplePie Settings", 'wp-rss-multi-importer')?></h2>	
	
<p><label class='o_textinput' for='noForcedFeed'><?php _e("By default, the plugin uses a forced feed setting for SimplePie.  By checking here, you can turn this off.", 'wp-rss-multi-importer')?>
	
<input type="checkbox" Name="rssmi_global_options[noForcedFeed]" Value="1" <?php if (isset($rssmi_global_options['noForcedFeed']) && $rssmi_global_options['noForcedFeed']==1){echo 'checked="checked"';} ?>></p>	
	
	
<hr>
<h2><?php _e("Get Image Size Settings", 'wp-rss-multi-importer')?></h2>	
	
<p><label class='o_textinput' for='noImageSize'><?php _e("By default, the plugin tries to get the image size remotely - but this can result timeouts and performance problems.  By checking here, you can turn this off.", 'wp-rss-multi-importer')?>
	
<input type="checkbox" Name="rssmi_global_options[noImageSize]" Value="1" <?php if (isset($rssmi_global_options['noImageSize']) && $rssmi_global_options['noImageSize']==1){echo 'checked="checked"';} ?>></p>	
	
	
	
	
	<hr>
	<h2><?php _e("Eliminate Lightbox problem domains", 'wp-rss-multi-importer')?></h2>
	
	<p><?php _e("Some domains do not allow lightbox access.  By putting in the domains of these site (including the subdomain, if relevant), you can make sure these sites open up in a new window.  Just add the main domain name and sub-domain if relevant.  For example, if you find that feeds from finance.yahoo.com don't work, put finance.yahoo.com in the text box (don't add the http://).  Add multiple domains by putting them on separate lines.", 'wp-rss-multi-importer')?></p>
	
	<div style="margin-bottom:20px;"><textarea name="rssmi_global_options[lightbox_filter]" rows="4" cols="40"
	  style="vertical-align: top"><?php echo $rssmi_global_options['lightbox_filter']?></textarea></div>

	
	
<hr>
<h2><?php _e("Suppress Warning Messages in the Shortcode and Widget", 'wp-rss-multi-importer')?></h2>
<p><label class='o_textinput' for='fb_title_check'><?php _e("Check this box to suppress warnings about empty feeds, etc.", 'wp-rss-multi-importer')?>

<input type="checkbox" Name="rssmi_global_options[suppress_warnings]" Value="1" <?php if (isset($rssmi_global_options['suppress_warnings']) && $rssmi_global_options['suppress_warnings']==1){echo 'checked="checked"';} ?>></p>

<hr>
<h2><?php _e("Set the Date Format in the Shortcode and Widget", 'wp-rss-multi-importer')?></h2>
<p><?php _e("You can set the date format for the shortcode and widget, using the date format strings in Wordpress. If you are using the AutoPost, those are entered using standard Wordpress formats - you configure how they are formatted on your web site using the General Settings tab for Wordpress.", 'wp-rss-multi-importer')?></p>

<p><label class='o_textinput' for='date_format'><?php _e("Date Format", 'wp-rss-multi-importer')?></label>

<input id="date_format" type="text" value="<?php echo $rssmi_global_options['date_format']?>" name="rssmi_global_options[date_format]"><a href="http://codex.wordpress.org/Formatting_Date_and_Time" target="_blank">Go here to see other ways to present the date</a></p>

<hr>
<h2><?php _e("Make Facebook Feed Titles Better", 'wp-rss-multi-importer')?></h2>
<p><label class='o_textinput' for='fb_title_check'><?php _e("Facebook RSS feeds often have titles that are very long and thus broken off in mid-word.  You can fix this by having the plugin try to stop the title at a question mark, period, etc..(available only for AutoPost)", 'wp-rss-multi-importer')?>

<input type="checkbox" Name="rssmi_global_options[fb_title_check]" Value="1" <?php if (isset($rssmi_global_options['fb_title_check']) && $rssmi_global_options['fb_title_check']==1){echo 'checked="checked"';} ?>></p>

       <p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>
       </form>


</div></div>

</div>


</div></div>

<div class="postbox">
<div class="inside">
	<p>Click the green button to update all feed items now or the red button to clear out the feed item database.  If you have many feeds, this could take several minutes to complete.</p>
		<div style="margin-bottom:40px">
			<button type="button" name="getFeedsNow" id="getFeeds-Now" class="button-fetch-green" value=""><?php _e("CLICK TO UPDATE THE FEED DATABASE NOW", 'wp-rss-multi-importer')?></button>	
			<div id="gfnote"></div><div id="rssmi-ajax-loader"></div></div>
	

		<div style="margin-bottom:20px">
			<button type="button" name="deleteFeedsNow" id="deleteFeeds-Now" class="button-delete-feeds-red" value=""><?php _e("CLICK TO CLEAR THE FEED DATABASE NOW", 'wp-rss-multi-importer')?></button>	
			<div id="dfnote"></div><div id="rssmi-ajax-loader"></div></div>

</div></div>
<?php
}

?>