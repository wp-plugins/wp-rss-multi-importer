<?php
/*  Plugin Name: RSS Multi Importer
  Plugin URI: http://www.allenweiss.com/wp_plugin
  Description: Import multiple RSS feeds, categorize them, 8 templates for customization, sort by date, assign an attribution label, limit the number of items per feed and much more.
  Version: 2.34
  Author: Allen Weiss
  Author URI: http://www.allenweiss.com/wp_plugin
  License: GPL2  - most WordPress plugins are released under GPL2 license terms
*/

/* Set the version number of the plugin. */
define( 'WP_RSS_MULTI_VERSION', 2.34 );

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
require_once ( WP_RSS_MULTI_UTILS . 'template_functions.php' );

/* Load the messages file. */
require_once ( WP_RSS_MULTI_UTILS . 'panel_messages.php' );

/* Load the database functions file. */
require_once ( WP_RSS_MULTI_UTILS . 'db_functions.php' );

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



//ON INIT

add_action('admin_init','wp_rss_multi_importer_start');

function wp_rss_multi_importer_start () {
	
register_setting('wp_rss_multi_importer_options', 'rss_import_items');
register_setting('wp_rss_multi_importer_categories', 'rss_import_categories');	
register_setting('wp_rss_multi_importer_item_options', 'rss_import_options');	 
register_setting('wp_rss_multi_importer_template_item', 'rss_template_item');	 
register_setting('wp_rss_multi_importer_feed_options', 'rss_feed_options');	 
add_settings_section( 'wp_rss_multi_importer_main', '', 'wp_section_text', 'wprssimport' );  

}

add_action('init', 'ilc_farbtastic_script');
function ilc_farbtastic_script() {
  wp_enqueue_style( 'farbtastic' );
  wp_enqueue_script( 'farbtastic' );
}



add_action('admin_menu','wp_rss_multi_importer_menu');

function wp_rss_multi_importer_menu () {
add_options_page('WP RSS Multi-Importer','RSS Multi-Importer','manage_options','wp_rss_multi_importer_admin', 'wp_rss_multi_importer_display');
}




add_action( 'widgets_init', 'src_load_widgets');  //load widget

function src_load_widgets() {
register_widget('WP_Multi_Importer_Widget');
}



function wp_rss_multi_importer_display( $active_tab = '' ) {
?>
	
	<div class="wrap">
	
		<div id="icon-themes" class="icon32"></div>
		<h2>WP RSS Multi-Importer Options</h2>
		<?php settings_errors(); ?>
		
		<?php if( isset( $_GET[ 'tab' ] ) ) {
			$active_tab = $_GET[ 'tab' ];
		} else if( $active_tab == 'setting_options' ) {
				$active_tab = 'setting_options';
		} else if( $active_tab == 'category_options' ) {
			$active_tab = 'category_options';
		} else if( $active_tab == 'style_options' ) {
			$active_tab = 'style_options';
		} else if( $active_tab == 'template_options' ){
				$active_tab = 'template_options';
		} else if( $active_tab == 'feed_options' ){
				$active_tab = 'feed_options';
		} else if( $active_tab == 'more_options' ){
			$active_tab = 'more_options';
		} else { $active_tab = 'items_list';	
			
		} // end if/else ?>
		
		<h2 class="nav-tab-wrapper">
			<a href="?page=wp_rss_multi_importer_admin&tab=items_list" class="nav-tab <?php echo $active_tab == 'items_list' ? 'nav-tab-active' : ''; ?>"><?php  _e("RSS Feeds")?></a>
				<a href="?page=wp_rss_multi_importer_admin&tab=setting_options" class="nav-tab <?php echo $active_tab == 'setting_options' ? 'nav-tab-active' : ''; ?>"><?php  _e("Setting Options")?></a>
			<a href="?page=wp_rss_multi_importer_admin&tab=category_options" class="nav-tab <?php echo $active_tab == 'category_options' ? 'nav-tab-active' : ''; ?>"><?php  _e("Category Options")?></a>
			<a href="?page=wp_rss_multi_importer_admin&tab=style_options" class="nav-tab <?php echo $active_tab == 'style_options' ? 'nav-tab-active' : ''; ?>"><?php  _e("Style Options")?></a>
				<a href="?page=wp_rss_multi_importer_admin&tab=template_options" class="nav-tab <?php echo $active_tab == 'template_options' ? 'nav-tab-active' : ''; ?>"><?php  _e("Template Options")?></a>
				<a href="?page=wp_rss_multi_importer_admin&tab=feed_options" class="nav-tab <?php echo $active_tab == 'feed_options' ? 'nav-tab-active' : ''; ?>"><?php  _e("Export Feed Options")?></a>
				<a href="?page=wp_rss_multi_importer_admin&tab=more_options" class="nav-tab <?php echo $active_tab == 'more_options' ? 'nav-tab-active' : ''; ?>"><?php  _e("Help & More...")?></a>
		</h2>

			<?php
			
				if( $active_tab == 'items_list' ) {
						
			wp_rss_multi_importer_items_page();
			
		} else if ( $active_tab == 'setting_options' ) {

				wp_rss_multi_importer_options_page();
			
		} else if ( $active_tab == 'category_options' ) {
			
			wp_rss_multi_importer_category_page();
			
		} else if ( $active_tab == 'style_options' ) {
			
			wp_rss_multi_importer_style_tags();
			
		} else if ( $active_tab == 'template_options' ) {
				
			wp_rss_multi_importer_template_page();	
			
		} else if ( $active_tab == 'feed_options' ) {
				
			wp_rss_multi_importer_feed_page();	
			
			
			
				
				} else {
						wp_rss_multi_importer_more_page();
				
				} // end if/else  	
				
				
			
			?>

		
	</div><!-- /.wrap -->
<?php
} 



function wp_section_text() {
    echo '<div class="postbox"><h3><label for="title">Usage Details</label></h3><div class="inside"><H4>Step 1:</H4><p>Enter a name and the full URL (with http://) for each of your feeds. The name will be used to identify which feed produced the link (see the Attribution Label option below). Click Save Settings.</p><H4>Step 2:</H4><p>Go to the tab called <a href="/wp-admin/options-general.php?page=wp_rss_multi_importer_admin&tab=setting_options">Setting Options</a>, choose options and click Save Settings.</p><H4>Step 3:</H4><p>Put this shortcode, [wp_rss_multi_importer], on the page you wish to have the feed.</p>';
    echo '<p>You can also assign each feed to a category. Go to the Category Options tab, enter as many categories as you like.</p><p>Then you can restrict what shows up on a given page by using this shortcode, like [wp_rss_multi_importer category="2"] (or [wp_rss_multi_importer category="1,2"] to have two categories) on the page you wish to have only show feeds from those categories.</p></div></div>';

}
 




   
   /**
   *  Shortcode setup and call (shortcode is [wp_rss_multi_importer]) with options
   */
   
   add_shortcode('wp_rss_multi_importer','wp_rss_multi_importer_shortcode');
 

// Helper functions


	function showexcerpt($content, $maxchars,$openWindow,$stripAll,$thisLink,$adjustImageSize,$float,$noFollow)  //show excerpt function
	{
		global $morestyle;
    $content=CleanHTML($content);

	if ($stripAll==1){
			$content=strip_tags(html_entity_decode($content));	
			$content= limitwords($maxchars,$content);	
	}else{
		$content=strip_tags(html_entity_decode($content),'<a><img>');
		$content=findalignImage($maxchars,$content,$adjustImageSize,$float,$openWindow);	
}
	
	//return str_replace($morestyle, "<a href=".$thisLink." ".$openWindow.">".$morestyle."</a>", $content);
	
		return str_replace($morestyle, "<a href=".$thisLink." ".$openWindow.'' 	.($noFollow==1 ? 'rel=nofollow':'').">".$morestyle."</a>", $content);

	}
	


	
	function limitwords($maxchars,$content){
	
		global $morestyle;
		if($maxchars !=99){


		  $words = explode(' ', $content, ($maxchars + 1));
	  			if(count($words) > $maxchars)
		  				array_pop($words);
	 				
						$content = implode(' ', $words)." ". $morestyle;
						
	
		}else{
						$content=$content."";
		}
		return $content;
	}
	
	
	
	
	
	function CleanHTML($content){
		
		$content=str_replace("&nbsp;&raquo;", "", $content);
		$content=str_replace("&nbsp;", " ", $content);	
		
	return 	$content;
	}
	
	
	

	
	function findalignImage($maxchars,$content,$adjustImageSize,$float,$openWindow){
		
		
	$strmatch='^\s*\<a.*href="(.*)">\s*(<img.*src=".*" \/?>)[^\<]*<\/a\>\s*(.*)$'; //match leading hyperlinked image
		
		$strmatch2='^(\s*)(<img.*src=".*"\s*?\/>)\s*(.*)$';  //match leading non-hyperlinked image  
		

			if (preg_match("/$strmatch/sU", $content, $matches) || preg_match("/$strmatch2/sU", $content, $matches)){


			if ($adjustImageSize==1){
				$tabledImage= "<div class=\"imagefix\" style=\"float:".$float.";\">".resize_image($matches[2])."</div>";
			}else{
				$tabledImage= "<div class=\"imagefix\" style=\"float:".$float.";\">".$matches[2]."</div>";
			}	
			
		
		
		
				$content=str_replace($matches[2], $tabledImage, $content); //format the leading image if it exists
				
			
				
				
				$content=str_replace($matches[3], limitwords($maxchars,strip_tags($matches[3])), $content); //strip away all tags after the leading image
				
				
					$content=str_replace("<a ","<a ".$openWindow, $content,  $count = 1);  // add window open to leading image, if it exists

		}else{
		
			
			$content = limitwords($maxchars,strip_tags($content));
		}
	return $content;	
	}
	
	
	function remove_img_hw( $imghtml ) {
	 $imghtml = preg_replace( '/(width|height)=\"\d*\"\s?/', "", $imghtml );
	    return $imghtml;
	}
	
	function resize_image($imghtml){
		global $maximgwidth;
		preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $imghtml, $matches);
		$thisWidth=getimagesize($matches[1]);
		if ($thisWidth > $maxImgWidth){
		return str_replace("<img", "<img width=".$maximgwidth, remove_img_hw($imghtml));
			}else{
		return str_replace("<img", "<img width=".$thisWidth, remove_img_hw($imghtml));		
	}
}








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
	$thisfeed = $parms['thisfeed'];  // max posts per category
	$timerstop = $parms['timer'];
	$dumpthis= $parms['dumpthis'];  //diagnostic parameter
	$cachename='wprssmi_'.$thisCat;
	$cachetime=$parms['cachetime'];
	
   	$readable = '';
   	$options = get_option('rss_import_options','option not found');
	$option_items = get_option('rss_import_items','option not found');

	if ($option_items==false) return "You need to set up the WP RSS Multi Importer Plugin before any results will show here.  Just go into the <a href='/wp-admin/options-general.php?page=wp_rss_multi_importer_admin'>settings panel</a> and put in some RSS feeds";


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


$cacheMin=$options['cacheMin'];
$maxposts=$options['maxfeed'];

if ($thisfeed!='') $maxposts=$thisfeed;


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
	
// $rssCatID=$option_items[$key];  ///this should be the category ID



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





if ($cacheMin!==0){
set_transient($cachename, $myarray, 60*$cacheMin);  //  added  for transient cache
}

}  //  added  for transient cache

if ($timerstop==1){
 timer_stop(1); echo ' seconds<br>';  //TIMER END for testing purposes
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
	return "One more step...go into the the <a href='/wp-admin/options-general.php?page=wp_rss_multi_importer_admin&tab=setting_options'>Settings Panel and choose a Template.</a>";
	}
	

	require( WP_RSS_MULTI_TEMPLATES . $template );

    


}

	//pagination controls at bottom
	
if ($pag==1){  
$readable .='<div class="pag_box">';

if($numPages > $currentPage && ($currentPage + 1) < $numPages)  
    $readable .=  '<a href="http://'.$currentURL.'pg=' . ($currentPage + 1) . '" class="more-prev">Next page »</a>';

	if($currentPage > 0 && $currentPage < $numPages)  
	    $readable .= '<a href="http://'.$currentURL.'pg=' . ($currentPage - 1) . '" class="more-prev">« Previous page</a>';  

$readable .='</div>';

}
     //end pagination controls at bottom
	

return $readable;

   }
   

    
   
?>