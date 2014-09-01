<?php








function rssmi_restore_all_delete() {
rssmi_restore_all();
}



add_action('wp_ajax_restore_all', 'restore_all_callback_delete');

function restore_all_callback_delete() {

			rssmi_restore_all_delete();
			echo '<h3>Everything has been deleted.</h3>';
			die();
}




add_action('wp_ajax_getFeeds_Now', 'rssmi_import_all_feed_items');

function rssmi_import_all_feed_items() {
				rssmi_fetch_all_feed_items();
				echo '<h3>The feeds database has been updated.</h3>';
			die();

}



add_action('wp_ajax_deleteFeeds_Now', 'rssmi_delete_all_feed_items');

function rssmi_delete_all_feed_items() {
				rssmi_delete_all_custom_posts();
				echo '<h3>The feeds database has been cleared.</h3>';
			die();

}



add_action('wp_ajax_upgrade_feeds', 'upgradefeeds_callback_activate');

function upgradefeeds_callback_activate() {

			rssmi_activate_upgrade();
			
			echo '<h3>Your data has been transferred.</h3><div style="margin-left:60px;"><input type="submit" name="submit" value="Reload This Page Now" onClick="window.location.reload()" /></div>';
			die();
}






function delete_db_transients() {

    global $wpdb;

  
    $expired = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_wprssmi_%';" );

    foreach( $expired as $transient ) {

        $key = str_replace('_transient_', '', $transient);
        delete_transient($key);

    }
}


function rssmi_list_the_plugins() {
    $plugins = get_option ( 'active_plugins', array () );
    foreach ( $plugins as $plugin ) {
        echo "<li>$plugin</li>";
    }
}





function rssmi_list_options(){
	
	 $options = get_option( 'rss_import_options' );
	
	 foreach ( $options as $option ) {
	        echo "<li>$option</li>";
	    }
	
}

function rssmi_change_post_status($post_id,$status){
    $current_post = get_post( $post_id, 'ARRAY_A' );
    $current_post['post_status'] = $status;
    wp_update_post($current_post);
}



function rssmi_delete_attachment($id_ID){
	$args = array( 'post_type' => 'attachment', 'numberposts' => -1, 'post_status' =>'any', 'post_parent' => $id_ID ); 
	$attachments = get_posts($args);			
	if ($attachments) {
		foreach ( $attachments as $attachment ) {
			 wp_delete_attachment($attachment->ID,true);
		}
	}
}


function rssmi_delete_posts(){  //TIMED DELETE OF AUTOPOSTS - FROM IMPORT_POSTS.PHP
	
	global $wpdb;
	$post_options_delete = get_option('rss_post_options');
	$expiration=$post_options_delete['expiration'];
	$oldPostStatus=$post_options_delete['oldPostStatus'];
	$keepcomments= $post_options_delete['keepcomments'];
	$serverTimezone=$post_options_delete['timezone'];
	
	if (isset($serverTimezone) && $serverTimezone!=''){
		date_default_timezone_set($serverTimezone);
	}
	

	$query = "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'post' AND DATEDIFF(NOW(), `post_date`) > ".$expiration;
	$ids = $wpdb->get_results($query);
	

	
	foreach ($ids as $id){
		$mypostids = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE meta_key = 'rssmi_source_link' AND post_id = ".$id->ID);
		
	
		if(get_post_meta($id->ID, 'rssmi_source_protect', true)==1) continue;
		if (!empty($mypostids)){
		
			
			if ($keepcomments==1 && get_comments_number($id->ID)==0){
				$okToDelete=1;
			}elseif ($keepcomments==1 && get_comments_number($id->ID)>0){
				add_post_meta($id->ID, 'rssmi_source_protect', 1);
				$okToDelete=0;
			}elseif ($keepcomments!=1){
				$okToDelete=1;
			}
			
			
			
			if($oldPostStatus==0  && $okToDelete==1){
					
				rssmi_delete_attachment($id->ID);
				wp_delete_post($id->ID, true);

			}elseif ($oldPostStatus==1){
				wp_delete_post($id->ID, false);
			}elseif($oldPostStatus==2){
				rssmi_change_post_status($id->ID,'pending');
			}
		
		}

	}
	
}



function rssmi_delete_custom_posts(){  // TIMED DELETE ITEMS USED FOR FEED ITEMS
	
	global $wpdb;
	$custom_post_options_delete = get_option( 'rssmi_global_options' );
	$post_options = get_option('rss_post_options');
	$expiration=$custom_post_options_delete['expiration'];
	$serverTimezone=$post_options['timezone'];


		if (isset($serverTimezone) && $serverTimezone!=''){
			date_default_timezone_set($serverTimezone);
		}
		
	if (isset($expiration) && $expiration!=0){

		$query = "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'rssmi_feed_item' AND DATEDIFF(NOW(), `post_date`) > ".$expiration;
		
	

	$ids = $wpdb->get_results($query);
//	var_dump($ids);
//	exit;
	
	
		if (!empty($ids)){
			foreach ($ids as $id){
					wp_delete_post($id->ID, true);
				}

		}
	}
}




function rssmi_delete_all_custom_posts(){  // DELETE ALL FEED ITEMS
	
	global $wpdb;
			
	
	$query = "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'rssmi_feed_item'";
	

	
	
	$ids = $wpdb->get_results($query);
		if (!empty($ids)){
			foreach ($ids as $id){
					wp_delete_post($id->ID, true);
				}

			}

}





function rssmi_delete_autoposts(){  //  USE FOR QUICK DELETE OF BLOG POSTS ONLY
	
	global $wpdb;

	$query = "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND post_type != 'rssmi_feed'";

	$ids = $wpdb->get_results($query);
	
	foreach ($ids as $id){

	$mypostids = $wpdb->get_results("SELECT * FROM $wpdb->postmeta WHERE  meta_key = 'rssmi_source_link' AND post_id = ".$id->ID);
	
		if (!empty($mypostids)){
			rssmi_delete_attachment($id->ID);
				wp_delete_post($id->ID, true);
		}
	}
	
}



add_action('delete_post', 'rssmi_on_delete');

function rssmi_on_delete($post_id) {
    $post = get_post($post_id);
    if ($post->post_type == 'rssmi_feed') {
        rssmi_delete_all_for_feed($post_id);
		rssmi_delete_all_posts_for_feed($post_id);
       }
}




function rssmi_delete_all_for_feed($id){   //  THIS DELETES FEED ITEMS FOR A SPECIFIC FEED ID

	global $wpdb;

	$myquery = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'rssmi_item_feed_id' AND meta_value = ".$id;	
//	echo $myquery;
	
	$myids = $wpdb->get_results($myquery);
	
	if (!empty($myids)){
		
		foreach ($myids as $id){
			delete_post_meta($id->post_id, "rssmi_item_date");
			delete_post_meta($id->post_id, "rssmi_item_description");
			delete_post_meta($id->post_id, "rssmi_item_feed_id");
			delete_post_meta($id->post_id, "rssmi_item_permalink");
			rssmi_delete_attachment($id->post_id);
			wp_delete_post($id->post_id, true);
		}
			
	}
	
}



function rssmi_delete_all_posts_for_feed($id){  //  THIS DELETES AUTOPOSTS FOR A SPECIFIC FEED ID
	
	global $wpdb;

	$query = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key = 'rssmi_source_feed' and meta_value = ".$id;
	
	$mypostids = $wpdb->get_results($query);
	
		if (!empty($mypostids)){
			foreach ($mypostids as $mypostid){
			rssmi_delete_attachment($mypostid->post_id);
				wp_delete_post($mypostid->post_id, true);
			}
		}
		
}





function rssmi_delete_posts_admin(){  //  USE FOR QUICK DELETE OF ALL AUTOPOSTS
	
	global $wpdb;
	$query = "SELECT * FROM $wpdb->postmeta WHERE meta_key = 'rssmi_source_link'";
	$ids = $wpdb->get_results($query);

		if (!empty($ids)){
				foreach ($ids as $id){
					rssmi_delete_attachment($id->ID);
					wp_delete_post($id->ID, true);
				}
			}
	

}









function rssmi_restore_all(){  //  DELETES EVERYTHING CAUSED BY THIS PLUGIN IN THE POST AND POST META TABLES
	global $wpdb;	
	rssmi_delete_posts_admin();

	$query = "SELECT ID FROM $wpdb->posts WHERE post_status = 'publish' AND (post_type = 'rssmi_feed_item' OR post_type = 'rssmi_feed')";

	$ids = $wpdb->get_results($query);

	if (!empty($ids)){
		foreach ($ids as $id){
			rssmi_delete_attachment($id->ID);
			wp_delete_post($id->ID, true);
			}

		}
	
}



function rssmi_delete_widow_links(){
global $wpdb;	
	
	$query='SELECT post_id from $wpdb->postmeta where (meta_key="rssmi_item_permalink" OR meta_key="rssmi_source_link")';
	
	$ids = $wpdb->get_results($query);
	
		foreach ($ids as $id){
			
			$mypostids = $wpdb->get_results("SELECT * FROM $wpdb->posts WHERE ID = ".$id->ID);
			
				if (empty($mypostids)){
					delete_post_meta($id->ID, "rssmi_item_date");
					delete_post_meta($id->ID, "rssmi_item_description");
					delete_post_meta($id->ID, "rssmi_item_feed_id");
					delete_post_meta($id->ID, "rssmi_item_permalink");
					delete_post_meta($id->ID, "rssmi_source_link");
					delete_post_meta($id->ID, "rssmi_source_feed");	
					
				}
			}
	
}



?>