<?php 
add_action( 'widgets_init', 'rssmi_src_load_widgets');  //load widget

function rssmi_src_load_widgets() {
register_widget('WP_Multi_Importer_Widget');
}

add_action('init', 'rssmi_check_widget');
function rssmi_check_widget(){
	
	 $options = get_option( 'rss_import_options' ); 
	
	$activeStatus=(isset($options['active'])?$options['active']:0 );
	
	if(is_active_widget( '', '', 'rss_multi_importer_widget') && $activeStatus!=1){
		
		wp_rss_multi_deactivation(2);
		wp_rss_multi_activation();
		}elseif (!is_active_widget( '', '', 'rss_multi_importer_widget') && $activeStatus!=1){	
		wp_rss_multi_deactivation(2);	
	}
}

    
class WP_Multi_Importer_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'rss_multi_importer_widget', // Base ID
			'RSS Multi-Importer', // Name
			array( 'description' => __( 'Use this to put RSS feeds on your site', 'text_domain' , 'wp-rss-multi-importer'), ) // Args
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget( $args, $instance ) {
		add_action('wp_footer','rssmi_footer_scripts');
		
		/* Load the excerpt functions file. */
		
		global $maximgwidth;
		$maximgwidth=100;
		$rssmi_global_options = get_option( 'rssmi_global_options' ); 
		$suppress_warnings= (isset($rssmi_global_options['suppress_warnings']) ? $rssmi_global_options['suppress_warnings'] : 0);	
		$strDate=rssmi_get_default_date_format();

		require_once ( WP_RSS_MULTI_INC . 'excerpt_functions.php' );
		
		extract( $args );
		
		$siteurl= get_site_url();

			$widget_images_url = $siteurl . '/wp-content/plugins/wp-rss-multi-importer/images';
			
			
		$title = apply_filters( 'widget_title', $instance['title'] );
		$count = $instance['numoption'];
		
		
		
		
		(array) $catArray = (isset($instance['category']) ? $instance['category'] : null);
	
		if (empty($catArray)) {
			$catArray=array("0");	
		}
		
		if(!isset($instance['category'])  || IS_NULL($instance['category'])){$instance['category']=array("0");}
		$catList=implode(', ', $instance['category']);
	
		$sortDir = $instance['checkbox'];
		$showdate = $instance['showdate'];
		$showicon = $instance['showicon'];
		$linktitle = $instance['linktitle'];
		$showdesc = $instance['showdesc'];
		$maxposts = $instance['maxposts'];
		$targetwindow= $instance['targetwindow'];
		$simplelist= $instance['simplelist'];
		$showimage= $instance['showimage'];
		$showsource=$instance['showsource'];
		$descNum=$instance['descnum'];
		
		$strDate=rssmi_get_default_date_format();
		
		global $RSSdefaultImage;
		$RSSdefaultImage=$instance['rssdefaultimage'];   // 0- process normally, 1=use default for category, 2=replace when no image available
		//$RSSdefaultImage=1;
		
		

		global $isMobileDevice;
		if (isset($isMobileDevice) && $isMobileDevice==1){  //open mobile device windows in new tab
			$targetwindow=2;

			}
		
		
		if (!empty($linktitle)){
			$title = '<a href="'.$linktitle.'">'.$title.'</a>';	
		}
		
		
		
		if ($showicon==1){
			$title=	'<img src="'.$widget_images_url.'/rss.png" width="14" height="14" style="border:0;margin-right:5px;">'.$title;
		}
		
		$addmotion = $instance['addmotion'];
		$background = $instance['background'];
		
		if($addmotion==1){
			add_action('wp_footer','widget_footer_scripts');		
		}
		
		if(!function_exists("wprssmi_hourly_feed")) {
		function wprssmi_hourly_feed() { return 3600; }
		}
	    add_filter( 'wp_feed_cache_transient_lifetime', 'wprssmi_hourly_feed' );
		
			
		if ( $targetwindow==0 ){
		add_action('wp_footer','colorbox_scripts');  // load colorbox only if not indicated as conflict
		   }
		
		if (empty( $sortDir ) ){$sortDir=0;}
	
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
	
		
		$readable = '';
	   	$options = get_option('rss_import_items','option not found');
		if (!empty($options)) {
		//	$targetwindow=(isset($options['targetWindow']) ? $options['targetWindow'] : null);
		}else{
		//	$targetwindow=2;	
		}
		


		global $wpdb;
		$myarray = array();

		
			
			if ($catList==0){
				$feedQuery="SELECT * FROM $wpdb->posts as a inner join $wpdb->postmeta as b ON a.id=b.post_id where post_type='rssmi_feed' AND post_status='publish' AND meta_key='rssmi_url'"; 
			}else{
				$feedQuery="SELECT * FROM $wpdb->posts as a inner join $wpdb->postmeta as b ON a.id=b.post_id where post_type='rssmi_feed' AND post_status='publish' AND meta_key='rssmi_cat' AND meta_value in ($catList) ";
				}
			
$feed_array=$wpdb->get_results($feedQuery);


if (empty($feed_array)){
	
//	return _e("There is a problem - it appears you are using categories and no feeds have been put into those categories.", 'wp-rss-multi-importer');

	return;
}

		foreach ($feed_array as $feed){

			$feedlimit=0;
			$rssmi_cat= get_post_meta($feed->ID, 'rssmi_cat', true );
			$rssmi_source= get_the_title( $feed->ID); 
			$catSourceArray=array(
				"myGroup"=>$rssmi_source,
				"mycatid"=>$rssmi_cat
			);
		

		
			
				
	
			$rssmi_sql = "SELECT a.post_id,b.meta_key,b.meta_value FROM $wpdb->postmeta as a inner join $wpdb->postmeta as b on a.post_id=b.post_id WHERE a.meta_value =$feed->ID and b.meta_key='rssmi_item_date' order by b.meta_value "; 

			if ($sortDir==0){
				$rssmi_sql .="desc";
			}elseif ($sortDir==1){
				$rssmi_sql .="asc";	
			}

		
		
		
		
				$desc_array = $wpdb->get_results($rssmi_sql);			
				
				
				foreach($desc_array as $arrayItem){
					$feedlimit=$feedlimit+1; if($feedlimit>$maxposts) continue;
					$post_ID=$arrayItem->post_id;
					$desc=get_post_meta($post_ID, 'rssmi_item_description', true );
					$arrayItem=array_merge ((array)$desc[0],$catSourceArray);  //  add the source and category ID
					if(include_post($rssmi_cat,$arrayItem['mydesc'],$arrayItem['mytitle'])==0) {continue;}   // FILTER 	
					array_push($myarray, $arrayItem);  //combine into final array

					}
		
				}		
				
				
				
				if (!isset($myarray) || empty($myarray)){
					if($suppress_warnings==0 && current_user_can('edit_post')){

					return _e("There is a problem with the feeds you entered.  Go to our <a href='http://www.wprssimporter.com/faqs/im-told-the-feed-isnt-valid-or-working/'>support page</a> to see how to solve this.", 'wp-rss-multi-importer');
					}
					return;
				}				
				


	

	


		global $isMobileDevice;
		if (isset($isMobileDevice) && $isMobileDevice==1){  //open mobile device windows in new tab
			$targetwindow=2;
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
		
		
		global $isMobileDevice;
			if (isset($isMobileDevice) && $isMobileDevice==1){  //open mobile device windows in new tab
			$targetwindow=2;
				}

		if($targetwindow==0){
			$openWindow='class="colorbox"';
		}elseif ($targetwindow==1){
			$openWindow='target=_self';		
		}else{
			$openWindow='target=_blank';	
		}
		
	


$total = -1;





if ($simplelist==1){

	echo '<ul class="wprssmi_widget_list">';
	

	
		foreach($myarray as $items) {
			
			
			
				// VIDEO CHECK
				if ($targetwindow==0){
					$getVideoArray=rssmi_video($items["mylink"]);
					$openWindow=$getVideoArray[1];
					$items["mylink"]=$getVideoArray[0];
				}
				
				
		$openWindow=rssmi_lightbox_filter($items["mylink"],$targetwindow); //Lightbox filter
		
	
		$total = $total +1;
			
		if ($count>0 && $total>=$count) break;
	
		echo '<li class="title"><a '.$openWindow.' href="'.$items["mylink"].'" '.($noFollow==1 ? 'rel=nofollow':'').'>'.$items["mytitle"].'</a>';
		if (!empty($items["mystrdate"])  && $showdate==1){
		echo '<span class="date">'. date_i18n($strDate,$items["mystrdate"]).'</span>';
	}
		
		echo '</li>';

	}  	//  don't mess with this php code
	
	
	
	
	
	
	echo '</ul>';	
	
	
} else{




echo ' <div class="news-wrapper" id="newsticker" style="10px;background-color:'.$background.';">';
echo '	<div class="news-contents">';

		foreach($myarray as $items) {

				// VIDEO CHECK
				if ($targetWindow==0){
					$getVideoArray=rssmi_video($items["mylink"],$targetwindow);
					$openWindow=$getVideoArray[1];
					$items["mylink"]=$getVideoArray[0];
				}
		$openWindow=rssmi_lightbox_filter($items["mylink"],$targetwindow); //Lightbox filter
			$total = $total +1;
			if ($count>0 && $total>=$count) break;




			echo '<div style="top: 101px;margin-left:5px;" class="news">';

			
			
			
			
			if($showimage==1 && $addmotion!=1){
							
	
			echo showexcerpt($items["mydesc"],0,$openWindow,0,$items["mylink"],1,"left",0,$items["myimage"],$items["mycatid"]);
			
			}
			
			echo '<div class="rssmi_title_class"><a '.$openWindow.' href="'.$items["mylink"].'" class="news_title">'.$items["mytitle"].'</a></div>';
			
		
				
			
			
			
			if ($showdesc==1 && $addmotion!=1){

							
						$desc= esc_attr(strip_tags(@html_entity_decode($items["mydesc"], ENT_QUOTES, get_option('blog_charset'))));	
						$desc= str_replace('[...]','',$desc);
						
					    $words = explode(" ",trim($desc));
						
						
					   	$desc= implode(" ",array_splice($words,0,$descNum));	
						
								
						$desc .= ' <a '.$openWindow.' href="'.$items["mylink"].'">[&hellip;]</a>';
			
							
			echo $desc.'<br/>';
			}
			
			
			
			
			
			

			if (!empty($items["mystrdate"])  && $showdate==1){
			 echo  date_i18n($strDate,$items["mystrdate"]).'<br />';
		
			
			}
				if (!empty($items["myGroup"]) && $showsource==1){
		    echo '<span class="rssmi_group_style" style="font-style:italic;">'.$items["myGroup"].'</span>';
			}
		//	 echo '</p>';
			echo '</div>';

		}

	echo '</div></div>';
		
	
	
	
	
	}
	
	
	

	
	
	
	
		
		

		echo $after_widget;	
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update( $new_instance, $old_instance ) {
		//$instance = array();
			$instance = $new_instance;
		
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['checkbox'] = strip_tags($new_instance['checkbox']);
	
		$instance['numoption'] = strip_tags($new_instance['numoption']);
		$instance['addmotion'] = strip_tags($new_instance['addmotion']);
		$instance['background'] = strip_tags($new_instance['background']);
		$instance['showdate'] = strip_tags($new_instance['showdate']);
		$instance['showicon'] = strip_tags($new_instance['showicon']);
		$instance['linktitle'] = strip_tags($new_instance['linktitle']);
		$instance['showdesc'] = strip_tags($new_instance['showdesc']);		
		$instance['maxposts'] = strip_tags($new_instance['maxposts']);	
		$instance['targetwindow'] = strip_tags($new_instance['targetwindow']);
		$instance['simplelist'] = strip_tags($new_instance['simplelist']);	
		$instance['showimage'] = strip_tags($new_instance['showimage']);	
		$instance['rssdefaultimage'] = strip_tags($new_instance['rssdefaultimage']);
		$instance['showsource'] = strip_tags($new_instance['showsource']);
		$instance['descnum'] = strip_tags($new_instance['descnum']);
	//	$instance['fetch_schedule'] = strip_tags($new_instance['fetch_schedule']);
	//	$instance['activate'] = strip_tags($new_instance['activate']);
		
		
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form( $instance ) {
		
		
		//Defaults
		$defaults = array(
			'title' => __( 'RSS Feeds', $this->textdomain, 'wp-rss-multi-importer'),
			'checkbox' => 0,
			'category' => array(),
			'exclude' => array(),
			'numoption' => 2,
			'maxposts' =>1,
			'addmotion' => 0,
		//	'activate' =>0,
			'showdate' => 1,
		//	'fetch_schedule'=>24,
			'simplelist'=>0,
			'showicon' => 0,
			'linktitle' => '',
			'targetwindow' => 0,
			'showdesc' => 0,
			'showsource'=>1,
			'rssdefaultimage' =>0,
			'showimage' => 0,
			'descnum' =>10,
			'background' => '#ffffff',
		);
		

			$instance = wp_parse_args( (array) $instance, $defaults );
		
		$rssdefaultimage=esc_attr($instance['rssdefaultimage']);
	//	$activate=esc_attr($instance['activate']);
	    $title = esc_attr($instance['title']);
		$checkbox = esc_attr($instance['checkbox']);
		$numoption = esc_attr($instance['numoption']);	
		$addmotion = esc_attr($instance['addmotion']);	
		$background = esc_attr($instance['background']);
		$showdate = esc_attr($instance['showdate']);
		$showicon = esc_attr($instance['showicon']);
		$linktitle = esc_attr($instance['linktitle']);
		$showdesc = esc_attr($instance['showdesc']);
		$maxposts = esc_attr($instance['maxposts']);
		$targetwindow = esc_attr($instance['targetwindow']);
		$simplelist= esc_attr($instance['simplelist']);
		$showimage= esc_attr($instance['showimage']);
		$showsource=esc_attr($instance['showsource']);
		$descnum=esc_attr($instance['descnum']);
	//	$fetch_schedule=esc_attr($instance['fetch_schedule']);
		settings_fields( 'wp_rss_multi_importer_categories' );
		$options = get_option('rss_import_categories' );
		
		
	//	$widgetoptions=get_option('widget_rss_multi_importer_widget');
	//	echo $widgetoptions[2]['fetch_schedule'];
		
	    ?>

		 <p>
	      	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'wp-rss-multi-importer'); ?></label>
	      	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	    </p>
	
	
		<!--
			<p>
		      	<input id="<?php echo $this->get_field_id('activate'); ?>" name="<?php echo $this->get_field_name('activate'); ?>" type="checkbox" value="1" <?php checked( '1', $activate ); ?>/>
		    	<label for="<?php echo $this->get_field_id('activate'); ?>"><?php _e('Check to activate', 'wp-rss-multi-importer'); ?></label>
		    </p>
		
			<p><label class='o_textinput' for='fetch_schedule'><?php _e("How often feeds will be updated", 'wp-rss-multi-importer')?></label>
			<SELECT NAME="<?php echo $this->get_field_name('fetch_schedule'); ?>" id="post_status">
			<OPTION VALUE="2" <?php if($fetch_schedule=="2"){echo 'selected';} ?>>Every 10 Min.</OPTION>
			<OPTION VALUE="3" <?php if($fetch_schedule=="3"){echo 'selected';} ?>>Every 15 Min.</OPTION>
			<OPTION VALUE="4" <?php if($fetch_schedule=="4"){echo 'selected';} ?>>Every 20 Min.</OPTION>
			<OPTION VALUE="5" <?php if($fetch_schedule=="5"){echo 'selected';} ?>>Every 30 Min.</OPTION>
			<OPTION VALUE="1" <?php if($fetch_schedule=="1"){echo 'selected';} ?>>Hourly</OPTION>
			<OPTION VALUE="6" <?php if($fetch_schedule=="6"){echo 'selected';} ?>>Every Two Hours</OPTION>
			<OPTION VALUE="7" <?php if($fetch_schedule=="7"){echo 'selected';} ?>>Every Four Hours</OPTION>
			<OPTION VALUE="12" <?php if($fetch_schedule=="12"){echo 'selected';} ?>>Twice Daily</OPTION>
			<OPTION VALUE="24" <?php if($fetch_schedule=="24"){echo 'selected';} ?>>Daily</OPTION>
			<OPTION VALUE="168" <?php if($fetch_schedule=="168"){echo 'selected';} ?>>Weekly</OPTION>
			</SELECT></p>
		
		-->
	
			<p>
		      	<input id="<?php echo $this->get_field_id('showicon'); ?>" name="<?php echo $this->get_field_name('showicon'); ?>" type="checkbox" value="1" <?php checked( '1', $showicon ); ?>/>
		    	<label for="<?php echo $this->get_field_id('showicon'); ?>"><?php _e('Show RSS icon', 'wp-rss-multi-importer'); ?></label>
		    </p>

			 <p>
		      	<label for="<?php echo $this->get_field_id('linktitle'); ?>"><?php _e('URL to link title to another page (optional)', 'wp-rss-multi-importer'); ?></label>
		      	<input class="widefat" id="<?php echo $this->get_field_id('linktitle'); ?>" name="<?php echo $this->get_field_name('linktitle'); ?>" type="text" value="<?php echo $linktitle; ?>" />
		    </p>
			
		
			
		<p>
				
					<label for="<?php echo $this->get_field_id('targetwindow'); ?>"><?php _e('Target Window', 'wp-rss-multi-importer'); ?></label>
					
					
						
				
				<select name="<?php echo $this->get_field_name('targetwindow'); ?>">

			
			<OPTION ID="0" VALUE="0" <?php if($targetwindow==0){echo 'selected="selected"';} ?>>Use LightBox</OPTION>
				<OPTION ID="1" VALUE="1" <?php if($targetwindow==1){echo 'selected="selected"';} ?>>Open in Same Window</OPTION>
				<OPTION ID="2" VALUE="2" <?php if($targetwindow==2){echo 'selected="selected"';} ?>>Open in New Window</OPTION>
				</SELECT>	
			</p>
			
		

		<p>
	      	<input id="<?php echo $this->get_field_id('checkbox'); ?>" name="<?php echo $this->get_field_name('checkbox'); ?>" type="checkbox" value="1" <?php checked( '1', $checkbox ); ?>/>
	    	<label for="<?php echo $this->get_field_id('checkbox'); ?>"><?php _e('Check to sort ascending', 'wp-rss-multi-importer'); ?></label>
	    </p>
	
		<p>
	      	<input id="<?php echo $this->get_field_id('showdate'); ?>" name="<?php echo $this->get_field_name('showdate'); ?>" type="checkbox" value="1" <?php checked( '1', $showdate ); ?>/>
	    	<label for="<?php echo $this->get_field_id('showdate'); ?>"><?php _e('Show date', 'wp-rss-multi-importer'); ?></label>
	    </p>

		<p>
	      	<input id="<?php echo $this->get_field_id('showdesc'); ?>" name="<?php echo $this->get_field_name('showdesc'); ?>" type="checkbox" value="1" <?php checked( '1', $showdesc ); ?>/>
	    	<label for="<?php echo $this->get_field_id('showdesc'); ?>"><?php _e('Show excerpt (will not show if scrolling)', 'wp-rss-multi-importer'); ?></label>
	    </p>
		
		
		<p>
	      	<input id="<?php echo $this->get_field_id('showimage'); ?>" name="<?php echo $this->get_field_name('showimage'); ?>" type="checkbox" value="1" <?php checked( '1', $showimage ); ?>/>
	    	<label for="<?php echo $this->get_field_id('showimage'); ?>"><?php _e('Show image (will not show if scrolling)', 'wp-rss-multi-importer'); ?></label>
	    </p>
		
		
		<p>
	      	<input id="<?php echo $this->get_field_id('showsource'); ?>" name="<?php echo $this->get_field_name('showsource'); ?>" type="checkbox" value="1" <?php checked( '1', $showsource ); ?>/>
	    	<label for="<?php echo $this->get_field_id('showsource'); ?>"><?php _e('Show feed source)', 'wp-rss-multi-importer'); ?></label>
	    </p>


		<p >	<label for="<?php echo $this->get_field_id('rssdefaultimage'); ?>"><?php _e('Default category image setting', 'wp-rss-multi-importer'); ?></label>
		<select name="<?php echo $this->get_field_name('rssdefaultimage'); ?>">	
			
		<OPTION VALUE="0" <?php if ($rssdefaultimage==0){echo 'selected';} ?>>Process normally</OPTION>
		<OPTION VALUE="1" <?php if ($rssdefaultimage==1){echo 'selected';} ?>>Only use default image</OPTION>
		<OPTION VALUE="2" <?php if ($rssdefaultimage==2){echo 'selected';} ?>>When no image, use default</OPTION>

		</SELECT></p>


		<p >	<label for="<?php echo $this->get_field_id('descnum'); ?>"><?php _e('Words in excerpt', 'wp-rss-multi-importer'); ?></label>
		<select name="<?php echo $this->get_field_name('descnum'); ?>">	
			
		<OPTION VALUE="0" <?php if ($descnum==0){echo 'selected';} ?>>None</OPTION>
		<OPTION VALUE="10" <?php if ($descnum==10){echo 'selected';} ?>>10</OPTION>
		<OPTION VALUE="20" <?php if ($descnum==20){echo 'selected';} ?>>20</OPTION>
		<OPTION VALUE="30" <?php if ($descnum==30){echo 'selected';} ?>>30</OPTION>
		<OPTION VALUE="40" <?php if ($descnum==40){echo 'selected';} ?>>40</OPTION>
		<OPTION VALUE="50" <?php if ($descnum==50){echo 'selected';} ?>>50</OPTION>
		</SELECT></p>





	
		<p>
	      	<input id="<?php echo $this->get_field_id('addmotion'); ?>" name="<?php echo $this->get_field_name('addmotion'); ?>" type="checkbox" value="1" <?php checked( '1', $addmotion ); ?>/>
	    	<label for="<?php echo $this->get_field_id('addmotion'); ?>"><?php _e('Check to add scrolling motion', 'wp-rss-multi-importer'); ?></label>
	    </p>
	
			<p>
		      	<input id="<?php echo $this->get_field_id('simplelist'); ?>" name="<?php echo $this->get_field_name('simplelist'); ?>" type="checkbox" value="1" <?php checked( '1', $simplelist ); ?>/>
		    	<label for="<?php echo $this->get_field_id('simplelist'); ?>"><?php _e('Check to get just a simple unordered list', 'wp-rss-multi-importer'); ?></label>
		    </p>
		
		
		<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Which category do you want displayed?', 'wp-rss-multi-importer'); ?></label>
			<select name="<?php echo $this->get_field_name('category'); ?>[]" id="<?php echo $this->get_field_id('category'); ?>" class="widefat" multiple="multiple">
				<option id="All" value="0" <?php echo in_array(0, (array) $instance['category'] ) ? ' selected="selected"' : ''?>>ALL CATEGORIES</option>
				<?php
					if ( !empty($options) ) {
						$size = count($options);
			
							for ( $i=1; $i<=$size; $i++ ) {   
									if( $i % 2== 0 ) continue;
										$key = key( $options );
											if ( strpos( $key, 'cat_name_' ) === 0 ) { $j = str_replace( 'cat_name_', '', $key );}
				
				$optionName=$options[$key];
				next( $options );
				 $key = key( $options );
				$optionValue=$options[$key];				
					
					echo '<option value="' . $optionValue . '" id="' . $optionName . '"', in_array( $optionValue, (array) $instance['category'] ) ? ' selected="selected"' : '', '>', $optionName, '</option>';
					
					
					
						next( $options );
				}
			}
				?>
			</select>
			<p>
					<label for="<?php echo $this->get_field_id('numoption'); ?>"><?php _e('How many total results displayed?', 'wp-rss-multi-importer'); ?></label>
					<select name="<?php echo $this->get_field_name('numoption'); ?>" id="<?php echo $this->get_field_id('numoption'); ?>" class="widefat">
						<?php
						$myoptions = array('2','3','4','5','6','7', '8', '10', '15','20','30','40','50');
						foreach ($myoptions as $myoption) {
							echo '<option value="' . $myoption . '" id="' . $myoption . '"', $numoption == $myoption ? ' selected="selected"' : '', '>', $myoption, '</option>';
						}
						?>
					</select>
				</p>
				
				
				
				<p>
						<label for="<?php echo $this->get_field_id('maxposts'); ?>"><?php _e('How many posts per feed?', 'wp-rss-multi-importer'); ?></label>
						<select name="<?php echo $this->get_field_name('maxposts'); ?>" id="<?php echo $this->get_field_id('maxposts'); ?>" class="widefat">
							<?php
							$postoptions = array('1','2', '3', '4', '5','6');
							foreach ($postoptions as $postoption) {
								echo '<option value="' . $postoption . '" id="' . $postoption . '"', $maxposts == $postoption ? ' selected="selected"' : '', '>', $postoption, '</option>';
							}
							?>
						</select>
					</p>
					<script type="text/javascript">
								//<![CDATA[
									jQuery(document).ready(function()
									{
										// colorpicker field
										jQuery('.cw-color-picker').each(function(){
											var $this = jQuery(this),
												id = $this.attr('rel');
												
													try
													{
														$this.farbtastic('#' + id);
													}
													catch(ex){ }

										
										});

									});
								//]]>   
							  </script>
				<p>
					 <label for="<?php echo $this->get_field_id('background'); ?>"><?php _e('Background Color:', 'wp-rss-multi-importer'); ?></label> 
					 <input class="widefat" id="<?php echo $this->get_field_id('background'); ?>" name="<?php echo $this->get_field_name('background'); ?>" type="text" value="<?php if($background) { echo $background; } else { echo '#cccccc'; } ?>" />
					<div class="cw-color-picker" rel="<?php echo $this->get_field_id('background'); ?>"></div>
							
					        </p>
					  
			
				
			
		<?php 
	}

} // class WP_Multi_Importer_Widget


?>