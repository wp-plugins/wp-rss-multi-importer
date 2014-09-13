<?php
function wp_rss_multi_importer_feed_page() {

       ?>
		<div id="icon-themes" class="icon32 icon32-posts-rssmi_feed"></div>
	<h2><?php  _e("Multi-Importer", 'wp-rss-multi-importer')?></h2>
       <div class="wrap">
	  <h2><?php _e("Export Feeds", 'wp-rss-multi-importer')?></h2>
	<div id="poststuff">


       <div id="options">

       <form action="options.php" method="post"  >            

       <?php

      settings_fields('wp_rss_multi_importer_feed_options');
      $options = get_option('rss_feed_options');    

       ?>


<div class="postbox">
	
<div class="inside">


<h2><?php _e("Export Your Feeds as an RSS Feed", 'wp-rss-multi-importer')?></h2>
<h3><?php _e("Export Feed Options Settings", 'wp-rss-multi-importer')?></h3>
<p><?php _e("You can re-export your feeds as an RSS feed for your readers.  You configure some options for this feed here.", 'wp-rss-multi-importer')?></p>

<p><label class='o_textinput' for='feedtitle'><?php _e("Feed Title", 'wp-rss-multi-importer')?></label>

<input id="feedtitle" type="text" value="<?php echo $options['feedtitle']?>" name="rss_feed_options[feedtitle]"></p>

<p><label class='o_textinput' for='feedslug'><?php _e("Feed Slug", 'wp-rss-multi-importer')?></label>

<input id="feedslug" size="10" type="text" value="<?php echo $options['feedslug']?>" name="rss_feed_options[feedslug]"> <?php _e("(no spaces are allowed!  See what a slug is below)", 'wp-rss-multi-importer')?></p>

<p><label class='o_textinput' for='feeddesc'><?php _e("Feed Description", 'wp-rss-multi-importer')?></label>

<input id="feeddesc" type="text" value="<?php echo $options['feeddesc']?>" name="rss_feed_options[feeddesc]" size="50"></p>

<p><label class='o_textinput' for='striptags'><?php _e("Check to get rid of all images in the feed output.", 'wp-rss-multi-importer')?><input type="checkbox" Name="rss_feed_options[striptags]" Value="1" <?php if (isset($options['striptags']) && $options['striptags']==1){echo 'checked="checked"';} ?></label>
</p>

       <p class="submit"><input type="submit" value="Save Settings" name="submit" class="button-primary"></p>

       </form>

	<?php
	$url=site_url();
	if (!empty($options['feedslug'])){

	echo "<h3>". __("Your RSS feed is here:", 'wp-rss-multi-importer'). "<br><br><a href=".$url."?feed=".$options['feedslug']." target='_blank'>".$url."?feed=".$options['feedslug']."</a></h3>";

	
		echo "<p>". __("To activate this feature, you may need to save your permalinks again by going to Settings -> Permalinks and clicking Save Changes.", 'wp-rss-multi-importer'). "</p>";
	}else{
		
		echo "<h3>". __("Your RSS feed is here:", 'wp-rss-multi-importer')." <br><br>".$url."?feed=[this is your slug]</h3>";
	}

	?>


</div></div>

<div class="postbox">
	
<div class="inside">

  <h2><?php _e("Export Your Feeds as a Text File", 'wp-rss-multi-importer')?></h2>
<p><?php _e("Export a text file with all of the feed URLS you have stored in this plugin", 'wp-rss-multi-importer')?> </p>
	<form method="post">
      <p class="submit">
	

    
          <input type="submit" name="rssmi_export_feeds" value="<?php _e( 'Export Your Feeds', 'wp-rss-multi-importer' ); ?>"  class="button" />
	Check to include all fields (title, URL, Category, User)<input type="checkbox" Name="rssmi_allInfo" Value="1">
      </p>
  </form>
</div></div>

</div></div>
<?php
}







function wp_rssmi_download_feed_stream($i){

	$rssmi_url_download='';
	global $wpdb;
	$myarray = array();
	$feed_array=$wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE  post_type ='rssmi_feed' AND post_status='publish'");
	foreach ($feed_array as $feed){ 
		
		if ($i==1){
			
			$rssmi_url_download .= get_the_title( $feed->ID).","; 
			$rssmi_url_download .= get_post_meta($feed->ID, 'rssmi_url', true ).","; 
			$rssmi_url_download .= get_post_meta($feed->ID, 'rssmi_cat', true ).",";
			$rssmi_url_download .= get_post_meta($feed->ID, 'rssmi_user', true );	
		}else{
			$rssmi_url_download .= get_post_meta($feed->ID, 'rssmi_url', true ); 
		}
		
		$rssmi_url_download .="\n" ;
	
	}
	echo $rssmi_url_download;
	die();	
}

add_action( 'admin_init', 'wp_rssmi_download_feeds', 1 );


function wp_rssmi_download_feeds() {
    if ( isset( $_POST['rssmi_export_feeds'] ) ) {  //watch for post
	$i=((isset($_POST['rssmi_allInfo']) && $_POST['rssmi_allInfo']==1)? 1:0);
        $file_name = "feeds.txt";
        header( 'Content-Description: File Transfer' );
        header( "Content-Type: text/plain; charset=" . get_option( 'blog_charset' ) );
        header( "Content-Disposition: attachment; filename=$file_name." );
        wp_rssmi_download_feed_stream($i);
        die();
    }
}



?>