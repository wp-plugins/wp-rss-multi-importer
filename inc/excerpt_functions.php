<?php

// Helper functions


function getCategoryName($catID){  //  Get the category name from the category ID

	$catOptions=get_option('rss_import_categories');
	if(!empty($catOptions)){
		$idnum='cat_name_'.$catID;
		return	$catOptions[$idnum];
	}
}





	function showexcerpt($content, $maxchars,$openWindow,$stripAll,$thisLink,$adjustImageSize,$float,$noFollow,$mediaImage)  //show excerpt function
	{
		

		global $morestyle;
    $content=CleanHTML($content);


	if ($stripAll==1){
			$content=strip_tags(html_entity_decode($content));	
			$content= limitwords($maxchars,$content);	
	}else{
		$content=strip_tags(html_entity_decode($content),'<a><img>');
			if($maxchars !=99){
		$content=findalignImage($maxchars,$content,$adjustImageSize,$float,$openWindow,$mediaImage,$thisLink);	
					}
}	
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
	
	
	
	
	
	
	
	function findalignImage($maxchars,$content,$adjustImageSize,$float,$openWindow,$mediaImage,$thisLink){
		$leadmatch=0;	
		global $YTmatch;
		global $anyimage;
		$strmatch='^\s*\<a.*href="(.*)">\s*(<img.*src=".*" \/?>)[^\<]*<\/a\>\s*(.*)$'; //match leading hyperlinked image if it exists
		
		$strmatch2='^(\s*)(<img.*src=".*"\s*?\/>)\s*(.*)$';  //match leading non-hyperlinked image if it exists
		
		$strmatch3='(.*)(<img.*src=".*"\s*?\/>)\s*(.*)$';  //match leading first image if it exists
		
	if (preg_match("/$strmatch/sU", $content, $matches)) { //matches a leading hperlinked image
		$leadmatch=1;
	}else if (preg_match("/$strmatch2/sU", $content, $matches)) {  //matches a leading non-hperlinked image
		$leadmatch=2;	
	}else if (preg_match("/$strmatch3/sU", $content, $matches)) { //matches first image
		$leadmatch=3;
	}
	


	if (($leadmatch==1 || $leadmatch==2) && isbug($matches[2])==False){



			if ($adjustImageSize==1){
				$tabledImage= "<div class=\"imagefix\" style=\"float:".$float.";\">".resize_image($matches[2])."</div>";
			}else{
				$tabledImage= "<div class=\"imagefix\" style=\"float:".$float.";\">".$matches[2]."</div>";
			}
		
			
				$content=str_replace($matches[2], $tabledImage, $content); //format the leading image if it exists

				$content=str_replace($matches[3], limitwords($maxchars,strip_tags($matches[3])), $content); //strip away all tags after the leading image
			
					if ($leadmatch==1 and $YTmatch==1){  //replace leading link with link to web page - ensures this works, especially for youtube

						$content=str_replace($matches[1], $thisLink, $content, $limit=1);  ///works with youtube
			
					}

				$content=str_replace("<a ","<a ".$openWindow." " , $content,  $count = 1);  // add window open to leading image, if it exists
	
	}else if (!IS_Null($mediaImage) && verifyimage($mediaImage)==True){  //  match media enclosure image if it exists

			$mediaImage="<img src=\"$mediaImage\">";
			
				if ($adjustImageSize==1){
					$tabledImage= "<div class=\"imagefix\" style=\"float:".$float.";\">".resize_image($mediaImage)."</div>";
				}else{
					$tabledImage= "<div class=\"imagefix\" style=\"float:".$float.";\">".$mediaImage."</div>";
				}	
			
			$content = limitwords($maxchars,strip_tags($content));
			
			$content=$tabledImage."".$content;
			

	}else if ($leadmatch==3 && $anyimage==1){

		$firstImage=$matches[2];
		
			if ($adjustImageSize==1){
				$tabledImage= "<div class=\"imagefix\" style=\"float:".$float.";\">".resize_image($firstImage)."</div>";
			}else{
				$tabledImage= "<div class=\"imagefix\" style=\"float:".$float.";\">".$firstImage."</div>";
			}	
		
		$content = limitwords($maxchars,strip_tags($content));
		
		$content=$tabledImage."".$content;
	
	
	}else{
			$content = limitwords($maxchars,strip_tags($content));  //matches no leading image or media enclosure
		}
		
	return $content;
		
	}
	
	
	
	
	function verifyimage($imageURL) {
		$imageURL = preg_replace('/\?.*/', '', $imageURL);
	
	    if( preg_match('#^http:\/\/(.*)\.(gif|png|jpg|jpeg)$#i', $imageURL))
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