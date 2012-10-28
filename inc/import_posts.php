<?php




//  POST FEED FUNCTIONS

function rssmi_import_feed_post() {
	
	$post_options = get_option('rss_post_options');
	
	if($post_options['active']==1){
		wp_rss_multi_importer_post();
	}
}




add_action('wp_ajax_fetch_now', 'fetch_rss_callback');

function fetch_rss_callback() {

	$post_options = get_option('rss_post_options');

		if($post_options['active']==1){

			wp_rss_multi_importer_post();
	        echo '<h3>The most recent feeds have been put into posts.</h3>';

		}else{
			
	 		echo '<h3>Nothing was done because you have not activated this service.</h3>';
}

	die(); 
}





function wp_rss_multi_importer_post(){
	


if(!function_exists("wprssmi_hourly_feed")) {
function wprssmi_hourly_feed() { return 0; }  // no caching of RSS feed
}
add_filter( 'wp_feed_cache_transient_lifetime', 'wprssmi_hourly_feed' );





	
  
   	$options = get_option('rss_import_options','option not found');
	$option_items = get_option('rss_import_items','option not found');
	$post_options = get_option('rss_post_options', 'option not found');

	if ($option_items==false) return "You need to set up the WP RSS Multi Importer Plugin before any results will show here.  Just go into the <a href='/wp-admin/options-general.php?page=wp_rss_multi_importer_admin'>settings panel</a> and put in some RSS feeds";


if(!empty($option_items)){
$cat_array = preg_grep("^feed_cat_^", array_keys($option_items));

	if (count($cat_array)==0) {  // for backward compatibility
		$noExistCat=1;
	}else{
		$noExistCat=0;	
	}

}

    
   if(!empty($option_items)){
	
//GET PARAMETERS  
$size = count($option_items);
$sortDir=0;  // 1 is ascending
$maxperPage=$options['maxperPage'];

$maxposts=$post_options['maxfeed'];
$post_status=$post_options['post_status'];

$thisCategory=$post_options['category'];

if (!isset($post_options['category'])){
	$thisCategory=0;
}
$catArray=array($thisCategory);






$targetWindow=$options['targetWindow'];  // 0=LB, 1=same, 2=new

if(empty($options['sourcename'])){
	$attribution='';
}else{
	$attribution=$options['sourcename'].': ';
}

global $maximgwidth;
$maximgwidth=$post_options['maximgwidth'];;
$descNum=$post_options['descnum'];
$stripAll=$post_options['stripAll'];
$maxperfetch=$post_options['maxperfetch'];
$showsocial=$post_options['showsocial'];
$targetWindow=2;	
$adjustImageSize=1;
$noFollow=0;
$floatType=1;

if ($floatType=='1'){
	$float="left";
}else{
	$float="none";	
}

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

  if ($maxposts=="") return "One more step...go into the the <a href='/wp-admin/options-general.php?page=wp_rss_multi_importer_admin&tab=setting_options'>Settings Panel and choose Options.</a>";  // check to confirm they set options

if (empty($myfeeds)){
	
	return "You've either entered a category ID that doesn't exist or have no feeds configured for this category.  Edit the shortcode on this page with a category ID that exists, or <a href=".$cat_options_url.">go here and and get an ID</a> that does exist in your admin panel.";
	exit;
}



 
 foreach($myfeeds as $feeditem){


	$url=(string)($feeditem["FeedURL"]);

	
	while ( stristr($url, 'http') != $url )
		$url = substr($url, 1);


				$feed = fetch_feed($url);

	
	

	if (is_wp_error( $feed ) ) {
		
		if ($size<4){
			return "You have one feed and it's not valid.  This is likely a problem with the source of the RSS feed.  Contact our support forum for help.";
			exit;

		}else{
	//echo $feed->get_error_message();	
		continue;
		}
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







//  CHECK $myarray BEFORE DOING ANYTHING ELSE //

if ($dumpthis==1){
	var_dump($myarray);
}
if (!isset($myarray) || empty($myarray)){
	
	return "There is a problem with the feeds you entered.  Go to our <a href='http://www.allenweiss.com/wp_plugin'>support page</a> and we'll help you diagnose the problem.";
		exit;
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
	$openWindow='target=_blank ';	
}

	$total=0;

global $wpdb;
foreach($myarray as $items) {
	
	$total = $total +1;
	if ($total>$maxperfetch) break;
	$thisLink=trim($items["mylink"]);
	$mypostids = $wpdb->get_results("select * from $wpdb->postmeta where meta_value='$thisLink'");
	$thisContent='';
if (empty( $mypostids )){  //only post if it hasn't been posted before
  	$post = array();
  	$post['post_status'] = $post_status;
  	$post['post_date'] = date('Y-m-d H:i:s',$items['mystrdate']);
  	$post['post_title'] = trim($items["mytitle"]);
//	$thisContent .= strip_tags($items["mydesc"],'<a><img>');
	
	$thisContent .= showexcerpt($items["mydesc"],$descNum,$openWindow,$stripAll,$items["mylink"],$adjustImageSize,$float,$noFollow,$items["myimage"]);

	$thisContent .= ' <br>Source: <a href='.$items["mylink"].' target=_blank>'.$items["myGroup"].'</a>';
	
	if ($showsocial==1){
	$thisContent .= '<span style="margin-left:10px;"><a href="http://www.facebook.com/sharer/sharer.php?u='.$items["mylink"].'"><img src="'.WP_RSS_MULTI_IMAGES.'facebook.png"/></a>&nbsp;&nbsp;<a href="http://twitter.com/intent/tweet?text='.rawurlencode($items["mytitle"]).'%20'.$items["mylink"].'"><img src="'.WP_RSS_MULTI_IMAGES.'twitter.png"/></a></span>';
	
	}
  	$post['post_content'] = $thisContent;
    $post_id = wp_insert_post($post);
	add_post_meta($post_id, 'rssmi_source_link', $items["mylink"]);
	//wp_set_post_terms( $post_id, $terms, $taxonomy, $append )
	unset($post);
}


}

}


  }

?>