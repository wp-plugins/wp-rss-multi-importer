<?php

add_action('admin_init','upgrade_db');  // Used starting in version 2.22...afterwards, version is being stored in db

function upgrade_db() {
	


	$myoptions = get_option( 'rss_import_items' ); 
	$newoptions = get_option('rss_import_options');
	$plugin_version=$newoptions['plugin_version'];
	
	if ( !empty($myoptions) && empty($newoptions)) {  // this transfers data to new table if upgrading
	//	$plugin_version=$newoptions['plugin_version'];  // might be useful in future updates
		//	if ($plugin_version<2.22){
					add_option( 'rss_import_options', $myoptions, '', '');
			//	}
	}
		$option_settings = get_option('rss_import_options');
		
		if(!empty($option_settings)){  //only if not a new install
		
	if (!isset($option_settings['template'])|| $option_settings['template']==='') {
		
		foreach ( $option_settings as $key => $value) {
			$template_settings[ $key ] = $value;
		}
		$template_settings['template'] = 'default.php';	
			update_option( 'rss_import_options', $template_settings );
	}

	
	}
	
	$post_options = get_option('rss_post_options');
	if (empty($post_options)){
	
	$post_settings = array(
		'active'=> 0,
		'post_status' => 'draft',
		'maxperfetch' => 5,
		'maxfeed' => 5,
		'category' => 0			
	);
	
		update_option( 'rss_post_options', $post_settings );
	}
	
	//this is for adding multiple categories to the feed to post feature (version 2.47)
	
		$post_options = get_option('rss_post_options');
		
		
	if (!isset($post_options['categoryid']['plugcatid'])|| $post_options['categoryid']['plugcatid']==='') {
	
		
		foreach ( $post_options as $key => $value) {
			$post_settings[ $key ] = $value;
		}
	
		$post_settings['categoryid']['plugcatid'][1]=$post_options['category'];
		$post_settings['categoryid']['wpcatid'][1]=$post_options['wpcategory'];
			update_option( 'rss_post_options', $post_settings );
	}

	
	
	
	
	//for resetting the admin message
	if ($plugin_version<2.40){
	$wprssmi_admin_options = get_option( 'rss_admin_options' );
	$wprssmi_admin_options['dismiss_slug'] ='false';

	}
	

	
	if (empty($option_settings)){

		$option_default_settings = array(
		'sortbydate' => 0,
		'pag' => 0,
		'targetWindow' => 0,
		'maxfeed'=> 4,
		'sourcename' => 'Source',
		'showcategory' => 0,
		'noFollow' => 0,
		'showdesc' => 1,
		'descnum' => 50,
		'floatType' => 1,
		'adjustImageSize' => 1,
		'showsocial' => 0
		);
	update_option( 'rss_import_options', $option_default_settings );

	}
	
	
	
}






?>