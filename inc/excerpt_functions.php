<?php

// Helper functions





function getDateSince($postDate,$nowDate){
	
	
	$dateDiff    = $nowDate - $postDate;
	$fullDays    = floor($dateDiff/(60*60*24));
	$fullHours   = floor(($dateDiff-($fullDays*60*60*24))/(60*60));
	$fullMinutes = floor(($dateDiff-($fullDays*60*60*24)-($fullHours*60*60))/60);
	
	$timeSince="published " ;
	
	if($fullDays>0){
		$timeSince.=$fullDays." days ";
	}
	if($fullHours>0){
		if ($fullHours==1){
		$timeSince.=$fullHours." hour ";	
		}else{
		$timeSince.=$fullHours." hours ";
	}
	}
	if($fullMinutes>0){
		$timeSince.=$fullMinutes." min ";
	}
	$timeSince.=" ago";
	return $timeSince;
}


function getDefaultCatImage($catID){
		$option_category_images = get_option('rss_import_categories_images');
		if(!empty($option_category_images)){
		$defaultCatImage=$option_category_images[$catID];
		if(verifyimage($defaultCatImage)==True){
			return array(True,$defaultCatImage);
		}else{
			return array(False,'');
		}
	}
}




function getCategoryName($catID){  //  Get the category name from the category ID

	$catOptions=get_option('rss_import_categories');
	if(!empty($catOptions)){
		$idnum='cat_name_'.$catID;
		return	$catOptions[$idnum];
	}
}





	function showexcerpt($content, $maxchars,$openWindow,$stripAll,$thisLink,$adjustImageSize,$float,$noFollow,$mediaImage,$catID=0)  //show excerpt function
	{
		
	global $ftp;	
	global $morestyle;
    $content=CleanHTML($content);


	if ($stripAll==1){
			$content=strip_tags(html_entity_decode($content));	
			$content= limitwords($maxchars,$content);	
	}else{
			if ($ftp==1){
				$content=strip_tags(html_entity_decode($content),'<a><img><p><strong><i><em>');
			}else{
				$content=strip_tags(html_entity_decode($content),'<a><img><p>');
			}
		
			if($maxchars !=99){
				$content=findalignImage($maxchars,$content,$adjustImageSize,$float,$openWindow,$mediaImage,$thisLink,$noFollow,$catID);	
			}
		}	

	$content=str_replace("<a ", "<a  ".$openWindow.' ' 	.($noFollow==1 ? 'rel=nofollow  ' :'' ) , $content);  

	return str_replace($morestyle, "<a href=".$thisLink." ".$openWindow.' ' 	.($noFollow==1 ? 'rel=nofollow':'').">".$morestyle."</a>", $content);
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
		$content=str_replace("&#160;&#187;","",$content);
		$content=str_replace("&#160;","",$content);
		$content=str_replace("&#173;","",$content);
		$content=str_replace("&#171;","'",$content);
		$content=str_replace("&laquo;","\"",$content);
		$content=str_replace("&pound;","&amp;pound;",$content);  // replace strange pound sign problem		

		$content = preg_replace('(<a.*href="(.*)">\s*(<img.*src=(.*)(tweetmeme|feedburner|ebuzzing|feedsportal|adportal)(.*)\/?>)<\/a\>)', '', $content);  //clean bugs  

		$content = preg_replace('(<img[^>]*height[:|=] *(\"?)[0|1](px|\"| )[^>]*>)', '', $content);  //clean bugs
		
		$content =_decodeAccented($content);

	return 	$content;
	}
	
	
	
	
	function _decodeAccented($encodedValue, $options = array()) {
	    $options += array(
	        'quote'     => ENT_NOQUOTES,
	        'encoding'  => 'UTF-8',
	    );
	    return preg_replace_callback(
	        '/&\w(acute|uml|tilde|cedil|circ|grave|ordm|ordf|laquo);/',
	        create_function(
	            '$m',
	            'return html_entity_decode($m[0], ' . $options['quote'] . ', "' .
	            $options['encoding'] . '");'
	        ),
	        $encodedValue
	    );
	}
	
	
	
	
	
	
	
	
	function joinContent($content,$adjustImageSize,$imagefix,$float,$anchorLink,$maxchars,$mediaImage){
		global $ftp;
		global $setFeaturedImage;
		
		
		if ($adjustImageSize==1){
			$tabledImage= "<div class=\"$imagefix\" style=\"float:".$float.";\">".$anchorLink.resize_image($mediaImage)."</a></div>";
		}else{
			$tabledImage= "<div class=\"$imagefix\" style=\"float:".$float.";\">".$anchorLink.$mediaImage."</a></div>";
		}	
	
	if($ftp==1){  
		$content = limitwords($maxchars,strip_tags($content,'<p><strong><i><em>'));
	}else{
		$content = limitwords($maxchars,strip_tags($content));
	}
	
	if($ftp!=1){  //  only return if not Feed to Post
		$content=$tabledImage."".$content;
	}else{
		if($setFeaturedImage!=2){  //  check for condition if Feed to Post
			$content=$tabledImage."".$content;
		}
			}

	return 	$content;
		
		
	}
	
	
	
	
	
	
	function findalignImage($maxchars,$content,$adjustImageSize,$float,$openWindow,$mediaImage,$thisLink,$noFollow,$catID){
		$leadmatch=0;	
		global $YTmatch;
		global $anyimage;
		global $ftp;
		global $RSSdefaultImage;
		global $featuredImage;
		
		if ($ftp==1){
			$imagefix="ftpimagefix";
		}else{
			$imagefix="imagefix";	
		}
		
		$anchorLink="<a href=".$thisLink." >";//construct hyperlink for image

		$strmatch='^\s*(?:<p>)?\<a.*href="(.*)">\s*(<img.*src=".*"\s*?\/?>)[^\<]*<\/a\>\s*(.*)$';
		
		$strmatch2='^(\s*)(?:<p>)?(<img.*src=".*"\s*?\/?>)\s*(.*)$';

		$strmatch3='^(.*)(<img.*src=".*"\s*?\/?>)\s*(.*)$';  //match first image if it exists
		
	if (preg_match("/$strmatch/sU", $content, $matches)) { //matches a leading hperlinked image
		$leadmatch=1;
	}else if (preg_match("/$strmatch2/sU", $content, $matches)) {  //matches a leading non-hperlinked image
		$leadmatch=2;	
	}else if (preg_match("/$strmatch3/sU", $content, $matches)) { //matches first image
		$leadmatch=3;
	}
	
	
	
	$catImageArray= getDefaultCatImage($catID);
	
	if($RSSdefaultImage==1 && $catImageArray[0]==True){

		$mediaImage="<img src=\"$catImageArray[1]\">";	
		$content=joinContent($content,$adjustImageSize,$imagefix,$float,$anchorLink,$maxchars,$mediaImage);
		$featuredImage=$catImageArray[1];
	}
	
	else if (($leadmatch==1 || $leadmatch==2) && isbug($matches[2])==False){
		
		$mediaImage = $matches[2];
		$content=joinContent($content,$adjustImageSize,$imagefix,$float,$anchorLink,$maxchars,$mediaImage);
		$featuredImage = preg_replace('#.*src="([^\"]+)".*#', '\1', $matches[2]);
	
	}else if (!IS_Null($mediaImage) && verifyimage($mediaImage)==True){  //  match media enclosure image if it exists

		$featuredImage=$mediaImage;
		$mediaImage="<img src=\"$mediaImage\">";		
		$content=joinContent($content,$adjustImageSize,$imagefix,$float,$anchorLink,$maxchars,$mediaImage);
		
			
	}else if ($leadmatch==3 && $anyimage==1){

		$mediaImage=$matches[2];	
		$content=joinContent($content,$adjustImageSize,$imagefix,$float,$anchorLink,$maxchars,$mediaImage);
		$featuredImage = preg_replace('#.*src="([^\"]+)".*#', '\1', $matches[2]);
	
	}else if($RSSdefaultImage==2 && $catImageArray[0]==True){

		$mediaImage="<img src=\"$catImageArray[1]\">";		
		$content=joinContent($content,$adjustImageSize,$imagefix,$float,$anchorLink,$maxchars,$mediaImage);
		$featuredImage=$catImageArray[1];
	
	}else{
		
		$content = limitwords($maxchars,strip_tags($content));  //matches no leading image or media enclosure and no default category image
		
		}
		
	return $content;
		
	}
	
	
	
	
	function verifyimage($imageURL) {
		$imageURL = preg_replace('/\?.*/', '', $imageURL);

	    if( preg_match('#^(http|https):\/\/(.*)\.(gif|png|jpg|jpeg)$#i', $imageURL))
	    {
	        $msg = TRUE; 
	    }
	    else
	    {
	        $msg = FALSE; 
	    }

	    return $msg; 
	}
	
	function isbug($imageLink){
		
		if(strpos($imageLink,'width="1"')>0){
			$msg = TRUE; 
		}
		else
		{
			$msg = FALSE; 
	}
		 return $msg; 
	}
	

	
	function remove_img_hw( $imghtml ) {
	 $imghtml = preg_replace( '/(width|height)=\"\d*\"\s?/', "", $imghtml );
	    return $imghtml;
	}
	
	function resize_image($imghtml){
		global $maximgwidth;
		if (preg_match('/< *img[^>]*src *= *["\']?([^"\']*)/i', $imghtml, $matches)) {
			if (!empty($matches[1])  && verifyimage($matches[1])){	
				$thisWidth=getimagesize($matches[1]);
					if ($thisWidth > $maxImgWidth){
							return str_replace("<img", "<img width=".$maximgwidth, remove_img_hw($imghtml));
						}else{
							return str_replace("<img", "<img width=".$thisWidth, remove_img_hw($imghtml));		
					}
			}
		}
	}


?>