jQuery(document).ready( function($) {
	$("#template-form").submit( function() {
		var str = jQuery(this).serialize();
		var filename= jQuery('[name=filename]').val();
		var filetemplate= jQuery('[name=filetemplate]').val();
		var data = {
			action: 'wprssmi_response',
			post_var: filetemplate,
			post_name: filename
		};
		// the_ajax_script.ajaxurl is a variable that will contain the url to the ajax processing file
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			// alert(response);
			jQuery("#note").html(response);
			// if(response.indexOf("Houston")=0){
				jQuery("#save_template").hide();
				// }
			});
			return false;
		});


		$("#template-restore").click( function() {
			var data = {
				action: 'wprssmi_response',
				restore_var: 1
			};
			$.post(the_ajax_script.ajaxurl, data, function(response) {
				jQuery("#note").html(response);

			});

			return false;
		});

		$("#template-save").click( function() {
			var filetemplate= jQuery('[name=filetemplate]').val();
			var data = {
				action: 'wprssmi_response',
				post_var: filetemplate,
				save_var: 2
			};
			$.post(the_ajax_script.ajaxurl, data, function(response) {
				location.reload();
				jQuery("#note").html(response);
				jQuery("#save_template").show();
				jQuery("#show_action_options").hide();
			});
			return false;
		});


		$("#css-save").click( function() {
			var data = {
				action: 'wprssmi_response',
				save_var: 3
			};
			$.post(the_ajax_script.ajaxurl, data, function(response) {
				// location.reload();
				jQuery("#note").html(response);
				jQuery("#save_template").hide();
				jQuery("#show_action_options").hide();
			});
			return false;
		});


		$("#fetch-now").click( function() {
			var data = {
				action: 'fetch_now'
			};
			$.post(the_ajax_script.ajaxurl, data, function(response) {

				jQuery("#note").html(response);
			});
			return false;
		});


	$("#getFeeds-Now").click( function() {

		var data = {
			action: 'getFeeds_Now'
		};
			rssmi_ajax_loader_show();
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			rssmi_ajax_loader_hide();
			jQuery("#gfnote").html(response);
		});
		return false;
	});
	
	
	
	$("#deleteFeeds-Now").click( function() {

		var data = {
			action: 'deleteFeeds_Now'
		};
			rssmi_ajax_loader_show();
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			rssmi_ajax_loader_hide();
			jQuery("#dfnote").html(response);
		});
		return false;
	});


	$("#fetch-delete").click( function() {

		var data = {
			action: 'fetch_delete'
		};
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			jQuery("#fnote").html(response);
		});
		return false;
	});

	$("#restore-all").click( function() {

		var data = {
			action: 'restore_all'
		};
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			jQuery("#restore_note").html(response);
		});
		return false;
	});



	$("#checkfeeds-all").click( function() {

		var data = {
			action: 'checkfeeds_all'
		};
			rssmi_ajax_loader_show();
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			rssmi_ajax_loader_hide();
			jQuery("#checkfeeds_note").html(response);
		});
		return false;
	});


	$("#upgrade-feeds").click( function() {

		var data = {
			action: 'upgrade_feeds'
		};
			rssmi_ajax_loader_show();
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			rssmi_ajax_loader_hide();
			jQuery("#upgradefeeds_note").html(response);
		});
		return false;
	});
	

	$("#delete-feedposts").click( function() {  // on feed item edit page
		var feedID= jQuery('[name=feedID]').val();
		var data = {
			action: 'delete_feedposts',
			post_var: feedID
		};
			rssmi_ajax_loader_show();
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			rssmi_ajax_loader_hide();
			location.reload();
			jQuery("#feedposts_note").html(response);
		});
		return false;
	});


	$("#rssmi-fetch-items-now").click( function(e) {  // on feed item edit page

		var feedID= jQuery('[name=feedID]').val();

		var data = {
			action: 'rssmi_fetch_items_now',
			pid: feedID
		};
			rssmi_ajax_loader_show();
		$.post(the_ajax_script.ajaxurl, data, function(response) {
			rssmi_ajax_loader_hide();
			location.reload();
			jQuery("#fetch_items_note").html(response);
		});
		return false;
	});










	function rssmi_ajax_loader_show(e)
	{
		$('#rssmi-ajax-loader').show();
		$('#rssmi-ajax-loader-delete-fetch').show();
		$('#rssmi-big-ajax-loader').show();
		$('#rssmi-ajax-loader-center').show();
	
	}

	function rssmi_ajax_loader_hide()
	{
		$('#rssmi-ajax-loader-delete-fetch').hide();
		$('#rssmi-ajax-loader').hide();
		$('#rssmi-big-ajax-loader').hide();
		$('#rssmi-ajax-loader-center').hide();
	}





});