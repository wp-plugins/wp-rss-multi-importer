<?php
/*  Plugin Name: RSS Multi Import
  Plugin URI: http://www.allenweiss/com/wp_plugin
  Description: This plugin helps you import multiple RSS feeds and have them sorted by date, assign an attribution label, and limit the number of items per feed.
  Version: 1.1
	Author: Allen Weiss
	Author URI: http://www.allenweiss/com/wp_plugin
	License: GPL2  - most WordPress plugins are released under GPL2 license terms
*/


function wp_rss_multi_importer_start () {
register_setting('wp_rss_multi_importer_options', 'rss_import_items');
add_settings_section( 'wp_rss_multi_importer_main', 'RSS Multi-Importer Settings', 'wp_section_text', 'wprssimport' );  
}

add_action('admin_init','wp_rss_multi_importer_start');
  

function wp_section_text() {
    echo '<p>Enter a name and the full URL (with http://) for each of your feeds. The name will be used to identify which feed produced the link (see the Attribution Label option below).</p><p>Put this shortcode, [wp_rss_multi_importer], on the page you wish to have the feed.</p>';
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



/**
    * Include Colorbox-related script and CSS in WordPress head on frontend
    */      
   
   add_action( 'wp_enqueue_scripts', 'wprssmi_frontend_scripts' );
   
   function wprssmi_frontend_scripts() {
		wp_enqueue_script( 'jquery' );
        wp_enqueue_style( 'styles', plugins_url( 'css/colorbox.css', __FILE__) );
        wp_enqueue_script( 'jquery.colorbox-min', plugins_url( 'scripts/jquery.colorbox-min.js', __FILE__) );         
   }




	
   /**
    * Output JQuery command to trigger Colorbox for links in the <head>
    */



add_action ( 'wp_head', 'wprssmi_head_output' );

function wprssmi_head_output() {
	   
    echo "<script type='text/javascript'>jQuery(document).ready(function(){ jQuery('a.colorbox').colorbox({iframe:true, width:'80%', height:'80%'})});</script>";
 
}


/**
 * Include CSS in plugin page header
 */
   


    function wprssmi_header() {        
        wp_enqueue_style( 'styles', plugins_url( 'css/styles.css', __FILE__) );
    }




function wp_rss_multi_importer_menu () {
add_options_page('WP RSS Multi-Importer','RSS Multi-Importer','manage_options','wp_rss_multi_importer_admin', 'wp_rss_multi_importer_options_page');
}

add_action('admin_menu','wp_rss_multi_importer_menu');


	
	
	
	function wprssmi_convert_key( $key ) { 

        if ( strpos( $key, 'feed_name_' ) === 0 ) { 

            $label = str_replace( 'feed_name_', 'Feed Name ', $key );
        }

        else if ( strpos( $key, 'feed_url_' ) === 0 ) { 

            $label = str_replace( 'feed_url_', 'Feed URL ', $key );
        }
        return $label;
    }

    function wprss_get_id_number($key){
	
	if ( strpos( $key, 'feed_name_' ) === 0 ) { 

        $j = str_replace( 'feed_name_', '', $key );
    }
	return $j;
	
    }



function wp_rss_multi_importer_options_page() {

       ?>

       <div class="wrap">

       <?php screen_icon(); ?>

       

        

       <h2>RSS Multi-Importer Admin</h2>

       <div id="options">

       <form action="options.php" method="post"  >            

       <?php
		$siteurl= get_site_url();
        $images_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/images';

       settings_fields( 'wp_rss_multi_importer_options' );

       do_settings_sections( 'wprssimport' );

       $options = get_option( 'rss_import_items' ); 
    	

       if ( !empty($options) ) {

           $size = count($options);  

           for ( $i=1; $i<=$size; $i++ ) {            

               if( $i % 2 == 0 ) continue;


               $key = key( $options );

           
            if ( !strpos( $key, '_' ) > 0 ) continue; //this makes sure only feeds are included here...everything else are options

				$j = wprss_get_id_number($key);
				
				
             echo "<div class='wprss-input' id='$j'>";

               echo "<p><label class='textinput' for='$key'>" . wprssmi_convert_key( $key ) . "</label>

               <input  class='wprss-input' size='75' name='rss_import_items[$key]' type='text' value='$options[$key]' /></p>";
               

               next( $options );
             

               $key = key( $options );
               

               echo "<p><label class='textinput' for='$key'>" . wprssmi_convert_key( $key ) . "</label>

               <input id='$j' class='wprss-input' size='75' name='rss_import_items[$key]' type='text' value='$options[$key]' />  
               <a href='#' class='btnDelete' id='$j'><img src='$images_url/remove.png'/></a></p>";

               next( $options );

               echo "</div>"; 

               

           }

       }

       

       $siteurl = get_option('siteurl');

    

       ?>

       <div id="buttons"><a href="#" id="add" class="addbutton"><img src="<?php echo $images_url; ?>/add.png"></a>  
      
   <h3>Option Settings</H3>   
      <p><label class='o_textinput' for='sortbydate'>Sort Output by Date (Ascending = Closest Date First)</label>
	
		<SELECT NAME="rss_import_items[sortbydate]">
		<OPTION VALUE="1" <?php if($options['sortbydate']==1){echo 'selected';} ?>>Ascending</OPTION>
		<OPTION VALUE="0" <?php if($options['sortbydate']==0){echo 'selected';} ?>>Descending</OPTION>
		
		</SELECT></p>  
	

<p><label class='o_textinput' for='maxfeed'>Number of Entries per Feed</label>
<SELECT NAME="rss_import_items[maxfeed]">
<OPTION VALUE="2" <?php if($options['maxfeed']==2){echo 'selected';} ?>>2</OPTION>
<OPTION VALUE="5" <?php if($options['maxfeed']==5){echo 'selected';} ?>>5</OPTION>
<OPTION VALUE="10" <?php if($options['maxfeed']==10){echo 'selected';} ?>>10</OPTION>
<OPTION VALUE="15" <?php if($options['maxfeed']==15){echo 'selected';} ?>>15</OPTION>
<OPTION VALUE="20" <?php if($options['maxfeed']==20){echo 'selected';} ?>>20</OPTION>
</SELECT></p>

<p><label class='o_textinput' for='targetWindow'>Target Window (when link clicked, where should it open)</label>
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
<p><label class='o_textinput' for='descnum'>Excerpt length (number of characters)</label>
<SELECT NAME="rss_import_items[descnum]" id="descnum">
<OPTION VALUE="50" <?php if($options['descnum']==50){echo 'selected';} ?>>50</OPTION>
<OPTION VALUE="100" <?php if($options['descnum']==100){echo 'selected';} ?>>100</OPTION>
<OPTION VALUE="200" <?php if($options['descnum']==200){echo 'selected';} ?>>200</OPTION>
<OPTION VALUE="300" <?php if($options['descnum']==300){echo 'selected';} ?>>300</OPTION>
</SELECT></p>
</span>


       <p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>

       

       </form>

       </div>

       </div>

       <?php 

   }
   
   /**
   *  Shortcode setup and call (shortcode is [wp_rss_multi_importer])
   */
   
   add_shortcode('wp_rss_multi_importer','wp_rss_multi_importer_shortcode');
 



	function showexcerpt($content, $maxchars)
	{
	  $words = explode(' ', $content, ($maxchars + 1));
	  if(count($words) > $maxchars)
	  array_pop($words);
	  return implode(' ', $words);
	}





   
   function wp_rss_multi_importer_shortcode($atts=array()){
   
   $readable = '';
   $options = get_option('rss_import_items','option not found');
    
   if(!empty($options)){
	
//GET PARAMETERS  
$size = count($options);
$sortDir=$options['sortbydate'];  //1 is ascending
$showDesc=$options['showdesc'];  //1 is show
$descNum=$options['descnum'];
$maxposts=$options['maxfeed'];
$targetWindow=$options['targetWindow'];  //0=LB, 1=same, 2=new
if(empty($options['sourcename'])){
	$attribution='';
}else{
	$attribution=$options['sourcename'].': ';
}




   
   for ($i=1;$i<=$size;$i=$i+2){
	
	
	


    


   			$key =key($options);
				if ( !strpos( $key, '_' ) > 0 ) continue; //this makes sure only feeds are included here...everything else are options
				
   			$rssName= $options[$key];
   
   			next($options);
   			
   			$key =key($options);
   			
   			$rssURL=$options[$key];
   
   $myfeeds[] = array("FeedName"=>$rssName,"FeedURL"=>$rssURL);   
   
  
  next($options);
   }
 
 

function wprssmi_hourly_feed() { return 3600; }
 
 foreach($myfeeds as $feeditem){

	
	$url=(string)($feeditem["FeedURL"]);
	   add_filter( 'wp_feed_cache_transient_lifetime', 'wprssmi_hourly_feed' );
	$feed = fetch_feed($url);
	 remove_filter( 'wp_feed_cache_transient_lifetime', 'wprssmi_hourly_feed' );



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


if($targetWindow==0){
	$openWindow='class="colorbox"';
}elseif ($targetWindow==1){
	$openWindow='target=_self';		
}else{
	$openWindow='target=_blank';	
}
	

foreach($myarray as $items) {

	$readable .=  '<p class="rss-output"><a '.$openWindow.' href='.$items["mylink"].'>'.$items["mytitle"].'</a><br />';
			
	if (!empty($items["mydesc"]) & 	$showDesc==1){
	 $readable .=  showexcerpt($items["mydesc"],$descNum).'<br />';
}


	
	if (!empty($items["mystrdate"])){
	 $readable .=  date("D, M d, Y",$items["mystrdate"]).'<br />';
	}
		if (!empty($items["myGroup"])){
     $readable .=  '<span style="font-style:italic;">'.$attribution.''.$items["myGroup"].'</span>';
	}
	 $readable .=  '</p>';
	

}
    
   }
return $readable;

   }
   

   
    
   
?>