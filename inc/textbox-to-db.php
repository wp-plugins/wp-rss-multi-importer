<?php

function rssmi_isValidURL($url){
   return preg_match('|^http(s)?://[a-z0-9-]+(.[a-z0-9-]+)*(:[0-9]+)?(/.*)?$|i', $url);
}

function wp_rss_multi_importer_upload_page()
{
	remove_action('save_post', 'rssmi_save_custom_fields');
	echo '<div class="wrap">';
	echo '<div id="poststuff"><div id="post-body">'; 	 	   	
	

	
    if (isset($_POST['multilookup']) && $_REQUEST['multiadd']=="SAVE" )
    {
    	$URL_Array=$_POST['multilookup'];
		$URL_arr = explode("\n", $URL_Array);

	$msg='';
	$activate=0;
//	date_default_timezone_set($serverTimezone);
	$rightNow==current_time( 'timestamp' );
	
foreach($URL_arr as $url_a){
	
	if (strpos($url_a, "ACTIVATE")!==false){
		$activate=1;
		continue;
	}
	
	
	$pos = strpos($url_a, ",");
	if ($pos === false){
		$URL_feed_address=$url_a;
		$URL_title="Unknown";
		
	}else{
		$URL_feed=explode(",",$url_a );
		$URL_feed_address=$URL_feed[1];
		$URL_title=$URL_feed[0];
		$URL_category=$URL_feed[2];
		$URL_user=$URL_feed[3];
	}

	$url_result = rssmi_isValidURL(trim($URL_feed_address));
	if(!$url_result) {
		$msg="  However, one or more of the feeds did not have a valid URL.";
		continue;
	}
	
	$rss_post = array(
  	'post_title'   => $URL_title,
 	'post_status'   => 'publish',
  	'post_type'   => 'rssmi_feed',
	'post_date'=>$rightNow

);

	$post_id = wp_insert_post( $rss_post, $wp_error );
	update_post_meta( $post_id, 'rssmi_url', $URL_feed_address);
	update_post_meta( $post_id, 'rssmi_cat', $URL_category);  
	update_post_meta( $post_id, 'rssmi_user', $URL_user);    

}
 



        echo '<div id="message" class="updated fade"><p><strong>';
        echo 'The feeds have been saved!';
		echo $msg;
        echo '</strong></p>';
		echo '</div>';
   		add_action('save_post', 'rssmi_save_custom_fields');
		rssmi_fetch_all_feed_items( ) ;
		if ($activate==1){
			rssmi_activate_now();  //activate if urls are from an upgrade
		}
}
    
    

    ?>
	<div id="icon-themes" class="icon32 icon32-posts-rssmi_feed"></div>
	<h2 class="brand-icon"><?php  _e("Multi-Importer", 'wp-rss-multi-importer')?></h2>
		<?php settings_errors(); ?>
		<div class="wrap">
    <h2>Upload Multiple RSS Feeds</h2>

	<div class="postbox">
	
	<div class="inside">



		<form action="" method="post">
		 
		  <h4>Add Multiple Feed Sources</h4>
		  <div>Enter one Feed Name per line, comma delimited (this is important) as follows (the plugin category and Blog User ID are optional):</div><br>
		  <div>[Feed Title],[Feed RSS URL],[Plugin Category ID number], [Blog User ID] (note:  do not use the brackets)</div><br>
		  <div><textarea name="multilookup" rows="8" cols="60"
		  style="vertical-align: top"></textarea></div>
		  <div style="border-top: 1px dotted black; padding-top: 10px">
		  <div class="alignright"><input type="submit" class="button-primary" name="multiadd" value="SAVE" /></div>
		  <div class="alignleft"><input type="button" class="button-secondary" name="action" value="CANCEL"/></div>
		  </div>
		</form>
	<br><br><p>If you need to get the plugin category ID number, simply click on the Categories Tab.  To get the Blog User ID, <a href="http://www.wprssimporter.com/faqs/finding-the-blog-user-id/" target="_blank">use this link to find out how</a>.</p>
	
       
    <?php  
  echo '</div></div>';
  echo '</div></div></div>';

}


?>