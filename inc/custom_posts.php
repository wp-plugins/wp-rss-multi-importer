<?php

/**
 * Create the 2 custom post types
 */
function create_rssmi_feed() {

	// RSS Feeds
	$feed_args = array(
		'public'              => true,
		'query_var'           => 'feed_source',
		'exclude_from_search' => true,
		'show_in_menu'        => false,
		'show_in_nav_menus'   => false,
		'supports'            => array( 'title' ),
		'rewrite'             => array(
			'slug'       => 'feeds',
			'with_front' => false
		),
		'labels'              => array(
			'name'               => __( 'Multi Importer - Feed List', 'wp-rss-multi-importer' ),
			'singular_name'      => __( 'Feed', 'wp-rss-multi-importer' ),
			'add_new'            => __( 'Add New Feed Source', 'wp-rss-multi-importer' ),
			'all_items'          => __( 'View Feed Sources', 'wp-rss-multi-importer' ),
			'add_new_item'       => __( 'Add New Feed Source', 'wp-rss-multi-importer' ),
			'edit_item'          => __( 'Edit Feed Source', 'wp-rss-multi-importer' ),
			'new_item'           => __( 'New Feed Source', 'wp-rss-multi-importer' ),
			'view_item'          => __( 'View Feed Source', 'wp-rss-multi-importer' ),
			'search_items'       => __( 'Search Feeds', 'wp-rss-multi-importer' ),
			'not_found'          => __( 'No Feed Sources Found', 'wp-rss-multi-importer' ),
			'not_found_in_trash' => __( 'No Feed Sources Found In Trash', 'wp-rss-multi-importer' ),
		),
	);

	register_post_type( 'rssmi_feed', $feed_args );

	// RSS Feed Items
	// TODO: Disallow adding new with capabilities
	$feed_item_args = array(
		'public'              => true,
		'query_var'           => 'feed_item',
		'exclude_from_search' => true,
		'show_in_menu'        => false,
		'show_in_nav_menus'   => false,
		'rewrite'             => array(
			'slug'       => 'feeds/items',
			'with_front' => false,
		),
		'labels'              => array(
			'name'               => __( 'Multi Importer Feed Items', 'wp-rss-multi-importer' ),
			'singular_name'      => __( 'Feed Items', 'wp-rss-multi-importer' ),
			'all_items'          => __( 'Feed Items', 'wp-rss-multi-importer' ),
			'view_item'          => __( 'View Feed Items', 'wp-rss-multi-importer' ),
			'search_items'       => __( 'Search Feed Items', 'wp-rss-multi-importer' ),
			'not_found'          => __( 'No Imported Feeds Found', 'wp-rss-multi-importer' ),
			'not_found_in_trash' => __( 'No Imported Feeds Found In Trash', 'wp-rss-multi-importer' )
		),
	);

	register_post_type( 'rssmi_feed_item', $feed_item_args );
}

add_action( 'init', 'create_rssmi_feed' );


/**
 * Remove the publish box from RSS Feed edit page
 */
function rssmi_remove_publish_box() {
	remove_meta_box( 'submitdiv', 'rssmi_feed', 'side' );
}

add_action( 'admin_menu', 'rssmi_remove_publish_box' );


/**
 * Display custom columns for RSS Feeds
 *
 * @param $columns
 *
 * @return array
 */
function rssmi_set_custom_columns( $columns ) {

	$columns = array(
		'cb'         => '<input type="checkbox" />',
		'title'      => __( 'Name', 'rssmi' ),
		'url'        => __( 'URL', 'rssmi' ),
		'category'   => __( 'Category', 'rssmi' ),
		'bloguser'   => __( 'User', 'rssmi' ),
		'feeditems'  => __( 'Feed Items in DB', 'rssmi' ),
		'lastupdate' => __( 'Last DB Update on', 'rssmi' ),
		'ID'         => __( 'ID', 'rssmi' ),
	);
	return $columns;
}

add_filter( 'manage_edit-rssmi_feed_columns', 'rssmi_set_custom_columns' );


/**
 * Display custom columns for RSS Feeds
 *
 * @param $column
 * @param $post_id
 */
function rssmi_show_custom_columns( $column, $post_id ) {
	global $wpdb;
	switch ( $column ) {
		case 'url':
			$url = get_post_meta( $post_id, 'rssmi_url', true );
			echo '<a href="' . esc_url( $url ) . '">' . esc_url( $url ) . '</a>';
			break;
		case 'category':
			$category = get_post_meta( $post_id, 'rssmi_cat', true );
			echo esc_html( wp_getCategoryName( $category ) );
			break;
		case 'bloguser':
			$bloguser = get_post_meta( $post_id, 'rssmi_user', true );
			$bloguser = (int) $bloguser;
			echo esc_html( get_userdata( $bloguser )->display_name );
			break;
		case 'feeditems':
			$post_count = $wpdb->get_var( $wpdb->prepare( "SELECT count(*) FROM (SELECT * from $wpdb->postmeta as a inner join $wpdb->posts as b on b.id=a.post_id where meta_key='rssmi_item_feed_id' and meta_value=%d order by post_id desc LIMIT 25) as c order by post_id ASC", $post_id ) );
			echo $post_count;
			break;
		case 'lastupdate':
			if ( get_post_meta( $post_id, 'rssmi_last_update', true ) != '' ) {
				$last_update = get_post_meta( $post_id, 'rssmi_last_update', true );
				echo get_date_from_gmt( date( 'Y-m-d H:i:s', $last_update ), 'M j, Y @ g:i a  ' );
			}
			else {
				echo "No Recent Update Yet";
			}
			break;

		case 'ID':
			echo $post_id;
			break;
	}
}

add_action( "manage_rssmi_feed_posts_custom_column", "rssmi_show_custom_columns", 10, 2 );


/**
 * Set custom CSS for feed columns
 * TODO: CSS should be more specific and included in the main CSS sheet
 */
function rssmi_feed_column_width() {
	echo '<style type="text/css">';
	echo '.column-title {width:280px !important;  }';
	echo '.column-url {width:280px !important;  }';
	echo '.column-bloguser {width:120px !important;  }';
//	echo 'th.column-feeditems {font-size:12px !important;  }';
	echo '.column-feeditems {text-align:center !important;  }';
	echo '</style>';
}

add_action( 'admin_head', 'rssmi_feed_column_width' );


/**
 * Make the custom columns sortable
 *
 * @return array
 */
function rssmi_sortable_columns() {
	return array(
		// meta column id => sortby value used in query
		'title'    => 'title',
		'category' => 'category'
	);
}

add_filter( 'manage_edit-rssmi_feed_sortable_columns', 'rssmi_sortable_columns' );


add_filter( 'manage_edit-rssmi_feed_item_columns', 'rssmi_set_feed_item_custom_columns' );
/*
	Set up the custom columns for the  source list

	*/
function rssmi_set_feed_item_custom_columns( $columns ) {
	//rssmi_fetch_all_feed_items();
	$columns = array(
		'cb'          => '<input type="checkbox" />',
		'title'       => __( 'Name', 'rssmi' ),
		'permalink'   => __( 'Permalink', 'rssmi' ),
		'publishdate' => __( 'Date published', 'rssmi' ),
		'source'      => __( 'Source', 'rssmi' )
	);
	return $columns;
}


add_action( "manage_rssmi_feed_item_posts_custom_column", "rssmi_show_feed_item_custom_columns", 10, 2 );
/*
	Show up the custom columns for the feed list
	*/
function rssmi_show_feed_item_custom_columns( $column, $post_id ) {

	switch ( $column ) {
		case "permalink":
			$url = get_post_meta( $post_id, 'rssmi_item_permalink', true );
			echo '<a href="' . $url . '">' . $url . '</a>';
			break;

		case "publishdate":

			$publishdate = get_date_from_gmt( date( 'Y-m-d H:i:s', intval( get_post_meta( get_the_ID(), 'rssmi_item_date', true ) ) ) );
			//	$publishdate = date( 'Y/m/d', intval(get_post_meta( get_the_ID(), 'rssmi_item_date', true ) )) ;
			echo $publishdate;
			break;

		case "source":
			//$query = new WP_Query();
			$source = get_the_title( get_post_meta( $post_id, 'rssmi_item_feed_id', true ) );

			// $source = '<a href="' . get_edit_post_link( get_post_meta( $post_id, 'rssmi_feed_id', true ) ) . '">' . get_the_title( get_post_meta( $post_id, 'rssmi_feed_id', true ) ) . '</a>';
			echo $source;
			break;
	}
}


add_filter( 'manage_edit-rssmi_feed_item_sortable_columns', 'rssmi_sortable_item_columns' );

/*
	* Make the custom columns sortable
	*/
function rssmi_sortable_item_columns() {
	return array(
		// meta column id => sortby value used in query
		'source' => 'source',

	);
}


add_action( 'add_meta_boxes', 'rssmi_add_meta_boxes' );
/*
	Set up the input boxes for the rssmi_feed post type
	*/
function rssmi_add_meta_boxes() {
	global $rssmi_meta_fields;
	$content_image = WP_RSS_MULTI_IMAGES . "content_image.png";
	$media_image   = WP_RSS_MULTI_IMAGES . "media_image.png";

	// Remove the default WordPress Publish box, because we will be using custom ones
	remove_meta_box( 'submitdiv', 'rssmi_feed', 'side' );
	add_meta_box(
		'rssmi-save-link-side-meta',
		'Save Feed Source',
		'rssmi_save_feed_source_meta_box',
		'rssmi_feed',
		'side',
		'high'
	);


	add_meta_box(
		'custom_meta_box', // $id
		__( 'Feed Source Details', 'rssmi' ), // $title
		'display_rssmi_feed_meta_box', // $callback
		'rssmi_feed', // $page
		'normal', // $context
		'high' ); // $priority


	add_meta_box(
		'note_meta_box', // $id
		__( 'Important Note', 'rssmi' ), // $title
		'rssmi_save_feed_note_meta_box', // $callback
		'rssmi_feed', // $page
		'side', // $context
		'low' ); // $priority

	add_meta_box(
		'images_explain_meta_box', // $id
		__( 'What do <img src=' . $content_image . '> & <img src=' . $media_image . '> mean?', 'rssmi' ), // $title
		'rssmi_images_explain_meta_box', // $callback
		'rssmi_feed', // $page
		'side', // $context
		'low' ); // $priority


	add_meta_box(
		'can_do_meta_box', // $id
		__( 'DO MORE WITH YOUR FEED', 'rssmi' ), // $title
		'rssmi_feed_can_do_notes', // $callback
		'rssmi_feed', // $page
		'side', // $context
		'low' ); // $priority


	add_meta_box(
		'preview_meta_box', // $id
		__( 'Feed Preview ', 'rssmi' ), // $title
		'rssmi_preview_meta_box', // $callback
		'rssmi_feed', // $page
		'normal', // $context
		'low' ); // $priority


	//  This adds the box with a direct link to the Add Multiple Feeds option
	add_meta_box(
		'rssmi-add-multiple',
		'Delete and Fetch the Items for this Feed Now',
		'rssmi_delete_posts_for_feed',
		'rssmi_feed',
		'normal',
		'low'
	);

	add_meta_box(
		'rssmi-add-multiple2',
		'Most Recent Items in the Database',
		'rssmi_most_current_posts_meta_box',
		'rssmi_feed',
		'normal',
		'low'
	);


}


function rssmi_save_feed_source_meta_box() {
	global $post;

	$post_status = ( $post->post_status == "auto-draft" ) ? "publish" : $post->post_status;

	/**
	 * Check if user has disabled trash, in that case he can only delete feed sources permanently,
	 * else he can deactivate them. By default, if not modified in wp_config.php, EMPTY_TRASH_DAYS is set to 30.
	 */
	if ( current_user_can( "delete_post", $post->ID ) ) {
		if ( ! EMPTY_TRASH_DAYS ) {
			$delete_text = __( 'Delete Permanently' );
		}
		else {
			$delete_text = __( 'Move Feed to Trash' );
		}
		echo '
						<div class="submitbox" id="submitpost" >
							<div id="major-publishing-actions">
								<div id="publishing-action" style="float:left" >
									<span class="spinner"></span>
		<input type="hidden" name="post_status" id="post_status" value="' . $post_status . '" />
									<input name="original_publish" type="hidden" id="original_publish" value="Update" />
									<input name="save" type="submit" class="button button-primary button-large" id="publish" accesskey="s" value="Save Feed" />
								</div>
								<div id="delete-action" style="float:right">
									<a class="submitdelete deletion" href="' . get_delete_post_link( $post->ID ) . '">' . $delete_text . '</a>
								</div>
								<div class="clear"></div>
								<p><strong>Please read the note below before deleting this feed.</strong></p>
							</div>
						</div>
						';
	}
}


function rssmi_save_feed_note_meta_box() {
	global $post;
	if ( current_user_can( "delete_post", $post->ID ) ) {

		echo '<strong>Important:</strong> Deleting a feed will also delete all of the associated feed items and auto-posts as well. If you move a feed to trash, these will remain until the feed is deleted permanently. To delete the feed and all items immediately, go to <strong>Multi Importer > Feed List</strong>, click the Trash link near the top, then click the <strong>Empty Trash</strong> button.';
	}
}

function rssmi_images_explain_meta_box() {
	$content_image = WP_RSS_MULTI_IMAGES . "content_image.png";
	$media_image   = WP_RSS_MULTI_IMAGES . "media_image.png";
	echo '<img src=' . $content_image . '> image exists in the content<br> <img src=' . $media_image . '> image exists in the media enclosure';
	echo '<p>If no icons show up next to the titles, it\'s likely there are no images in this feed.</p>';
}


function rssmi_add_multiple_feeds_meta_box() {

	_e( "<a href=\"admin.php?page=wprssmi_options8\">Click here to add a bunch of feeds.</a>", 'wp-rss-multi-importer' );
}


function rssmi_feed_can_do_notes() {

	_e( "<p>You can filter your feed by keywords, add a category image and more. First, put your feed into a category then <a href=\"admin.php?page=wprssmi_options\">go here to add keywords.</a></p><p>Learn how the keyword filtering works by <a href='http://www.wprssimporter.com/faqs/how-do-the-filters-work/' target='_blank'>going here</a>.", 'wp-rss-multi-importer' );
}


// Hide "add new" button on edit page
function hd_add_buttons() {
	global $pagenow;

	if ( is_admin() ) {
		if ( $pagenow == 'edit.php' && isset( $_GET['post_type'] ) && $_GET['post_type'] == 'rssmi_feed_item' ) {
			echo '<style>.add-new-h2{display: none;}</style>';
		}
	}
}

add_action( 'admin_head', 'hd_add_buttons' );


// Highlight plugin admin menu when on edit screen
function highlight_rssmi_menu() {
	$screen = get_current_screen();
	global $pagenow;
	global $parent_file;
	if ( $pagenow == 'post.php' && 'rssmi_feed' == $screen->post_type ) {
		global $parent_file;
		$parent_file = 'wprssmi';
		echo '<style>#edit-slug-box{display: none;}</style>';

	}
}

add_action( 'admin_head', 'highlight_rssmi_menu' );


//  give message that feed has been saved (instead of default post message)
add_filter( 'post_updated_messages', 'rssmi_updated_messages' );
function rssmi_updated_messages( $messages ) {
	global $pagenow;
	$screen = get_current_screen();
	if ( $pagenow == 'post.php' && 'rssmi_feed' == $screen->post_type ) {
		$messages["post"][6] = 'The feed has been successfully saved.';
	}
	return $messages;
}


function change_default_title( $title ) {
	$screen = get_current_screen();

	if ( 'rssmi_feed' == $screen->post_type ) {
		$title = 'Name your feed here - e.g., the source of the RSS feed';
	}
	return $title;
}

add_filter( 'enter_title_here', 'change_default_title' );


add_filter( 'post_row_actions', 'rssmi_delete_view_link', 10, 2 );

function rssmi_delete_view_link( $actions, $page_object ) {
	$screen = get_current_screen();

	if ( 'rssmi_feed_item' == $screen->post_type ) {
		unset( $actions['view'] );
		unset( $actions['edit'] );
		unset( $actions['inline hide-if-no-js'] ); //  remove quick edit
	}
	return $actions;
}


add_filter( 'post_row_actions', 'rssmi_feed_list_view_link', 10, 2 ); //  NEEDS WORK

function rssmi_feed_list_view_link( $actions, $post ) {
	$screen              = get_current_screen();
	$dismiss_link_joiner = ( count( $_GET ) > 0 ) ? '&amp;' : '?';
	if ( 'rssmi_feed' == $screen->post_type ) {
		unset( $actions['view'] );
		unset( $actions['inline hide-if-no-js'] ); //  remove quick edit
		//	$actions['rssmi_fetch_items'] = '<a  href="javascript:;" pid="'.$post->ID.'" class="rssmi-fetch-items-now" >' . __('Delete Items') . '</a>';
		$actions['rssmi_delete_items'] = '<a  href="' . $_SERVER['REQUEST_URI'] . $dismiss_link_joiner . 'rssmi_delete_items=' . $post->ID . '" class="rssmi-fetch-items-now" >' . __( 'Delete Items' ) . '</a>';
		$actions['rssmi_view_items']   = '<a  href="' . admin_url() . 'edit.php?post_type=rssmi_feed_item&rssmi_feed_id=' . $post->ID . '" >' . __( 'View Items' ) . '</a>';
	}
	return $actions;
}


add_action( 'admin_init', 'rssmi_check_delete_items' );
function rssmi_check_delete_items() {
	if ( isset( $_GET['rssmi_delete_items'] ) && ! empty( $_GET['rssmi_delete_items'] ) ) {
		$feed_id = $_GET['rssmi_delete_items'];
		rssmi_on_delete( $feed_id );
		$page = isset( $_GET['paged'] ) ? '&paged=' . $_GET['paged'] : '';
		header( 'Location: ' . admin_url( 'edit.php?post_type=rssmi_feed' . $page ) );
		exit();
	}

}


/**
 * Set up the meta box for the wprss_feed post type
 *
 * @since 2.0
 */
function rssmi_show_meta_box() {
	global $post;

	// Use nonce for verification
	echo '<input type="hidden" name="rssmi_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';

	// Begin the field table and loop
	echo '<table class="form-table">';
	foreach ( $meta_fields as $field ) {
		// get value of this field if it exists for this post
		$meta = get_post_meta( $post->ID, $field['id'], true );
		// begin a table row with
		echo '<tr>
                       <th><label for="' . $field['id'] . '">' . $field['label'] . '</label></th>
                       <td>';

		switch ( $field['type'] ) {

			// text
			case 'text':
				echo '<input type="text" name="' . $field['id'] . '" id="' . $field['id'] . '" value="' . $meta . '" size="55" />
                                   <br /><span class="description">' . $field['desc'] . '</span>';
				break;

			// textarea
			case 'textarea':
				echo '<textarea name="' . $field['id'] . '" id="' . $field['id'] . '" cols="60" rows="4">' . $meta . '</textarea>
                                   <br /><span class="description">' . $field['desc'] . '</span>';
				break;

			// checkbox
			case 'checkbox':
				echo '<input type="checkbox" name="' . $field['id'] . '" id="' . $field['id'] . '" ', $meta ? ' checked="checked"' : '', '/>
                                   <label for="' . $field['id'] . '">' . $field['desc'] . '</label>';
				break;

			// select
			case 'select':
				echo '<select name="' . $field['id'] . '" id="' . $field['id'] . '">';
				foreach ( $field['options'] as $option ) {
					echo '<option', $meta == $option['value'] ? ' selected="selected"' : '', ' value="' . $option['value'] . '">' . $option['label'] . '</option>';
				}
				echo '</select><br /><span class="description">' . $field['desc'] . '</span>';
				break;

		} //end switch
		echo '</td></tr>';
	} // end foreach
	echo '</table>'; // end table
}


function rssmi_most_current_posts_meta_box( $post ) {
	global $wpdb;


	$sql = "SELECT * FROM (SELECT post_id, post_title from $wpdb->postmeta as a inner join $wpdb->posts as b on b.id=a.post_id where meta_key='rssmi_item_feed_id' and meta_value=" . $post->ID . " order by post_id desc LIMIT 10) as c order by post_id ASC";

	$current_post_array = $wpdb->get_results( $sql );

	if ( ! empty( $current_post_array ) ) {
		echo '<h4>A few of the most recent feed items available for the Shortcode or to put in the AutoPost database from ' . get_the_title() . '</h4>';
		echo '<ul>';
		foreach ( $current_post_array as $a ) {

			echo '<li>' . $a->post_title . '</li>';
		}
		echo '</ul>';
		echo "There are " . $wpdb->num_rows . " total items in the database.";
	}
	else {
		echo 'There are no feed items in the database. Click Fetch Now.';
	}
}


function rssmi_preview_meta_box() {
	global $post;
	$feed_url             = get_post_meta( $post->ID, 'rssmi_url', true );
	$content_image        = WP_RSS_MULTI_IMAGES . "content_image.png";
	$media_image          = WP_RSS_MULTI_IMAGES . "media_image.png";
	$imageExists          = 0;
	$rssmi_global_options = get_option( 'rssmi_global_options' );
	$noDirectFetch        = ( isset( $rssmi_global_options['noForcedFeed'] ) ? $rssmi_global_options['noForcedFeed'] : 0 );

	if ( ! empty( $feed_url ) ) {
		if ( $noDirectFetch == 1 ) {
			$feed = fetch_feed( $feed_url );
		}
		else {
			$feed = wp_rss_fetchFeed( $feed_url, 20, true, 0 );
		}

		if ( ! $feed->error() ) {
			$items     = $feed->get_items();
			$feedCount = count( $items );

			echo '<h4>Latest 5 available feed items in the RSS feed for ' . get_the_title() . '</h4>';
			$count     = 0;
			$feedlimit = 5;
			foreach ( $items as $item ) {
				echo '<ul>';
				//      echo '<li>' . $item->get_title() . '</li>';

				echo '<li>' . html_entity_decode( $item->get_title() );
				if ( rssmi_image_test( $item->get_content() ) == 1 ) {
					echo '  <img src=' . $content_image . '>';
					$imageExists = 1;
				}
				if ( rss_mediaImage_test( $item ) == 1 ) {
					echo '  <img src=' . $media_image . '>';
					//	echo rssmi_mediaImage_choice($item);  //experimental
					$imageExists = 1;
				}

				echo '</li>';
				echo '</ul>';
				if ( ++$count == $feedlimit ) break; //break if count is met
			}
			echo "THIS FEED CURRENTLY HAS A TOTAL OF " . $feedCount . " ITEMS.";
			if ( $imageExists == 0 ) {
				echo "<br><strong>IMPORTANT</strong> - THIS FEED HAS NO IMAGES (AT LEAST NOT IN THE MOST RECENT 5 POSTS) - SO YOU WILL SEE NO IMAGES IN YOUR POSTS UNLESS YOU USE A DEFAULT CATEGORY IMAGE.";
			}
		}
		else echo "<strong>Invalid feed URL</strong> - Validate the feed source URL by <a href=\"http://validator.w3.org/feed/check.cgi?url=" . $feed_url . "\" target=\"_blank\">clicking here</a> and if the feed is valid then  <a href=\"http://www.wprssimporter.com/faqs/im-told-the-feed-isnt-valid-or-working/\" target=\"_blank\">go here to learn more about what might be wrong</a>.";
	}

	else echo 'No feed URL defined yet';
}


function rss_mediaImage_test( $item ) {
	$is_media_image = 0;
	if ( $enclosure = $item->get_enclosure() ) {
		if ( ! IS_NULL( $item->get_enclosure()->get_thumbnails() ) ) {
			$inum            = 0;
			$mediaImageArray = $item->get_enclosure()->get_thumbnails();
			//	$mediaImage=$mediaImageArray[$inum];
			$is_media_image = 1;
		}
		else if ( ! IS_NULL( $item->get_enclosure()->get_link() ) ) {
			$mediaImage     = $item->get_enclosure()->get_link();
			$is_media_image = 1;
		}
	}
	return $is_media_image;
}


function rssmi_mediaImage_choice( $item ) {
	$mediaImageArray = $item->get_enclosure()->get_thumbnails();
	for ( $inum = 0; $inum <= 3; $inum ++ ) {
		$mediaImage = $mediaImageArray[0];
		return $mediaImage;
	}
}

function rssmi_image_test( $content ) {
	$leadMatch = 0;
	$is_image  = 0;

	$strmatch = '^\s*(?:<p.*>)?\<a.*href="(.*)">\s*(<img.*src=[\'"].*[\'"]\s*?\/?>)[^\<]*<\/a\>\s*(.*)$';

	$strmatch2 = '^(\s*)(?:<p.*>)?(<img.*src=[\'"].*[\'"]\s*?\/?>)\s*(.*)$';

	$strmatch3 = '^(.*)(<img.*src=[\'"].*[\'"]\s*?\/?>)\s*(.*)$'; //match first image if it exists

	if ( preg_match( "/$strmatch/sU", $content, $matches ) ) { //matches a leading hperlinked image
		$leadMatch = 1;
	}
	else if ( preg_match( "/$strmatch2/sU", $content, $matches ) ) { //matches a leading non-hperlinked image
		$leadMatch = 2;
	}
	else if ( preg_match( "/$strmatch3/sU", $content, $matches ) ) { //matches first image
		$leadMatch = 3;
	}

	if ( ( isset( $leadMatch ) && $leadMatch == 1 ) && rssmi_isbug( $matches[2] ) == False ) {
		$is_image = 1;
	}
	else if ( ( isset( $leadMatch ) && $leadMatch == 2 ) || ( isset( $leadMatch ) && $leadMatch == 3 ) && rssmi_isbug( $matches[2] ) == False ) {
		$is_image = 1;
	}

	return $is_image;

}

function rssmi_isbug( $imageLink ) {

	if ( strpos( $imageLink, 'width="1"' ) > 0 ) {
		$msg = TRUE;
	}
	else {
		$msg = FALSE;
	}
	return $msg;
}


function rssmi_delete_posts_for_feed() {
	( isset( $_GET['post'] ) ? $postid = $_GET['post'] : $postid = null );
	echo '<div style="float:left"><button type="button" id="delete-feedposts" name="feedID" value="' . $postid . '"  class="button-delete-red" />Delete Feed Items Now</button></div>';

	echo '<div style="margin-left:50px"><button type="button" id="rssmi-fetch-items-now" name="feedID" value="' . $postid . '"  class="button-fetch-green" />Fetch Feed Items Now</button></div>';

	echo '<div id="feedposts_note"></div><div id="fetch_items_note"></div><div id="rssmi-ajax-loader-delete-fetch"></div>';

}


add_action( 'wp_ajax_rssmi_fetch_items_now', 'fetch_feeds_for_id' );

function fetch_feeds_for_id() {
	$post_id              = $_POST["pid"];
	$rssmi_global_options = get_option( 'rssmi_global_options' );
	$max                  = ( isset( $rssmi_global_options['single_feed_max'] ) ? $rssmi_global_options['single_feed_max'] : 20 );

	if ( isset( $post_id ) ) {
		wp_rss_multi_importer_post( $post_id, $catID = NULL );
		rssmi_fetch_feed_items( $post_id, $max );
		echo "Most recent feed items have been imported";

	}


	die();
}


add_action( 'wp_ajax_delete_feedposts', 'fetch_rss_feed_delete' );

function fetch_rss_feed_delete() {
	$postit = $_POST["post_var"]; ///holds the Feed ID number
	rssmi_delete_all_for_feed( $postit );
	rssmi_delete_all_posts_for_feed( $postit );
	//	rssmi_delete_all_for_feed($id);
	echo 'All posts for this feed have been deleted';
	die();
}


function rssmi_check_url_status() {
	global $post;
	$feed_url  = get_post_meta( $post->ID, 'rssmi_url', true );
	$checkmark = WP_RSS_MULTI_IMAGES . "check_mark.png";
	$urlerror  = WP_RSS_MULTI_IMAGES . "error.png";
	if ( ! empty( $feed_url ) ) {
//	echo $feed_url;    
		$feed = wp_rss_fetchFeed( $feed_url );
		//  if ( ! is_wp_error( $feed ) ) {
		if ( ! $feed->error() ) {
			//	return "<span style=\"color:green\">OK</span>";
			//			return "<img src=$checkmark>";
		}
		else {
			//			return "<img src=$urlerror>";
			//	return "<span style=\"color:red\">NOT AVAILABLE</span>";
		}
	}
}


add_action( 'add_meta_boxes', 'rssmi_remove_meta_boxes', 100 );
/*
	Remove unneeded meta boxes from add feed source scre
	*/
function rssmi_remove_meta_boxes() {
	// if ( 'rssmi_feed' !== get_current_screen()->id ) return;
	remove_meta_box( 'sharing_meta', 'rssmi_feed', 'advanced' );
	remove_meta_box( 'content-permissions-meta-box', 'rssmi_feed', 'advanced' );
	remove_meta_box( 'wpseo_meta', 'rssmi_feed', 'normal' );
	remove_meta_box( 'theme-layouts-post-meta-box', 'rssmi_feed', 'side' );
	remove_meta_box( 'post-stylesheets', 'rssmi_feed', 'side' );
	remove_meta_box( 'hybrid-core-post-template', 'rssmi_feed', 'side' );
	remove_meta_box( 'trackbacksdiv22', 'rssmi_feed', 'advanced' );
	remove_action( 'post_submitbox_start', 'fpp_post_submitbox_start_action' );

}


add_filter( 'gettext', 'rssmi_change_publish_button', 10, 2 );

/**
 * Change 'Publish' button text
 */
function rssmi_change_publish_button( $translation, $text ) {
	if ( 'rssmi_feed' == get_post_type() )
		if ( $text == 'Publish' )
			return 'Add Feed';

	return $translation;
}


function rssmi_my_admin() {
	add_meta_box( 'rssmi_feed_meta_box',
		'Feed Source Details',
		'display_rssmi_feed_meta_box',
		'rssmi_feeds', 'normal', 'high' );
}

add_action( 'admin_init', 'rssmi_my_admin' );

function display_rssmi_feed_meta_box( $rssmi_feed ) {
	echo '<input type="hidden" name="rssmi_meta_box_nonce" value="' . wp_create_nonce( basename( __FILE__ ) ) . '" />';
	$rssmi_url     =
		esc_html( get_post_meta( $rssmi_feed->ID,
			'rssmi_url', true ) );
	$rssmi_cat     =
		intval( get_post_meta( $rssmi_feed->ID,
			'rssmi_cat', true ) );
	$rssmi_user    =
		intval( get_post_meta( $rssmi_feed->ID,
			'rssmi_user', true ) );
	$rssmi_mediaID =
		intval( get_post_meta( $rssmi_feed->ID,
			'rssmi_mediaID', true ) );
	?>
	<table>
		<tr>
			<td style="width: 20%">Feed URL</td>
			<td><input type="text" size="50"
					   name="rssmi_url"
					   value="<?php echo $rssmi_url; ?>" /><?php if ( isset( $rssmi_url ) && $rssmi_url != '' ) {
					echo 'Validate this feed source URL by <a href="http://feedvalidator.org/check?url=' . $rssmi_url . '" target="_blank">clicking here</a>';
				} ?></td>
		</tr>
		<tr>
			<td style="width: 20%">Feed Category</td>
			<td>
				<select style="width: 200px"
						name="rssmi_cat">
					<OPTION selected VALUE=''>None</OPTION>
					<?php
					// Generate all items of drop-down list
					$catOptions = get_option( 'rss_import_categories' );
					if (! empty( $catOptions )){
					$catsize = count( $catOptions );
					for ($k = 1;
					$k <= $catsize;
					$k ++) {
					if ( $k % 2 == 0 ) continue;
					$catkey = key( $catOptions );
					$nameValue = $catOptions[$catkey];
					next( $catOptions );
					$catkey = key( $catOptions );
					$IDValue = $catOptions[$catkey];
					?>
					<option value="<?php echo $IDValue; ?>"
						<?php echo selected( $IDValue,
							$rssmi_cat ); ?>>
						<?php echo $nameValue; ?>
						<?php
						next( $catOptions );
						}
						}?>
				</select> <?php if ( empty( $catOptions ) ) {
					echo 'If you added categories, you can assign this feed to a category. <a href="admin.php?page=wprssmi_options">Go here to set up categories.</a>';
				} ?>
			</td>
		</tr>

		<tr>
			<td style="width: 20%">Blog User</td>
			<td>
				<select style="width: 200px"
						name="rssmi_user">
					<?php
					$blogusers = get_users( array( 'orderby' => 'ID' ) );
					foreach ($blogusers as $user){
					?>
					<option value="<?php echo $user->ID; ?>"
						<?php echo selected( $user->ID,
							$rssmi_user ); ?>>
						<?php echo $user->display_name; ?>
						<?php } ?>
				</select> (for use on AutoPost)
			</td>
		</tr>

		<tr>
			<td style="width: 20%">Media Image Number</td>
			<td>
				<select style="width: 200px" name="rssmi_mediaID">
					<option value="">Default</option>
					<?php
					for ($i = 1;
					$i <= 5;
					$i ++){
					?>
					<option value="<?php echo $i; ?>"
						<?php echo selected( $i,
							$rssmi_mediaID ); ?>>
						<?php echo $i; ?>
						<?php } ?>
				</select> (for Media Image - selects which media image in the feed's image enclosure. If you choose an image number, this will be used over any other image found.
				<a href="http://www.wprssimporter.com/faqs/what-is-the-media-image-number/" target="_blank">Learn more about this here.</a>)
			</td>
		</tr>


	</table>
<?php
}


function rssmi_custom_fields() {
	$prefix = 'rssmi_';

	$rssmi_meta_fields['url']  = array(
		'label' => __( 'URL', 'rssmi' ),
		'id'    => $prefix . 'url'
	);
	$rssmi_meta_fields['cat']  = array(
		'label' => __( 'Category', 'rssmi' ),
		'id'    => $prefix . 'cat'
	);
	$rssmi_meta_fields['user'] = array(
		'label' => __( 'User', 'rssmi' ),
		'id'    => $prefix . 'user'
	);

	$rssmi_meta_fields['mediaID'] = array(
		'label' => __( 'mediaID', 'rssmi' ),
		'id'    => $prefix . 'mediaID'
	);


	return $rssmi_meta_fields;
}


//add_action('delete_post', 'rssmi_trash_function');  //  Needs work

function rssmi_trash_function( $post_id ) {
	wp_delete_post( $post_id, true );
}

function rssmi_delete_prior_posts( $post_id ) {
	global $wpdb;
	$query       = "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='rssmi_source_feed' AND meta_value = $post_id";
	$prior_posts = $wpdb->get_results( $query );
	foreach ( $prior_posts as $prior_post ) {
		wp_delete_post( $prior_post->post_id, true );
	}
}


add_action( 'save_post', 'rssmi_save_custom_fields' );

function rssmi_save_custom_fields( $post_id ) {

	$meta_fields = rssmi_custom_fields();


	if ( isset( $_POST['post_type'] ) && $_POST['post_type'] != 'rssmi_feed' ) {
		return;
	}


	$rssmi_nonce_var = ( isset( $_POST['rssmi_meta_box_nonce'] ) ? $_POST['rssmi_meta_box_nonce'] : NULL );
	// verify nonce
	if ( ! wp_verify_nonce( $rssmi_nonce_var, basename( __FILE__ ) ) )
//if ( ! wp_verify_nonce ($_POST[ 'rssmi_meta_box_nonce' ], basename( __FILE__ ) ) ) 
	return $post_id;

	// check autosave
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;

	// check permissions
	if ( 'page' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
			return $post_id;
	}
	elseif ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}

	// delete prior posts if this post_id url changes
	$oldURL = get_post_meta( $post_id, 'rssmi_url', true );
	$newURL = $_POST['rssmi_url'];
	if ( $newURL && $newURL != $oldURL ) {
		rssmi_delete_all_for_feed( $post_id );
		rssmi_delete_prior_posts( $post_id );
	}

	// loop through fields and save the data
	foreach ( $meta_fields as $field ) {
		$old = get_post_meta( $post_id, $field['id'], true );
		$new = $_POST[$field['id']];
		if ( $new && $new != $old ) {
			update_post_meta( $post_id, $field['id'], $new );
		}
		elseif ( '' == $new && $old ) {
			delete_post_meta( $post_id, $field['id'], $old );
		}
	} // end foreach

}


add_action( 'before_delete_post', 'rssmi_delete_custom_fields' );

function rssmi_delete_custom_fields( $postid ) {
	global $wpdb;
	$delete_array = $wpdb->get_results( "SELECT post_id FROM $wpdb->postmeta WHERE meta_key='rssmi_source_feed' AND meta_value =$postid" );
	foreach ( $delete_array as $delete_item ) {
		wp_delete_post( $delete_item->post_id, true );
	}
}


//add_action( 'before_delete_post', 'rssmi_delete_posts_admin_attachment' ); //this function in db_functions.php file


add_filter( "manage_edit-rssmi_feed_item_sortable_columns", 'rssmi_shortcode_sortable_columns' );


function rssmi_shortcode_sortable_columns( $columns ) {
	$custom = array(
		// meta column id => sortby value used in query
		'publishdate' => 'publishdate',
	);

	return wp_parse_args( $custom, $columns );
}

add_action( 'pre_get_posts', 'rssmi_feed_source_order' );

function rssmi_feed_source_order( $query ) {
	if ( ! is_admin() )
		return;
	$post_type = $query->get( 'post_type' );
	if ( $post_type == 'rssmi_feed_item' ) {
		$query->set( 'orderby', 'publishdate' );
		$orderby = $query->get( 'orderby' );
		if ( 'publishdate' == $orderby ) {
			$query->set( 'meta_key', 'rssmi_item_date' );
			$query->set( 'orderby', 'meta_value_num' );
		}
	}
}

//  ADD THIS ONLY IF YOU DO NOT WANT SHORTCODE POSTS TO BE SEARCHABLE

//	add_filter('pre_get_posts', 'rssmi_search_filter');

function rssmi_search_filter( $query ) {
	if ( is_search() && ! is_admin() ) {
		if ( $query->is_search ) {
			$post_types = get_post_types();
			unset( $post_types['rssmi_feed_item'] );
			$query->set( 'post_type', $post_types );
			return $query;
		}
	}
}

//  This function makes searchable shortcode posts click directly to the feed source

add_filter( 'the_title', 'rssmi_search_modified_post_title' );

function rssmi_search_modified_post_title( $title ) {
	if ( in_the_loop() && is_search() && ! is_admin() ) {
		$post_options = get_option( 'rss_import_items' );
		$targetWindow = $options['targetWindow']; // 0=LB, 1=same, 2=new
		if ( $targetWindow == 0 ) {
			$openWindow = 'class="colorbox"';
		}
		elseif ( $targetWindow == 1 ) {
			$openWindow = 'target=_self';
		}
		else {
			$openWindow = 'target=_blank ';
		}
		global $wp_query;
		$postID = $wp_query->post->ID;
		$myLink = get_post_meta( $postID, 'rssmi_item_permalink', true );
		if ( ! empty( $myLink ) ) {
			$myTitle     = $wp_query->post->post_title;
			$myLinkTitle = '<a href=' . $myLink . ' ' . $openWindow . '>' . $myTitle . '</a>'; // change how the link opens here
			return $myLinkTitle;
		}
	}
	return $title;
}


//**  Bulk edit of Feed Properties **/

add_action( 'bulk_edit_custom_box', 'display_custom_quickedit_category', 10, 2 );

function display_custom_quickedit_category( $column_name, $post_type ) {
	/*  static $printNonce = TRUE;
		if ( $printNonce ) {
			$printNonce = FALSE;
			wp_nonce_field( plugin_basename( __FILE__ ), 'category_edit_nonce' );
		}
*/
	?>
	<fieldset class="inline-edit-col-right inline-edit-category">
		<div class="inline-edit-col column-<?php echo $column_name ?>">
			<label class="inline-edit-group">
				<?php
				switch ( $column_name ) {
					case 'category':
						?><span class="title">Category</span>

						<select style="width: 200px"
								name="rssmi_cat">
							<OPTION selected VALUE=''>-- No Change --</OPTION>
							<?php
							// Generate all items of drop-down list
							$catOptions = get_option( 'rss_import_categories' );
							if (! empty( $catOptions )){
							$catsize = count( $catOptions );
							for ($k = 1;
							$k <= $catsize;
							$k ++) {
							if ( $k % 2 == 0 ) continue;
							$catkey = key( $catOptions );
							$nameValue = $catOptions[$catkey];
							next( $catOptions );
							$catkey = key( $catOptions );
							$IDValue = $catOptions[$catkey];
							?>
							<option value="<?php echo $IDValue; ?>"
								<?php echo selected( $IDValue,
									$rssmi_cat ); ?>>
								<?php echo $nameValue; ?>
								<?php
								next( $catOptions );
								}
								}?>
						</select>

						<?php
						break;
						//   case 'inprint':
						?>
					<?php
					// break;
				}
				?>
			</label>
		</div>
	</fieldset>
<?php
}


add_action( 'wp_ajax_save_bulk_edit_category', 'save_bulk_edit_category' );
function save_bulk_edit_category() {
	// TODO perform nonce checking
	// get our variables

	$post_ids = ( ! empty( $_POST['post_ids'] ) ) ? $_POST['post_ids'] : array();
	$category = ( ! empty( $_POST['category'] ) ) ? $_POST['category'] : null;


	// if everything is in order
	if ( ! empty( $post_ids ) && is_array( $post_ids ) ) {
		foreach ( $post_ids as $post_id ) {
			if ( ! is_null( $category ) ) {
				update_post_meta( $post_id, 'rssmi_cat', $category );
			}

		}
	}

	die();
}


add_filter( 'pre_get_posts', 'myfilter' );


function myfilter( $query ) {
	$feed_id = ( ! empty( $_GET['rssmi_feed_id'] ) ) ? $_GET['rssmi_feed_id'] : null;

	if ( isset( $_GET['post_type'] ) && ( $_GET['post_type'] == 'rssmi_feed_item' ) ) {
		if ( is_admin() ) {
			$query->set( 'meta_key', 'rssmi_item_feed_id' );
			$query->set( 'meta_value', $feed_id );

		}
	}
	return $query;
}


?>