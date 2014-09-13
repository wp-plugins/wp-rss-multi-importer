<?php
/*
Feed Item Imports - The code on this page was adapted and expanded from an earlier version of WP RSS Aggregator by jeangalea
Copyright 2012-2014 Jean Galea (email : info@jeangalea.com)
*/

function rssmi_fetch_all_feed_items( ) {     
	
	$rssmi_global_options = get_option('rssmi_global_options'); 
	$single_feed_max=(isset($rssmi_global_options['single_feed_max']) ? $rssmi_global_options['single_feed_max'] : 20);
	$noDirectFetch=(isset($rssmi_global_options['noForcedFeed']) ? $rssmi_global_options['noForcedFeed'] : 0);
	$timeout=20;
	$forceFeed=true;
	$showVideo=1;
	
        // Get all feed sources
        $feed_sources = new WP_Query( array(
            'post_type'      => 'rssmi_feed',
            'post_status'    => 'publish',
            'posts_per_page' => -1,
        ) );

        if( $feed_sources->have_posts() ) {
            /* Get feed sources*/
            while ( $feed_sources->have_posts() ) {     
	            $didUpdate=0;
                $feed_sources->the_post();
                $feed_ID = get_the_ID();
                $feed_url = get_post_meta( get_the_ID(), 'rssmi_url', true );
				$feed_cat = get_post_meta( get_the_ID(), 'rssmi_cat', true );
				$rssmi_user= get_post_meta(get_the_ID(), 'rssmi_user', true );
				$rssmi_title= get_the_title(get_the_ID());
                    
                // Use the URL custom field to fetch the feed items for this source
                if( !empty( $feed_url ) ) {    
					
							$url = esc_url_raw(strip_tags($feed_url));
	
							if ($noDirectFetch==1){
								$feed = fetch_feed($url);
							}else{
								$feed = wp_rss_fetchFeed($url,$timeout,$forceFeed,$showVideo);	
							}
					
					         
                    if ( !is_wp_error( $feed ) ) {
                        // Limit items it to 10. 
                        $maxitems = $feed->get_item_quantity($single_feed_max); 

                        // Build an array of all the items, starting with element 0 (first element).
                        $items = $feed->get_items( 0, $maxitems );   
                    }
                }

                if ( ! empty( $items ) ) {
                    // Gather the permalinks of existing feed item's related to this feed source
                    global $wpdb;
                   

                    foreach ( $items as $item ) {
	


                        // Check if newly fetched item already present in existing feed item item, 
                        // if not insert it into wp_posts and insert post meta.

					$cleanLink = strip_qs_var_match('news.google.com',$item->get_permalink(),'url');  // clean all parameters except the url from links from Google News

					$mypostids = $wpdb->get_results("select post_id from $wpdb->postmeta where meta_key = 'rssmi_item_permalink' and meta_value like '%".$cleanLink."%'");

				//	$myposttitle=$wpdb->get_results("select post_title from $wpdb->posts where post_type='rssmi_feed_item' and post_title like '%".mysql_real_escape_string(trim($item->get_title()))."%'");
							
                       	if ((empty( $mypostids ) && $mypostids !== false)){ 
	
						$didUpdate=1;  //indicates this feed has an update
						
							if (IS_NULL($item->get_date())){
								$post_date = $rightNow;  
								$unix_date=strtotime($rightNow);
					
							}else{
						  		$post_date = get_date_from_gmt( $item->get_date( 'Y-m-d H:i:s' ) ) ; 
								$unix_date=$item->get_date( 'U' );
								
							}

						if (rssmi_is_not_fresh($post_date)==1){continue;}  //filter for days old
						
                            // Create post object
                            $feed_item = array(
                                'post_title' => html_entity_decode($item->get_title()),
                                'post_content' => '',
								'post_date' =>$post_date, 
                                'post_status' => 'publish',
                                'post_type' => 'rssmi_feed_item'
                            );                
                            $inserted_ID = wp_insert_post( $feed_item, $wp_error );

							
							
							if ($feedAuthor = $item->get_author())
							{
								$feedAuthor=$item->get_author()->get_name();
							}
							
					
								
									
									if ($enclosure = $item->get_enclosure())
									{

										$FeedMediaID=get_post_meta(  $feed_ID ,'rssmi_mediaID', true );// GET THE CHOSEN MEDIA IMAGE FROM THE ENCLOSURE
										$useMediaImage= ($FeedMediaID > 0 ? 1 : 0);
										$inum = (isset($FeedMediaID) ? $FeedMediaID-1 : 0); 
										if(!IS_NULL($item->get_enclosure()->get_thumbnails()))
											{
												$mediaImageArray=$item->get_enclosure()->get_thumbnails();
												$mediaImage=$mediaImageArray[$inum];

											}
												else if (!IS_NULL($item->get_enclosure()->get_link()))
											{
												$mediaImage=$item->get_enclosure()->get_link();	
											}	
										}
										
										
										if (!IS_Null($item->get_categories())){	
														$categoryTerms="";
														foreach ($item->get_categories() as $category)
														    {
														    	$categoryTerms .= $category->get_term().', ';
														    }
														$postCategories=rtrim($categoryTerms,', ');
											}else{
													$postCategories=Null;
											}
									
						
						
											if ($itemAuthor = $item->get_author())
											{
												$itemAuthor=(!IS_NULL($item->get_author()->get_name()) ? $item->get_author()->get_name() : $item->get_author()->get_email());
												$itemAuthor=html_entity_decode($itemAuthor,ENT_QUOTES,'UTF-8');		
											}else if (!IS_NULL($feedAuthor)){
												$itemAuthor=$feedAuthor;
												$itemAuthor=html_entity_decode($itemAuthor,ENT_QUOTES,'UTF-8');		

											}
									
						
						$myarray[]=array(
							"mystrdate"=>strtotime($post_date),
							"mytitle"=>html_entity_decode($item->get_title()),
							"mylink"=>$item->get_permalink(),
						    "mydesc"=>$item->get_content(),
							"myimage"=>$mediaImage,
							"myAuthor"=>$itemAuthor,
							"itemcategory"=>$postCategories,
							"mycatid"=>$feed_cat,
							"myGroup"=>$rssmi_title,
							"feedID"=>$feed_ID,
							"useMediaImage"=>$useMediaImage
							);
				
									unset($mediaImage);
									unset($itemAuthor);
									unset($useMediaImage);
									unset($post_date);
									unset($rssmi_title);
									unset($feed_cat);
									
                           	update_post_meta( $inserted_ID, 'rssmi_item_permalink', $cleanLink );
                            update_post_meta( $inserted_ID, 'rssmi_item_description', $myarray );                        
                            update_post_meta( $inserted_ID, 'rssmi_item_date', $unix_date ); // Save as Unix timestamp format
                            update_post_meta( $inserted_ID, 'rssmi_item_feed_id', $feed_ID);
							unset($myarray);
							unset($unix_date);
							unset($cleanLink);
							
                       } //end if
                    } //end foreach
                } // end if
					if ($didUpdate==1){
						update_post_meta($feed_ID, 'rssmi_last_update', time()) ;
					}
					unset($feed_ID);
					wp_reset_postdata(); // Restore the $post global to the current post in the main query

            } // end $feed_sources while loop

    } // end if

	rssmi_update_feed_time();

} 


add_action('wp_insert_post', 'rssmi_fetch_feed_items'); 
/**
 * Fetches feed items for a specific feed
 */
function rssmi_fetch_feed_items( $post_id , $feed_total_fetch=10) { 
	$rssmi_global_options = get_option('rssmi_global_options'); 
	$noDirectFetch=(isset($rssmi_global_options['noForcedFeed']) ? $rssmi_global_options['noForcedFeed'] : 0);
	$timeout=20;
	$forceFeed=true;
	$showVideo=1;
 	global $wpdb;
    $didUpdate=0;
    $post = get_post( $post_id );
    
    if( ( $post->post_type == 'rssmi_feed' ) && ( $post->post_status == 'publish' ) ) { 
        // Get the feed source

		$query = "SELECT ID, post_date, post_title, guid FROM $wpdb->posts WHERE post_status = 'publish' AND post_type = 'rssmi_feed' AND ID = $post_id";
		$feed_source =$wpdb->get_results($query);
           
        if( !empty($feed_source)) {
	

                 
                $feed_ID = $post_id;
                $feed_url = get_post_meta( $post_id, 'rssmi_url', true );
				$feed_cat = get_post_meta( $post_id, 'rssmi_cat', true );
				$rssmi_user= get_post_meta($post_id, 'rssmi_user', true );
				$rssmi_title= get_the_title($post_id);
 
                if( !empty( $feed_url ) ) {             
                    
                			$url = esc_url_raw(strip_tags($feed_url));
	
							if ($noDirectFetch==1){
								$feed = fetch_feed($url);
							}else{
								$feed = wp_rss_fetchFeed($url,$timeout,$forceFeed,$showVideo);	
							}
                    
                    if ( !is_wp_error( $feed ) ) {
                        // Limit  to 10 unless fetched from feed page. 
                        $maxitems = $feed->get_item_quantity($feed_total_fetch); 

                        // Build an array of all the items, starting with element 0 (first element).
                        $items = $feed->get_items( 0, $maxitems );   
                    }
                    else { return; }
                }       

                if ( ! empty( $items ) ) {
                   
					

                    foreach ( $items as $item ) {
	
	$cleanLink = strip_qs_var_match('news.google.com',$item->get_permalink(),'url');  // clean all parameters except the url from links from Google News
	
	 
	

                     	$mypostids = $wpdb->get_results("select post_id from $wpdb->postmeta where meta_key = 'rssmi_item_permalink' and meta_value like '%".$cleanLink."%'");

						//	$myposttitle=$wpdb->get_results("select post_title from $wpdb->posts where post_type='rssmi_feed_item' and post_title like '%".mysql_real_escape_string(trim($item->get_title()))."%'");
							
                       	if ((empty( $mypostids ) && $mypostids !== false)){
	
	   						$didUpdate=1;
	
							if (IS_NULL($item->get_date())){
								$post_date = $rightNow;  
								$unix_date=strtotime($rightNow);
								
							}else{
						  		$post_date = get_date_from_gmt( $item->get_date( 'Y-m-d H:i:s' ) ) ;  
								$unix_date=$item->get_date( 'U' );
								
							}

	
								if (rssmi_is_not_fresh($post_date)==1){continue;}  //filter for days old
	
                            // Create post object
                            $feed_item = array(
                                'post_title' => html_entity_decode($item->get_title()),
                                'post_content' => '',
								'post_date' =>$post_date, 
                                'post_status' => 'publish',
                                'post_type' => 'rssmi_feed_item'
                            ); 
               				remove_action('save_post', 'rssmi_save_custom_fields');
                            $inserted_ID = wp_insert_post( $feed_item );
							add_action( 'save_post', 'rssmi_save_custom_fields' ); 
  							if ($feedAuthor = $item->get_author())
								{
									$feedAuthor=$item->get_author()->get_name();
								}
									
										
										if ($enclosure = $item->get_enclosure())
										{

											$FeedMediaID=get_post_meta(  $feed_ID ,'rssmi_mediaID', true );// GET THE CHOSEN MEDIA IMAGE FROM THE ENCLOSURE
											$useMediaImage= ($FeedMediaID > 0 ? 1 : 0);
											$inum = (isset($FeedMediaID) ? $FeedMediaID-1 : 0); 
											if(!IS_NULL($item->get_enclosure()->get_thumbnails()))
												{
													$mediaImageArray=$item->get_enclosure()->get_thumbnails();
													$mediaImage=$mediaImageArray[$inum];

												}
													else if (!IS_NULL($item->get_enclosure()->get_link()))
												{
													$mediaImage=$item->get_enclosure()->get_link();	
												}	
											}
										
								
											if (!IS_Null($item->get_categories())){	
															$categoryTerms="";
															foreach ($item->get_categories() as $category)
															    {
															    	$categoryTerms .= $category->get_term().', ';
															    }
															$postCategories=rtrim($categoryTerms,', ');
												}else{
														$postCategories=Null;
												}	
										

												if ($itemAuthor = $item->get_author())
												{
													$itemAuthor=(!IS_NULL($item->get_author()->get_name()) ? $item->get_author()->get_name() : $item->get_author()->get_email());
													$itemAuthor=html_entity_decode($itemAuthor,ENT_QUOTES,'UTF-8');		
												}else if (!IS_NULL($feedAuthor)){
													$itemAuthor=$feedAuthor;
													$itemAuthor=html_entity_decode($itemAuthor,ENT_QUOTES,'UTF-8');		

												}
										

										
										$myarray[]=array(
											"mystrdate"=>strtotime($post_date),
											"mytitle"=>html_entity_decode($item->get_title()),
											"mylink"=>$item->get_permalink(),
											"mydesc"=>$item->get_content(),
											"myimage"=>$mediaImage,
											"myAuthor"=>$itemAuthor,
											"itemcategory"=>$postCategories,
											"mycatid"=>$feed_cat,
											"myGroup"=>$rssmi_title,
											"feedID"=>$feed_ID,
											"useMediaImage"=>$useMediaImage
											);

													unset($mediaImage);
													unset($itemAuthor);
													unset($useMediaImage);
													unset($post_date);
													unset($rssmi_title);
													unset($feed_cat);			

	                            update_post_meta( $inserted_ID, 'rssmi_item_permalink', $cleanLink );
	                            update_post_meta( $inserted_ID, 'rssmi_item_description', $myarray );                        
	                            update_post_meta( $inserted_ID, 'rssmi_item_date', $unix_date ); // Save as Unix timestamp format
	                            update_post_meta( $inserted_ID, 'rssmi_item_feed_id', $post_id);
								unset($myarray);
								unset($unix_date);
								unset($cleanLink);
								
           
                       } //end if
                    } //end foreach
										
                } // end if
            } // end if not empty

			if ($didUpdate==1){
			update_post_meta($feed_ID, 'rssmi_last_update', time()) ;
			}
    } // end if
} // end 





?>