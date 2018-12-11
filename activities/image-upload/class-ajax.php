<?php
$imperialImageUploadAJAX = new imperialImageUploadAJAX();


class imperialImageUploadAJAX
{
	
	
	//~~~~~
	function __construct ()
	{
		$this->addWPActions();
	}	
	
	function addWPActions()
	{	
	
		add_action( 'wp_ajax_imperialImageUpload', array($this, 'imageUpload' ));		
		add_action( 'wp_ajax_imageRemove', array($this, 'imageRemove' ));		
		
		
		
		//  add_action( 'wp_ajax_nopriv_md_support_save','md_support_save' );

	}



	public function imageUpload()
	{

		$postID = $_POST['postID'];
		$userID = get_current_user_id();
		
		$fileInfo = $_FILES;
		
		$tmpName = $fileInfo['file']['tmp_name'];
		$originalFileName = $fileInfo['file']['name'];
		
		$upload_dir   = wp_upload_dir();
		$uploadPath = $upload_dir['basedir'].'/imperial-activity-uploads/'.$postID;
		
		if(!is_dir($uploadPath))
		{
			wp_mkdir_p($uploadPath);
		}
				
		$file_parts = pathinfo($originalFileName);

		$extension =  $file_parts['extension'];
		
		
		$filename= $userID.'.'.$extension;
		

		$finalSrcPath =  $uploadPath . '/' . $filename;
		
		
		// Get the post meta			
		$savedData = get_post_meta( $postID, 'imperialData', true );		
		if(!is_array($savedData) )
		{
			$savedData = array();
		}
		else
		{
			$oldImage = $savedData[$userID];

			// Delete the old image
			$oldImageRef =$uploadPath.'/'.$oldImage;
			unlink ($oldImageRef);
			
		}
		
		$savedData[$userID] = $filename;
		update_post_meta( $postID, 'imperialData', $savedData );			
		
		// Copy the file over
		copy( $tmpName, $finalSrcPath );
			
			
	

		echo imperialImageUpload::drawImageUploadForm($postID);
		
		
		
		die();
	
		
	}
	
	public function imageRemove()
	{

		$postID = $_POST['postID'];
		$userID = get_current_user_id();		
		
		// Get the post meta			
		$savedData = get_post_meta( $postID, 'imperialData', true );		
		if(!is_array($savedData) )
		{
			$savedData = array();
		}
		else
		{
			$oldImage = $savedData[$userID];

			// Delete the old image
			$oldImageRef =$uploadPath.'/'.$oldImage;
			unlink ($oldImageRef);
			
		}
		
		$savedData[$userID] = "";
		update_post_meta( $postID, 'imperialData', $savedData );			
		


		echo imperialImageUpload::drawImageUploadForm($postID);
		
		
		
		die();
	
		
	}
	

} // End Class


?>