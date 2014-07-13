/*
* adapted from http://www.problogdesign.com/wordpress/load-next-wordpress-posts-with-ajax/
*/
jQuery(document).ready(function($) {

	// The number of the next page to load.
	var pageNum = parseInt(pbd_alp.startPage);
	
	// The maximum number of pages.
	var max = parseInt(pbd_alp.maxPages)-1;
	
	// The link to the next page of shortcode pagination.
	var nextLink = pbd_alp.nextLink;
	/**
	 * Replace the traditional navigation with this,
	 * but only if there is at least one page of new posts to load.
	 */
	if(pageNum <= max) {
		// Insert the "More Posts" link.
		$('.rssmi_wrap')
			.append('<div class="pbd-alp-placeholder-'+ pageNum +'"></div>')
			.append('<p id="pbd-alp-load-posts"><a href="#">See More Content</a></p>');
			
		// Remove the current navigation if present.
		$('.rssmi_pagination').remove();
	}
	
	
	/**
	 * Load new posts when the link is clicked.
	 */
	$('#pbd-alp-load-posts a').click(function() {
		
			
		// Are there more posts to load?
		if(pageNum <= max) {
			// Show that we're working.
			//alert(pageNum);
			$(this).text('Loading content....');
			$('.pbd-alp-placeholder-'+ pageNum).load(nextLink + ' .rssmi_wrap',
				function() {
					// Have the posts fade in
					$( this ).hide().fadeIn(500);
					//  Bind colorbox elements to load more entries
					var comp = new RegExp('colorbox');
					$('a').each(function(){
					   if(comp.test($(this).attr('class'))){
							$(this).colorbox({width:"80%", height:"80%", iframe:true});
					   }
					});
					
					$('a.rssmi_youtube').each(function(){				
							$(this).colorbox({innerWidth:"425", innerHeight:"344", iframe:true});
					});
					
					$('a.rssmi_vimeo').each(function(){				
							$(this).colorbox({innerWidth:"500", innerHeight:"409", iframe:true});
					});
					
					
					pageNum++;	
					nextLink = nextLink.replace(/\pg=[0-9]*/, 'pg='+ pageNum);
					// Remove the current navigation if present.
					
					$('.rssmi_pagination').remove();
					// Add a new placeholder, for when user clicks again.
					$('#pbd-alp-load-posts')
						.before('<div class="pbd-alp-placeholder-'+ pageNum +'"></div>')
					
					// Update the button message.
					if(pageNum <= max) {
						$('#pbd-alp-load-posts a').text('See More Content');
					} else {
						$('#pbd-alp-load-posts a').text('No more content to load.');
					//	$('#pbd-alp-load-posts a').fadeOut(6000, function(){});
						$('#pbd-alp-load-posts a').fadeTo(6000, 0);
					}
				}
			);
		} else {
			$('#pbd-alp-load-posts a').append('.');
		}	
		
		return false;
	});
});