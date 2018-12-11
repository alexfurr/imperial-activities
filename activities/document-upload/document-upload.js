jQuery(document).on('click', '.icl_upload_element', function (e) {


		var postIDStr = this.id;
		var postID = postIDStr.split("_")[1];	
		var pluginURL = imperialUpload_frontEndAjax.plugin_url;
		
		
		var file_data = jQuery('#fileUpload_'+postID).prop('files')[0];
		var allowed_ext = document.getElementById('allowed_ext_'+postID).value;
		var form_data = new FormData();
		
		
		// no files uploaded
		if( document.getElementById("fileUpload_"+postID).files.length == 0 )
		{
			return;			
		}
		
		var file_name = file_data['name'];
		
		
		if(allowed_ext!="")
		{
			var allowedExtArray = allowed_ext.split(",");
		
			// Get the file ext
			var file_ext = file_name.split('.').pop();
			file_ext.toLowerCase();
			
			if (allowedExtArray.indexOf(file_ext) == -1)
			{
				var thisFeedbackDiv = "imperial-upload-feedback-"+postID;		
				document.getElementById(thisFeedbackDiv).innerHTML = "<span style='color:red'>Sorry! The file with extension ."+file_ext+"  is not allowed</span>";

				return;
			}
			
		}
		

		
		// Add preloader
		var thisContentDiv = "imperial-upload-content-"+postID;
		document.getElementById(thisContentDiv).innerHTML = '<img src="'+pluginURL+'/assets/loading.gif" class="icl-preloader">';
		
		
		
		form_data.append('file', file_data);
		form_data.append('postID', postID);
		form_data.append('security', imperialUpload_frontEndAjax.ajax_nonce);
		form_data.append('action', "imperialDocumentUpload");
				
		jQuery.ajax({
			type: 'POST',
			url: imperialUpload_frontEndAjax.ajaxurl,
			contentType: false,
			processData: false,
			data: form_data,
			fileType: "document",
			success: function(data){
				
				var thisFeedbackDiv = "#imperial-upload-feedback-"+postID;
				
				console.log("thisFeedbackDiv="+thisFeedbackDiv);
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
			url: imperialUpload_frontEndAjax.ajaxurl,

			data: {			
				"action": "documentRemove",
				"postID": postID,
				"security": imperialUpload_frontEndAjax.ajax_nonce
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