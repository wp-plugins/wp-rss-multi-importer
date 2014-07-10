<?php
	
		$readable.="<ul class='wprssmi_bullet_list'>";
	//  don't mess with this php code 
	foreach($myarray as $items) {

	if ($pag!==1){ 	
		$total = $total +1;
		if ($maxperPage>0 && $total>=$maxperPage) break;
	}

	$idnum=$idnum +1;
	//  END don't mess with this php code 
	
	

	$readable .= '<li class="title"><a href="#msq'.$total.'">'.$items["mytitle"].'</a> </li>';

	
	

}  	//  don't mess with this php code 

		$readable.="</ul>";					
		reset ($myarray);
		$total=-1;
		foreach($myarray as $items) {

		if ($pag!==1){ 	
			$total = $total +1;
			if ($maxperPage>0 && $total>=$maxperPage) break;
		}

		$idnum=$idnum +1;
		//  END don't mess with this php code 
	
	
	
	
		$readable .= '<a name="msq'.$total.'"></a><div class="wprssmi-cs-items">';

	$readable .= '<div class="title"><span style="font-size:'.$hdsize.'; font-weight:'.$hdweight.';"><a '.$openWindow.' href="'.$items["mylink"].'" '.($noFollow==1 ? 'rel=nofollow':'').' style="color:'.$anchorcolor.'">'.$items["mytitle"].'</a></span></div>';


			
	
	if (!empty($items["mydesc"]) && $showDesc==1){
	
	
	
		if ($showmore==1 && $showDesc==1){
			$readable .=  '<div id="'.$idnum.'" style="display:none">';
		}else{
			$readable .=  '<div class="body">';		
		}
	
	
	$readable .=  showexcerpt($items["mydesc"],$descNum,$openWindow,1,$items["mylink"],$adjustImageSize,$float,$noFollow,$items["myimage"],$items["mycatid"]);

	$readable .=  '</div></div>';	
	
	}
	
	

	
		//$readable .= '<div class="wprssmi-cs-source">'.date_i18n("D, M d, Y g:i:s A",$items["mystrdate"]).', Continue reading <a '.$openWindow.' href='.$items["mylink"].' '.($noFollow==1 ? 'rel=nofollow':'').'">at the source</a></div></div>';
	
	
	
	
	

	}  	//  don't mess with this php code 

						

?>