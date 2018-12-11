function ajaxSaveImperialData(dataID, myDataObj)
{

	
	jQuery.ajax({
		type: 'POST',
		url: imperialData_frontEndAjax.ajaxurl,
		data: {			
			"action": "saveImperialData",
			"myDataObj": myDataObj,
			"dataID": dataID,
			"security": imperialData_frontEndAjax.ajax_nonce
		},
		success: function(data){
			
			document.getElementById("dataCollectionWrap_"+dataID).innerHTML = data;	
			ICL_DATA.add_listeners_options();
		}
	});	
	return false;
	
}


