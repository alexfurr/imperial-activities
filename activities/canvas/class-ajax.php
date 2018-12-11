<?php
$imperialDrawingAJAX = new imperialDrawingAJAX();


class imperialDrawingAJAX
{
	
	
	//~~~~~
	function __construct ()
	{
		$this->addWPActions();
	}	
	
	function addWPActions()
	{	
		// Add textual feedback for clicks
		add_action( 'wp_ajax_saveDrawing', array($this, 'saveDrawing' ));
	}

	public function saveDrawing()
	{
		
		// Check the AJAX nonce				
		//check_ajax_referer( 'ek_notes_ajax_nonce', 'security' );
		
		
		$noteContent = wp_kses_post($_POST['noteContent']);
		$canvasID = $_POST['canvasID']; 
		$drawingData = $_POST['drawingData'];		
				
		// Get current logged in user
		$userID = get_current_user_id();
		
		// Get the post meta
		$savedData = get_post_meta( $canvasID, 'canvasData', true );
		
		if(!is_array($savedData) )
		{
			$savedData = array();
		}
		
		$savedData[$userID] = $drawingData;
		update_post_meta( $canvasID, 'canvasData', $savedData );

	
		die();
	}
	

} // End Class


?>