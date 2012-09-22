<?php
/*  Plugin Name: RSS Multi Importer
  Plugin URI: http://www.allenweiss.com/wp_plugin
  Description: This plugin helps you import multiple RSS feeds, categorize them and have them sorted by date, assign an attribution label, and limit the number of items per feed.
  Version: 2.23
  Author: Allen Weiss
  Author URI: http://www.allenweiss.com/wp_plugin
  License: GPL2  - most WordPress plugins are released under GPL2 license terms
*/
 define( 'WP_RSS_MULTI_PATH', plugin_dir_path( __FILE__ ), true );

 require_once 'inc/rss_multi_importer_widget.php' ;


function wp_rss_multi_importer_start () {
	

register_setting('wp_rss_multi_importer_options', 'rss_import_items');
register_setting('wp_rss_multi_importer_categories', 'rss_import_categories');	
register_setting('wp_rss_multi_importer_item_options', 'rss_import_options');	 
add_settings_section( 'wp_rss_multi_importer_main', '', 'wp_section_text', 'wprssimport' );  



}

add_action('init', 'ilc_farbtastic_script');
function ilc_farbtastic_script() {
  wp_enqueue_style( 'farbtastic' );
  wp_enqueue_script( 'farbtastic' );
}


add_action('admin_init','wp_rss_multi_importer_start');


add_action('admin_init','upgrade_db');  // Used starting in version 2.22...afterwards, version is being stored in db

function upgrade_db() {

	$myoptions = get_option( 'rss_import_items' ); 
	$newoptions = get_option('rss_import_options');
	
	if ( !empty($myoptions) && empty($newoptions)) {  // this transfers data to new table
	//	$plugin_version=$newoptions['plugin_version'];  // might be useful in future updates
		//	if ($plugin_version<2.22){
					add_option( 'rss_import_options', $myoptions, '', '');
			//	}
			}
}


  


add_action('admin_menu','wp_rss_multi_importer_menu');

function wp_rss_multi_importer_menu () {
add_options_page('WP RSS Multi-Importer','RSS Multi-Importer','manage_options','wp_rss_multi_importer_admin', 'wp_rss_multi_importer_display');
}




add_action( 'widgets_init', 'src_load_widgets');  //load widget

function src_load_widgets() {
register_widget('WP_Multi_Importer_Widget');
}



function wp_rss_multi_importer_display( $active_tab = '' ) {
?>
	
	<div class="wrap">
	
		<div id="icon-themes" class="icon32"></div>
		<h2>WP RSS Multi-Importer Options</h2>
		<?php settings_errors(); ?>
		
		<?php if( isset( $_GET[ 'tab' ] ) ) {
			$active_tab = $_GET[ 'tab' ];
		} else if( $active_tab == 'setting_options' ) {
				$active_tab = 'setting_options';
		} else if( $active_tab == 'category_options' ) {
			$active_tab = 'category_options';
		} else if( $active_tab == 'style_options' ) {
			$active_tab = 'style_options';
		} else if( $active_tab == 'more_options' ){
			$active_tab = 'more_options';
		} else { $active_tab = 'items_list';	
			
		} // end if/else ?>
		
		<h2 class="nav-tab-wrapper">
			<a href="?page=wp_rss_multi_importer_admin&tab=items_list" class="nav-tab <?php echo $active_tab == 'items_list' ? 'nav-tab-active' : ''; ?>">RSS Feeds</a>
				<a href="?page=wp_rss_multi_importer_admin&tab=setting_options" class="nav-tab <?php echo $active_tab == 'setting_options' ? 'nav-tab-active' : ''; ?>">Setting Options</a>
			<a href="?page=wp_rss_multi_importer_admin&tab=category_options" class="nav-tab <?php echo $active_tab == 'category_options' ? 'nav-tab-active' : ''; ?>">Category Options</a>
			<a href="?page=wp_rss_multi_importer_admin&tab=style_options" class="nav-tab <?php echo $active_tab == 'style_options' ? 'nav-tab-active' : ''; ?>">Style Options</a>
				<a href="?page=wp_rss_multi_importer_admin&tab=more_options" class="nav-tab <?php echo $active_tab == 'more_options' ? 'nav-tab-active' : ''; ?>">More</a>
		</h2>
	
		<form method="post" action="options.php">
			<?php
			
				if( $active_tab == 'items_list' ) {
						
			wp_rss_multi_importer_items_page();
			
		} else if ( $active_tab == 'setting_options' ) {

				wp_rss_multi_importer_options_page();
			
		} else if ( $active_tab == 'category_options' ) {
			
			wp_rss_multi_importer_category_page();
			
		} else if ( $active_tab == 'style_options' ) {
			
			wp_rss_multi_importer_style_tags();
				
				} else {
						wp_rss_multi_importer_more_page();
				
				} // end if/else  	
				
				//submit_button();
			
			?>
		</form>
		
	</div><!-- /.wrap -->
<?php
} 



function wp_rss_multi_importer_more_page(){
   ?>	
	   <div class="wrap">
	<div id="poststuff">


<?php    echo '<div class="postbox"><h3><label for="title">Help Us Help You</label></h3><div class="inside"><p>Hi All<br>In an attempt to increase the functionality of this plugin, let me know if you have any feature requests by <a href="http://www.allenweiss.com/wp_plugin" target="_blank">going here.</a></p>';
	
	echo '<p>If you\'d like to support the development and maintenance of this plugin, you can do so by <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=M6GC7V8BARAJL" target="_blank">donating here.</a></p>';

echo '<p>If you find this plugin helpful, let others know by <a href="http://wordpress.org/extend/plugins/wp-rss-multi-importer/" target="_blank">rating it here</a>.  That way, it will help others determine whether or not they should try out the plugin.  Thank you.<br>Allen</p></div></div></div></div>';	

}


	function wp_rss_multi_importer_style_tags(){
	   ?>	
		   <div class="wrap">
		<div id="poststuff">


	<?php    echo '<div class="postbox"><h3><label for="title">Shortcode Parameters</label></h3><div class="inside"><h2>You can now customize some of the ways the feeds are presented on your page by using shortcode parameters.  Here are some examples:</h2>';
	?>
	<p>
<ul><li>Headline font size - the parameter is hdsize (set at 16px by default)</li>
	<li>Headline bold weight - the parameter is hdweight (set at 400 by default)</li>
	<li>Style of the Today and Earlier tags - the parameter is testyle (set by default to: color: #000000; font-weight: bold;margin: 0 0 0.8125em;) </li>
	<li>If using excerpt, symbol or word you want to indicate More..- the parameter is morestyle (set by default to [...])</li>
	<li>Change the width of the maximum image size using the parameter maximgwidth (set by default to 150)</li>
	<li>Change the style of the date - the parameter is datestyle (set by default to: font-style:italic; )</li>
	<li>Change how images float on a page - the parameter is floattype (set by default to whatever is set in the admin options)</li>
	<li>Change whether the date shows or not - the parameter is showdate (set to 0 to suppress the date)</li>
	<li>Change whether the attribution shows or not (e.g., news source) - the parameter is showgroup (set to 0 to suppress the source affiliation)</li>
	<li>Specific the number of posts per feed instead of using the general number in the settings panel - the parameter is thisfeed (set to a number, as in thisfeed="4")</li>
	<ul>
		</p>
<p>So, if you'd like to change the headline font size to 18px and make it a heavier bold and change the more in the excerpt to >>, just do this:   [wp_rss_multi_importer hdsize="18px" hdweight="500" morestyle=">>"] </p>
<p>If setting the style of the Today and Earlier tags, you need to enter the entire inline css - so be careful.</p>
		
<?php
	echo '</div></div></div></div>';	

	}






function wp_section_text() {
    echo '<div class="postbox"><h3><label for="title">Usage Details</label></h3><div class="inside"><p>Enter a name and the full URL (with http://) for each of your feeds. The name will be used to identify which feed produced the link (see the Attribution Label option below).</p><p>Put this shortcode, [wp_rss_multi_importer], on the page you wish to have the feed.</p>';
    echo '<p>You can also assign each feed to a category. Go to the Category Options tab, enter as many categories as you like.</p><p>Then you can restrict what shows up on a given page by using this shortcode, like [wp_rss_multi_importer category="2"] (or [wp_rss_multi_importer category="1,2"] to have two categories) on the page you wish to have only show feeds from those categories.</p></div></div>';

}
 


// Only load scripts and CSS if we are on this plugin's options page (admin)

if ( isset( $_GET['page'] ) && $_GET['page'] == 'wp_rss_multi_importer_admin' ) {

    add_action( 'init', 'wprssmi_register_scripts' );

   add_action( 'admin_print_styles', 'wprssmi_header' );


}




/**
    * Load scripts for admin, including check for version since new method (.on) used available in jquery 1.7.1
    */


function wprssmi_register_scripts() {

 global $wp_version;

if ( version_compare($wp_version, "3.3.1", "<" ) ) {  
 	wp_enqueue_script( 'jquery' );
} else {	
	wp_deregister_script( 'jquery' );
    wp_register_script( 'jquery', 'http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js');
    wp_enqueue_script( 'jquery' );	
}
    wp_enqueue_script( 'add-remove', plugins_url( 'scripts/add-remove.js', __FILE__) );

  
}



 
   
  add_action( 'wp_enqueue_scripts', 'wprssmi_frontend_scripts' );
   
   function wprssmi_frontend_scripts() {
		wp_enqueue_script( 'jquery' );  
   }



/**
    * Include Colorbox-related script and CSS in WordPress in footer
    */



function footer_scripts(){
	wp_enqueue_style( 'frontend', plugins_url( 'css/frontend.css', __FILE__) );
	wp_enqueue_script( 'showexcerpt', plugins_url( 'scripts/show-excerpt.js', __FILE__) );  	
}

function colorbox_scripts(){
	wp_enqueue_style( 'colorbox', plugins_url( 'css/colorbox.css', __FILE__) );
    wp_enqueue_script( 'jquery.colorbox-min', plugins_url( 'scripts/jquery.colorbox-min.js', __FILE__) );
	echo "<script type='text/javascript'>jQuery(document).ready(function(){ jQuery('a.colorbox').colorbox({iframe:true, width:'80%', height:'80%'})});</script>";	
}


function widget_footer_scripts(){
	wp_enqueue_style( 'newstickercss', plugins_url( 'css/newsticker.css', __FILE__) );
	wp_enqueue_script( 'newsticker', plugins_url( 'scripts/newsticker.js', __FILE__) );  //  Testing	
	echo "<script type='text/javascript'>jQuery(document).ready(function () {jQuery('#newsticker').vscroller();});</script>";  //Testing
}

 





function delete_db_transients() {

    global $wpdb;

  
    $expired = $wpdb->get_col( "SELECT option_name FROM {$wpdb->options} WHERE option_name LIKE '_transient_wprssmi_%';" );

    foreach( $expired as $transient ) {

        $key = str_replace('_transient_', '', $transient);
        delete_transient($key);

    }
}







/**
 * Include CSS in plugin page header
 */
   


    function wprssmi_header() {        
        wp_enqueue_style( 'styles', plugins_url( 'css/styles.css', __FILE__) );
    }


	
	
	function wprssmi_convert_key( $key ) { 

        if ( strpos( $key, 'feed_name_' ) === 0 ) { 

            $label = str_replace( 'feed_name_', 'Feed Name ', $key );
        }

        else if ( strpos( $key, 'feed_url_' ) === 0 ) { 

            $label = str_replace( 'feed_url_', 'Feed URL ', $key );
        }

		else if ( strpos( $key, 'feed_cat_' ) === 0 ) { 

            $label = str_replace( 'feed_url_', 'Feed Category ', $key );
        }

		else if ( strpos( $key, 'cat_name_' ) === 0 ) { 

            $label = str_replace( 'cat_name_', 'Category ID # ', $key );
        }


        return $label;
    }

    function wprss_get_id_number($key){
	
	if ( strpos( $key, 'feed_name_' ) === 0 ) { 

        $j = str_replace( 'feed_name_', '', $key );
    }
	return $j;
	
    }


   function cat_get_id_number($key){

	if ( strpos( $key, 'cat_name_' ) === 0 ) { 

        $j = str_replace( 'cat_name_', '', $key );
    }
	return $j;

    }



function wp_rss_multi_importer_options_page() {


delete_db_transients();


       ?>

       <div class="wrap">
	<div id="poststuff">
  <h2>RSS Multi-Importer Admin</h2>
       <?php screen_icon(); 

//do_settings_sections( 'wprssimport' );

?>

    

       <div id="options">
	

       <form action="options.php" method="post"  >            

       <?php
		$siteurl= get_site_url();
        $images_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/images';

      settings_fields( 'wp_rss_multi_importer_item_options' );


       $options = get_option( 'rss_import_options' ); 


    	




  

    

       ?>

      
      

<div class="postbox"><h3><label for="title">Options Settings</label></h3>
<div class="inside">



<h3>Sorting and Separating Posts</h3>
 
      <p><label class='o_textinput' for='sortbydate'>Sort Output by Date (Descending = Closest Date First)</label>
	
		<SELECT NAME="rss_import_options[sortbydate]">
		<OPTION VALUE="1" <?php if($options['sortbydate']==1){echo 'selected';} ?>>Ascending</OPTION>
		<OPTION VALUE="0" <?php if($options['sortbydate']==0){echo 'selected';} ?>>Descending</OPTION>
		
		</SELECT></p>  
		
		
		<p><label class='o_textinput' for='todaybefore'>Separate Today and Earlier Posts</label>

		<SELECT NAME="rss_import_options[todaybefore]">
		<OPTION VALUE="1" <?php if($options['todaybefore']==1){echo 'selected';} ?>>Yes</OPTION>
		<OPTION VALUE="0" <?php if($options['todaybefore']==0){echo 'selected';} ?>>No</OPTION>

		</SELECT></p>
	
<h3>Number of Posts and Pagination</h3>
<p><label class='o_textinput' for='maxfeed'>Number of Entries per Feed</label>
<SELECT NAME="rss_import_options[maxfeed]">
<OPTION VALUE="1" <?php if($options['maxfeed']==1){echo 'selected';} ?>>1</OPTION>
<OPTION VALUE="2" <?php if($options['maxfeed']==2){echo 'selected';} ?>>2</OPTION>
<OPTION VALUE="3" <?php if($options['maxfeed']==3){echo 'selected';} ?>>3</OPTION>
<OPTION VALUE="4" <?php if($options['maxfeed']==4){echo 'selected';} ?>>4</OPTION>
<OPTION VALUE="5" <?php if($options['maxfeed']==5){echo 'selected';} ?>>5</OPTION>
<OPTION VALUE="10" <?php if($options['maxfeed']==10){echo 'selected';} ?>>10</OPTION>
<OPTION VALUE="15" <?php if($options['maxfeed']==15){echo 'selected';} ?>>15</OPTION>
<OPTION VALUE="20" <?php if($options['maxfeed']==20){echo 'selected';} ?>>20</OPTION>
</SELECT></p>


<p><label class='o_textinput' for='maxperPage'>Number of Entries per Page of Output</label>
<SELECT NAME="rss_import_options[maxperPage]">
<OPTION VALUE="10" <?php if($options['maxperPage']==10){echo 'selected';} ?>>10</OPTION>
<OPTION VALUE="20" <?php if($options['maxperPage']==20){echo 'selected';} ?>>20</OPTION>
<OPTION VALUE="30" <?php if($options['maxperPage']==30){echo 'selected';} ?>>30</OPTION>
<OPTION VALUE="40" <?php if($options['maxperPage']==40){echo 'selected';} ?>>40</OPTION>
<OPTION VALUE="50" <?php if($options['maxperPage']==50){echo 'selected';} ?>>50</OPTION>
</SELECT></p>




<p><label class='o_textinput' for='pag'>Do you want pagination?</label>
<SELECT NAME="rss_import_options[pag]" id="pagination">
<OPTION VALUE="1" <?php if($options['pag']==1){echo 'selected';} ?>>Yes</OPTION>
<OPTION VALUE="0" <?php if($options['pag']==0){echo 'selected';} ?>>No</OPTION>
</SELECT>  (Note: this will override the Number of Entries per Page of Output)</p>



<span id="pag_options" <?php if($options['pag']==0){echo 'style="display:none"';}?>>
	
	<p style="padding-left:15px"><label class='o_textinput' for='perPage'>Number of Posts per Page for Pagination</label>
	<SELECT NAME="rss_import_options[perPage]">
	<OPTION VALUE="5" <?php if($options['perPage']==5){echo 'selected';} ?>>5</OPTION>
	<OPTION VALUE="10" <?php if($options['perPage']==10){echo 'selected';} ?>>10</OPTION>
	<OPTION VALUE="15" <?php if($options['perPage']==15){echo 'selected';} ?>>15</OPTION>
	<OPTION VALUE="20" <?php if($options['perPage']==20){echo 'selected';} ?>>20</OPTION>
	</SELECT></p>	
	
</span>



<h3>How Links Open and No Follow Option</h3>

<p><label class='o_textinput' for='targetWindow'>Target Window (when link clicked, where should it open?)</label>
	<SELECT NAME="rss_import_options[targetWindow]" id="targetWindow">
	<OPTION VALUE="0" <?php if($options['targetWindow']==0){echo 'selected';} ?>>Use LightBox</OPTION>
	<OPTION VALUE="1" <?php if($options['targetWindow']==1){echo 'selected';} ?>>Open in Same Window</OPTION>
	<OPTION VALUE="2" <?php if($options['targetWindow']==2){echo 'selected';} ?>>Open in New Window</OPTION>
	</SELECT>	
</p>
<p style="padding-left:15px"><label class='o_textinput' for='noFollow'>Set links as No Follow.  <input type="checkbox" Name="rss_import_options[noFollow]" Value="1" <?php if ($options['noFollow']==1){echo 'checked="checked"';} ?></label></p>








<h3>What Shows - Excerpts and Attribution</h3>





<p><label class='o_textinput' for='sourcename'>Attribution Label</label>
<SELECT NAME="rss_import_options[sourcename]">
<OPTION VALUE="Source" <?php if($options['sourcename']=='Source'){echo 'selected';} ?>>Source</OPTION>
<OPTION VALUE="Club" <?php if($options['sourcename']=='Club'){echo 'selected';} ?>>Club</OPTION>
<OPTION VALUE="Sponsor" <?php if($options['sourcename']=='Sponsor'){echo 'selected';} ?>>Sponsor</OPTION>
<OPTION VALUE="" <?php if($options['sourcename']==''){echo 'selected';} ?>>No Attribution</OPTION>
</SELECT></p>



<p><label class='o_textinput' for='showdesc'>Show Excerpt</label>
<SELECT NAME="rss_import_options[showdesc]" id="showdesc">
<OPTION VALUE="1" <?php if($options['showdesc']==1){echo 'selected';} ?>>Yes</OPTION>
<OPTION VALUE="0" <?php if($options['showdesc']==0){echo 'selected';} ?>>No</OPTION>
</SELECT></p>


<span id="secret" <?php if($options['showdesc']==0){echo 'style="display:none"';}?>>
	
	
	<p style="padding-left:15px"><label class='o_textinput' for='showmore'>Let your readers determine if they want to see the excerpt with a show-hide option. <input type="checkbox" Name="rss_import_options[showmore]" Value="1" <?php if ($options['showmore']==1){echo 'checked="checked"';} ?></label>
	</p>	
	
	
<p style="padding-left:15px"><label class='o_textinput' for='descnum'>Excerpt length (number of words)</label>
<SELECT NAME="rss_import_options[descnum]" id="descnum">
<OPTION VALUE="20" <?php if($options['descnum']==20){echo 'selected';} ?>>20</OPTION>
<OPTION VALUE="30" <?php if($options['descnum']==30){echo 'selected';} ?>>30</OPTION>
<OPTION VALUE="50" <?php if($options['descnum']==50){echo 'selected';} ?>>50</OPTION>
<OPTION VALUE="100" <?php if($options['descnum']==100){echo 'selected';} ?>>100</OPTION>
<OPTION VALUE="200" <?php if($options['descnum']==200){echo 'selected';} ?>>200</OPTION>
<OPTION VALUE="300" <?php if($options['descnum']==300){echo 'selected';} ?>>300</OPTION>
<OPTION VALUE="99" <?php if($options['descnum']==99){echo 'selected';} ?>>Give me everything</OPTION>
</SELECT></p>
<h4>Image Handling</h4>
<p><label class='o_textinput' for='stripAll'>Check to get rid of all images in the excerpt.  <input type="checkbox" Name="rss_import_options[stripAll]" Value="1" <?php if ($options['stripAll']==1){echo 'checked="checked"';} ?></label>
</p>
<p>You can adjust the leading image, if it exists.  Note that including images in your feed may slow down how quickly it renders on your site, so you'll need to experiment with these settings.</p>
<p style="padding-left:15px"><label class='o_textinput' for='adjustImageSize'>If you want excerpt images, check to fix their width at 150 (can be over-written in shortcode).  <input type="checkbox" Name="rss_import_options[adjustImageSize]" Value="1" <?php if ($options['adjustImageSize']==1){echo 'checked="checked"';} ?></label></p>
	
<p style="padding-left:15px"><label class='o_textinput' for='floatType'>Float images to the left (can be over-written in shortcode).  <input type="checkbox" Name="rss_import_options[floatType]" Value="1" <?php if ($options['floatType']==1){echo 'checked="checked"';} ?></label></p>
</span>

<h3>Cache and Conflict Handling</h3>

<p><label class='o_textinput' for='cacheMin'>Number of minutes you want the post data held in cache (match to how often your feeds are updated)</label>
<SELECT NAME="rss_import_options[cacheMin]" id="cacheMin">
<OPTION VALUE="0" <?php if($options['cacheMin']==0){echo 'selected';} ?>>Turn off caching</OPTION>
<OPTION VALUE="1" <?php if($options['cacheMin']==1){echo 'selected';} ?>>1</OPTION>
<OPTION VALUE="5" <?php if($options['cacheMin']==5){echo 'selected';} ?>>5</OPTION>
<OPTION VALUE="10" <?php if($options['cacheMin']==10){echo 'selected';} ?>>10</OPTION>
<OPTION VALUE="20" <?php if($options['cacheMin']==20){echo 'selected';} ?>>20</OPTION>
<OPTION VALUE="30" <?php if($options['cacheMin']==30){echo 'selected';} ?>>30</OPTION>
<OPTION VALUE="40" <?php if($options['cacheMin']==40){echo 'selected';} ?>>40</OPTION>
<OPTION VALUE="60" <?php if($options['cacheMin']==60){echo 'selected';} ?>>60</OPTION>
<OPTION VALUE="120" <?php if($options['cacheMin']==120){echo 'selected';} ?>>120</OPTION>
<OPTION VALUE="180" <?php if($options['cacheMin']==180){echo 'selected';} ?>>180</OPTION>
<OPTION VALUE="240" <?php if($options['cacheMin']==240){echo 'selected';} ?>>240</OPTION>
<OPTION VALUE="300" <?php if($options['cacheMin']==300){echo 'selected';} ?>>300</OPTION>
</SELECT></p>




<p ><label class='o_textinput' for='cb'>Check if you are having colorbox conflict problems.   <input type="checkbox" Name="rss_import_options[cb]" Value="1" <?php if ($options['cb']==1){echo 'checked="checked"';} ?></label></p>
<input   size='10' name='rss_import_options[plugin_version]' type='hidden' value='2.23' />

</div></div>

       <p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>



       </form>

      <div class="postbox"><h3><label for="title">   Help Others</label></h3><div class="inside">If you find this plugin helpful, let others know by <a href="http://wordpress.org/extend/plugins/wp-rss-multi-importer/" target="_blank">rating it here</a>.  That way, it will help others determine whether or not they should try out the plugin.  Thank you.</div></div> 

       </div>
</div>
       </div>

       <?php 

  }




function wp_rss_multi_importer_items_page() {


	delete_db_transients();

       ?>

       <div class="wrap">
	<div id="poststuff">
  <h2>RSS Multi-Importer Admin</h2>
       <?php screen_icon(); 

do_settings_sections( 'wprssimport' );

?>



       <div id="options">


       <form action="options.php" method="post"  >            

       <?php
		$siteurl= get_site_url();
        $images_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/images';

      settings_fields( 'wp_rss_multi_importer_options' );


       $options = get_option( 'rss_import_items' ); 



//this included for backward compatibility
  if ( !empty($options) ) {
$cat_array = preg_grep("^feed_cat_^", array_keys($options));

	if (count($cat_array)==0) {
	   //echo "category was not found\n";
		$catExists=0;
		$modnumber=2;
	}else{
		$catExists=1;
		$modnumber=3;	
	}
}


       if ( !empty($options) ) {

           $size = count($options);  

           for ( $i=1; $i<=$size; $i++ ) {            

               if( $i % $modnumber == 0 ) continue;


               $key = key( $options );


            if ( !strpos( $key, '_' ) > 0 ) continue; //this makes sure only feeds are included here...everything else are options

				$j = wprss_get_id_number($key);


             echo "<div class='wprss-input' id='$j'>";

               echo "<p><label class='textinput' for='$key'>" . wprssmi_convert_key( $key ) . "</label>

               <input  class='wprss-input' size='75' name='rss_import_items[$key]' type='text' value='$options[$key]' />  <a href='#' class='btnDelete' id='$j'><img src='$images_url/remove.png'/></a></p>";


               next( $options );


               $key = key( $options );


               echo "<p><label class='textinput' for='$key'>" . wprssmi_convert_key( $key ) . "</label>

               <input id='$j' class='wprss-input' size='75' name='rss_import_items[$key]' type='text' value='$options[$key]' />" ; 


			if (empty($catOptions)){
				echo " <input id='$j' class='wprss-input' size='10' name='rss_import_items[feed_cat_$j]' type='hidden' value='0' />" ; 	

			}



	if ($catExists==1){
		    next( $options );
            $key = key( $options );	
			$selectName="rss_import_items[feed_cat_$j]";
	}else{
		$selectName="rss_import_items[feed_cat_$j]";		
	}


$catOptions= get_option( 'rss_import_categories' ); 

	if ( !empty($catOptions) ) {
		echo "Category ";
echo "<SELECT NAME=".$selectName." id='feed_cat'>";
echo "<OPTION VALUE='0'>NONE</OPTION>";
	$catsize = count($catOptions);

echo $options[$key];

	for ( $i=1; $i<=$catsize; $i++ ) {   

if( $i % 2== 0 ) continue;

 	$catkey = key( $catOptions );
 	$nameValue=$catOptions[$catkey];
next( $catOptions );
 	$catkey = key( $catOptions );
	$IDValue=$catOptions[$catkey];


	 if($options[$key]==$IDValue){
		$sel='selected  ';

		} else {
		$sel='';

		}

echo "<OPTION " .$sel.  "VALUE=".$IDValue.">".$nameValue."</OPTION>";
next( $catOptions );

}
echo "</SELECT>";
}


              echo " </p>";



               next( $options );

               echo "</div>"; 



           }

       }







       ?>

       <div id="buttons"><a href="#" id="add" class="addbutton"><img src="<?php echo $images_url; ?>/add.png"></a>  



       <p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>



       </form>

      <div class="postbox"><h3><label for="title">   Help Others</label></h3><div class="inside">If you find this plugin helpful, let others know by <a href="http://wordpress.org/extend/plugins/wp-rss-multi-importer/" target="_blank">rating it here</a>.  That way, it will help others determine whether or not they should try out the plugin.  Thank you.</div></div> 

       </div>
</div>
       </div>

       <?php 

  }


















//  Categories Page

function wp_rss_multi_importer_category_page() {
	
		$siteurl= get_site_url();
        $images_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/images';

       ?>
      <div class="wrap">
	<div id="poststuff">
  
  <h2>RSS Multi-Importer Categories (and their shortcodes)</h2>

     <form action="options.php" method="post"  >  
	
	<?php
	
	settings_fields( 'wp_rss_multi_importer_categories' );

	$options = get_option('rss_import_categories' ); 
	
	
	if ( !empty($options) ) {
		$size = count($options);


		for ( $i=1; $i<=$size; $i++ ) {   
			   
if( $i % 2== 0 ) continue;

  
					
				   $key = key( $options );

	$j = cat_get_id_number($key);
		$textUpper=strtoupper($options[$key]);
 			echo "<div class='cat-input' id='$j'>";
	echo "<p><label class='textinput' for='Category ID'>" . wprssmi_convert_key( $key ) . "</label>
	


       <input id='5'  size='20' name='rss_import_categories[$key]' type='text' value='$textUpper' />  [wp_rss_multi_importer category=\"".$j."\"]";
next( $options );
   $key = key( $options );

     echo"  <input id='5'  size='20' name='rss_import_categories[$key]' type='hidden' value='$options[$key]' />" ; 
	echo "</div>";
	next( $options );	
}

		 

}
	?>
  <div id="category"><a href="#" id="addCat" class="addCategory"><img src="<?php echo $images_url; ?>/addCat.png"></a>  	
<p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>
	          
</form>
</div></div>

<?php

}






   
   /**
   *  Shortcode setup and call (shortcode is [wp_rss_multi_importer]) with options
   */
   
   add_shortcode('wp_rss_multi_importer','wp_rss_multi_importer_shortcode');
 




	function showexcerpt($content, $maxchars,$openWindow,$stripAll,$thisLink,$adjustImageSize,$float,$noFollow)  //show excerpt function
	{
		global $morestyle;
    $content=CleanHTML($content);

	if ($stripAll==1){
			$content=strip_tags(html_entity_decode($content));	
			$content= limitwords($maxchars,$content);	
	}else{
		$content=strip_tags(html_entity_decode($content),'<a><img>');
		$content=findalignImage($maxchars,$content,$adjustImageSize,$float);	
}
	
	//return str_replace($morestyle, "<a href=".$thisLink." ".$openWindow.">".$morestyle."</a>", $content);
	
		return str_replace($morestyle, "<a href=".$thisLink." ".$openWindow.'' 	.($noFollow==1 ? 'rel=nofollow':'').">".$morestyle."</a>", $content);

	}
	


	
	function limitwords($maxchars,$content){
	
		global $morestyle;
		if($maxchars !=99){


		  $words = explode(' ', $content, ($maxchars + 1));
	  			if(count($words) > $maxchars)
		  				array_pop($words);
	 				
						$content = implode(' ', $words)." ". $morestyle;
						
	
		}else{
						$content=$content."";
		}
		return $content;
	}
	
	
	
	
	
	function CleanHTML($content){
		
		$content=str_replace("&nbsp;&raquo;", "", $content);
		$content=str_replace("&nbsp;", " ", $content);	
		
	return 	$content;
	}
	
	
	

	
	function findalignImage($maxchars,$content,$adjustImageSize,$float){
	

		$strmatch='^\s*\<a.*href="(.*)">\s*(<img.*src=".*" \/?>)[^\<]*<\/a\>\s*(.*)$'; ///match leading hyperlinked image
		
		$strmatch2='^(\s*)(<img.*src=".*"\s*?\/>)\s*(.*)$';  //match leading non-hyperlinked image
		
			if (preg_match("/$strmatch/sU", $content, $matches) || preg_match("/$strmatch2/sU", $content, $matches)){


			if ($adjustImageSize==1){
				$tabledImage= "<div class=\"imagefix\" style=\"float:".$float.";\">".resize_image($matches[2])."</div>";
			}else{
				$tabledImage= "<div class=\"imagefix\" style=\"float:".$float.";\">".$matches[2]."</div>";
			}	
			
		
				$content=str_replace($matches[2], $tabledImage, $content); //format the leading image if it exists
				
				$content=str_replace($matches[3], limitwords($maxchars,strip_tags($matches[3])), $content); //strip away all tags after the leading image
				

		}else{
		
			
			$content = limitwords($maxchars,strip_tags($content));
		}
	return $content;	
	}
	
	
	function remove_img_hw( $imghtml ) {
	 $imghtml = preg_replace( '/(width|height)=\"\d*\"\s?/', "", $imghtml );
	    return $imghtml;
	}
	
	function resize_image($imghtml){
		global $maximgwidth;
		preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $imghtml, $matches);
		$thisWidth=getimagesize($matches[1]);
		if ($thisWidth > $maxImgWidth){
		return str_replace("<img", "<img width=".$maximgwidth, remove_img_hw($imghtml));
			}else{
		return str_replace("<img", "<img width=".$thisWidth, remove_img_hw($imghtml));		
	}
}







   
   function wp_rss_multi_importer_shortcode($atts=array()){
	
add_action('wp_footer','footer_scripts');









	
	$siteurl= get_site_url();
    $cat_options_url = $siteurl . '/wp-admin/options-general.php?page=wp_rss_multi_importer_admin&tab=category_options/';
	$images_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/images';	
	
	$parms = shortcode_atts(array(  //Get shortcode parameters
		'category' => 0, 
		'hdsize' => '16px', 
		'hdweight'=>400, 
		'anchorcolor' =>'',
		'testyle'=>'color: #000000; font-weight: bold;margin: 0 0 0.8125em;',
		'maximgwidth'=> 150,
		'datestyle'=>'font-style:italic;',
		'floattype'=>'',
		'showdate' => 1,
		'showgroup'=> 1,
		'thisfeed'=>'',
		'timer' => 0,  
		'morestyle' =>'[...]'
		), $atts);
	
	$anchorcolor=$parms['anchorcolor'];
	$datestyle=$parms['datestyle'];
	$hdsize = $parms['hdsize'];
    $thisCat = $parms['category'];
	$parmfloat=$parms['floattype'];
	$catArray=explode(",",$thisCat);
	$showdate=$parms['showdate'];
	$showgroup=$parms['showgroup'];
	$hdweight = $parms['hdweight'];
	$testyle = $parms['testyle'];
	global $morestyle;
    $morestyle = $parms['morestyle'];
	global $maximgwidth;
	$maximgwidth = $parms['maximgwidth'];
	$thisfeed = $parms['thisfeed'];  // max posts per category
	$timerstop = $parms['timer'];
	
	$cachename='wprssmi_'.$thisCat;
	
	
   	$readable = '';
   	$options = get_option('rss_import_options','option not found');
	$option_items = get_option('rss_import_items','option not found');



$cat_array = preg_grep("^feed_cat_^", array_keys($option_items));

	if (count($cat_array)==0) {  // for backward compatibility
		$noExistCat=1;
	}else{
		$noExistCat=0;	
	}



    
   if(!empty($option_items)){
	
//GET PARAMETERS  
$size = count($option_items);
$sortDir=$options['sortbydate'];  // 1 is ascending
$stripAll=$options['stripAll'];
$todaybefore=$options['todaybefore'];
$adjustImageSize=$options['adjustImageSize'];
$showDesc=$options['showdesc'];  // 1 is show
$descNum=$options['descnum'];
$maxperPage=$options['maxperPage'];


$cacheMin=$options['cacheMin'];
$maxposts=$options['maxfeed'];

if ($thisfeed!='') $maxposts=$thisfeed;


$targetWindow=$options['targetWindow'];  // 0=LB, 1=same, 2=new
$floatType=$options['floatType'];
$noFollow=$options['noFollow'];
$showmore=$options['showmore'];
$cb=$options['cb'];  // 1 if colorbox should not be loaded
$pag=$options['pag'];  // 1 if pagination
$perPage=$options['perPage'];
if(empty($options['sourcename'])){
	$attribution='';
}else{
	$attribution=$options['sourcename'].': ';
}

if ($floatType=='1'){
	$float="left";
}else{
	$float="none";	
}

if ($parmfloat!='') $float=$parmfloat;


if ($cacheMin==''){
$cacheMin=0;  //set caching minutes	
}



if ($cb!=='1'){
add_action('wp_footer','colorbox_scripts');  // load colorbox only if not indicated as conflict
   }




timer_start();  //TIMER START - for testing purposes


	$myarray=get_transient($cachename);  // added  for transient cache
	
	if ($cacheMin==0){
		delete_transient($cachename); 
	}
	
   if (false===$myarray) {   //  added  for transient cache - only get feeds and put into array if the array isn't cached (for a given category set)



   for ($i=1;$i<=$size;$i=$i+1){

	

   			$key =key($option_items);
				if ( !strpos( $key, '_' ) > 0 ) continue; //this makes sure only feeds are included here...everything else are options
				
   			$rssName= $option_items[$key];

   
   			next($option_items);
   			
   			$key =key($option_items);
   			
   			$rssURL=$option_items[$key];



  	next($option_items);
	$key =key($option_items);
	




if (((!in_array(0, $catArray ) && in_array($option_items[$key], $catArray ))) || in_array(0, $catArray ) || $noExistCat==1) {



   $myfeeds[] = array("FeedName"=>$rssName,"FeedURL"=>$rssURL);   
	
}
   
$cat_array = preg_grep("^feed_cat_^", array_keys($option_items));  // for backward compatibility

	if (count($cat_array)>0) {

  next($option_items); //skip feed category
}

   }
 //var_dump($myfeeds);

if (empty($myfeeds)){
	
	echo "You've either entered a category ID that doesn't exist or have no feeds configured for this category.  Edit the shortcode on this page with a category ID that exists, or <a href=".$cat_options_url.">go here and and get an ID</a> that does exist in your admin panel.";
	exit;
}



 
 foreach($myfeeds as $feeditem){


	$url=(string)($feeditem["FeedURL"]);
	

				$feed = fetch_feed($url);

	
	

	if (is_wp_error( $feed ) ) {

		continue;
	
	}

	$maxfeed= $feed->get_item_quantity(0);  


//SORT DEPENDING ON SETTINGS

	if($sortDir==1){

		for ($i=$maxfeed-1;$i>=$maxfeed-$maxposts;$i--){
			$item = $feed->get_item($i);
			 if (empty($item))	continue;
		
				$myarray[] = array("mystrdate"=>strtotime($item->get_date()),"mytitle"=>$item->get_title(),"mylink"=>$item->get_link(),"myGroup"=>$feeditem["FeedName"],"mydesc"=>$item->get_description());
			}

		}else{	

		for ($i=0;$i<=$maxposts-1;$i++){
				$item = $feed->get_item($i);
				if (empty($item))	continue;	
				
					
					$myarray[] = array("mystrdate"=>strtotime($item->get_date()),"mytitle"=>$item->get_title(),"mylink"=>$item->get_link(),"myGroup"=>$feeditem["FeedName"],"mydesc"=>$item->get_description());
				}	
		}


	}





if ($cacheMin!==0){
set_transient($cachename, $myarray, 60*$cacheMin);  //  added  for transient cache
}

}  //  added  for transient cache

if ($timerstop==1){
 timer_stop(1); echo ' seconds<br>';  //TIMER END for testing purposes
}



//$myarrary sorted by mystrdate

foreach ($myarray as $key => $row) {
    $dates[$key]  = $row["mystrdate"]; 
}



//SORT, DEPENDING ON SETTINGS

if($sortDir==1){
	array_multisort($dates, SORT_ASC, $myarray);
}else{
	array_multisort($dates, SORT_DESC, $myarray);		
}

// HOW THE LINK OPENS

if($targetWindow==0){
	$openWindow='class="colorbox"';
}elseif ($targetWindow==1){
	$openWindow='target=_self';		
}else{
	$openWindow='target=_blank';	
}
	
$total = -1;
$todayStamp=0;
$idnum=0;

//for pagination
$currentPage = trim($_REQUEST[pg]);
$currentURL = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; 
$currentURL = str_replace( '&pg='.$currentPage, '', $currentURL );
$currentURL = str_replace( '?pg='.$currentPage, '', $currentURL );

if ( strpos( $currentURL, '?' ) == 0 ){
	$currentURL=$currentURL.'?';
}else{
	$currentURL=$currentURL.'&';	
}



//pagination controls and parameters


if (!isset($perPage)){$perPage=5;}

$numPages = ceil(count($myarray) / $perPage);
if(!$currentPage || $currentPage > $numPages)  
    $currentPage = 0;
$start = $currentPage * $perPage;  
$end = ($currentPage * $perPage) + $perPage;

	
		if ($pag==1){   //set up pagination array and put into myarray
	foreach($myarray AS $key => $val)  
		{  
	    if($key >= $start && $key < $end)  
	        $pagedData[] = $myarray[$key];  
		}
		
			$myarray=$pagedData;
	}
      //end set up pagination array and put into myarray



foreach($myarray as $items) {

if ($pag!==1){ 	
	$total = $total +1;
	if ($maxperPage>0 && $total>=$maxperPage) break;
}

$idnum=$idnum +1;


//beginning of default template


//  Today and Earlier Script


if ($sortDir==0 && $todaybefore==1){
	
	$from=date("d-m-Y",strtotime('now'));
	$to=date("d-m-Y",$items["mystrdate"]);
	$nodays=(strtotime($to) - strtotime($from))/ (60 * 60 * 24); 


if ($nodays==0){
	

	if ($todayStamp==0){
		$readable.='<span style="'.$testyle.'">Today</span>';
		$todayStamp=1;
		} 
	}

  elseif ($nodays!=0) {
	

		if ($todayStamp==1 || $total==0){

		$readable.= '<span style="'.$testyle.'">Earlier</span>';
			
		$todayStamp=2;
		}
	}
	
}





	
		$readable .=  '<div class="rss-output"><div><span style="font-size:'.$hdsize.'; font-weight:'.$hdweight.';"><a '.$openWindow.' href='.$items["mylink"].' '.($noFollow==1 ? 'rel=nofollow':'').' style="color:'.$anchorcolor.'">'.$items["mytitle"].'</a></span>';
		
		if ($showmore==1 && $showDesc==1){
			
			$readable .=  ' <a href="javascript:void(0)"><img src="'.$images_url.'/arrow_down.png"/  id="#'.$idnum.'" class="nav-toggle"></a></div>';	
			
		} else{
			
			$readable .=  '</div>';	
		}
			
	if (!empty($items["mydesc"]) && $showDesc==1){
		
		
		
		if ($showmore==1 && $showDesc==1){
			$readable .=  '<div id="'.$idnum.'" style="display:none">';
		}else{
			$readable .=  '<div>';		
		}
		
		
	$readable .=  showexcerpt($items["mydesc"],$descNum,$openWindow,$stripAll,$items["mylink"],$adjustImageSize,$float,$noFollow);
	
	$readable .=  '</div>';	

	
}


	
	if (!empty($items["mystrdate"]) && $showdate==1){
	// $readable .=  '<span style="'.$datestyle.'">'. date_i18n("D, M d, Y g:i:s A",$items["mystrdate"]).'</span><br />';  // use if you want time to show
	$readable .=  '<span style="'.$datestyle.'">'. date_i18n("D, M d, Y",$items["mystrdate"]).'</span><br />';
	}
		if (!empty($items["myGroup"]) && $showgroup==1){
     $readable .=  '<span style="font-style:italic;">'.$attribution.''.$items["myGroup"].'</span>';
	}
	 $readable .=  '</div>';
	
	
	
	
	//  This is the end of the default template
	
	
	
	
	
	
	
	}
    


}

	//pagination controls at bottom
	
if ($pag==1){  
$readable .='<div class="pag_box">';

if($numPages > $currentPage && ($currentPage + 1) < $numPages)  
    $readable .=  '<a href="http://'.$currentURL.'pg=' . ($currentPage + 1) . '" class="more-prev">Next page »</a>';

	if($currentPage > 0 && $currentPage < $numPages)  
	    $readable .= '<a href="http://'.$currentURL.'pg=' . ($currentPage - 1) . '" class="more-prev">« Previous page</a>';  

$readable .='</div>';

}
     //end pagination controls at bottom
	

return $readable;

   }
   

    
   
?>