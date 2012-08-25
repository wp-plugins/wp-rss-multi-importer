<?php 

    
class WP_Multi_Importer_Widget extends WP_Widget {

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'rss_multi_importer_widget', // Base ID
			'RSS Multi-Importer', // Name
			array( 'description' => __( 'Use this to put RSS feeds on your site', 'text_domain' ), ) // Args
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
		add_action('wp_footer','footer_scripts');
		extract( $args );
		$title = apply_filters( 'widget_title', $instance['title'] );
		
		$count = $instance['numoption'];
		$categoryID = $instance['category'];
		$sortDir = $instance['checkbox'];
		if (empty( $sortDir ) ){$sortDir=0;}
	
		echo $before_widget;
		if ( ! empty( $title ) )
			echo $before_title . $title . $after_title;
	
		
		$readable = '';
	   	$options = get_option('rss_import_items','option not found');
	
	
	
		$cat_array = preg_grep("^feed_cat_^", array_keys($options));

			if (count($cat_array)==0) {  //for backward compatibility
				$noExistCat=1;
			}else{
				$noExistCat=0;	
			}
	
	
		$size = count($options);
		$targetWindow=$options['targetWindow']; 
		
	
	//	$sortDir=$options['sortbydate'];
		//$sortDir=0;
		
		for ($i=1;$i<=$size;$i=$i+1){



		   			$key =key($options);
						if ( !strpos( $key, '_' ) > 0 ) continue; //this makes sure only feeds are included here...everything else are options

		   			$rssName= $options[$key];


		   			next($options);

		   			$key =key($options);

		   			$rssURL=$options[$key];



		  	next($options);
			$key =key($options);




		if ((($categoryID>0 && $options[$key]==$categoryID))|| $categoryID==0 || $noExistCat==1) {

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




		if(!function_exists("wprssmi_hourly_feed")) {
		function wprssmi_hourly_feed() { return 3600; }
		}


	

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

				for ($i=$maxfeed;$i>=$maxfeed-$count;$i--){
					$item = $feed->get_item($i);
					 if (empty($item))	continue;

						$myarray[] = array("mystrdate"=>strtotime($item->get_date()),"mytitle"=>$item->get_title(),"mylink"=>$item->get_link(),"myGroup"=>$feeditem["FeedName"],"mydesc"=>$item->get_description());
					}

				}else{	

				for ($i=0;$i<=$count-1;$i++){
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



		foreach($myarray as $items) {


		
			$total = $total +1;
			if ($count>0 && $total>=$count) break;





			echo '<p class="rss-output"><a '.$openWindow.' href='.$items["mylink"].'>'.$items["mytitle"].'</a><br />';


			if (!empty($items["mystrdate"])){
			 echo  date("D, M d, Y",$items["mystrdate"]).'<br />';
			}
				if (!empty($items["myGroup"])){
		     echo '<span style="font-style:italic;">'.$attribution.''.$items["myGroup"].'</span>';
			}
			 echo '</p>';


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
		$instance = array();
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['category'] = strip_tags($new_instance['category']);
		$instance['checkbox'] = strip_tags($new_instance['checkbox']);
		$instance['numoption'] = strip_tags($new_instance['numoption']);
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
		
	    $title = esc_attr($instance['title']);
		$checkbox = esc_attr($instance['checkbox']);
		$category = esc_attr($instance['category']);
		$numoption = esc_attr($instance['numoption']);	
		settings_fields( 'wp_rss_multi_importer_categories' );
		$options = get_option('rss_import_categories' );
		
	    ?>

		 <p>
	      	<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title'); ?></label>
	      	<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
	    </p>

	

		<p>
	      	<input id="<?php echo $this->get_field_id('checkbox'); ?>" name="<?php echo $this->get_field_name('checkbox'); ?>" type="checkbox" value="1" <?php checked( '1', $checkbox ); ?>/>
	    	<label for="<?php echo $this->get_field_id('checkbox'); ?>"><?php _e('Check to sort ascending'); ?></label>
	    </p>

	

		<p>
			<label for="<?php echo $this->get_field_id('category'); ?>"><?php _e('Which categories do you want displayed?'); ?></label>
			<select name="<?php echo $this->get_field_name('category'); ?>" id="<?php echo $this->get_field_id('category'); ?>" class="widefat">
				<option id="All" value="0">ALL CATEGORIES</option>
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

				
					echo '<option value="' . $optionValue . '" id="' . $optionName . '"', $category == $optionValue ? ' selected="selected"' : '', '>', $optionName, '</option>';
						next( $options );
				}
			}
				?>
			</select>
			<p>
					<label for="<?php echo $this->get_field_id('numoption'); ?>"><?php _e('How many results displayed?'); ?></label>
					<select name="<?php echo $this->get_field_name('numoption'); ?>" id="<?php echo $this->get_field_id('numoption'); ?>" class="widefat">
						<?php
						$myoptions = array('2','5', '8', '10');
						foreach ($myoptions as $myoption) {
							echo '<option value="' . $myoption . '" id="' . $myoption . '"', $numoption == $myoption ? ' selected="selected"' : '', '>', $myoption, '</option>';
						}
						?>
					</select>
				</p>
		</p>
		<?php 
	}

} // class WP_Multi_Importer_Widget


?>