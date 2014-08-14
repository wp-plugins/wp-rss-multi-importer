<?php







add_action( 'admin_head', 'rssmi_custom_post_type_icon' );

function rssmi_custom_post_type_icon() {
    ?>
    <style>
        /* Post Screen - 32px */
        .icon32-posts-rssmi_feed {
            background: transparent url( <?php echo WP_RSS_MULTI_IMAGES.'RSSadmin32.png'; ?> ) no-repeat left top !important;
        } 
        /* Post Screen - 32px */
        .icon32-posts-rssmi_feed_item {
            background: transparent url( <?php echo WP_RSS_MULTI_IMAGES.'RSSadmin32.png'; ?> ) no-repeat left top !important;
        }   
    </style>
<?php }

//ON INIT

add_action('admin_init','wp_rss_multi_importer_start');









function wp_rss_multi_importer_start () {
	
register_setting('wp_rss_multi_importer_options', 'rss_import_items');
register_setting('wp_rss_multi_importer_categories', 'rss_import_categories');	
register_setting('wp_rss_multi_importer_item_options', 'rss_import_options');	 
register_setting('wp_rss_multi_importer_template_item', 'rss_template_item');	 
register_setting('wp_rss_multi_importer_feed_options', 'rss_feed_options');	 
register_setting('wp_rss_multi_importer_post_options', 'rss_post_options');	 
register_setting('wp_rss_multi_importer_admin_options', 'rss_admin_options');
register_setting('wp_rss_multi_importer_categories_images', 'rss_import_categories_images');
register_setting('wp_rss_multi_importer_global_options', 'rssmi_global_options','rssmi_sanitize_global');
	 
add_settings_section( 'wp_rss_multi_importer_main', '', 'wp_section_text', 'wprssimport' );  

}

add_action('admin_init', 'rssmi_ilc_farbtastic_script');

function rssmi_ilc_farbtastic_script() {
  wp_enqueue_style( 'farbtastic' );
  wp_enqueue_script( 'farbtastic' );
}



add_action('init', 'wp_rss_multi_importer_post_to_feed');

function wp_rss_multi_importer_post_to_feed(){
  $post_options = get_option('rss_post_options'); 
	if (!empty($post_options)) {
		if ($post_options['targetWindow']==0 && (isset($post_options['active']) && $post_options['active']==1)){
			add_action('wp_footer','colorbox_scripts');
		}
		if ($post_options['noindex']==1){
			add_action('wp_head', 'rssmi_noindex_function');
		}
			if (isset($post_options['addcanonical']) && $post_options['addcanonical']==1){
				remove_action('wp_head', 'rel_canonical');
				add_action('wp_head', 'rssmi_canonical_function');
			}
	}
}



function rssmi_isMobile() {
    return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i", $_SERVER["HTTP_USER_AGENT"]);
}

function isMobileForWordPress() {
	global $isMobileDevice;
    if(rssmi_isMobile()){
       $isMobileDevice=1;
		}else{
 			$isMobileDevice=0;
		}
		return $isMobileDevice;
}

add_action('init', 'isMobileForWordPress', 1);


function startSimplePie(){
	if(! class_exists('SimplePie')){
	     		require_once(ABSPATH . WPINC . '/class-simplepie.php');
	}

	class SimplePie_RSSMI extends SimplePie {}	

	
}
add_action('init', 'startSimplePie');




//  The main admin menu system

add_action('admin_menu','wp_rss_multi_importer_menu');

function wp_rss_multi_importer_menu () {
	$x=1;
	if (rssmi_check_didUpgrade()==1){
			$menuColor="#2ea2cc";
	}else{
			$menuColor=" #F00";
	}

	
	
	add_menu_page(__('Overview'), __('Multi Importer'), 'manage_options', 'wprssmi', 'wp_rss_multi_importer_intro_page', WP_RSS_MULTI_IMAGES."RSSadmin16.png", '150'); 
	
	add_submenu_page('wprssmi', 'Start Here','<span style="color:'.$menuColor.'">'.'Start Here'.'</span>', 'manage_options', 'wprssmi', 'wp_rss_multi_importer_intro_page', '', '');

//	add_submenu_page('wprssmi', 'Start Here','<span style="color:#2ea2cc">'.'Start Here'.'</span>', 'manage_options', 'wprssmi', 'wp_rss_multi_importer_intro_page', '', '');

	add_submenu_page( 'wprssmi', 'Feed List', 'Feed List', 'manage_options', 'edit.php?post_type=rssmi_feed', '' );

	add_submenu_page( 'wprssmi', 'Add a Feed', 'Add a Feed', 'edit_posts', 'post-new.php?post_type=rssmi_feed', '' );
	
	add_submenu_page( 'wprssmi', 'Upload Feeds', 'Upload Feeds', 'manage_options', 'wprssmi_options8', 'wp_rss_multi_importer_upload_page' );
	add_submenu_page( 'wprssmi', 'Categories', 'Categories', 'manage_options', 'wprssmi_options', 'rssmi_category_pages','' );  
	
		add_submenu_page( 'wprssmi', 'Feed Items', 'Feed Items', 'edit_posts', 'edit.php?post_type=rssmi_feed_item', '' );
			add_submenu_page( 'wprssmi', 'Global Settings', 'Global Settings', 'manage_options', 'wprssmi_options5', 'wp_rss_multi_importer_dateformat_page' );
	
	add_submenu_page( 'wprssmi', 'AutoPost', 'AutoPost', 'manage_options', 'wprssmi_options3', 'wp_rss_multi_importer_display_autopost' );  
	
	
	add_submenu_page( 'wprssmi', 'Shortcode', 'Shortcode', 'manage_options', 'wprssmi_options2', 'wp_rss_multi_importer_display_shortcode' ); 
	 

	
	
	
//	add_submenu_page( 'wprssmi', 'Settings', '<span style="color:#2ea2cc">'.'Settings'.'</span>', 'manage_options', 'wprssmi_options2', 'wp_rss_multi_importer_display' ); 
	
//	add_submenu_page( 'wprssmi', 'Shortcode Parameters', 'Shortcode Parameters', 'manage_options', 'wprssmi_options6', 'wp_rss_multi_importer_style_tags' );  
	
//	add_submenu_page( 'wprssmi', 'Shortcode Settings', 'Shortcode Settings', 'manage_options', 'wprssmi_options2', 'wp_rss_multi_importer_options_page' );   
	
//	add_submenu_page( 'wprssmi', 'Manage AutoPosts', 'Manage AutoPosts', 'manage_options', 'wprssmi_options4', 'rssmi_posts_list' ); 
	




	
	add_submenu_page( 'wprssmi', 'Export', 'Export Feeds', 'manage_options', 'wprssmi_options7', 'wp_rss_multi_importer_feed_page' );
	
	

	
	add_submenu_page( 'wprssmi', 'Diagnostics', 'Diagnostics', 'manage_options', 'wprssmi_options9', 'wp_rss_multi_importer_diagnostics' );
	

		
	
}


function rssmi_category_pages(){
	
		wp_rss_multi_importer_category_page();
	wp_rss_multi_importer_category_images_page();
}

function rssmi_posts_list(){
	global $myListTable;
	my_add_menu_items();
	rssmi_add_options();
//	add_options();
	my_render_list_page();
	$myListTable->admin_header();
}


function wp_rss_multi_importer_display_autopost( $active_tab = '' ) {
		
?>
	
	<div class="wrap">
	
		<div id="icon-themes" class="icon32"></div>
		<h2><?php  _e("Multi-Importer", 'wp-rss-multi-importer')?></h2>
		<?php //settings_errors(); ?>
		
		<?php if( isset( $_GET[ 'tab' ] ) ) {
			$active_tab = $_GET[ 'tab' ];
		} else if( $active_tab == 'feed_to_post_options' ) {
					$active_tab = 'feed_to_post_options';
		} else if( $active_tab == 'manage_autoposts' ) {
				$active_tab = 'manage_autoposts';
		} else { $active_tab = 'feed_to_post_options';	
			
		} // end if/else ?>
		
		<!-- AutoPost menu  -->
		
		<h2 class="nav-tab-wrapper">
				<a href="?page=wprssmi_options3&tab=feed_to_post_options" class="nav-tab <?php echo $active_tab == 'feed_to_post_options' ? 'nav-tab-active' : ''; ?>"><?php  _e("AutoPost Settings", 'wp-rss-multi-importer')?></a>
					<a href="?page=wprssmi_options3&tab=manage_autoposts" class="nav-tab <?php echo $active_tab == 'manage_autoposts' ? 'nav-tab-active' : ''; ?>"><?php  _e("Manage AutoPosts", 'wp-rss-multi-importer')?></a>
				
		</h2>
		
		
		

			<?php
			
				if ( $active_tab == 'feed_to_post_options' ) {

				wp_rss_multi_importer_post_page();
				
				} else if ( $active_tab == 'manage_autoposts' ) {

				
				
			
			
			rssmi_posts_list();
		
			
		} else if ( $active_tab == 'feed_to_post_options' ) {
				
			wp_rss_multi_importer_options_page();
		
			} else if ( $active_tab == 'category_options' ) {

				wp_rss_multi_importer_category_page();
			wp_rss_multi_importer_category_images_page();
			
			} else if ( $active_tab == 'more_options' ) {
				
				wp_rss_multi_importer_dateformat_page();	
				
				} else {
			wp_rss_multi_importer_options_page();
					
				
				} // end if/else  	
				
				
			
			?>
	</div>
	
<?php
}



function wp_rss_multi_importer_display_shortcode( $active_tab = '' ) {
		
?>
	
	<div class="wrap">
		
		<div id="icon-themes" class="icon32"></div>
		<h2><?php  _e("Multi-Importer", 'wp-rss-multi-importer')?></h2>
		<?php //settings_errors(); ?>
		
		<?php if( isset( $_GET[ 'tab' ] ) ) {
			$active_tab = $_GET[ 'tab' ];
		} else if( $active_tab == 'setting_options' ) {
					$active_tab = 'setting_options';
		} else if( $active_tab == 'shortcode_parameters' ) {
				$active_tab = 'shortcode_parameters';
			} else if( $active_tab == 'save_template' ) {
					$active_tab = 'save_template';
		} else { $active_tab = 'setting_options';	
			
		} // end if/else ?>
		
		<!-- AutoPost menu  -->
		
		<h2 class="nav-tab-wrapper">
				<a href="?page=wprssmi_options2&tab=setting_options" class="nav-tab <?php echo $active_tab == 'setting_options' ? 'nav-tab-active' : ''; ?>"><?php  _e("Shortcode Settings", 'wp-rss-multi-importer')?></a>
					<a href="?page=wprssmi_options2&tab=shortcode_parameters" class="nav-tab <?php echo $active_tab == 'shortcode_parameters' ? 'nav-tab-active' : ''; ?>"><?php  _e("Shortcode Parameters", 'wp-rss-multi-importer')?></a>
								
		<a href="?page=wprssmi_options2&tab=save_template" class="nav-tab <?php echo $active_tab == 'save_template' ? 'nav-tab-active' : ''; ?>"><?php  _e("Save Template and CSS", 'wp-rss-multi-importer')?></a>
		
		</h2>
		


			<?php
			
				if ( $active_tab == 'setting_options' ) {

				wp_rss_multi_importer_options_page();
				
				} else if ( $active_tab == 'shortcode_parameters' ) {

				wp_rss_multi_importer_style_tags();
				
				} else if ( $active_tab == 'save_template' ) {

				wp_rss_multi_importer_template_page();

				
				} else {
			wp_rss_multi_importer_options_page();
					
				
				} // end if/else  	
				
				
			
			?>
	</div>
	
<?php
}





?>