<?php
/*  Plugin Name: RSS Multi Importer
  Plugin URI: http://www.allenweiss.com/wp_plugin
  Description: Imports and merges multiple RSS Feeds. 8 templates, customize, sort, feed to post option, limit feeds/page by category, include excerpts with images and much more. 
  Version: 2.47
  Author: Allen Weiss
  Author URI: http://www.allenweiss.com/wp_plugin
  License: GPL2  - most WordPress plugins are released under GPL2 license terms
*/

/* Set the version number of the plugin. */
define( 'WP_RSS_MULTI_VERSION', 2.47 );

 /* Set constant path to the plugin directory. */
define( 'WP_RSS_MULTI_PATH', plugin_dir_path( __FILE__ ) );

 /* Set constant url to the plugin directory. */
define( 'WP_RSS_MULTI_URL', plugin_dir_url( __FILE__ ) );

/* Set the constant path to the plugin's includes directory. */
define( 'WP_RSS_MULTI_INC', WP_RSS_MULTI_PATH . trailingslashit( 'inc' ), true );

/* Set the constant path to the plugin's utils directory. */
define( 'WP_RSS_MULTI_UTILS', WP_RSS_MULTI_PATH . trailingslashit( 'utils' ), true );

/* Set the constant path to the plugin's template directory. */
define( 'WP_RSS_MULTI_TEMPLATES', WP_RSS_MULTI_PATH . trailingslashit( 'templates' ), true );

/* Set the constant path to the plugin's scripts directory. */
define( 'WP_RSS_MULTI_SCRIPTS', WP_RSS_MULTI_URL . trailingslashit( 'scripts' ), true );

/* Set the constant path to the plugin's css directory. */
define( 'WP_RSS_MULTI_CSS', WP_RSS_MULTI_URL . trailingslashit( 'css' ), true );

/* Set the constant path to the plugin's image directory. */
define( 'WP_RSS_MULTI_IMAGES', WP_RSS_MULTI_URL . trailingslashit( 'images' ), true );

/* Load the template functions file. */
require_once ( WP_RSS_MULTI_INC . 'template_functions.php' );

/* Load the messages file. */
require_once ( WP_RSS_MULTI_INC . 'panel_messages.php' );

/* Load the database functions file. */
require_once ( WP_RSS_MULTI_INC . 'db_functions.php' );

/* Load the excerpt functions file. */
require_once ( WP_RSS_MULTI_INC . 'excerpt_functions.php' );

/* Load the cron file. */
require_once ( WP_RSS_MULTI_INC . 'cron.php' );

/* Load the options file. */
require_once ( WP_RSS_MULTI_INC . 'options.php' );

/* Load the widget functions file. */
require_once ( WP_RSS_MULTI_INC . 'rss_multi_importer_widget.php' );

/* Load the upgrade file. */
require_once ( WP_RSS_MULTI_INC . 'upgrade.php' );

/* Load the scripts files. */
require_once ( WP_RSS_MULTI_INC . 'scripts.php' );

/* Load the feed files. */
require_once ( WP_RSS_MULTI_INC . 'rss_feed.php' );

require_once(  WP_RSS_MULTI_INC . 'import_posts.php');  // testing

/* Load the admin_init files. */
require_once ( WP_RSS_MULTI_INC . 'admin_init.php' );

    //wp_rss_multi_importer_post(); // testing

   
   /**
   *  Shortcode setup and call (shortcode is [wp_rss_multi_importer]) with options
   */
   
   add_shortcode('wp_rss_multi_importer','wp_rss_multi_importer_shortcode');
 

function wp_rss_mi_lang_init() {
  load_plugin_textdomain( 'wp-rss-multi-importer', false, dirname( plugin_basename( __FILE__ ) ). '/lang/' );   // load the language files
}
add_action('plugins_loaded', 'wp_rss_mi_lang_init');



//  MAIN SHORTCODE OUTPUT FUNCTION


   
   function wp_rss_multi_importer_shortcode($atts=array()){
	

	
add_action('wp_footer','footer_scripts');

if(!function_exists("wprssmi_hourly_feed")) {
function wprssmi_hourly_feed() { return 0; }
}
add_filter( 'wp_feed_cache_transient_lifetime', 'wprssmi_hourly_feed' );


	
	$siteurl= get_site_url();
    $cat_options_url = $siteurl . '/wp-admin/options-general.php?page=wp_rss_multi_importer_admin&tab=category_options/';
	$images_url = $siteurl . '/wp-content/plugins/' . basename(dirname(__FILE__)) . '/images';	
	
	$parms = shortcode_atts(array(  //Get shortcode parameters
		'category' => 0, 
		'hdsize' => '16px', 
		'hdweight'=>400, 
		'anchorcolor' =>'',
		'testyle'=>'color: #000000; font-weight: bold;margin: 0 0 0.8125em;',
		'maximgwidth'=> 150,
		'datestyle'=>'font-style:italic;',
		'floattype'=>'',
		'showdate' => 1,
		'showgroup'=> 1,
		'thisfeed'=>'',
		'timer' => 0, 
		'dumpthis' =>0,
		'cachetime'=>NULL,
		'pinterest'=>0,
		'maxperpage' =>0,
		'noimage' => 0,
		'morestyle' =>'[...]'
		), $atts);
	
	$anchorcolor=$parms['anchorcolor'];
	$datestyle=$parms['datestyle'];
	$hdsize = $parms['hdsize'];
    $thisCat = $parms['category'];
	$parmfloat=$parms['floattype'];
	$catArray=explode(",",$thisCat);
	$showdate=$parms['showdate'];
	$showgroup=$parms['showgroup'];
	$hdweight = $parms['hdweight'];
	$testyle = $parms['testyle'];
	global $morestyle;
    $morestyle = $parms['morestyle'];
	global $maximgwidth;
	$maximgwidth = $parms['maximgwidth'];
	$thisfeed = $parms['thisfeed'];  // max posts per feed
	$timerstop = $parms['timer'];
	$dumpthis= $parms['dumpthis'];  //diagnostic parameter
	$cachename='wprssmi_'.$thisCat;
	$cachetime=$parms['cachetime'];
	$pinterest=$parms['pinterest'];
	$parmmaxperpage=$parms['maxperpage'];
	$noimage=$parms['noimage'];
   	$readable = '';
   	$options = get_option('rss_import_options','option not found');
	$option_items = get_option('rss_import_items','option not found');

	if ($option_items==false) return _e("You need to set up the WP RSS Multi Importer Plugin before any results will show here.  Just go into the <a href='/wp-admin/options-general.php?page=wp_rss_multi_importer_admin'>settings panel</a> and put in some RSS feeds", 'wp-rss-multi-importer');


$cat_array = preg_grep("^feed_cat_^", array_keys($option_items));

	if (count($cat_array)==0) {  // for backward compatibility
		$noExistCat=1;
	}else{
		$noExistCat=0;	
	}



    
   if(!empty($option_items)){
	
//GET PARAMETERS  
$size = count($option_items);
$sortDir=$options['sortbydate'];  // 1 is ascending
$stripAll=$options['stripAll'];
$todaybefore=$options['todaybefore'];
$adjustImageSize=$options['adjustImageSize'];
$showDesc=$options['showdesc'];  // 1 is show
$descNum=$options['descnum'];
$maxperPage=$options['maxperPage'];

$showcategory=$options['showcategory'];
$cacheMin=$options['cacheMin'];
$maxposts=$options['maxfeed'];

if ($thisfeed!='') $maxposts=$thisfeed;

$showsocial=$options['showsocial'];
$targetWindow=$options['targetWindow'];  // 0=LB, 1=same, 2=new
$floatType=$options['floatType'];
$noFollow=$options['noFollow'];
$showmore=$options['showmore'];
$cb=$options['cb'];  // 1 if colorbox should not be loaded
$pag=$options['pag'];  // 1 if pagination
$perPage=$options['perPage'];
if(empty($options['sourcename'])){
	$attribution='';
}else{
	$attribution=$options['sourcename'].': ';
}

if ($floatType=='1'){
	$float="left";
}else{
	$float="none";	
}

if ($parmfloat!='') $float=$parmfloat;
if($parmmaxperpage!=0) $maxperPage=$parmmaxperpage;
if ($noimage==1) $stripAll=1;

if ($pinterest==1){
		$divfloat="left";
	}else{
		$divfloat='';	
	}

if ($cacheMin==''){
$cacheMin=0;  //set caching minutes	
}


if (!is_null($cachetime)) {$cacheMin=$cachetime;}  //override caching minutes with shortcode parameter	




if ($cb!=='1'){
add_action('wp_footer','colorbox_scripts');  // load colorbox only if not indicated as conflict
   }

$template=$options['template'];


timer_start();  //TIMER START - for testing purposes


	$myarray=get_transient($cachename);  // added  for transient cache
	
	if ($cacheMin==0){
		delete_transient($cachename); 
	}
	
   if (false===$myarray) {   //  added  for transient cache - only get feeds and put into array if the array isn't cached (for a given category set)



   for ($i=1;$i<=$size;$i=$i+1){

	

   			$key =key($option_items);
				if ( !strpos( $key, '_' ) > 0 ) continue; //this makes sure only feeds are included here...everything else are options
				
   			$rssName= $option_items[$key];

   
   			next($option_items);
   			
   			$key =key($option_items);
   			
   			$rssURL=$option_items[$key];



  	next($option_items);
	$key =key($option_items);
	
 $rssCatID=$option_items[$key];  ///this should be the category ID



if (((!in_array(0, $catArray ) && in_array($option_items[$key], $catArray ))) || in_array(0, $catArray ) || $noExistCat==1) {


$myfeeds[] = array("FeedName"=>$rssName,"FeedURL"=>$rssURL,"FeedCatID"=>$rssCatID); //with Feed Category ID

	
}
   
$cat_array = preg_grep("^feed_cat_^", array_keys($option_items));  // for backward compatibility

	if (count($cat_array)>0) {

  next($option_items); //skip feed category
}

   }

  if ($maxposts=="") return _e("One more step...go into the the <a href='/wp-admin/options-general.php?page=wp_rss_multi_importer_admin&tab=setting_options'>Settings Panel and choose Options.</a>", 'wp-rss-multi-importer');  // check to confirm they set options

if (empty($myfeeds)){
	
	return _e("You've either entered a category ID that doesn't exist or have no feeds configured for this category.  Edit the shortcode on this page with a category ID that exists, or <a href=".$cat_options_url.">go here and and get an ID</a> that does exist in your admin panel.", 'wp-rss-multi-importer');
	exit;
}


if ($dumpthis==1){
	echo "<strong>Feeds</strong><br>";
	var_dump($myfeeds);
}


 
 foreach($myfeeds as $feeditem){


	$url=(string)($feeditem["FeedURL"]);

	
	while ( stristr($url, 'http') != $url )
		$url = substr($url, 1);

if (empty($url)) {continue;}


	$url = esc_url_raw(strip_tags($url));


				$feed = fetch_feed($url);

	
	

	if (is_wp_error( $feed ) ) {
		
		if ($dumpthis==1){
				echo $feed->get_error_message();
				}	
		if ($size<4){
			return _e("You have one feed and it's not valid.  This is likely a problem with the source of the RSS feed.  Contact our support forum for help.", 'wp-rss-multi-importer');
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
		
			
					
					
						if ($enclosure = $item->get_enclosure()){
							if(!IS_NULL($item->get_enclosure()->get_thumbnail())){			
								$mediaImage=$item->get_enclosure()->get_thumbnail();
							}else if (!IS_NULL($item->get_enclosure()->get_link())){
								$mediaImage=$item->get_enclosure()->get_link();	
							}

						}
						
						$myarray[] = array("mystrdate"=>strtotime($item->get_date()),"mytitle"=>$item->get_title(),"mylink"=>$item->get_link(),"myGroup"=>$feeditem["FeedName"],"mydesc"=>$item->get_content(),"myimage"=>$mediaImage,"mycatid"=>$feeditem["FeedCatID"]);
				
						unset($mediaImage);
			}

		}else{	

		for ($i=0;$i<=$maxposts-1;$i++){
				$item = $feed->get_item($i);
				if (empty($item))	continue;	
				
					
		
				
				
			if ($enclosure = $item->get_enclosure()){

				if(!IS_NULL($item->get_enclosure()->get_thumbnail())){			
					$mediaImage=$item->get_enclosure()->get_thumbnail();
				}else if (!IS_NULL($item->get_enclosure()->get_link())){
					$mediaImage=$item->get_enclosure()->get_link();	
				}
				
			}
				
	
				$myarray[] = array("mystrdate"=>strtotime($item->get_date()),"mytitle"=>$item->get_title(),"mylink"=>$item->get_link(),"myGroup"=>$feeditem["FeedName"],"mydesc"=>$item->get_content(),"myimage"=>$mediaImage,"mycatid"=>$feeditem["FeedCatID"]);
				
					
						unset($mediaImage);
					
				}	
		}


	}





if ($cacheMin!==0){
set_transient($cachename, $myarray, 60*$cacheMin);  //  added  for transient cache
}

}  //  added  for transient cache

if ($timerstop==1){
 timer_stop(1); echo ' seconds<br>';  //TIMER END for testing purposes
}





//  CHECK $myarray BEFORE DOING ANYTHING ELSE //

if ($dumpthis==1){
	echo "<br><strong>Array</strong><br>";
	var_dump($myarray);
	exit;
}
if (!isset($myarray) || empty($myarray)){
	
	return _e("There is a problem with the feeds you entered.  Go to our <a href='http://www.allenweiss.com/wp_plugin'>support page</a> and we'll help you diagnose the problem.", 'wp-rss-multi-importer');
		exit;
}

global $isMobileDevice;
if (isset($isMobileDevice) && $isMobileDevice==1){  //open mobile device windows in new tab
	$targetWindow=2;
	
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
$idnum=0;

//for pagination
$currentPage = trim($_REQUEST[pg]);
$currentURL = $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"]; 
$currentURL = str_replace( '&pg='.$currentPage, '', $currentURL );
$currentURL = str_replace( '?pg='.$currentPage, '', $currentURL );

if ( strpos( $currentURL, '?' ) == 0 ){
	$currentURL=$currentURL.'?';
}else{
	$currentURL=$currentURL.'&';	
}



//pagination controls and parameters


if (!isset($perPage)){$perPage=5;}

$numPages = ceil(count($myarray) / $perPage);
if(!$currentPage || $currentPage > $numPages)  
    $currentPage = 0;
$start = $currentPage * $perPage;  
$end = ($currentPage * $perPage) + $perPage;

	
		if ($pag==1){   //set up pagination array and put into myarray
	foreach($myarray AS $key => $val)  
		{  
	    if($key >= $start && $key < $end)  
	        $pagedData[] = $myarray[$key];  
		}
		
			$myarray=$pagedData;
	}
      //end set up pagination array and put into myarray



	
//  templates checked and added here

	if (!isset($template) || $template=='') {
	return _e("One more step...go into the <a href='/wp-admin/options-general.php?page=wp_rss_multi_importer_admin&tab=setting_options'>Settings Panel and choose a Template.</a>", 'wp-rss-multi-importer');
	}
	

	require( WP_RSS_MULTI_TEMPLATES . $template );

    


}

	//pagination controls at bottom
	
if ($pag==1){  
$readable .='<div class="pag_box">';

if($numPages > $currentPage && ($currentPage + 1) < $numPages)  
    $readable .=  '<a href="http://'.$currentURL.'pg=' . ($currentPage + 1) . '" class="more-prev">'.__('Next page', 'wp-rss-multi-importer').' »</a>';

	if($currentPage > 0 && $currentPage < $numPages)  
	    $readable .= '<a href="http://'.$currentURL.'pg=' . ($currentPage - 1) . '" class="more-prev">« '.__('Previous page', 'wp-rss-multi-importer').'</a>';  

$readable .='</div>';

}
     //end pagination controls at bottom
	

return $readable;

   }
   

    
   
?>