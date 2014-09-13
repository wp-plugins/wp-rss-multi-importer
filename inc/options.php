<?php




//  Categories Page

function wp_rss_multi_importer_category_page() {


       ?>

		<div id="icon-themes" class="icon32 icon32-posts-rssmi_feed"></div>
		<h2><?php  _e("Multi-Importer", 'wp-rss-multi-importer')?></h2>
		<?php settings_errors(); ?>
      <div class="wrap">
	 <h2>Categories Admin</h2>	
	

	<div id="poststuff">



     <form action="options.php" method="post"  >  

		<div class="postbox">
		<div class="inside">
	<h3><?php _e("RSS Multi-Importer Categories (and their shortcodes)", 'wp-rss-multi-importer')?><span class='vtip' title='You cannot delete categories, but you can you change their name or just not use any categories you create.'>?</span></h3>
	
	
	<?php
//	$removeurl=WP_RSS_MULTI_IMAGES."delete_remove.png";
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



       <input id='5' class='cat-input' size='20' name='rss_import_categories[$key]' type='text' value='$textUpper' />  [wp_rss_multi_importer category=\"".$j."\"]";
next( $options );
   $key = key( $options );

     echo"  <input id='5'  size='20' name='rss_import_categories[$key]' type='hidden' value='$options[$key]' />" ; 
	echo "</div>";
	next( $options );	
}



}
	?>
  <div id="category"><a href="javascript:void(0)" id="addCat" class="addCategory"><img src="<?php echo WP_RSS_MULTI_IMAGES; ?>addCat.png"></a>  	
<p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>
</div></div>	          
</form>
</div></div>

<?php

}





//  Categories Images Page

function wp_rss_multi_importer_category_images_page() {


       ?>

	<h2><?php  _e("Multi-Importer", 'wp-rss-multi-importer')?></h2>
	       <div class="wrap">
      <div class="wrap">
		 <h2>Category Default Images, Post Tags and Filter Words Admin</h2>
	<div id="poststuff">
  


     <form action="options.php" method="post" class="catform" >  
	
		<div class="postbox">
		<div class="inside">
	<h3><?php _e("Set a Default Feed Category Image, Post Category Tags and Category Word Filters (all are optional) - note: filters work for shortcode, AutoPost, and the widget", 'wp-rss-multi-importer')?></h3>

	
	<?php
	settings_fields( 'wp_rss_multi_importer_categories_images' );


	$options = get_option('rss_import_categories' ); 
	$options_images = get_option('rss_import_categories_images' ); 

	if ( !empty($options) ) {
		
				echo '<div class="default-image-wrapper"><span class="default-image-text">Default Feed Category Image</span><span class="vtip" title="The image link you put here will be used as a default image for feeds in a category - if you choose to use this default image.">?</span><span class="default-tags-text">Post Category Tags</span><span class="default-filter-text">Include Filter Words</span><span class="vtip" title="Only include posts that have these words in the titles or content.">?</span><br>';
				echo '<span class="default-image-text-more">(full URL required)</span><span class="default-tags-text-more">(comma delimited list)</span><span class="default-tags-filter-more">(comma delimited list)</span><span class="default-tags-exclude-more">(check to exclude words)</span><span class="vtip" title="Make it so posts that have these words<br> in the titles or content will not be be included.">?</span></div>';
				
		$size = count($options);


		for ( $i=1; $i<=$size; 	$i++ ) {   
		   
if( $i % 2== 0 ) continue;

				
				   $key = key( $options );

	$j = cat_get_id_number($key);
	$textUpper=strtoupper($options[$key]);
		if ( !empty($options_images) ) {
	$cat_default_image=$options_images[$j]['imageURL'];
	$cat_default_tags=$options_images[$j]['tags'];
	$cat_default_filterwords=$options_images[$j]['filterwords'];
	$cat_default_filterwords_exclude=(isset($options_images[$j]['exclude']) ? $options_images[$j]['exclude'] : null);	
	if ($cat_default_filterwords_exclude==1) {$checkmsg='checked=checked';}else{$checkmsg='';}
		}
	
echo "<div class='default-list-name'>".$textUpper.":</div>";

	next( $options );

echo "<div class='default-list-image'><input class='default-cat-image'  size='50' name='rss_import_categories_images[$j][imageURL]' type='text' value='$cat_default_image' /></div>";

echo "<div class='default-list-tags'><input id='default-cat-tags' class='default-cat-tags'  size='20' name='rss_import_categories_images[$j][tags]' type='text' value='$cat_default_tags' /></div>";
echo "<div class='default-list-tags'><input id='default-cat-tags' class='default-cat-tags'  size='40' name='rss_import_categories_images[$j][filterwords]' type='text' value='$cat_default_filterwords' /><input type='checkbox' Name='rss_import_categories_images[$j][exclude]' Value='1' $checkmsg></div>";
		next( $options );

}

echo "<br><p class='submit'><input type='submit' value='Save Settings' name='submit' class='button-primary'></p>";		 

}else{
	 _e("<br>Once you add categories (above), you'll be able to add default images and tags here.", 'wp-rss-multi-importer');
	
}
	?>

</div></div>	          
</form>
</div></div>
</div>
<?php

}





function catDropDown($thisCatID){

if($thisCatID[1]=="0") {
	$thisCatID[1]=1;
}
	$category_ids = get_all_category_ids();
	echo 	'<OPTION  '.((isset($thisCatID[0]) && is_null($thisCatID[0])) ? 'selected':'').'  VALUE=NULL>Not in Use</OPTION>';	 
	foreach($category_ids as $cat_id) {
	  $cat_name = get_cat_name($cat_id);
	
		if (!empty($thisCatID)){
			echo 	'<OPTION  '.selected(true, in_array($cat_id, $thisCatID), false).'  VALUE="'.$cat_id.'">'.$cat_name.'</OPTION>';
		}else{
			echo 	'<OPTION   VALUE="'.$cat_id.'">'.$cat_name.'</OPTION>';
		}
 
	}

}









/*  Old code - some can be tossed */


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

/*  End of old code - some can be tossed */



function wp_rss_multi_importer_intro_page() {
		$feed = fetch_feed("http://rss.marketingprofs.com/marketingprofs");
		$options = get_option( 'rss_import_options' ); 
		$post_options = get_option('rss_post_options');  
	
	?>
		<div id="icon-themes" class="icon32 icon32-posts-rssmi_feed"></div>
		<h2><?php  _e("Multi-Importer", 'wp-rss-multi-importer')?></h2>
	<div class="wrap">
	
				
			
	                           <div class="postbox-container" style="min-width:400px; max-width:600px; padding: 0 20px 0 0;">	<?php _e("<h2>Instructions: Get Up and Running Quickly</h2>",'wp-rss-multi-importer')?>
					<div class="metabox-holder">	
						<div class="postbox-container">
							<H3 class="info_titles"><?php _e("Add the RSS feeds and optionally assign them to categories", 'wp-rss-multi-importer')?></H3>
							<p class="info_text"><?php _e("Start by adding feeds (Add a Feed or Upload RSS Feeds tabs).  Then, if you want, you can add Categories (Settings->Categories tab).  If you add categories, you can then go back and assign each feed to a category.", 'wp-rss-multi-importer')?></p>
							<H3 class="info_titles"><?php _e("Decide how you want to present the items from the RSS feeds on your web site", 'wp-rss-multi-importer')?></H3>
							<p class="info_text"><?php _e("You can present them on any page using Shortcode, which looks like this - [wp_rss_multi_importer], and display them using one of the several templates provided.  Or, you can have the items from RSS feeds become blog posts, and then let the settings of your Wordpress theme determine how they will look.  Finally, you might simply want the feeds in the side bar - and here a widget would work best.<br><br>You don't have to choose one way or another to present the feeds.  You can do all 3 at the same time.", 'wp-rss-multi-importer')?></p>	
		
							<H3 class="info_titles"><?php _e("1. Using the shortcode to display the feed items", 'wp-rss-multi-importer')?></H3>
							<p class="info_text"><?php _e("Go to the Shortcode Settings tab and select the template you want to use and set the settings. Add the shortcode to your Wordpress page. Use shortcode parameters (Shortcode->Shortcode Parameters tab) to put more customization onto your feed presentation.  If you put your feeds into categories, you can restrict which shows on a page to whatever categories you want.", 'wp-rss-multi-importer')?><?php if (!isset($options['active']) || $options['active']==0){echo 'This has not been activated.';} ?></p>
							<H3 class="info_titles"><?php _e("2. Create blog posts from the feed items (AutoPost)", 'wp-rss-multi-importer')?></H3>
							<p class="info_text"><?php if (!isset($post_options['active']) || $post_options['active']==0){echo '<span style="color:red;font-size:14px">This has not been activated.  Go to the AutoPost tab to activate this feature.</span><br>';}else{echo '<span style="color:green;font-size:14px">The AutoPost has been activated.</span><br>';} ?><?php _e("Click on the AutoPost Settings tab and set the options.  Make sure this feature is activated. At the bottom of that page you can assign the plugin categories to your WP blog categories", 'wp-rss-multi-importer')?></p>
							<H3 class="info_titles"><?php _e("3. Display the aggregated feed items in a widget", 'wp-rss-multi-importer')?></H3>
							<p class="info_text"><?php _e("If your theme supports widgets, go to Appearance->Widgets, add the RSS Multi-Importer widget, configure the options and then click Save.", 'wp-rss-multi-importer')?></p>
							
							<H2  ><?php _e("Understanding the menus", 'wp-rss-multi-importer')?></H2>
							<p><?php _e("On the left are the menu selections for this plugin:", 'wp-rss-multi-importer')?></p>
							<ul>
								<li style="margin:8px;"><?php _e("<b>Feed List</b> - this is your list of RSS feeds.", 'wp-rss-multi-importer')?></li>
					
							<li style="margin:8px;"><?php _e("<b>Add a Feed</b> - this is where you add another RSS feed.", 'wp-rss-multi-importer')?></li>
							<li style="margin:8px;"><?php _e("<b>Upload RSS Feeds</b> - this is where you add a large number of RSS feeds at the same time.", 'wp-rss-multi-importer')?></li>
							<li style="margin:8px;"><?php _e("<b>Categories</b> - this is where you add categories, including add default category images and filters.", 'wp-rss-multi-importer')?></li>
								<li style="margin:8px;"><?php _e("<b>Feed Items</b> - this is where you can manage the feed items.", 'wp-rss-multi-importer')?></li>
									<li style="margin:8px;"><?php _e("<b>Global Settings</b> - the date format for the shortcode is set here.", 'wp-rss-multi-importer')?></li>
							<li style="margin:8px;"><?php _e("<b>AutoPost</b> - this is where you set the settings for the AutoPosts and manage these posts.", 'wp-rss-multi-importer')?></li>
							<li style="margin:8px;"><?php _e("<b>Shortcode</b> - this is where you set the settings for the Shortcode, the parameters and saving shortcode tempates.", 'wp-rss-multi-importer')?></li>
						
					<li style="margin:8px;"><?php _e("<b>Export Feeds</b> - this is where you can export the aggregated feed you create or export a file of your feeds.", 'wp-rss-multi-importer')?></li>
				
						<li style="margin:8px;"><?php _e("<b>Diagnostics</b> - go here first if you are running into any problems.", 'wp-rss-multi-importer')?></li>
					
							</ul>
							<hr>
							
						</div>
					</div>
				</div>
				
		
				
					<div class="postbox-container" style="width:25%;min-width:200px;max-width:350px;">
						
						<!--  MarketingProfs
						
			<div id="sidebar" class="MP_box">
					<div >
			<h2 class="MP_title">Cutting Edge Marketing Know-How</h2>
		</div>
		
		
											
												
													<div class="txtorange">Join MarketingProfs.com</div>
														<div class="txtwhite">Over 600,000 have already</div>
													<div class="txtorange">Your Free Membership Includes:</div>
													<ul class="padding_nomargin txtleft" style="margin-left:30px;padding-top:5px;padding-bottom:5px;margin-top:0px;">
														<li style="margin:3px;"><b>FREE</b> access to all marketing articles</li>
														<li style="margin:3px;"><b>FREE</b> community forum use</li>
													</ul>
													<form style="padding-bottom:4px;" onsubmit="validateEmail(document.getElementById('e'));" action="https://www.marketingprofs.com/login/signup.asp" method="POST">
																				<div class="center width_full"><input type="text" onfocus="this.value=''" value="you@company.com" style="width:225px;color:#444;" id="e" name="e"></div>
																				<div class="center width_full"><input type="image" style="margin-top:4px;" src="http://www.mpdailyfix.com/wp-content/themes/mpdailyfix/images/signup_blue.gif" id="btnsignup" name="btnsignup"></div>
																				<input type="hidden" value="amwplugin" name="adref">
																				<script type="text/javascript">
																					function validateEmail(emailField){
																							var re = /^(([^&lt;&gt;()[\]\\.,;:\s@\"]+(\.[^&lt;&gt;()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
																							if (re.test(emailField.value) == false) 
																							{
																								alert('Oops! That email address doesn\'t look quite right.\n\nPlease make sure it was typed correctly and try again.');
																								return false;
																							}
																							return true;
																					}				
																				</script>
																			</form>
																		<span class="MP_title">	<a class="button-primary" style="text-align:center" href="https://www.marketingprofs.com/login/join?adref=amwplugin" target="_blank">Learn more »</a></span>
																
							
			
			
			
			</div>
			
			
			<?php
				if (!is_wp_error( $feed ) ){
		?>
			
			<h3 style="text-align:center;"><?php print 'Latest '.$feed->get_title(); ?></h3>
			<ul>
			<?php foreach ($feed->get_items(0, 5) as $item): ?>
			    <li>
			        <a href="<?php print $item->get_permalink(); ?>" target="_blank">
			        <?php print $item->get_title(); ?></a>
			    </li>
			<?php endforeach; ?>
			</ul>
			<?php	
			}
			
			?>
			
	-->
			
		<?php  if(rssmi_check_didUpgrade()==1)
	{
		?>	
			<div id="sidebar" class="MP_box">
				<div >
		<h2 class="MP_title">Need Plugin Help?</h2>
	</div>
	<p><a href="http://www.wprssimporter.com/faqs/" target="_blank" style="color:white">Go here to find FAQs.</a></p>
	<p><a href="http://www.wprssimporter.com/faqs/im-told-the-feed-isnt-valid-or-working/" target="_blank" style="color:white">Go here if you have a feed that isn't working</a><p>	
<p><a href="http://www.wprssimporter.com/faqs/the-cron-scheduler-isnt-working-whats-happening/" target="_blank" style="color:white">Go here if the scheduler doesn't appear to be working.</a><p>			
				<h2 class="MP_title">If you like this free plugin...</h2>				
				<p><a href="http://wordpress.org/support/view/plugin-reviews/wp-rss-multi-importer#postform" target="_blank" style="color:white">Consider saying thank you with a good rating.</a></p>
				</div>
				<!--	<hr>
					<h3 class="info_titles" style="text-align:center"><?php _e("DELETE EVERYTHING !!", 'wp-rss-multi-importer')?></H3>
					<p ><?php _e("At any time you can delete all entries in the tables created by this plugin by clicking on the button below. Only do this if you want to delete all posts, feed entries, featured images, etc, that resulted from using this plugin. (This will not include custom post types you created with this plugin).  THIS IS IRREVERSIBLE.", 'wp-rss-multi-importer')?></p>	
					<div style="margin-left:60px">
						<button  type="button" class="button-delete-red" name="restoreall" id="restore-all" value="" ><?php _e("CLICK TO DELETE ALL ENTRIES NOW", 'wp-rss-multi-importer')?></button> </p><div id="restore_note"></div>
						</div>-->
			
			
	<?php }else{?>		
			
			
			
						<div id="sidebar" class="activate_box">
							<div >
					<h2 class="MP_title">You Need to Upgrade This Plugin Before It Will Work</h2>
				</div>
				<p style="color:white;font-size:16px">You have 2 options:</p>
				<p style="color:white;font-size:14px">1. If you don't have many feeds (less than, say, 100), or are on a server with lots of memory, then you can upgrade by clicking below</p>
				<div style="margin-left:60px">
					<button type="button"  name="upgradefeeds" id="upgrade-feeds" value="" ><?php _e("CLICK TO UPGRADE NOW", 'wp-rss-multi-importer')?></button></div> <p style="color:white;font-size:14px">This could take a few minutes, so click once and then be patient!</p><div id="upgradefeeds_note"></div><div id="rssmi-big-ajax-loader"></div>
	
				<p style="color:white;font-size:14px">2. If you have many feeds and are on a server without a lot of memory, then you should upgrade by clicking below to export a text file of your feeds, then going to the Upload Feeds tab and simply copy and pasting the contents of the text file into the text box and clicking Submit. 
					<div style="margin-left:90px">
					<form method="post">
					  <p class="submit">

					      <input type="submit" name="rssmi_export" value="<?php _e( 'Export Your Feeds', 'wp-rss-multi-importer' ); ?>"  class="button" />
					  </p>
					</form>
					</div>
					</p>		
											
							</div>
							
							
			<?php
		}
			?>
			
			
			
			
			
			
			
			
			
			
		</div>
				
				</div>
	
	
	<?php
	
}


function wp_rssmi_download_feed_stream_for_activation(){
		$rssmi_url_download='';
		$option_items = get_option( 'rss_import_items' ); 
		$option_values = array_values($option_items);
		
		$rssmi_url_download .="DO NOT DELETE THIS LINE - ******ACTIVATE*******\n" ;

		for ($i = 0; $i <= count($option_items) - 1; $i++) {
			$name=$option_values[$i];
			$i=$i+1;
			$url=$option_values[$i];
			$i=$i+1;
			$cat=$option_values[$i];
			
			$rssmi_url_download .= $name.",".$url;
				if ($cat!=0){
			$rssmi_url_download .=",".$cat; 		
				}
			$rssmi_url_download .="\n" ;
		}
	
	echo $rssmi_url_download;
	die();	
}

add_action( 'admin_init', 'wp_rssmi_download_feeds_for_activation', 1 );


function wp_rssmi_download_feeds_for_activation() {
    if ( isset( $_POST['rssmi_export'] ) ) {  //watch for post
        $file_name = "feeds.txt";
        header( 'Content-Description: File Transfer' );
        header( "Content-Type: text/plain; charset=" . get_option( 'blog_charset' ) );
        header( "Content-Disposition: attachment; filename=$file_name." );
        wp_rssmi_download_feed_stream_for_activation();
        die();
    }
}




// SHORTCODE

function wp_rss_multi_importer_options_page() {


delete_db_transients();


       ?>

		<div id="icon-themes" class="icon32 icon32-posts-rssmi_feed"></div>

		<?php settings_errors(); ?>

       <div class="wrap">
	<h2><?php _e("Shortcode Settings", 'wp-rss-multi-importer')?></h2>
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

      
      

<div class="postbox"><h3><label for="title"><?php _e("Options Settings for Displaying the Shortcode Feed Items", 'wp-rss-multi-importer')?></label></h3>
	<p style="margin-left:20px">These are settings for the option to display the feed items on your site using shortcode.  If you want the settings for the AutoPost option, use that tab instead.</p>
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
 
      <p><label class='o_textinput' for='sortbydate'><?php _e("Sort Output by Date (Descending = Closest Date First <span class='vtip' title='Choose whether and how the items will be sorted by date.'>?</span>", 'wp-rss-multi-importer')?></label>
	
		<SELECT NAME="rss_import_options[sortbydate]">
		<OPTION VALUE="1" <?php if($options['sortbydate']==1){echo 'selected';} ?>><?php _e("Ascending", 'wp-rss-multi-importer')?></OPTION>
		<OPTION VALUE="0" <?php if($options['sortbydate']==0){echo 'selected';} ?>><?php _e("Descending", 'wp-rss-multi-importer')?></OPTION>
		<OPTION VALUE="2" <?php if($options['sortbydate']==2){echo 'selected';} ?>><?php _e("No Sorting", 'wp-rss-multi-importer')?></OPTION>
		
		</SELECT></p>  
		
		
		<p><label class='o_textinput' for='todaybefore'><?php _e("Separate Today and Earlier Posts", 'wp-rss-multi-importer')?></label>

		<SELECT NAME="rss_import_options[todaybefore]">
		<OPTION VALUE="1" <?php if($options['todaybefore']==1){echo 'selected';} ?>><?php _e("Yes", 'wp-rss-multi-importer')?></OPTION>
		<OPTION VALUE="0" <?php if($options['todaybefore']==0){echo 'selected';} ?>><?php _e("No", 'wp-rss-multi-importer')?></OPTION>

		</SELECT></p>
	
<h3><?php _e("Number of Posts, Pagination and Load More", 'wp-rss-multi-importer')?></h3>
		<p><label class='o_textinput' for='maxfeed'><?php _e("Number of Posts per Feed<span class='vtip' title='Bring in this number of the latest posts from each feed.'>?</span>", 'wp-rss-multi-importer')?></label>
		<SELECT NAME="rss_import_options[maxfeed]">
		<OPTION VALUE="1" <?php if($options['maxfeed']==1){echo 'selected';} ?>>1</OPTION>
		<OPTION VALUE="2" <?php if($options['maxfeed']==2){echo 'selected';} ?>>2</OPTION>
		<OPTION VALUE="3" <?php if($options['maxfeed']==3){echo 'selected';} ?>>3</OPTION>
		<OPTION VALUE="4" <?php if($options['maxfeed']==4){echo 'selected';} ?>>4</OPTION>
		<OPTION VALUE="5" <?php if($options['maxfeed']==5){echo 'selected';} ?>>5</OPTION>
		<OPTION VALUE="10" <?php if($options['maxfeed']==10){echo 'selected';} ?>>10</OPTION>
		<OPTION VALUE="15" <?php if($options['maxfeed']==15){echo 'selected';} ?>>15</OPTION>
		<OPTION VALUE="20" <?php if($options['maxfeed']==20){echo 'selected';} ?>>20</OPTION>
		<OPTION VALUE="30" <?php if($options['maxfeed']==30){echo 'selected';} ?>>30</OPTION>
		<OPTION VALUE="40" <?php if($options['maxfeed']==40){echo 'selected';} ?>>40</OPTION>
		<OPTION VALUE="50" <?php if($options['maxfeed']==50){echo 'selected';} ?>>50</OPTION>
		<OPTION VALUE="60" <?php if($options['maxfeed']==60){echo 'selected';} ?>>60</OPTION>
		<OPTION VALUE="70" <?php if($options['maxfeed']==70){echo 'selected';} ?>>70</OPTION>
		<OPTION VALUE="80" <?php if($options['maxfeed']==80){echo 'selected';} ?>>80</OPTION>
		</SELECT></p>

		<span id="posts_per_pag_options" <?php if($options['pag']==1 || $options['pag']==2 || $options['pag']==3){echo 'style="display:none"';}?>>
		<p><label class='o_textinput' for='maxperPage'><?php _e("Number of Posts Shown per Page of Output (<a href=\"http://www.wprssimporter.com/faqs/how-does-the-number-of-entries-per-feed-and-page-or-fetch-work//\" target=\"_blank\">GO HERE TO SEE HOW TO SET THIS OPTION</a>)", 'wp-rss-multi-importer')?></label>
		<SELECT NAME="rss_import_options[maxperPage]">
		<OPTION VALUE="5" <?php if($options['maxperPage']==5){echo 'selected';} ?>>5</OPTION>
		<OPTION VALUE="10" <?php if($options['maxperPage']==10){echo 'selected';} ?>>10</OPTION>
		<OPTION VALUE="20" <?php if($options['maxperPage']==20){echo 'selected';} ?>>20</OPTION>
		<OPTION VALUE="30" <?php if($options['maxperPage']==30){echo 'selected';} ?>>30</OPTION>
		<OPTION VALUE="40" <?php if($options['maxperPage']==40){echo 'selected';} ?>>40</OPTION>
		<OPTION VALUE="50" <?php if($options['maxperPage']==50){echo 'selected';} ?>>50</OPTION>
		<OPTION VALUE="100" <?php if($options['maxperPage']==100){echo 'selected';} ?>>100</OPTION>
		</SELECT></p>

		</span>


		<p><label class='o_textinput' for='pag'><?php _e("Do you want to use Pagination or Load More to show the results?", 'wp-rss-multi-importer')?></label>
		<SELECT NAME="rss_import_options[pag]" id="pagination">
		<OPTION VALUE="0" <?php if($options['pag']==0){echo 'selected';} ?>><?php _e("None", 'wp-rss-multi-importer')?></OPTION>
		<OPTION VALUE="1" <?php if($options['pag']==1){echo 'selected';} ?>><?php _e("Pagination", 'wp-rss-multi-importer')?></OPTION>
		<OPTION VALUE="2" <?php if($options['pag']==2){echo 'selected';} ?>><?php _e("Load More with Click", 'wp-rss-multi-importer')?></OPTION>
	<!--	<OPTION VALUE="3" <?php if($options['pag']==3){echo 'selected';} ?>><?php _e("Load More with Scroll", 'wp-rss-multi-importer')?></OPTION>-->

		</SELECT> </p>



		<span id="pag_options" <?php if($options['pag']==0 ){echo 'style="display:none"';}?>>

			<p style="padding-left:15px"><label class='o_textinput' for='perPage'><?php _e("Number of Posts per Page for Pagination or per click on Load More", 'wp-rss-multi-importer')?></label>
			<SELECT NAME="rss_import_options[perPage]">
			<OPTION VALUE="3" <?php if($options['perPage']==3){echo 'selected';} ?>>3</OPTION>
			<OPTION VALUE="6" <?php if($options['perPage']==6){echo 'selected';} ?>>6</OPTION>
			<OPTION VALUE="12" <?php if($options['perPage']==12){echo 'selected';} ?>>12</OPTION>
			<OPTION VALUE="15" <?php if($options['perPage']==15){echo 'selected';} ?>>15</OPTION>
			<OPTION VALUE="20" <?php if($options['perPage']==20){echo 'selected';} ?>>20</OPTION>
			<OPTION VALUE="25" <?php if($options['perPage']==25){echo 'selected';} ?>>25</OPTION>
			<OPTION VALUE="30" <?php if($options['perPage']==30){echo 'selected';} ?>>30</OPTION>
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
<p style="padding-left:15px"><label class='o_textinput' for='noFollow'>Set links as No Follow.  <input type="checkbox" Name="rss_import_options[noFollow]" Value="1" <?php if ($options['noFollow']==1){echo 'checked="checked"';} ?>></label></p>





<h3><?php _e("What Shows - Attribution", 'wp-rss-multi-importer')?></h3>



<p><label class='o_textinput' for='sourcename'><?php _e("Feed Source Attribution Label", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_import_options[sourcename]">
<OPTION VALUE="Source:" <?php if($options['sourcename']=='Source'){echo 'selected';} ?>><?php _e("Source", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="Via:" <?php if($options['sourcename']=='Via'){echo 'selected';} ?>><?php _e("Via", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="Club:" <?php if($options['sourcename']=='Club'){echo 'selected';} ?>><?php _e("Club", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="Sponsor:" <?php if($options['sourcename']=='Sponsor'){echo 'selected';} ?>><?php _e("Sponsor", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="" <?php if($options['sourcename']==''){echo 'selected';} ?>><?php _e("No Attribution", 'wp-rss-multi-importer')?></OPTION>
</SELECT></p>

<p ><label class='o_textinput' for='addAuthor'><?php _e("Show Feed or Author Name (if available)", 'wp-rss-multi-importer')?>   <input type="checkbox" Name="rss_import_options[addAuthor]" Value="1" <?php if (isset($options['addAuthor']) && $options['addAuthor']==1){echo 'checked="checked"';} ?>></label></p>



<h3><?php _e("What Shows - EXCERPTS", 'wp-rss-multi-importer')?></h3>

<p><label class='o_textinput' for='showdesc'><?php _e("<b>Show Excerpt</b>", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_import_options[showdesc]" id="showdesc">
<OPTION VALUE="1" <?php if($options['showdesc']==1){echo 'selected';} ?>><?php _e("Yes", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="0" <?php if($options['showdesc']==0){echo 'selected';} ?>><?php _e("No", 'wp-rss-multi-importer')?></OPTION>
</SELECT></p>

<p style="padding-left:15px"><label class='o_textinput' for='showcategory'><?php _e("Show Category Name", 'wp-rss-multi-importer')?>   <input type="checkbox" Name="rss_import_options[showcategory]" Value="1" <?php if (isset($options['showcategory']) && $options['showcategory']==1){echo 'checked="checked"';} ?>></label></p>


<span id="secret" <?php if($options['showdesc']==0){echo 'style="display:none"';}?>>
	
		<p style="padding-left:15px"><label class='o_textinput' for='stripSome'><?php _e("Keep limited html in the excerpt (&lt;a&gt;,&lt;br&gt;,&lt;p&gt;)  ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_import_options[stripSome]" Value="1" <?php if (isset($options['stripSome']) && $options['stripSome']==1){echo 'checked="checked"';} ?>></label> </p>
	
	<p style="padding-left:15px"><label class='o_textinput' for='showmore'><?php _e("Let your readers determine if they want to see the excerpt with a show-hide option. ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_import_options[showmore]" Value="1" <?php if (isset($options['showmore']) && $options['showmore']==1){echo 'checked="checked"';} ?>></label>
	</p>	
	
	
<p style="padding-left:15px"><label class='o_textinput' for='descnum'><?php _e("Excerpt length (number of words)", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_import_options[descnum]" id="descnum">
<OPTION VALUE="0" <?php if($options['descnum']==0){echo 'selected';} ?>>0</OPTION>
<OPTION VALUE="20" <?php if($options['descnum']==20){echo 'selected';} ?>>20</OPTION>
<OPTION VALUE="30" <?php if($options['descnum']==30){echo 'selected';} ?>>30</OPTION>
<OPTION VALUE="50" <?php if($options['descnum']==50){echo 'selected';} ?>>50</OPTION>
<OPTION VALUE="75" <?php if($options['descnum']==75){echo 'selected';} ?>>75</OPTION>
<OPTION VALUE="100" <?php if($options['descnum']==100){echo 'selected';} ?>>100</OPTION>
<OPTION VALUE="200" <?php if($options['descnum']==200){echo 'selected';} ?>>200</OPTION>
<OPTION VALUE="300" <?php if($options['descnum']==300){echo 'selected';} ?>>300</OPTION>
<OPTION VALUE="400" <?php if($options['descnum']==400){echo 'selected';} ?>>400</OPTION>
<OPTION VALUE="500" <?php if($options['descnum']==500){echo 'selected';} ?>>500</OPTION>
<OPTION VALUE="1000" <?php if($options['descnum']==1000){echo 'selected';} ?>>1000</OPTION>
<OPTION VALUE="99" <?php if($options['descnum']==99){echo 'selected';} ?>><?php _e("Give me everything", 'wp-rss-multi-importer')?></OPTION>
</SELECT><?php _e("  Note: Choosing Give me everything will just be a pure extract of the content without any image processsing, etc.", 'wp-rss-multi-importer')?></p>


<h3><?php _e("Image Handling", 'wp-rss-multi-importer')?></h3>

<?php

if (ini_get('allow_url_fopen')==0){
echo 'You server is not configured to accept images from outside sources.  Please contact your web host to set allow_url_fopen to ON.  You might be able to do this for yourself if your host gives you a way to edit the php.ini file.';	
}
?>

<p><?php _e("An attempt will be made to select an image for your post.  Usually this is the first image in the content or in a feed enclosure, but you have the option - if those are not available - to get the first image in the content.", 'wp-rss-multi-importer')?>
<p><label class='o_textinput' for='stripAll'><?php _e("Check to get rid of all images in the excerpt.", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_import_options[stripAll]" Value="1" <?php if (isset($options['stripAll']) && $options['stripAll']==1){echo 'checked="checked"';} ?>></label>
</p>


<p><?php _e("You can adjust the image, if it exists.  The default image size is 150px width, but you can override this using the shortcode.  Note:  If the image in the RSS feed is small, this may result in rendering a fuzzy image.", 'wp-rss-multi-importer')?></p>
<p style="padding-left:15px"><label class='o_textinput' for='adjustImageSize'><?php _e("If you want excerpt images, check to fix their width at 150 (can be over-written in shortcode).", 'wp-rss-multi-importer')?>  <input type="checkbox" Name="rss_import_options[adjustImageSize]" Value="1" <?php if ($options['adjustImageSize']==1){echo 'checked="checked"';} ?>></label></p>
	
<p style="padding-left:15px"><label class='o_textinput' for='floatType'><?php _e("Float images to the left (can be over-written in shortcode).", 'wp-rss-multi-importer')?>  <input type="checkbox" Name="rss_import_options[floatType]" Value="1" <?php if ($options['floatType']==1){echo 'checked="checked"';} ?>></label></p>
</span>


	<p style="padding-left:15px"><label class='o_textinput' for='RSSdefaultImage'><?php _e("Default category image setting", 'wp-rss-multi-importer')?></label>
	<SELECT NAME="rss_import_options[RSSdefaultImage]" id="RSSdefaultImage">
	<OPTION VALUE="0" <?php if($options['RSSdefaultImage']==0){echo 'selected';} ?>>Process normally</OPTION>
	<OPTION VALUE="1" <?php if($options['RSSdefaultImage']==1){echo 'selected';} ?>>Use default image for category</OPTION>
	<OPTION VALUE="2" <?php if($options['RSSdefaultImage']==2){echo 'selected';} ?>>Replace articles with no image with default category image</OPTION>

	</SELECT></p>




<h3><?php _e("Get Social", 'wp-rss-multi-importer')?></h3>
<p ><label class='o_textinput' for='showsocial'><?php _e("Add social icons (Twitter, Facebook, and Google+) to each post. ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_import_options[showsocial]" Value="1" <?php if ($options['showsocial']==1){echo 'checked="checked"';} ?>></label>
</p>


<h3><?php _e("Warning and Conflict Handling", 'wp-rss-multi-importer')?></h3>



<p ><label class='o_textinput' for='cb'><?php _e("Check if you are having colorbox conflict problems.", 'wp-rss-multi-importer')?>   <input type="checkbox" Name="rss_import_options[cb]" Value="1" <?php if (isset($options['cb']) && $options['cb']==1){echo 'checked="checked"';} ?>></label></p>


<p ><label class='o_textinput' for='warnmsg'><?php _e("If you want to suppress warning messages, do this on the Global Settings Page.", 'wp-rss-multi-importer')?></label></p>

<p ><label class='o_textinput' for='directFetch'><?php _e("Check if you are having simplepie conflict problems.", 'wp-rss-multi-importer')?>   <input type="checkbox" Name="rss_import_options[directFetch]" Value="1" <?php if (isset($options['directFetch']) && $options['directFetch']==1){echo 'checked="checked"';} ?>></label></p>


<input   size='10' name='rss_import_options[plugin_version]' type='hidden' value='<?php echo WP_RSS_MULTI_VERSION ?>' />

</div></div>

       <p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>
<!--
<div style="margin-bottom:20px">
	<button type="button" name="getFeedsNow" id="getFeeds-Now" value=""><?php _e("CLICK TO FETCH FEED ITEMS NOW", 'wp-rss-multi-importer')?></button>	
	<div id="gfnote"></div>
-->

<?php

echo rssmi_show_last_feed_update();



?>
<!--</div>-->
<br>
       </form>

      <div class="postbox"></div> 


       </div>
</div>
       </div>

       <?php 

  }













function wp_rss_multi_importer_post_page() {

       ?>

			<div id="icon-themes" class="icon32 icon32-posts-rssmi_feed"></div>
		
		<?php settings_errors(); ?>

       <div class="wrap">
	 <h2><?php _e("AutoPost Settings - Put Your RSS Feed Items Into Blog Posts", 'wp-rss-multi-importer')?></h2>
	<div id="poststuff">


       <div id="options">

       <form action="options.php" method="post"  >            

       <?php

      settings_fields('wp_rss_multi_importer_post_options');
      $post_options = get_option('rss_post_options');    

       ?>


<div class="postbox">
<h3><label for="title"><?php _e("AutoPost Options Settings", 'wp-rss-multi-importer')?></label></h3>

<div class="inside">

<h3><?php _e("Activation and Post Type Settings", 'wp-rss-multi-importer')?></h3>



<p><label class='o_textinput' for='active'><?php _e("Check to Activate this Feature", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_post_options[active]" Value="1" <?php if (isset($post_options['active']) && $post_options['active']==1){echo 'checked="checked"';} ?>></label><?php if (isset($post_options['active']) && $post_options['active']!=1){echo "   <span style=\"color:red\">This feature is not active</span>";}?>
</p>
<?php
if (isset($post_options['active']) && $post_options['active']==1){
wp_rss_multi_deactivation(1);
wp_rss_multi_activation();
}else{	
wp_rss_multi_deactivation(1);
}
?>

<p><label class='o_textinput' for='fetch_schedule'><?php _e("How often to import feeds (NOTE: Importing of feeds comes from the feed item database.  Make sure that database is updated to get the most recent feeds.)", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[fetch_schedule]" id="post_status">
<OPTION VALUE="2" <?php if($post_options['fetch_schedule']=="2"){echo 'selected';} ?>>Every 10 Min.</OPTION>
<OPTION VALUE="3" <?php if($post_options['fetch_schedule']=="3"){echo 'selected';} ?>>Every 15 Min.</OPTION>
<OPTION VALUE="4" <?php if($post_options['fetch_schedule']=="4"){echo 'selected';} ?>>Every 20 Min.</OPTION>
<OPTION VALUE="5" <?php if($post_options['fetch_schedule']=="5"){echo 'selected';} ?>>Every 30 Min.</OPTION>
<OPTION VALUE="1" <?php if($post_options['fetch_schedule']=="1"){echo 'selected';} ?>>Hourly</OPTION>
<OPTION VALUE="6" <?php if($post_options['fetch_schedule']=="6"){echo 'selected';} ?>>Every Two Hours</OPTION>
<OPTION VALUE="7" <?php if($post_options['fetch_schedule']=="7"){echo 'selected';} ?>>Every Four Hours</OPTION>
<OPTION VALUE="12" <?php if($post_options['fetch_schedule']=="12"){echo 'selected';} ?>>Twice Daily</OPTION>
<OPTION VALUE="24" <?php if($post_options['fetch_schedule']=="24"){echo 'selected';} ?>>Daily</OPTION>
<OPTION VALUE="168" <?php if($post_options['fetch_schedule']=="168"){echo 'selected';} ?>>Weekly</OPTION>
</SELECT></p>



<p><label class='o_textinput' for='post_status'><?php _e("Default status of posts", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[post_status]" id="post_status">
<OPTION VALUE="draft" <?php if($post_options['post_status']=="draft"){echo 'selected';} ?>>draft</OPTION>
<OPTION VALUE="publish" <?php if($post_options['post_status']=="publish"){echo 'selected';} ?>>publish</OPTION>
<OPTION VALUE="pending" <?php if($post_options['post_status']=="pending"){echo 'selected';} ?>>pending</OPTION>
<OPTION VALUE="future" <?php if($post_options['post_status']=="future"){echo 'selected';} ?>>future</OPTION>
<OPTION VALUE="private" <?php if($post_options['post_status']=="private"){echo 'selected';} ?>>private</OPTION>
</SELECT></p>


<p><label class='o_textinput' for='post_format'><?php _e("Default post format", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[post_format]" id="post_format">
<OPTION VALUE="standard" <?php if($post_options['post_format']=="standard"){echo 'selected';} ?>>Standard</OPTION>
<OPTION VALUE="aside" <?php if($post_options['post_format']=="aside"){echo 'selected';} ?>>Aside</OPTION>
<OPTION VALUE="gallery" <?php if($post_options['post_format']=="gallery"){echo 'selected';} ?>>Gallery</OPTION>
<OPTION VALUE="link" <?php if($post_options['post_format']=="link"){echo 'selected';} ?>>Link</OPTION>
<OPTION VALUE="image" <?php if($post_options['post_format']=="image"){echo 'selected';} ?>>Image</OPTION>
<OPTION VALUE="quote" <?php if($post_options['post_format']=="quote"){echo 'selected';} ?>>Quote</OPTION>
<OPTION VALUE="status" <?php if($post_options['post_format']=="status"){echo 'selected';} ?>>Status</OPTION>
<OPTION VALUE="video" <?php if($post_options['post_format']=="video"){echo 'selected';} ?>>Video</OPTION>
</SELECT></p>



<p ><label class='o_textinput' for='custom_type_name'><?php _e("Post as a custom type", 'wp-rss-multi-importer')?>   <input  id='custom_type_name' type="text" size='20' Name="rss_post_options[custom_type_name]" Value="<?php echo $post_options['custom_type_name'] ?>">(provide the custom type name - if left blank, no custom type will be used)</label></p>


<p ><label class='o_textinput' for='plugindelete'><span style="color:red"><?php _e("IMPORTANT: Check to delete all posts and featured images created by this plugin if this plugin is deleted (Note: this will not delete posts that have a custom type name you created above) ", 'wp-rss-multi-importer')?></span><input type="checkbox" Name="rss_post_options[plugindelete]" Value="1" <?php if (isset($post_options['plugindelete']) && $post_options['plugindelete']==1){echo 'checked="checked"';} ?>></label>
</p>


<h3><?php _e("Post Time Settings", 'wp-rss-multi-importer')?></h3>
<p><label class='o_textinput' for='overridedate'><?php _e("Check to over-ride the posts date/time with the current date and time   ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_post_options[overridedate]" Value="1" <?php if (isset($post_options['overridedate']) && $post_options['overridedate']==1){echo 'checked="checked"';} ?>></label>
</p>

<p ><label class='o_textinput' for='timezone'><?php _e("Server Time Zone", 'wp-rss-multi-importer')?>   <input  id='timezone' type="text" size='40'  Name="rss_post_options[timezone]" Value="<?php echo $post_options['timezone'] ?>"> - <?php _e("Only fill this if your posts are showing up at the wrong time, even if the override box is checked - (<a href=\"http://www.wprssimporter.com/faqs/my-posts-are-showing-up-with-the-wrong-time//\" target=\"_blank\">Read this for what to do here</a>).", 'wp-rss-multi-importer')?> </label></p>

<h3><?php _e("Fetch Quantity Settings", 'wp-rss-multi-importer')?></h3>


<p><label class='o_textinput' for='maxfeed'><?php _e("Number of Items per Feed to Fetch<span class='vtip' title='Bring in this number of the latest posts from each feed.'>?</span>", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[maxfeed]">
<OPTION VALUE="1" <?php if($post_options['maxfeed']==1){echo 'selected';} ?>>1</OPTION>
<OPTION VALUE="2" <?php if($post_options['maxfeed']==2){echo 'selected';} ?>>2</OPTION>
<OPTION VALUE="3" <?php if($post_options['maxfeed']==3){echo 'selected';} ?>>3</OPTION>
<OPTION VALUE="4" <?php if($post_options['maxfeed']==4){echo 'selected';} ?>>4</OPTION>
<OPTION VALUE="5" <?php if($post_options['maxfeed']==5){echo 'selected';} ?>>5</OPTION>
<OPTION VALUE="10" <?php if($post_options['maxfeed']==10){echo 'selected';} ?>>10</OPTION>
<OPTION VALUE="15" <?php if($post_options['maxfeed']==15){echo 'selected';} ?>>15</OPTION>
<OPTION VALUE="20" <?php if($post_options['maxfeed']==20){echo 'selected';} ?>>20</OPTION>
<OPTION VALUE="30" <?php if($post_options['maxfeed']==30){echo 'selected';} ?>>30</OPTION>
<OPTION VALUE="40" <?php if($post_options['maxfeed']==40){echo 'selected';} ?>>40</OPTION>
<OPTION VALUE="50" <?php if($post_options['maxfeed']==50){echo 'selected';} ?>>50</OPTION>
<OPTION VALUE="60" <?php if($post_options['maxfeed']==60){echo 'selected';} ?>>60</OPTION>
<OPTION VALUE="70" <?php if($post_options['maxfeed']==70){echo 'selected';} ?>>70</OPTION>
<OPTION VALUE="80" <?php if($post_options['maxfeed']==80){echo 'selected';} ?>>80</OPTION>
<OPTION VALUE="100" <?php if($post_options['maxfeed']==100){echo 'selected';} ?>>100</OPTION>
</SELECT></p>



<p><label class='o_textinput' for='maxperfetch'><?php _e("Total Number of Entries per Fetch <span class='vtip' title='This is the total number to be fetched from all of your feeds, with each feed providing the number of items chosen above.'>?</span>(<a href=\"http://www.wprssimporter.com/faqs/how-does-the-number-of-entries-per-feed-and-page-or-fetch-work//\" target=\"_blank\">Go here to see how to set this option</a>)", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[maxperfetch]">
<OPTION VALUE="1" <?php if($post_options['maxperfetch']==1){echo 'selected';} ?>>1</OPTION>
<OPTION VALUE="2" <?php if($post_options['maxperfetch']==2){echo 'selected';} ?>>2</OPTION>
<OPTION VALUE="3" <?php if($post_options['maxperfetch']==3){echo 'selected';} ?>>3</OPTION>
<OPTION VALUE="4" <?php if($post_options['maxperfetch']==4){echo 'selected';} ?>>4</OPTION>
<OPTION VALUE="5" <?php if($post_options['maxperfetch']==5){echo 'selected';} ?>>5</OPTION>
<OPTION VALUE="10" <?php if($post_options['maxperfetch']==10){echo 'selected';} ?>>10</OPTION>
<OPTION VALUE="15" <?php if($post_options['maxperfetch']==15){echo 'selected';} ?>>15</OPTION>
<OPTION VALUE="20" <?php if($post_options['maxperfetch']==20){echo 'selected';} ?>>20</OPTION>
<OPTION VALUE="30" <?php if($post_options['maxperfetch']==30){echo 'selected';} ?>>30</OPTION>
<OPTION VALUE="40" <?php if($post_options['maxperfetch']==40){echo 'selected';} ?>>40</OPTION>
<OPTION VALUE="50" <?php if($post_options['maxperfetch']==50){echo 'selected';} ?>>50</OPTION>
<OPTION VALUE="100" <?php if($post_options['maxperfetch']==100){echo 'selected';} ?>>100</OPTION>
<OPTION VALUE="200" <?php if($post_options['maxperfetch']==200){echo 'selected';} ?>>200</OPTION>
<OPTION VALUE="300" <?php if($post_options['maxperfetch']==300){echo 'selected';} ?>>300</OPTION>
<OPTION VALUE="400" <?php if($post_options['maxperfetch']==400){echo 'selected';} ?>>400</OPTION>
<OPTION VALUE="500" <?php if($post_options['maxperfetch']==500){echo 'selected';} ?>>500</OPTION>
<OPTION VALUE="600" <?php if($post_options['maxperfetch']==600){echo 'selected';} ?>>600</OPTION>
<OPTION VALUE="700" <?php if($post_options['maxperfetch']==700){echo 'selected';} ?>>700</OPTION>
<OPTION VALUE="800" <?php if($post_options['maxperfetch']==800){echo 'selected';} ?>>800</OPTION>
<OPTION VALUE="1000" <?php if($post_options['maxperfetch']==1000){echo 'selected';} ?>>1000</OPTION>
<OPTION VALUE="2000" <?php if($post_options['maxperfetch']==2000){echo 'selected';} ?>>2000</OPTION>
</SELECT></p>


<h3><?php _e("Link Settings", 'wp-rss-multi-importer')?></h3>


<p><label class='o_textinput' for='targetWindow'><?php _e("Target Window (when link clicked, where should it open?)", 'wp-rss-multi-importer')?></label>
	<SELECT NAME="rss_post_options[targetWindow]" id="targetWindow">
	<OPTION VALUE="0" <?php if($post_options['targetWindow']==0){echo 'selected';} ?>><?php _e("Use LightBox", 'wp-rss-multi-importer')?></OPTION>
	<OPTION VALUE="1" <?php if($post_options['targetWindow']==1){echo 'selected';} ?>><?php _e("Open in Same Window", 'wp-rss-multi-importer')?></OPTION>
	<OPTION VALUE="2" <?php if($post_options['targetWindow']==2){echo 'selected';} ?>><?php _e("Open in New Window", 'wp-rss-multi-importer')?></OPTION>
	</SELECT></p>
	
	<p ><label class='o_textinput' for='titleFilter'><?php _e("Make title clickable on listing page with same settings as above <span class='vtip' title='Checking this option may result in problems if you are using the  Wordpress Custom Menu Widget - see the FAQs if this happens.'>?</span>", 'wp-rss-multi-importer')?>   <input type="checkbox" Name="rss_post_options[titleFilter]" Value="1" <?php if (isset($post_options['titleFilter']) && $post_options['titleFilter']==1){echo 'checked="checked"';} ?>></label></p>
	
	
	
	<p ><label class='o_textinput' for='readmore'><?php _e("Text to use for Read More (default is ...Read More)", 'wp-rss-multi-importer')?>   <input  id='readmore' type="text" size='18' Name="rss_post_options[readmore]" Value="<?php echo $post_options['readmore'] ?>"></label></p>
	
	
	

<h3><?php _e("Word Output Setting", 'wp-rss-multi-importer')?></h3>
<p><label class='o_textinput' for='descnum'><?php _e("Excerpt length (number of words)", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[descnum]" id="descnum">
<OPTION VALUE="0" <?php if($post_options['descnum']==0){echo 'selected';} ?>>0</OPTION>
<OPTION VALUE="20" <?php if($post_options['descnum']==20){echo 'selected';} ?>>20</OPTION>
<OPTION VALUE="30" <?php if($post_options['descnum']==30){echo 'selected';} ?>>30</OPTION>
<OPTION VALUE="50" <?php if($post_options['descnum']==50){echo 'selected';} ?>>50</OPTION>
<OPTION VALUE="75" <?php if($post_options['descnum']==75){echo 'selected';} ?>>75</OPTION>
<OPTION VALUE="100" <?php if($post_options['descnum']==100){echo 'selected';} ?>>100</OPTION>
<OPTION VALUE="200" <?php if($post_options['descnum']==200){echo 'selected';} ?>>200</OPTION>
<OPTION VALUE="300" <?php if($post_options['descnum']==300){echo 'selected';} ?>>300</OPTION>
<OPTION VALUE="400" <?php if($post_options['descnum']==400){echo 'selected';} ?>>400</OPTION>
<OPTION VALUE="500" <?php if($post_options['descnum']==500){echo 'selected';} ?>>500</OPTION>
<OPTION VALUE="1000" <?php if($post_options['descnum']==1000){echo 'selected';} ?>>1000</OPTION>
<OPTION VALUE="99" <?php if($post_options['descnum']==99){echo 'selected';} ?>><?php _e("Give me everything", 'wp-rss-multi-importer')?></OPTION>
</SELECT></p>

<h3><?php _e("Author and Source Settings", 'wp-rss-multi-importer')?></h3>
<p ><label class='o_textinput' for='addAuthor'><?php _e("Show Feed or Author Name (if available)", 'wp-rss-multi-importer')?>   <input type="checkbox" Name="rss_post_options[addAuthor]" Value="1" <?php if (isset($post_options['addAuthor']) && $post_options['addAuthor']==1){echo 'checked="checked"';} ?>></label></p>


<p ><label class='o_textinput' for='addSource'><?php _e("Show Feed Source", 'wp-rss-multi-importer')?>   <input type="checkbox" Name="rss_post_options[addSource]" Value="1" <?php if (isset($post_options['addSource']) && $post_options['addSource']==1){echo 'checked="checked"';} ?>></label></p>


<p style="padding-left:15px"><label class='o_textinput' for='sourceWords'><?php _e("Feed Source Attribution Label", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[sourceWords]">
<OPTION VALUE="1" <?php if($post_options['sourceWords']==1){echo 'selected';} ?>><?php _e("Source", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="2" <?php if($post_options['sourceWords']==2){echo 'selected';} ?>><?php _e("Via", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="3" <?php if($post_options['sourceWords']==3){echo 'selected';} ?>><?php _e("Read more here", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="4" <?php if($post_options['sourceWords']==4){echo 'selected';} ?>><?php _e("From", 'wp-rss-multi-importer')?></OPTION>
<OPTION VALUE="5" <?php if($post_options['sourceWords']==5){echo 'selected';} ?>><?php _e("Other (fill in below)", 'wp-rss-multi-importer')?></OPTION>
</SELECT></p>

<p style="padding-left:15px"><label class='o_textinput' for='sourceWords_Label'><?php _e("Your own attribution label", 'wp-rss-multi-importer')?>   <input  id='sourceWords_Label' type="text" size='12'  Name="rss_post_options[sourceWords_Label]" Value="<?php echo $post_options['sourceWords_Label'] ?>">(make sure to choose Other in drop down list)</label></p>


<p><label class='o_textinput' for='sourceAnchorText'><?php _e("Read More anchor text", 'wp-rss-multi-importer')?></label>
	<SELECT NAME="rss_post_options[sourceAnchorText]" id="sourceAnchorText">
	<OPTION VALUE="1" <?php if($post_options['sourceAnchorText']==1){echo 'selected';} ?>><?php _e("Feed Name", 'wp-rss-multi-importer')?></OPTION>
	<OPTION VALUE="2" <?php if($post_options['sourceAnchorText']==2){echo 'selected';} ?>><?php _e("Title", 'wp-rss-multi-importer')?></OPTION>
	<OPTION VALUE="3" <?php if($post_options['sourceAnchorText']==3){echo 'selected';} ?>><?php _e("Link", 'wp-rss-multi-importer')?></OPTION>
	</SELECT></p>


<h3><?php _e("HTML, Image and Video Handling", 'wp-rss-multi-importer')?></h3>


<p><label class='o_textinput' for='showVideo'><?php _e("Place video into the post when available", 'wp-rss-multi-importer')?></label>
	<input type="checkbox" Name="rss_post_options[showVideo]" Value="1" <?php if ($post_options['showVideo']==1){echo 'checked="checked"';} ?>></label>
		<?php _e("(<a href=\"http://www.wprssimporter.com/faqs/the-videos-are-not-working-on-my-site//\" target=\"_blank\">GO HERE TO READ MORE ABOUT THIS</a>", 'wp-rss-multi-importer')?>
	</p>

	<p ><label class='o_textinput' for='noProcess'><?php _e("Bring in all images without processing (this will bypass all of the images settings below - i.e., no excerpt or featured image)", 'wp-rss-multi-importer')?>  <input type="checkbox" Name="rss_post_options[noProcess]" Value="1" <?php if (isset($post_options['noProcess']) && $post_options['noProcess']==1){echo 'checked="checked"';} ?>></label></p>


<p><label class='o_textinput' for='stripAll'><?php _e("Check to get rid of all html and images in the excerpt", 'wp-rss-multi-importer')?>
	<SELECT NAME="rss_post_options[stripAll]" id="stripAll">
	<OPTION VALUE="1" <?php if($post_options['stripAll']==1){echo 'selected';} ?>><?php _e("Yes", 'wp-rss-multi-importer')?></OPTION>
	<OPTION VALUE="0" <?php if($post_options['stripAll']==0){echo 'selected';} ?>><?php _e("No", 'wp-rss-multi-importer')?></OPTION>
	</SELECT>
</p>







<span id="stripAllsecret" <?php if($post_options['stripAll']==1){echo 'style="display:none"';}?>>
	
		<p ><label class='o_textinput' for='floatType'><?php _e("Float images to the left.", 'wp-rss-multi-importer')?>  <input type="checkbox" Name="rss_post_options[floatType]" Value="1" <?php if ($post_options['floatType']==1){echo 'checked="checked"';} ?>></label></p>
		
	<p><label class='o_textinput' for='stripSome'><?php _e("Keep limited html in the excerpt (&lt;a&gt;,&lt;br&gt;,&lt;p&gt;)  ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_post_options[stripSome]" Value="1" <?php if (isset($post_options['stripSome']) && $post_options['stripSome']==1){echo 'checked="checked"';} ?>></label> </p>

<p><label class='o_textinput' for='maximgwidth'><?php _e("Maximum width size of images", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[maximgwidth]">
<OPTION VALUE="100" <?php if($post_options['maximgwidth']==100){echo 'selected';} ?>>100px</OPTION>
<OPTION VALUE="150" <?php if($post_options['maximgwidth']==150){echo 'selected';} ?>>150px</OPTION>
<OPTION VALUE="250" <?php if($post_options['maximgwidth']==250){echo 'selected';} ?>>250px</OPTION>
<OPTION VALUE="350" <?php if($post_options['maximgwidth']==350){echo 'selected';} ?>>350px</OPTION>
<OPTION VALUE="500" <?php if($post_options['maximgwidth']==500){echo 'selected';} ?>>500px</OPTION>
<OPTION VALUE="600" <?php if($post_options['maximgwidth']==600){echo 'selected';} ?>>600px</OPTION>
<OPTION VALUE="700" <?php if($post_options['maximgwidth']==700){echo 'selected';} ?>>700px</OPTION>
<OPTION VALUE="999" <?php if($post_options['maximgwidth']==999){echo 'selected';} ?>><?php _e("unrestricted", 'wp-rss-multi-importer')?></OPTION>
</SELECT></p>

<p ><label class='o_textinput' for='RSSdefaultImage'><?php _e("Default category image setting", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[RSSdefaultImage]" id="RSSdefaultImage">
<OPTION VALUE="0" <?php if($post_options['RSSdefaultImage']==0){echo 'selected';} ?>>Process normally</OPTION>
<OPTION VALUE="1" <?php if($post_options['RSSdefaultImage']==1){echo 'selected';} ?>>Use default image for category</OPTION>
<OPTION VALUE="2" <?php if($post_options['RSSdefaultImage']==2){echo 'selected';} ?>>Replace articles with no image with default category image</OPTION>

</SELECT></p>




<p ><label class='o_textinput' for='setFeaturedImage'><?php _e("Select how to use the image (in excerpt and/or as the Featured Image).", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[setFeaturedImage]" id="setFeaturedImage">
<OPTION VALUE="0" <?php if($post_options['setFeaturedImage']==0){echo 'selected';} ?>>Excerpt Only</OPTION>
<OPTION VALUE="1" <?php if($post_options['setFeaturedImage']==1){echo 'selected';} ?>>Excerpt and Featured Image</OPTION>
<OPTION VALUE="2" <?php if($post_options['setFeaturedImage']==2){echo 'selected';} ?>>Featured Image Only</OPTION>
</SELECT></p>


</span>


<h3><?php _e("Get Social", 'wp-rss-multi-importer')?></h3>
<p ><label class='o_textinput' for='showsocial'><?php _e("Add social icons (Twitter, Facebook, and Google+) to each post ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_post_options[showsocial]" Value="1" <?php if (isset($post_options['showsocial']) && $post_options['showsocial']==1){echo 'checked="checked"';} ?>></label>
</p>

<h3><?php _e("Comment Status", 'wp-rss-multi-importer')?></h3>
<p ><label class='o_textinput' for='showsocial'><?php _e("Turn off comments on posts made by this plugin ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_post_options[commentstatus]" Value="1" <?php if (isset($post_options['commentstatus']) && $post_options['commentstatus']==1){echo 'checked="checked"';} ?>></label>
</p>

<h3><?php _e("Excerpt Handling", 'wp-rss-multi-importer')?></h3>
<p ><label class='o_textinput' for='includeExcerpt'><?php _e("Put the contents also in the excerpts field. ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_post_options[includeExcerpt]" Value="1" <?php if (isset($post_options['includeExcerpt']) && $post_options['includeExcerpt']==1){echo 'checked="checked"';} ?>></label>
</p>

<h3><?php _e("No Index, No Follow, Canonical ", 'wp-rss-multi-importer')?></h3>
<p ><label class='o_textinput' for='noindex'><?php _e("Make the AutoPost items not search engine visible (It is up to search engines to honor this request.). ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_post_options[noindex]" Value="1" <?php if ($post_options['noindex']==1){echo 'checked="checked"';} ?>></label>
</p>

<p ><label class='o_textinput' for='noFollow'>Set links as No Follow.  <input type="checkbox" Name="rss_post_options[noFollow]" Value="1" <?php if (isset($post_options['noFollow']) && $post_options['noFollow']==1){echo 'checked="checked"';} ?>></label></p>

<p ><label class='o_textinput' for='addcanonical'>Add canonical URL to page linking back to original article.  <input type="checkbox" Name="rss_post_options[addcanonical]" Value="1" <?php if (isset($post_options['addcanonical']) && $post_options['addcanonical']==1){echo 'checked="checked"';} ?>></label></p>


<h3><?php _e("Auto Remove Posts", 'wp-rss-multi-importer')?></h3>

<p ><label class='o_textinput' for='autoDelete'><?php _e("Check to Auto Remove Posts Created by this Plugin", 'wp-rss-multi-importer')?>   <input type="checkbox" id="autoRemoveCB" Name="rss_post_options[autoDelete]" Value="1" <?php if (isset($post_options['autoDelete']) && $post_options['autoDelete']==1){echo 'checked="checked"';} ?>></label>   (<a href="/wp-admin/admin.php?page=wprssmi_options3&tab=manage_autoposts">Manage what posts to keep here.</a>)</p>

<span id="autoremoveposts" <?php if(isset($post_options['autoDelete']) && $post_options['autoDelete']!=1){echo 'style="display:none"';}?>>

<p ><label class='o_textinput' for='expiration'><?php _e("Select the expiration time (number of days, weeks, etc.) before removing posts (NOTE:  This will cause any old items that are brought in to be immediately deleted)", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[expiration]" id="expiration">
<OPTION VALUE="1" <?php if($post_options['expiration']==1){echo 'selected';} ?>>1 Day</OPTION>
<OPTION VALUE="2" <?php if($post_options['expiration']==2){echo 'selected';} ?>>2 Days</OPTION>
<OPTION VALUE="3" <?php if($post_options['expiration']==3){echo 'selected';} ?>>3 Days</OPTION>
<OPTION VALUE="4" <?php if($post_options['expiration']==4){echo 'selected';} ?>>4 Days</OPTION>
<OPTION VALUE="5" <?php if($post_options['expiration']==5){echo 'selected';} ?>>5 Days</OPTION>
<OPTION VALUE="6" <?php if($post_options['expiration']==6){echo 'selected';} ?>>6 Days</OPTION>
<OPTION VALUE="7" <?php if($post_options['expiration']==7){echo 'selected';} ?>>7 Days</OPTION>
<OPTION VALUE="14" <?php if($post_options['expiration']==14){echo 'selected';} ?>>2 Weeks</OPTION>
<OPTION VALUE="21" <?php if($post_options['expiration']==21){echo 'selected';} ?>>3 Weeks</OPTION>
<OPTION VALUE="28" <?php if($post_options['expiration']==28){echo 'selected';} ?>>4 Weeks</OPTION>
<OPTION VALUE="56" <?php if($post_options['expiration']==56){echo 'selected';} ?>>2 Months</OPTION>
<OPTION VALUE="365" <?php if($post_options['expiration']==365){echo 'selected';} ?>>1 Year</OPTION>
</SELECT></p>

<p ><label class='o_textinput' for='oldPostStatus'><?php _e("Move posts to what status?", 'wp-rss-multi-importer')?></label>
<SELECT NAME="rss_post_options[oldPostStatus]" id="setFeaturedImage">
<OPTION VALUE="0" <?php if($post_options['oldPostStatus']==0){echo 'selected';} ?>>Permanently Delete</OPTION>
<OPTION VALUE="1" <?php if($post_options['oldPostStatus']==1){echo 'selected';} ?>>Trash (but don't permanently delete)</OPTION>
<OPTION VALUE="2" <?php if($post_options['oldPostStatus']==2){echo 'selected';} ?>>Pending</OPTION>
</SELECT><?php _e("  NOTE: Choosing Permanently Delete may result in posts being imported again", 'wp-rss-multi-importer')?></p>

<p ><label class='o_textinput' for='keepcomments'><?php _e("Only delete posts with no comments. ", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_post_options[keepcomments]" Value="1" <?php if (isset($post_options['keepcomments']) && $post_options['keepcomments']==1){echo 'checked="checked"';} ?>></label>
</p>

</span>
<?php



$catOptions= get_option( 'rss_import_categories' ); 


	if ( !empty($catOptions) ) {
		echo "<h3><label class='o_textinput' for='category'>".__('Restrict feeds to one of your defined RSS Multi Importer categories and place them in your blog categories', 'wp-rss-multi-importer')."</label></h3>";
			echo "<p>".__('Choose a plugin category and associate it with one of your blog post categories.', 'wp-rss-multi-importer')."</p>";
				
	

echo '<div class="ftpost_head">Plugin Category --></div><div class="ftpost_head">Blog Post Category</div><div style="clear:both;"></div>';	
		$catsize = count($catOptions);
		$postoptionsize= $catsize/2;




//var_dump($post_options['categoryid']['wpcatid']);

		for ( $q=1; $q<=$postoptionsize; $q++ ){
			
//echo $q."=".isEmpty($post_options['categoryid']['wpcatid'][$q])."<br>";

//if ((isset($post_options['categoryid']['wpcatid'][$q]) && isEmpty($post_options['categoryid']['wpcatid'][$q])==0) || $q==1){

		if ((isset($post_options['categoryid']['wpcatid'][$q]) && isEmpty($post_options['categoryid']['wpcatid'][$q])==0) || $q==1){
		
		
			echo "<div class='category_id_options' id='$q'>";
			$selclear=0; // added
			}else{	
			echo "<div class='category_id_options' id='$q' style='display:none'>";
			$selclear=1; // added
			}
?>



<p><span class="ftpost_l"><SELECT NAME="rss_post_options[categoryid][plugcatid][<?php echo $q ?>]">
	<?php if ($selclear==1){  // added
	?>
	<OPTION selected VALUE=''>None</OPTION>
	<?php
}
if($q==1){
	?>
<OPTION VALUE='0' <?php if($post_options['categoryid']['plugcatid'][$q]==0){echo 'selected="selected"';} ?>>ALL</OPTION>
<?php
}

	for ( $k=1; $k<=$catsize; $k++) {   

if( $k % 2== 0 ) continue;

 	$catkey = key( $catOptions );
 	$nameValue=$catOptions[$catkey];
next( $catOptions );
 	$catkey = key( $catOptions );
	$IDValue=$catOptions[$catkey];


	 if($post_options['categoryid']['plugcatid'][$q]==$IDValue && $selclear==0){  // selclear added
		$sel='selected  ';

		} else {
		$sel='';

		}

echo "<OPTION " .$sel.  "VALUE=".$IDValue.">".$nameValue."</OPTION>";
next( $catOptions );

}
echo "</SELECT></span><span class='ftpost_r'>";



//var_dump($post_options['categoryid']['wpcatid'][$q]);

echo "<SELECT multiple='multiple' size='4' id='wpcategory2' NAME='rss_post_options[categoryid][wpcatid][$q][]'>";


catDropDown($post_options['categoryid']['wpcatid'][$q]);


echo "</SELECT></span></p></div>";






reset($catOptions);

}


echo "<a href='javascript:void(0)' class='add_cat_id'>Add another plugin to blog post category association</a>";



}else{
	
	echo __("<b>NOTE: If you set up categories (in Category Options) you can restrict only feeds in that category to go into blog posts.</b> ", 'wp-rss-multi-importer');
}

?>


</div></div>

       <p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>



       </form>

<button type="button" name="fetchnow" id="fetch-now" value=""><?php _e("CLICK TO FETCH FEED ITEMS NOW", 'wp-rss-multi-importer')?></button>	
<div id="note"></div><div id="rssmi-ajax-loader-center"></div>
<?php
echo rssmi_show_last_feed_update();
?>
	
</div></div>

<!--******DELETE THIS LINE BEFORE UPLOADING****** -->
<!--
<button type="button" name="fetchdelete" id="fetch-delete" value=""><?php _e("CLICK TO DELETE FEEDS NOW", 'wp-rss-multi-importer')?></button>  
-->


</div>
<?php
}


function chk_zero_callback($val) {
    if ($val != null){
	return true;
}
}


function isEmpty_old($arr) {
	if(empty($arr)) return 1;
  foreach ($arr as $k => $v) {
    if ($v === '') {
      return 1;
    }
  }
  return 0;
}

function isEmpty($arr) {
	if(empty($arr)) return 1;
  foreach ($arr as $k => $v) {
    if ($v != '' && $v !='NULL') {
      return 0;
    }
  }
  return 1;
}

?>