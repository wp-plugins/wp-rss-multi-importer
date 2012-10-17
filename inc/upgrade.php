<?php

add_action('admin_init','upgrade_db');  // Used starting in version 2.22...afterwards, version is being stored in db

function upgrade_db() {

	$myoptions = get_option( 'rss_import_items' ); 
	$newoptions = get_option('rss_import_options');
	
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
}

?>