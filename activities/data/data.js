var ICL_DATA = 
{
	
	

	add_listeners_options: function () {
		jQuery('.imperial-data-save-button').on( 'click', function ( e )
		{
			
			var dataID = this.id;
			console.log('dataID='+dataID);
			dataID = dataID.split('-');
			dataID = dataID[3];
			var parentID = "imperial-data-wrap-"+dataID;

			
			// Create blank object
			var myDataObj = {};
			
			jQuery('#'+parentID+' .imperial-data-input').each(function(i, obj) {
				
				var thisElementID = this.id;
				var thisElementValue = this.value;
				myDataObj[thisElementID] = thisElementValue;
				
			});
			
			// Now pass via ajax and save in post meta
			ajaxSaveImperialData(dataID, myDataObj);
			
		});
	},
	
	

	// Setup the listeners
	init: function () {		
		ICL_DATA.add_listeners_options();
	}
	
};



jQuery( document ).ready( function ()
{
	// Initialise the responses	
	ICL_DATA.init();
	

	
});
