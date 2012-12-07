<?php
//  this is the default template

foreach($myarray as $items) {

if ($pag!==1){ 	
	$total = $total +1;
	if ($maxperPage>0 && $total>=$maxperPage) break;
}

$idnum=$idnum +1;

//  Today and Earlier Script


if ($sortDir==0 && $todaybefore==1){
	
	$from=date("d-m-Y",strtotime('now'));
	$to=date("d-m-Y",$items["mystrdate"]);
	$nodays=(strtotime($to) - strtotime($from))/ (60 * 60 * 24); 


if ($nodays==0){
	

	if ($todayStamp==0){
		$readable.='<span style="'.$testyle.'">Today</span>';
		$todayStamp=1;
		} 
	}

  elseif ($nodays!=0) {
	

		if ($todayStamp==1 || $total==0){

		$readable.= '<span style="'.$testyle.'">Earlier</span>';
			
		$todayStamp=2;
		}
	}
	
}



//  YouTube
if ($targetWindow==0 && strpos($items["mylink"],'www.youtube.com')>0){

	if (preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/ ]{11})%i', $items["mylink"], $match)) {
	    $video_id = $match[1];
		$items["mylink"]='http://www.youtube.com/embed/'.$video_id.'?rel=0&amp;wmode=transparent';
		$openWindow='class="rssmi_youtube"';
		global $YTmatch;
		$YTmatch=1;
	}
}



	
		$readable .=  '<div class="rss-output" style="float:'.$divfloat.'"><div class="title"><span style="font-size:'.$hdsize.'; font-weight:'.$hdweight.';"><a '.$openWindow.' href='.$items["mylink"].' '.($noFollow==1 ? 'rel=nofollow':'').' style="color:'.$anchorcolor.'">'.$items["mytitle"].'</a></span>';
		if(!empty($items["myAuthor"]) && $addAuthor==1){
		 $readable .=  '<br><span style="font-style:italic; font-size:16px;">'.$authorPrep.' <a '.$openWindow.' href='.$items["mylink"].' '.($noFollow==1 ? 'rel=nofollow':'').'">'.$items["myAuthor"].'</a></span>';  
			}
		
		
		if ($showmore==1 && $showDesc==1){
			
			$readable .=  ' <a href="javascript:void(0)"><img src="'.$images_url.'/arrow_down.png"/  id="#'.$idnum.'" class="nav-toggle"></a></div>';	
			
		} else{
			
			$readable .=  '</div>';	
		}
			
	if (!empty($items["mydesc"]) && $showDesc==1){
		
		
		
		if ($showmore==1 && $showDesc==1){
			$readable .=  '<div id="'.$idnum.'" style="display:none" class="show_body">';
		}else{
			$readable .=  '<div class="body">';		
		}
		

	$readable .=  showexcerpt($items["mydesc"],$descNum,$openWindow,$stripAll,$items["mylink"],$adjustImageSize,$float,$noFollow,$items["myimage"]);
	
	$readable .=  '</div>';	

	
}


	
	if (!empty($items["mystrdate"]) && $showdate==1){
	// $readable .=  '<span style="'.$datestyle.'">'. date_i18n("D, M d, Y g:i:s A",$items["mystrdate"]).'</span><br />';  // use this instead if you want time to show
	$readable .=  '<span class="date" style="'.$datestyle.'">'. date_i18n("D, M d, Y",$items["mystrdate"]).'</span><br />';
	}
		if (!empty($items["myGroup"]) && $showgroup==1){
     $readable .=  '<span class="source" style="font-style:italic;">'.$attribution.''.$items["myGroup"].'</span>';
	}

	$getCatName=getCategoryName($items["mycatid"]);  // use these 5 lines of code to get and display the category name
	if (!empty($getCatName) && $showcategory==1){
		$catClassID='classID'.$items["mycatid"];
 $readable .=  '  <span class="categoryname  ' .$catClassID.'">Category: '.$getCatName.'</span>';
	}
	
	if ($showsocial==1){
	 $readable .=  '  <span class="socialicons"><a href="http://www.facebook.com/sharer/sharer.php?u='.$items["mylink"].'"><img src="'.WP_RSS_MULTI_IMAGES.'facebook.png"/></a>&nbsp;&nbsp;<a href="http://twitter.com/intent/tweet?text='.rawurlencode($items["mytitle"]).'%20'.$items["mylink"].'"><img src="'.WP_RSS_MULTI_IMAGES.'twitter.png"/></a></span>';
	}
	
	 $readable .=  '</div>';
	
	
	
		}
	//  This is the end of the default template
?>