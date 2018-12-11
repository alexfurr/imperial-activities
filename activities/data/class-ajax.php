<?php
$imperialDataAJAX = new imperialDataAJAX();


class imperialDataAJAX
{
	
	
	//~~~~~
	function __construct ()
	{
		$this->addWPActions();
	}	
	
	function addWPActions()
	{	
		// Add textual feedback for clicks
		add_action( 'wp_ajax_saveImperialData', array($this, 'saveData' ));
	}

	public function saveData()
	{
		
		// Check the AJAX nonce				
		//check_ajax_referer( 'ek_notes_ajax_nonce', 'security' );
		
		$dataID = $_POST['dataID']; 
		$myDataObj = $_POST['myDataObj'];		
				

		// Get current logged in user
		$userID = get_current_user_id();
		
		// Get the post meta		
		$savedData = get_post_meta( $dataID, 'imperialData', true );
		
		if(!is_array($savedData) )
		{
			$savedData = array();
		}
		
		$currentDate = date('Y-m-d H:i:s');
		
		$dataArrayStore = array
		(
			"data" => $myDataObj,
			"dateSubmitted"	=> $currentDate,
		);
		
		
		$savedData[$userID] = $dataArrayStore;
		update_post_meta( $dataID, 'imperialData', $savedData );
		
		echo imperialDataCollection::drawDataCollection($dataID);		
		echo '<div class="feedback-success">Saved</div>';

	
		die();
	}
	

} // End Class


?>