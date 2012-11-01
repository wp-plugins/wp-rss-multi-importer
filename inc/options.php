<?php


	function wprssmi_convert_key( $key ) { 

     if ( strpos( $key, 'feed_name_' ) === 0 ) { 
	

 $label = str_replace( 'feed_name_', __('Feed Name ','wp-rss-multi-importer') , $key );

     }

     else if ( strpos( $key, 'feed_url_' ) === 0 ) { 

         $label = str_replace( 'feed_url_', __('Feed URL ','wp-rss-multi-importer'), $key );
     }

		else if ( strpos( $key, 'feed_cat_' ) === 0 ) { 

         $label = str_replace( 'feed_url_', __('Feed Category ','wp-rss-multi-importer'), $key );
     }

		else if ( strpos( $key, 'cat_name_' ) === 0 ) { 

         $label = str_replace( 'cat_name_', __('Category ID # ','wp-rss-multi-importer'), $key );
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


function check_feed($url){
	
		$url=(string)($url);


		while ( stristr($url, 'http') != $url )
			$url = substr($url, 1);

		$url = esc_url_raw(strip_tags($url));


					$feed = fetch_feed($url);

		if (is_wp_error( $feed ) ) {
			return "<span class=chk_feed>".__('This feed has errors', 'wp-rss-multi-importer')."</span>";
		//	$error_msg=$feed->get_error_message();
		//	return "<span  ><button id=my-button class=chk_feed >Error: Click to see</button></span>
		//	<div id=element_to_pop_up>
		//	    <a class=bClose>x<a/>
		//	   This feed is causing errors - this is the error message: '.$error_msg.'
		//	</div>";
		}
		
}





function wp_rss_multi_importer_options_page() {


delete_db_transients();


       ?>

       <div class="wrap">
	<div id="poststuff">

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

      
      

<div class="postbox"><h3><label for="title"><?php _e("Options Settings", 'wp-rss-multi-importer')?></label></h3>
<div class="inside">

<h3><?php _e("Template", 'wp-rss-multi-importer')?></h3>


<?php
$thistemplate=$options['template'];
	get_template_function($thistemplate);
?>

<?php
if ($options['maxperPage']=='' || $options['maxperPage']=='NULL') {
?>
<H2 class="save_warning"><?php _e("You must choose and then click Save Settings for the plugin to function correctly.  If not sure which options to choose right now, don't worry - the most common settings have been set for you - just click Save Settings.", 'wp-rss-multi-importer')?></H2>
<?php
}
?>


<h3><?php _e("Sorting and Separating Posts", 'wp-rss-multi-importer')?></h3>
 
      <p><label class='o_textinput' for='sortbydate'><?php _e("Sort Output by Date (Descending = Closest Date First", 'wp-rss-multi-importer')?></label>
	
		<SELECT NAME="rss_import_options[sortbydate]">
		<OPTION VALUE="1" <?php if($options['sortbydate']==1){echo 'selected';} ?>><?php _e("Ascending", 'wp-rss-multi-importer')?></OPTION>
		<OPTION VALUE="0" <?php if($options['sortbydate']==0){echo 'selected';} ?>><?php _e("Descending", 'wp-rss-multi-importer')?></OPTION>
		
		</SELECT></p>  
		
		
		<p><label class='o_textinput' for='todaybefore'><?php _e("Separate Today and Earlier Posts", 'wp-rss-multi-importer')?></label>

		<SELECT NAME="rss_import_options[todaybefore]">
		<OPTION VALUE="1" <?php if($options['todaybefore']==1){echo 'selected';} ?>><?php _e("Yes", 'wp-rss-multi-importer')?></OPTION>
		<OPTION VALUE="0" <?php if($options['todaybefore']==0){echo 'selected';} ?>><?php _e("No", 'wp-rss-multi-importer')?></OPTION>

		</SELECT></p>
	
<h3><?php _e("Number of Posts and Pagination", 'wp-rss-multi-importer')?></h3>
<p><label class='o_textinput' for='maxfeed'><?php _e("Number of Entries per Feed", 'wp-rss-multi-importer')?></label>
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


<p><label class='o_textinput' for='maxperPage'><?php _e("Number of Entries per Page of Output", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_import_options[maxperPage]">
<OPTION VALUE="10" <?php if($options['maxperPage']==10){echo 'selected';} ?>>10</OPTION>
<OPTION VALUE="20" <?php if($options['maxperPage']==20){echo 'selected';} ?>>20</OPTION>
<OPTION VALUE="30" <?php if($options['maxperPage']==30){echo 'selected';} ?>>30</OPTION>
<OPTION VALUE="40" <?php if($options['maxperPage']==40){echo 'selected';} ?>>40</OPTION>
<OPTION VALUE="50" <?php if($options['maxperPage']==50){echo 'selected';} ?>>50</OPTION>
</SELECT></p>




<p><label class='o_textinput' for='pag'><?php _e("Do you want pagination?", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_import_options[pag]" id="pagination">
<OPTION VALUE="1" <?php if($options['pag']==1){echo 'selected';} ?>><?php _e("Yes", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="0" <?php if($options['pag']==0){echo 'selected';} ?>><?php _e("No", 'wp-rss-multi-importer')?></OPTION>
</SELECT>  <?php _e("(Note: this will override the Number of Entries per Page of Output)", 'wp-rss-multi-importer')?></p>



<span id="pag_options" <?php if($options['pag']==0){echo 'style="display:none"';}?>>
	
	<p style="padding-left:15px"><label class='o_textinput' for='perPage'><?php _e("Number of Posts per Page for Pagination", 'wp-rss-multi-importer')?></label>
	<SELECT NAME="rss_import_options[perPage]">
	<OPTION VALUE="6" <?php if($options['perPage']==6){echo 'selected';} ?>>6</OPTION>
	<OPTION VALUE="12" <?php if($options['perPage']==12){echo 'selected';} ?>>12</OPTION>
	<OPTION VALUE="15" <?php if($options['perPage']==15){echo 'selected';} ?>>15</OPTION>
	<OPTION VALUE="20" <?php if($options['perPage']==20){echo 'selected';} ?>>20</OPTION>
	</SELECT></p>	
	
</span>



<h3><?php _e("How Links Open and No Follow Option", 'wp-rss-multi-importer')?></h3>

<p><label class='o_textinput' for='targetWindow'><?php _e("Target Window (when link clicked, where should it open?)", 'wp-rss-multi-importer')?></label>
	<SELECT NAME="rss_import_options[targetWindow]" id="targetWindow">
	<OPTION VALUE="0" <?php if($options['targetWindow']==0){echo 'selected';} ?>><?php _e("Use LightBox", 'wp-rss-multi-importer')?></OPTION>
	<OPTION VALUE="1" <?php if($options['targetWindow']==1){echo 'selected';} ?>><?php _e("Open in Same Window", 'wp-rss-multi-importer')?></OPTION>
	<OPTION VALUE="2" <?php if($options['targetWindow']==2){echo 'selected';} ?>><?php _e("Open in New Window", 'wp-rss-multi-importer')?></OPTION>
	</SELECT>	
</p>
<p style="padding-left:15px"><label class='o_textinput' for='noFollow'>Set links as No Follow.  <input type="checkbox" Name="rss_import_options[noFollow]" Value="1" <?php if ($options['noFollow']==1){echo 'checked="checked"';} ?></label></p>





<h3><?php _e("What Shows - Attribution", 'wp-rss-multi-importer')?></h3>



<p><label class='o_textinput' for='sourcename'><?php _e("Attribution Label", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_import_options[sourcename]">
<OPTION VALUE="Source" <?php if($options['sourcename']=='Source'){echo 'selected';} ?>><?php _e("Source", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="Via" <?php if($options['sourcename']=='Via'){echo 'selected';} ?>><?php _e("Via", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="Club" <?php if($options['sourcename']=='Club'){echo 'selected';} ?>><?php _e("Club", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="Sponsor" <?php if($options['sourcename']=='Sponsor'){echo 'selected';} ?>><?php _e("Sponsor", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="" <?php if($options['sourcename']==''){echo 'selected';} ?>><?php _e("No Attribution", 'wp-rss-multi-importer')?></OPTION>
</SELECT></p>

<h3><?php _e("What Shows - EXCERPTS", 'wp-rss-multi-importer')?></h3>

<p><label class='o_textinput' for='showdesc'><?php _e("<b>Show Excerpt</b>", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_import_options[showdesc]" id="showdesc">
<OPTION VALUE="1" <?php if($options['showdesc']==1){echo 'selected';} ?>><?php _e("Yes", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="0" <?php if($options['showdesc']==0){echo 'selected';} ?>><?php _e("No", 'wp-rss-multi-importer')?></OPTION>
</SELECT></p>

<p style="padding-left:15px"><label class='o_textinput' for='showcategory'><?php _e("Show Category Name", 'wp-rss-multi-importer')?>   <input type="checkbox" Name="rss_import_options[showcategory]" Value="1" <?php if ($options['showcategory']==1){echo 'checked="checked"';} ?></label></p>


<span id="secret" <?php if($options['showdesc']==0){echo 'style="display:none"';}?>>
	
	
	<p style="padding-left:15px"><label class='o_textinput' for='showmore'><?php _e("Let your readers determine if they want to see the excerpt with a show-hide option. ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_import_options[showmore]" Value="1" <?php if ($options['showmore']==1){echo 'checked="checked"';} ?></label>
	</p>	
	
	
<p style="padding-left:15px"><label class='o_textinput' for='descnum'><?php _e("Excerpt length (number of words)", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_import_options[descnum]" id="descnum">
<OPTION VALUE="20" <?php if($options['descnum']==20){echo 'selected';} ?>>20</OPTION>
<OPTION VALUE="30" <?php if($options['descnum']==30){echo 'selected';} ?>>30</OPTION>
<OPTION VALUE="50" <?php if($options['descnum']==50){echo 'selected';} ?>>50</OPTION>
<OPTION VALUE="100" <?php if($options['descnum']==100){echo 'selected';} ?>>100</OPTION>
<OPTION VALUE="200" <?php if($options['descnum']==200){echo 'selected';} ?>>200</OPTION>
<OPTION VALUE="300" <?php if($options['descnum']==300){echo 'selected';} ?>>300</OPTION>
<OPTION VALUE="99" <?php if($options['descnum']==99){echo 'selected';} ?>><?php _e("Give me everything", 'wp-rss-multi-importer')?></OPTION>
</SELECT></p>
<h4><?php _e("Image Handling", 'wp-rss-multi-importer')?></h4>
<p><label class='o_textinput' for='stripAll'><?php _e("Check to get rid of all images in the excerpt.", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_import_options[stripAll]" Value="1" <?php if ($options['stripAll']==1){echo 'checked="checked"';} ?></label>
</p>
<p><?php _e("You can adjust the leading image, if it exists.  Note that including images in your feed may slow down how quickly it renders on your site, so you'll need to experiment with these settings.", 'wp-rss-multi-importer')?></p>
<p style="padding-left:15px"><label class='o_textinput' for='adjustImageSize'><?php _e("If you want excerpt images, check to fix their width at 150 (can be over-written in shortcode).", 'wp-rss-multi-importer')?>  <input type="checkbox" Name="rss_import_options[adjustImageSize]" Value="1" <?php if ($options['adjustImageSize']==1){echo 'checked="checked"';} ?></label></p>
	
<p style="padding-left:15px"><label class='o_textinput' for='floatType'><?php _e("Float images to the left (can be over-written in shortcode).", 'wp-rss-multi-importer')?>  <input type="checkbox" Name="rss_import_options[floatType]" Value="1" <?php if ($options['floatType']==1){echo 'checked="checked"';} ?></label></p>
</span>

<h3><?php _e("Get Social", 'wp-rss-multi-importer')?></h3>
<p ><label class='o_textinput' for='showsocial'><?php _e("Add social icons (Twitter and Facebook) to each post. ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_import_options[showsocial]" Value="1" <?php if ($options['showsocial']==1){echo 'checked="checked"';} ?></label>
</p>


<h3><?php _e("Cache and Conflict Handling", 'wp-rss-multi-importer')?></h3>

<p><label class='o_textinput' for='cacheMin'><?php _e("Number of minutes you want the post data held in cache (match to how often your feeds are updated)", 'wp-rss-multi-importer')?></label>
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




<p ><label class='o_textinput' for='cb'><?php _e("Check if you are having colorbox conflict problems.", 'wp-rss-multi-importer')?>   <input type="checkbox" Name="rss_import_options[cb]" Value="1" <?php if ($options['cb']==1){echo 'checked="checked"';} ?></label></p>
<input   size='10' name='rss_import_options[plugin_version]' type='hidden' value='<?php echo WP_RSS_MULTI_VERSION ?>' />

</div></div>

       <p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>



       </form>

      <div class="postbox"><h3><label for="title"><?php _e("Help Others", 'wp-rss-multi-importer')?></label></h3><div class="inside"><?php _e("If you find this plugin helpful, let others know by <a href=\"http://wordpress.org/extend/plugins/wp-rss-multi-importer/\" target=\"_blank\">rating it here</a>.  That way, it will help others determine whether or not they should try out the plugin.  Thank you.", 'wp-rss-multi-importer')?></div></div> 

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

	$removeurl=WP_RSS_MULTI_IMAGES."remove.png";

      settings_fields( 'wp_rss_multi_importer_options' );


       $options = get_option( 'rss_import_items' ); 

       $catOptions_exist= get_option( 'rss_import_categories' ); 

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

               <input  class='wprss-input' size='75' name='rss_import_items[$key]' type='text' value='$options[$key]' />  <a href='javascript:void(0)' class='btnDelete' id='$j'><img src='$removeurl'/></a></p>";


               next( $options );


               $key = key( $options );

$url_esc=esc_url($options[$key]);
               echo "<p><label class='textinput' for='$key'>" . wprssmi_convert_key( $key ) . "</label>

               <input id='$j' class='wprss-input' size='75' name='rss_import_items[$key]' type='text' value='$url_esc' />" ; 


			if (empty($catOptions_exist)){
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
		echo "<span class=category_list>Category ";
echo "<SELECT NAME=".$selectName." id='feed_cat'>";
echo "<OPTION VALUE='0'>NONE</OPTION>";
	$catsize = count($catOptions);

echo $options[$key];

	for ( $k=1; $k<=$catsize; $k++ ) {   

if( $k % 2== 0 ) continue;

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
echo "</SELECT></span>";
}
echo check_feed($url_esc);  // needs style

              echo " </p>";



               next( $options );

               echo "</div>"; 



           }

       }







       ?>

       <div id="buttons"><a href="javascript:void(0)" id="add" class="addbutton"><img src="<?php echo WP_RSS_MULTI_IMAGES; ?>add.png"></a>  



       <p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>



       </form>

      <div class="postbox"><h3><label for="title">   <?php _e("Help Others", 'wp-rss-multi-importer')?></label></h3><div class="inside"><?php _e("If you find this plugin helpful, let others know by <a href=\"http://wordpress.org/extend/plugins/wp-rss-multi-importer/\" target=\"_blank\">rating it here</a>.  That way, it will help others determine whether or not they should try out the plugin.  Thank you.", 'wp-rss-multi-importer')?></div></div> 

       </div>
</div>
       </div>

       <?php 

  }


















//  Categories Page

function wp_rss_multi_importer_category_page() {


       ?>
      <div class="wrap">
	<div id="poststuff">
  


     <form action="options.php" method="post"  >  
	
		<div class="postbox">
		<div class="inside">
	<h3><?php _e("RSS Multi-Importer Categories (and their shortcodes)", 'wp-rss-multi-importer')?></h3>
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
  <div id="category"><a href="#" id="addCat" class="addCategory"><img src="<?php echo WP_RSS_MULTI_IMAGES; ?>addCat.png"></a>  	
<p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>
</div></div>	          
</form>
</div></div>

<?php

}




function wp_rss_multi_importer_feed_page() {

       ?>

       <div class="wrap">
	<div id="poststuff">
  <h2><?php _e("Export Your RSS Feed", 'wp-rss-multi-importer')?></h2>
  
<p><?php _e("You can re-export your feeds as an RSS feed for your readers.  You configure some options for this feed here.", 'wp-rss-multi-importer')?></p>


       <div id="options">

       <form action="options.php" method="post"  >            

       <?php

      settings_fields('wp_rss_multi_importer_feed_options');
      $options = get_option('rss_feed_options');    

       ?>


<div class="postbox">
<div class="inside">



<h3><?php _e("Export Feed Options Settings", 'wp-rss-multi-importer')?></h3>


<p><label class='o_textinput' for='feedtitle'><?php _e("Feed Title", 'wp-rss-multi-importer')?></label>

<input id="feedtitle" type="text" value="<?php echo $options['feedtitle']?>" name="rss_feed_options[feedtitle]"></p>

<p><label class='o_textinput' for='feedslug'><?php _e("Feed Slug", 'wp-rss-multi-importer')?></label>

<input id="feedslug" size="10" type="text" value="<?php echo $options['feedslug']?>" name="rss_feed_options[feedslug]"> <?php _e("(no spaces are allowed!  See what a slug is below)", 'wp-rss-multi-importer')?></p>

<p><label class='o_textinput' for='feeddesc'><?php _e("Feed Description", 'wp-rss-multi-importer')?></label>

<input id="feeddesc" type="text" value="<?php echo $options['feeddesc']?>" name="rss_feed_options[feeddesc]" size="50"></p>

<p><label class='o_textinput' for='striptags'><?php _e("Check to get rid of all images in the feed output.", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_feed_options[striptags]" Value="1" <?php if ($options['striptags']==1){echo 'checked="checked"';} ?></label>
</p>

</div></div>

       <p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>



       </form>

	<?php
	$url=site_url();
	if (!empty($options['feedslug'])){

		echo "<h3>". __("Your RSS feed is here:", 'wp-rss-multi-importer'). "<br><br><a href=".$url."?feed=".$options['feedslug']." target='_blank'>".$url."?feed=".$options['feedslug']."</a></h3>";
	}else{
		
		echo "<h3>". __("Your RSS feed is here:", 'wp-rss-multi-importer')." <br><br>".$url."?feed=[this is your slug]</h3>";
	}

	?>

</div></div></div>
<?php
}




function wp_rss_multi_importer_post_page() {

       ?>

       <div class="wrap">
	<div id="poststuff">
  <h2><?php _e("Put Your RSS Feed Into Blog Posts", 'wp-rss-multi-importer')?></h2>
<p><?php _e("You can have your RSS feed imported into blog posts, so people can comment on them.  You configure some options for this here.", 'wp-rss-multi-importer')?></p>
<p><?php _e("You can delete any post created by this plugin by simply deleting the post.", 'wp-rss-multi-importer')?></p>
<p><?php _e("The feed will update into posts every hour.  You must check to activate this feature.", 'wp-rss-multi-importer')?></p>

       <div id="options">

       <form action="options.php" method="post"  >            

       <?php

      settings_fields('wp_rss_multi_importer_post_options');
      $post_options = get_option('rss_post_options');    

       ?>


<div class="postbox">
<div class="inside">



<h3><?php _e("Feed to Post Options Settings", 'wp-rss-multi-importer')?></h3>

<p><label class='o_textinput' for='active'><?php _e("Check to Activate this Feature", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_post_options[active]" Value="1" <?php if ($post_options['active']==1){echo 'checked="checked"';} ?></label>
</p>

<p><label class='o_textinput' for='post_status'><?php _e("Default status of posts", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[post_status]" id="post_status">
<OPTION VALUE="draft" <?php if($post_options['post_status']=="draft"){echo 'selected';} ?>>draft</OPTION>
<OPTION VALUE="publish" <?php if($post_options['post_status']=="publish"){echo 'selected';} ?>>publish</OPTION>
<OPTION VALUE="pending" <?php if($post_options['post_status']=="pending"){echo 'selected';} ?>>pending</OPTION>
<OPTION VALUE="future" <?php if($post_options['post_status']=="future"){echo 'selected';} ?>>future</OPTION>
<OPTION VALUE="private" <?php if($post_options['post_status']=="private"){echo 'selected';} ?>>private</OPTION>
</SELECT></p>

<p><label class='o_textinput' for='wpcategory'><?php _e("What Wordpress blog post category, if any, do you want these posts to enter as?  Enter the ID number of this category.", 'wp-rss-multi-importer')?></label>
	<input id="wpcategory" type="text" value="<?php echo $post_options['wpcategory']?>" name="rss_post_options[wpcategory]" size="2" maxlength="3"><a href="http://www.allenweiss.com/faqs/finding-the-id-number-for-feed-to-post-category" target=_"blank"><?php _e("How to find this ID number.", 'wp-rss-multi-importer')?></a></p>



<p><label class='o_textinput' for='maxfeed'><?php _e("Number of Entries per Feed", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[maxfeed]">
<OPTION VALUE="1" <?php if($post_options['maxfeed']==1){echo 'selected';} ?>>1</OPTION>
<OPTION VALUE="2" <?php if($post_options['maxfeed']==2){echo 'selected';} ?>>2</OPTION>
<OPTION VALUE="3" <?php if($post_options['maxfeed']==3){echo 'selected';} ?>>3</OPTION>
<OPTION VALUE="4" <?php if($post_options['maxfeed']==4){echo 'selected';} ?>>4</OPTION>
<OPTION VALUE="5" <?php if($post_options['maxfeed']==5){echo 'selected';} ?>>5</OPTION>
<OPTION VALUE="10" <?php if($post_options['maxfeed']==10){echo 'selected';} ?>>10</OPTION>
<OPTION VALUE="15" <?php if($post_options['maxfeed']==15){echo 'selected';} ?>>15</OPTION>
<OPTION VALUE="20" <?php if($post_options['maxfeed']==20){echo 'selected';} ?>>20</OPTION>
</SELECT></p>



<p><label class='o_textinput' for='maxperfetch'><?php _e("Number of Post Entries per Hourly Fetch", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[maxperfetch]">
<OPTION VALUE="1" <?php if($post_options['maxperfetch']==1){echo 'selected';} ?>>1</OPTION>
<OPTION VALUE="2" <?php if($post_options['maxperfetch']==2){echo 'selected';} ?>>2</OPTION>
<OPTION VALUE="3" <?php if($post_options['maxperfetch']==3){echo 'selected';} ?>>3</OPTION>
<OPTION VALUE="4" <?php if($post_options['maxperfetch']==4){echo 'selected';} ?>>4</OPTION>
<OPTION VALUE="5" <?php if($post_options['maxperfetch']==5){echo 'selected';} ?>>5</OPTION>
<OPTION VALUE="10" <?php if($post_options['maxperfetch']==10){echo 'selected';} ?>>10</OPTION>
<OPTION VALUE="15" <?php if($post_options['maxperfetch']==15){echo 'selected';} ?>>15</OPTION>
<OPTION VALUE="20" <?php if($post_options['maxperfetch']==20){echo 'selected';} ?>>20</OPTION>
</SELECT></p>




<p><label class='o_textinput' for='descnum'><?php _e("Excerpt length (number of words)", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[descnum]" id="descnum">
<OPTION VALUE="20" <?php if($post_options['descnum']==20){echo 'selected';} ?>>20</OPTION>
<OPTION VALUE="30" <?php if($post_options['descnum']==30){echo 'selected';} ?>>30</OPTION>
<OPTION VALUE="50" <?php if($post_options['descnum']==50){echo 'selected';} ?>>50</OPTION>
<OPTION VALUE="100" <?php if($post_options['descnum']==100){echo 'selected';} ?>>100</OPTION>
<OPTION VALUE="200" <?php if($post_options['descnum']==200){echo 'selected';} ?>>200</OPTION>
<OPTION VALUE="300" <?php if($post_options['descnum']==300){echo 'selected';} ?>>300</OPTION>
<OPTION VALUE="99" <?php if($post_options['descnum']==99){echo 'selected';} ?>><?php _e("Give me everything", 'wp-rss-multi-importer')?></OPTION>
</SELECT></p>




<p><label class='o_textinput' for='maximgwidth'><?php _e("Maximum width size of images", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[maximgwidth]">
<OPTION VALUE="150" <?php if($post_options['maximgwidth']==150){echo 'selected';} ?>>150px</OPTION>
<OPTION VALUE="250" <?php if($post_options['maximgwidth']==250){echo 'selected';} ?>>250px</OPTION>
<OPTION VALUE="900" <?php if($post_options['maximgwidth']==900){echo 'selected';} ?>><?php _e("unrestricted", 'wp-rss-multi-importer')?></OPTION>
</SELECT></p>








<p><label class='o_textinput' for='stripAll'><?php _e("Check to get rid of all images in the excerpt.", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_post_options[stripAll]" Value="1" <?php if ($post_options['stripAll']==1){echo 'checked="checked"';} ?></label>
</p>

<p ><label class='o_textinput' for='showsocial'><?php _e("Add social icons (Twitter and Facebook) to each post. ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_post_options[showsocial]" Value="1" <?php if ($post_options['showsocial']==1){echo 'checked="checked"';} ?></label>
</p>

<?php
$catOptions= get_option( 'rss_import_categories' ); 

	if ( !empty($catOptions) ) {
?>
<p><label class='o_textinput' for='category'><?php _e("Restrict feeds to one of your defined RSS Multi Importer categories", 'wp-rss-multi-importer')?></label>
	<SELECT NAME="rss_post_options[category]">
<OPTION VALUE='0'>All</OPTION>
<?php
	$catsize = count($catOptions);



	for ( $k=1; $k<=$catsize; $k++ ) {   

if( $k % 2== 0 ) continue;

 	$catkey = key( $catOptions );
 	$nameValue=$catOptions[$catkey];
next( $catOptions );
 	$catkey = key( $catOptions );
	$IDValue=$catOptions[$catkey];


	 if($post_options['category']==$IDValue){
		$sel='selected  ';

		} else {
		$sel='';

		}

echo "<OPTION " .$sel.  "VALUE=".$IDValue.">".$nameValue."</OPTION>";
next( $catOptions );

}
echo "</SELECT>";
}else{
	
	echo __("<b>NOTE: If you set up categories (in Category Options) you can restrict only feeds in that category to go into blog posts.</b> ", 'wp-rss-multi-importer');
}

?>


</div></div>

       <p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>



       </form>

<button type="button" name="fetchnow" id="fetch-now" value=""><?php _e("CLICK TO FETCH FEEDS NOW", 'wp-rss-multi-importer')?></button>	
<div id="note"></div>
</div></div></div>
<?php
}




?>