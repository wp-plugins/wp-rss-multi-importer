<?php
/*  Plugin Name: RSS Multi Importer
  Plugin URI: http://www.allenweiss.com/wp_plugin
  Description: This plugin helps you import multiple RSS feeds, categorize them and have them sorted by date, assign an attribution label, and limit the number of items per feed.
  Version: 2.11
	Author: Allen Weiss
	Author URI: http://www.allenweiss.com/wp_plugin
	License: GPL2  - most WordPress plugins are released under GPL2 license terms
*/
 define( 'WP_RSS_MULTI_PATH', plugin_dir_path( __FILE__ ), true );

 require_once 'inc/rss_multi_importer_widget.php' ;

function wp_rss_multi_importer_start () {
	

register_setting('wp_rss_multi_importer_options', 'rss_import_items');
register_setting('wp_rss_multi_importer_categories', 'rss_import_categories');	
add_settings_section( 'wp_rss_multi_importer_main', '', 'wp_section_text', 'wprssimport' );  



}

add_action('admin_init','wp_rss_multi_importer_start');
  


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
		<h2>RSS Feed Options</h2>
		<?php settings_errors(); ?>
		
		<?php if( isset( $_GET[ 'tab' ] ) ) {
			$active_tab = $_GET[ 'tab' ];
		} else if( $active_tab == 'category_options' ) {
			$active_tab = 'category_options';
		} else if( $active_tab == 'style_options' ) {
			$active_tab = 'style_options';
		} else if( $active_tab == 'more_options' ){
			$active_tab = 'more_options';
		} else { $active_tab = 'main_options';	
			
		} // end if/else ?>
		
		<h2 class="nav-tab-wrapper">
			<a href="?page=wp_rss_multi_importer_admin&tab=main_options" class="nav-tab <?php echo $active_tab == 'main_options' ? 'nav-tab-active' : ''; ?>">RSS Feeds & Options</a>
			<a href="?page=wp_rss_multi_importer_admin&tab=category_options" class="nav-tab <?php echo $active_tab == 'category_options' ? 'nav-tab-active' : ''; ?>">Category Options</a>
			<a href="?page=wp_rss_multi_importer_admin&tab=style_options" class="nav-tab <?php echo $active_tab == 'style_options' ? 'nav-tab-active' : ''; ?>">Style Options</a>
				<a href="?page=wp_rss_multi_importer_admin&tab=more_options" class="nav-tab <?php echo $active_tab == 'more_options' ? 'nav-tab-active' : ''; ?>">More</a>
		</h2>
	
		<form method="post" action="options.php">
			<?php
			
				if( $active_tab == 'main_options' ) {
						
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











function wp_section_text() {
    echo '<div class="postbox"><h3><label for="title">Usage Details</label></h3><div class="inside"><p>Enter a name and the full URL (with http://) for each of your feeds. The name will be used to identify which feed produced the link (see the Attribution Label option below).</p><p>Put this shortcode, [wp_rss_multi_importer], on the page you wish to have the feed.</p>';
    echo '<p>You can also assign each feed to a category. Go to the Category Options tab, enter as many categories as you like.</p><p>Then you can restrict what shows up on a given page by using this shortcode,  like [wp_rss_multi_importer category="2"], on the page you wish to have only show feeds from that category.</p></div></div>';

}
 


// Only load scripts and CSS if we are on this plugin's options page (admin)

if ( isset( $_GET['page'] ) && $_GET['page'] == 'wp_rss_multi_importer_admin' ) {

    add_action( 'admin_print_scripts', 'wprssmi_register_scripts' );

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

	  wp_enqueue_style( 'colorbox', plugins_url( 'css/colorbox.css', __FILE__) );
      wp_enqueue_script( 'jquery.colorbox-min', plugins_url( 'scripts/jquery.colorbox-min.js', __FILE__) );  
	  wp_enqueue_style( 'frontend', plugins_url( 'css/frontend.css', __FILE__) );

echo "<script type='text/javascript'>jQuery(document).ready(function(){ jQuery('a.colorbox').colorbox({iframe:true, width:'80%', height:'80%'})});</script>";
	
}

 


/**
 * Include CSS in plugin page header
 */
   


    function wprssmi_header() {        
        wp_enqueue_style( 'styles', plugins_url( 'css/styles.css', __FILE__) );
    }




	function wp_rss_multi_importer_more_page(){
	   ?>	
		   <div class="wrap">
		<div id="poststuff">


	<?php    echo '<div class="postbox"><h3><label for="title">Help Us Help You</label></h3><div class="inside"><p>Hi All<br>In an attempt to increase the functionality of this plugin, let me know if you have any feature requests by <a href="http://www.allenweiss.com/wp_plugin" target="_blank">going here.</a></p>';

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
	<li>Style of the Today and Earlier tags - the parameter is testyle (set by default to: color: #000000; font-weight: bold;margin: 0 0 0.8125em) </li>
	<li>If using excerpt, symbol or word you want to indicate More..- the parameter is morestyle (set by default to [...])</li>
	<ul>
		</p>
<p>So, if you'd like to change the headline font size to 18px and make it a heavier bold and change the more in the excerpt to >>, just do this:   [wp_rss_multi_importer hdsize="18px" hdweight="500" morestyle=">>"] </p>
<p>If setting the style of the Today and Earlier tags, you need to enter the entire inline css - so be careful.</p>
		
<?php
	echo '</div></div></div></div>';	

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
      

<div class="postbox"><h3><label for="title">Options Settings</label></h3><div class="inside">




 
      <p><label class='o_textinput' for='sortbydate'>Sort Output by Date (Descending = Closest Date First)</label>
	
		<SELECT NAME="rss_import_items[sortbydate]">
		<OPTION VALUE="1" <?php if($options['sortbydate']==1){echo 'selected';} ?>>Ascending</OPTION>
		<OPTION VALUE="0" <?php if($options['sortbydate']==0){echo 'selected';} ?>>Descending</OPTION>
		
		</SELECT></p>  
		
		
		<p><label class='o_textinput' for='todaybefore'>Separate Today and Earlier Posts</label>

		<SELECT NAME="rss_import_items[todaybefore]">
		<OPTION VALUE="1" <?php if($options['todaybefore']==1){echo 'selected';} ?>>Yes</OPTION>
		<OPTION VALUE="0" <?php if($options['todaybefore']==0){echo 'selected';} ?>>No</OPTION>

		</SELECT></p>
	

<p><label class='o_textinput' for='maxfeed'>Number of Entries per Feed</label>
<SELECT NAME="rss_import_items[maxfeed]">
<OPTION VALUE="1" <?php if($options['maxfeed']==1){echo 'selected';} ?>>1</OPTION>
<OPTION VALUE="2" <?php if($options['maxfeed']==2){echo 'selected';} ?>>2</OPTION>
<OPTION VALUE="5" <?php if($options['maxfeed']==5){echo 'selected';} ?>>5</OPTION>
<OPTION VALUE="10" <?php if($options['maxfeed']==10){echo 'selected';} ?>>10</OPTION>
<OPTION VALUE="15" <?php if($options['maxfeed']==15){echo 'selected';} ?>>15</OPTION>
<OPTION VALUE="20" <?php if($options['maxfeed']==20){echo 'selected';} ?>>20</OPTION>
</SELECT></p>


<p><label class='o_textinput' for='maxperPage'>Number of Entries per Page of Output</label>
<SELECT NAME="rss_import_items[maxperPage]">
<OPTION VALUE="10" <?php if($options['maxperPage']==10){echo 'selected';} ?>>10</OPTION>
<OPTION VALUE="20" <?php if($options['maxperPage']==20){echo 'selected';} ?>>20</OPTION>
<OPTION VALUE="30" <?php if($options['maxperPage']==30){echo 'selected';} ?>>30</OPTION>
<OPTION VALUE="40" <?php if($options['maxperPage']==40){echo 'selected';} ?>>40</OPTION>
<OPTION VALUE="50" <?php if($options['maxperPage']==50){echo 'selected';} ?>>50</OPTION>
</SELECT></p>


<p><label class='o_textinput' for='targetWindow'>Target Window (when link clicked, where should it open?)</label>
	<SELECT NAME="rss_import_items[targetWindow]" id="targetWindow">
	<OPTION VALUE="0" <?php if($options['targetWindow']==0){echo 'selected';} ?>>Use LightBox</OPTION>
	<OPTION VALUE="1" <?php if($options['targetWindow']==1){echo 'selected';} ?>>Open in Same Window</OPTION>
	<OPTION VALUE="2" <?php if($options['targetWindow']==2){echo 'selected';} ?>>Open in New Window</OPTION>
	</SELECT>	
</p>

<p><label class='o_textinput' for='sourcename'>Attribution Label</label>
<SELECT NAME="rss_import_items[sourcename]">
<OPTION VALUE="Source" <?php if($options['sourcename']=='Source'){echo 'selected';} ?>>Source</OPTION>
<OPTION VALUE="Club" <?php if($options['sourcename']=='Club'){echo 'selected';} ?>>Club</OPTION>
<OPTION VALUE="Sponsor" <?php if($options['sourcename']=='Sponsor'){echo 'selected';} ?>>Sponsor</OPTION>
<OPTION VALUE="" <?php if($options['sourcename']==''){echo 'selected';} ?>>No Attribution</OPTION>
</SELECT></p>

<p><label class='o_textinput' for='showdesc'>Show Excerpt</label>
<SELECT NAME="rss_import_items[showdesc]" id="showdesc">
<OPTION VALUE="1" <?php if($options['showdesc']==1){echo 'selected';} ?>>Yes</OPTION>
<OPTION VALUE="0" <?php if($options['showdesc']==0){echo 'selected';} ?>>No</OPTION>
</SELECT></p>
<span id="secret">
<p style="padding-left:15px"><label class='o_textinput' for='descnum'>Excerpt length (number of words)</label>
<SELECT NAME="rss_import_items[descnum]" id="descnum">
<OPTION VALUE="50" <?php if($options['descnum']==50){echo 'selected';} ?>>50</OPTION>
<OPTION VALUE="100" <?php if($options['descnum']==100){echo 'selected';} ?>>100</OPTION>
<OPTION VALUE="200" <?php if($options['descnum']==200){echo 'selected';} ?>>200</OPTION>
<OPTION VALUE="300" <?php if($options['descnum']==300){echo 'selected';} ?>>300</OPTION>
<OPTION VALUE="99" <?php if($options['descnum']==99){echo 'selected';} ?>>Give me everything</OPTION>
</SELECT></p>
<p style="padding-left:15px"><label class='o_textinput' for='stripAll'>Check to get rid of all images in the excerpt.  <input type="checkbox" Name="rss_import_items[stripAll]" Value="1" <?php if ($options['stripAll']==1){echo 'checked="checked"';} ?></label>


</p>
</span>
</div></div>

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
 






	function showexcerpt($content, $maxchars,$openWindow,$stripAll,$thisLink)  //show excerpt function
	{
global $morestyle;
    $content=CleanHTML($content);

	if ($stripAll==1){
			$content=strip_tags(html_entity_decode($content));	
			$content= limitwords($maxchars,$content);	
	}else{
		$content=strip_tags(html_entity_decode($content),'<a><img>');
		$content=findalignImage($maxchars,$content);	
}
	
	return  str_replace($morestyle, "<a href=".$thisLink." ".$openWindow.">".$morestyle."</a>", $content);
	//return str_replace("<a ", "<a " .$openWindow, $content);
	}
	
	

	
	function limitwords($maxchars,$content){
	
global $morestyle;
		if($maxchars !=99){


		  $words = explode(' ', $content, ($maxchars + 1));
	  			if(count($words) > $maxchars)
		  				array_pop($words);
	 					//$content = implode(' ', $words)." [...]";
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
	
	
	

	
	function findalignImage($maxchars,$content){
	

		$strmatch='^\s*\<a.*href="(.*)">\s*(<img.*src=".*" \/?>)[^\<]*<\/a\>\s*(.*)$'; ///match leading image

		if (preg_match("/$strmatch/sU", $content, $matches)){


				$tabledImage= "<div class=\"imagefix\">".$matches[2]."</div>";
		
				$content=str_replace($matches[2], $tabledImage, $content); //format the leading image if it exists
				
				$content=str_replace($matches[3], limitwords($maxchars,strip_tags($matches[3])), $content); //strip away all tags after the leading image
				

		}else{
		
			
			$content = limitwords($maxchars,strip_tags($content));
		}
	return $content;	
	}
	
	




   
   function wp_rss_multi_importer_shortcode($atts=array()){
	
add_action('wp_footer','footer_scripts');
function wprssmi_hourly_feed() { return 3600; }
add_filter( 'wp_feed_cache_transient_lifetime', 'wprssmi_hourly_feed' );

	
		$siteurl= get_site_url();
       $cat_options_url = $siteurl . '/wp-admin/options-general.php?page=wp_rss_multi_importer_admin&tab=category_options/';
		
	
	$parms = shortcode_atts(array(  //Get shortcode parameters
		'category' => 0, 
		'hdsize' => '16px', 
		'hdweight'=>400, 
		'testyle'=>'color: #000000; font-weight: bold;margin: 0 0 0.8125em',
		'morestyle' =>'[...]'
		), $atts);
	
	$hdsize = $parms['hdsize'];
    $thisCat = $parms['category'];
	$hdweight = $parms['hdweight'];
	$testyle = $parms['testyle'];
	global $morestyle;
    $morestyle = $parms['morestyle'];


   $readable = '';
   $options = get_option('rss_import_items','option not found');


$cat_array = preg_grep("^feed_cat_^", array_keys($options));

	if (count($cat_array)==0) {  //for backward compatibility
		$noExistCat=1;
	}else{
		$noExistCat=0;	
	}



    
   if(!empty($options)){
	
//GET PARAMETERS  
$size = count($options);
$sortDir=$options['sortbydate'];  //1 is ascending
$stripAll=$options['stripAll'];
$todaybefore=$options['todaybefore'];
$showDesc=$options['showdesc'];  //1 is show
$descNum=$options['descnum'];
$maxperPage=$options['maxperPage'];
$maxposts=$options['maxfeed'];
$targetWindow=$options['targetWindow'];  //0=LB, 1=same, 2=new
if(empty($options['sourcename'])){
	$attribution='';
}else{
	$attribution=$options['sourcename'].': ';
}




   
   for ($i=1;$i<=$size;$i=$i+1){

	

   			$key =key($options);
				if ( !strpos( $key, '_' ) > 0 ) continue; //this makes sure only feeds are included here...everything else are options
				
   			$rssName= $options[$key];

   
   			next($options);
   			
   			$key =key($options);
   			
   			$rssURL=$options[$key];



  	next($options);
	$key =key($options);
	



if ((($thisCat>0 && $options[$key]==$thisCat))|| $thisCat==0 || $noExistCat==1) {

   $myfeeds[] = array("FeedName"=>$rssName,"FeedURL"=>$rssURL);   
	
}
   
$cat_array = preg_grep("^feed_cat_^", array_keys($options));  // for backward compatibility

	if (count($cat_array)>0) {

  next($options); //skip feed category
}

   }
 

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





foreach($myarray as $items) {
	
$total = $total +1;
if ($maxperPage>0 && $total>=$maxperPage) break;







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
	

	
	
		$readable .=  '<div class="rss-output"><span style="font-size:'.$hdsize.'; font-weight:'.$hdweight.';"><a '.$openWindow.' href='.$items["mylink"].'>'.$items["mytitle"].'</a></span><br>';
	

			
	if (!empty($items["mydesc"]) & 	$showDesc==1){

	$readable .=  showexcerpt($items["mydesc"],$descNum,$openWindow,$stripAll,$items["mylink"]).'<br />';
}


	
	if (!empty($items["mystrdate"])){
	 $readable .=  date("D, M d, Y",$items["mystrdate"]).'<br />';
	}
		if (!empty($items["myGroup"])){
     $readable .=  '<span style="font-style:italic;">'.$attribution.''.$items["myGroup"].'</span>';
	}
	 $readable .=  '</div>';


}
    
   }
return $readable;

   }
   

   
    
   
?>