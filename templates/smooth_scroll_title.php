<?php
	add_action('wp_footer','smooth_scroll_scripts');  // DON'T MESS WITH THIS

$strDate=rssmi_get_default_date_format();
	


	
	$readable .= '	<div id="wprssmi_main_container">';


	$readable .= '<div id="wprssmi_RssParade">';
	
	
	
	
	//  BEGIN don't mess with this php code below
	foreach($myarray as $items) {

	if ($pag!==1){ 	
		$total = $total +1;
		if ($maxperPage>0 && $total>=$maxperPage) break;
	}

	$idnum=$idnum +1;
	//  END don't mess with this php code
$openWindow=rssmi_lightbox_filter($items["mylink"],$targetWindow); //Lightbox filter


				
					$readable .= '	<div class="wprssmi_item_inner">';

		     

		            $readable .= '<div class="wprssmi_container">';
		            $readable .= '<p class="wprssmi_title"><a '.$openWindow.' href='.$items["mylink"].' '.($noFollow==1 ? 'rel=nofollow':'').' style="color:'.$anchorcolor.'">'.$items["mytitle"].'</a></p>';
		            $readable .= '<p><div class="wprssmi-excerpt">'.showexcerpt($items["mydesc"],0,$openWindow,0,$items["mylink"],$adjustImageSize,$float,$noFollow,$items["myimage"],$items["mycatid"]).'</span></div></p>';
		
		
		
		
		
					if ((strpos($items["mylink"],'www.youtube.com')>0 || strpos($items["mylink"],'player.vimeo')>0 ) && $showVideo==1){

						if ($vt=='yt'){
					//		$readable = rssmi_yt_video_content($items["mydesc"])."<br>";
						}else if ($vt=='vm'){
					//		$readable = rssmi_vimeo_video_content($items["mydesc"])."<br>";
						}


			//			$readable .= '<iframe class="rss_multi_frame" title=".$items["mytitle"]." width="420" height="315" src="'.$items["mylink"].'" frameborder="0" allowfullscreen allowTransparency="true"></iframe>';

					}
		
		
		
		
		
					//$readable .=  '<span style="'.$datestyle.'">'. date_i18n("D, M d, Y",$items["mystrdate"]).'</span><br />';
		            //$readable .= '<p><a '.$openWindow.' href='.$items["mylink"].' '.($noFollow==1 ? 'rel=nofollow':'').' style="color:'.$anchorcolor.'">'.$items["myGroup"].'</a></p>';
		           	$readable .= '</div>';
		          	$readable .= '</div>';
									
									
				} 

	$readable .= '</div>';
	$readable .= '</div>';

?>

<!-- Don't change any code below here until you really know what you're doing -->
<script type="text/javascript">

jQuery(document).ready(function() {
	jQuery("#wprssmi_RssParade").smoothDivScroll({ 
		autoScrollingMode: "always", 
		autoScrollingDirection: "endlessLoopRight", 
		autoScrollingStep: 1, 
		autoScrollingInterval: 25 
	});

	// Logo parade event handlers
	jQuery("#wprssmi_RssParade").bind("mouseover", function() {
		jQuery(this).smoothDivScroll("stopAutoScrolling");
	}).bind("mouseout", function() {
		jQuery(this).smoothDivScroll("startAutoScrolling");
	});

});
</script>
