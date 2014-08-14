<?php

//  RSS FEED FUNCTIONS

function rssmi_feed() 
{
	wp_rss_multi_importer_feed();
}

add_action('init', 'rssmi_rss');

function rssmi_rss()
{
	$feed_options = get_option('rss_feed_options', 'option not found');
	if (!empty($feed_options) && isset($feed_options['feedslug']))
	{
//		echo "yes";
		add_feed( $feed_options['feedslug'] , 'rssmi_feed');
//		die;
	}
}

function rss_text_limit($striptags=0,$string, $length, $replacer = '...') 
{
	if ($striptags==1)
	{
		$string = strip_tags($string);
	}
	
	if(strlen($string) > $length)
	{
	    return (preg_match('/^(.*)\W.*$/', substr($string, 0, $length+1), $matches) ? $matches[1] : substr($string, 0, $length)) . $replacer;  
	}
	return $string;
}

function wp_rss_multi_importer_feed()
{	
	header("Content-type: text/xml");
	$catArray=array(0);
	if(!function_exists("wprssmi_hourly_feed")) 
	{
		function wprssmi_hourly_feed() { return 0; }  // no caching of RSS feed
	}
//	add_filter( 'wp_feed_cache_transient_lifetime', 'wprssmi_hourly_feed' );
  
   	$options = get_option('rss_import_options','option not found');
	$option_items = get_option('rss_import_items','option not found');
	$feed_options = get_option('rss_feed_options', 'option not found');

	if ($option_items==false) 
	{
		return "You need to set up the WP RSS Multi Importer Plugin before any results will show here.  Just go into the <a href='/wp-admin/options-general.php?page=wp_rss_multi_importer_admin'>settings panel</a> and put in some RSS feeds";
	}
   if(!empty($option_items))
   {
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
		
		if ($thisfeed!='') { $maxposts=$thisfeed;	}
		
		$targetWindow=$options['targetWindow'];  // 0=LB, 1=same, 2=new
		$floatType=$options['floatType'];
		$noFollow=$options['noFollow'];
		$showmore=$options['showmore'];
		$cb=$options['cb'];  // 1 if colorbox should not be loaded
		$pag=$options['pag'];  // 1 if pagination
		$perPage=$options['perPage'];
		if(empty($options['sourcename']))
		{
			$attribution='';
		}else{
			$attribution=$options['sourcename'].' ';
		}

		if ($floatType=='1')
		{
			$float="left";
		}else{
			$float="none";	
		}
		
		if ($parmfloat!='') {	$float=$parmfloat;	}

		$cacheMin=0;
		
		if ($cacheMin=='')
		{	
			$cacheMin=0;  //set caching minutes		
		}

		global $wpdb;
		$myarray = array();
		$feed_array=$wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE  post_type ='rssmi_feed' AND post_status='publish'");

		if(empty($feed_array) || is_null($feed_array)) return 'No feed urls entered';

		foreach ($feed_array as $feed)
		{
			$feedlimit=0;
			$rssmi_cat= get_post_meta($feed->ID, 'rssmi_cat', true );
			$rssmi_url= get_post_meta($feed->ID, 'rssmi_url', true );
			$rssmi_user= get_post_meta($feed->ID, 'rssmi_user', true );
			$rssmi_title= get_the_title( $feed->ID); 
			$catSourceArray=array(
				"myGroup"=>$rssmi_source,
				"mycatid"=>$rssmi_cat
			);
			
			/*
			
			if (((!in_array(0, $catArray ) && in_array(intval($rssmi_cat), $catArray ))) || in_array(0, $catArray )) 
			{
				$myfeeds[] = array("FeedName"=>$rssmi_title,"FeedURL"=>$rssmi_url,"FeedCatID"=>$rssmi_cat, "FeedUser"=>$rssmi_user); 
			}
		}
		
		if(!$maxposts) { $maxposts=20;}
		

		$myarray=get_my_array($myfeeds,$sortDir,$maxposts, $dumpthis);

	*/
	
		$rssmi_sql = "SELECT a.post_id,b.meta_key,b.meta_value FROM $wpdb->postmeta as a inner join $wpdb->postmeta as b on a.post_id=b.post_id WHERE a.meta_value =$feed->ID and b.meta_key='rssmi_item_date' order by b.meta_value desc"; 
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
		


		if (!isset($myarray) || empty($myarray))
		{
			return "There is a problem with the feeds you entered.  Go to our <a href='http://www.wprssimporter.com/faqs'>support page</a> and we'll help you diagnose the problem.";
				exit;
		}
		
		//$myarrary sorted by mystrdate
//print_r($myarray);
		foreach ($myarray as $key => $row) 
		{
			$dates[$key]  = $row["mystrdate"]; 
		}
		
		//var_dump($myarray);   //  UNCOMMMENT THIS LINE TO PRINT OUT THE ARRAY, WHICH SHOWS IT EXISTS//
		
		//SORT, DEPENDING ON SETTINGS
		
		if($sortDir==1)
		{
			array_multisort($dates, SORT_ASC, $myarray);
		}else{
			array_multisort($dates, SORT_DESC, $myarray);		
		}
			if(!$maxposts) { $maxposts=20;}
			
			
//		print_r($myarray);
		header('Content-Type: ' . feed_content_type('rss-http') . '; charset=' . get_option('blog_charset'), true);

//	echo get_option('blog_charset');
//	die;
	
//		echo '<?xml version="1.0" encoding="'.get_option('blog_charset').'"?'.'>'; 
?>

<rss version="2.0">
<channel>
	<title><?php echo $feed_options['feedtitle'] ?></title>
	<link>some link</link>
	<description><?php echo $feed_options['feeddesc'] ?></description>
	<language>en-us</language>
<?php
		$total=0;	
		foreach($myarray as $items) 
		{
			$total = $total +1;
			if ($total>20) {	break;	}
?>
	<item>		
		<title><?php echo $items["mytitle"]?></title>	
		<link><?php echo $items["mylink"]?></link>
			
		<description><?php echo '<![CDATA['.rss_text_limit($feed_options['striptags'],$items["mydesc"], 500).'<br/><br/>Keep on reading: <a href="'.$items["mylink"].'">'.$items["mytitle"].'</a>'.']]>';  ?></description>
		<pubdate><?php echo  date_i18n("D, M d, Y",$items["mystrdate"])?></pubdate>
		<guid><?php echo $items["mylink"]?></guid>
	</item>	
<?php
		}
?>
</channel>
</rss>
<?php   
	}
}
?>