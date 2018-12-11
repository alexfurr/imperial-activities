jQuery(document).on('click', '.icl_upload_element', function (e) {


		var postIDStr = this.id;
		var postID = postIDStr.split("_")[1];	
		var pluginURL = imperialImageUpload_frontEndAjax.plugin_url;
		
		
		var file_data = jQuery('#fileUpload_'+postID).prop('files')[0];
		var form_data = new FormData();
		
		// Add preloader
		var thisContentDiv = "imperial-upload-content-"+postID;
		console.log("ID = "+thisContentDiv);
		document.getElementById(thisContentDiv).innerHTML = '<img src="'+pluginURL+'/assets/loading.gif" class="icl-preloader">';
		
		
		
		form_data.append('file', file_data);
		form_data.append('postID', postID);
		form_data.append('security', imperialImageUpload_frontEndAjax.ajax_nonce);
		form_data.append('action', "imperialImageUpload");
		
		console.log("DATA = "+file_data);
		
		jQuery.ajax({
			type: 'POST',
			url: imperialImageUpload_frontEndAjax.ajaxurl,
			contentType: false,
			processData: false,
			data: form_data,
			success: function(data){
				
				var thisFeedbackDiv = "#imperial-upload-feedback-"+postID;
				document.getElementById(thisContentDiv).innerHTML = data;
				jQuery(thisFeedbackDiv).show("fast");
				console.log(data);			
			}
		});	
		return false;						
		
		


});



jQuery(document).on('click', '.icl_delete_upload_confirm', function (e) {
	
	var postIDStr = this.id;
	var postID = postIDStr.split("_")[1];	
	
	var confirmDeleteCheckDiv = '#uploadDeleteCheckDiv_'+postID;

	console.log("Show "+confirmDeleteCheckDiv);
	jQuery(confirmDeleteCheckDiv).show("fast");
	
	return false;	
		


});	

jQuery(document).on('click', '.icl_delete_upload', function (e) {


		var postIDStr = this.id;
		var postID = postIDStr.split("_")[1];	
		
		
		

		jQuery.ajax({
			type: 'POST',
			url: imperialImageUpload_frontEndAjax.ajaxurl,

			data: {			
				"action": "imageRemove",
				"postID": postID,
				"security": imperialImageUpload_frontEndAjax.ajax_nonce
			},


			success: function(data){


				var thisContentDiv = "#imperial-upload-content-"+postID;
				var thisFeedbackDiv = "#imperial-upload-feedback-"+postID;
				document.getElementById("imperial-upload-content-"+postID).innerHTML = data;
				jQuery(thisFeedbackDiv).show("fast");
				console.log(data);			
			}
		});	
		return false;	
		


});				