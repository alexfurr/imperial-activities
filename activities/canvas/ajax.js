function ajaxSaveDrawing(drawingData, canvasID)
{
	jQuery.ajax({
		type: 'POST',
		url: imperialCanvas_frontEndAjax.ajaxurl,
		data: {			
			"action": "saveDrawing",
			"drawingData": drawingData,
			"canvasID": canvasID,
			"security": imperialCanvas_frontEndAjax.ajax_nonce
		},
		success: function(data){
			
			console.log("SAVED");		
			
			
		}
	});	
	return false;
	
}


