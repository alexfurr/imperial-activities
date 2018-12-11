<?php
$imperialDocumentUploadAJAX = new imperialDocumentUploadAJAX();


class imperialDocumentUploadAJAX
{
	
	
	//~~~~~
	function __construct ()
	{
		$this->addWPActions();
	}	
	
	function addWPActions()
	{	
	
		add_action( 'wp_ajax_imperialDocumentUpload', array($this, 'documentUpload' ));		
		add_action( 'wp_ajax_documentRemove', array($this, 'documentRemove' ));		
		

		//  add_action( 'wp_ajax_nopriv_md_support_save','md_support_save' );

	}



	public function documentUpload()
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
		

		
		// Get the Type of submission - anoymous or not		
		$filenameConvention = get_post_meta($postID, 'filenameConvention', true);
		
		switch ($filenameConvention)
		{
			
			case "anonymous":
				$randomStr = imperialActivityUtils::generateRandomString(10);
				$filename= $randomStr.'_'.$userID.'.'.$extension;
			
			break;
			
			case "userFullname":
			
				$current_user = wp_get_current_user();
				
			
				$fullname = $current_user->user_lastname .' '.$current_user->user_firstname;
				$fullname = preg_replace("/[^A-Za-z0-9 ]/", '', $fullname);

				
				if($fullname=="")
				{
					$fullname = $current_user->user_login;
					
				}
				$filename= $fullname.'_'.$userID.'.'.$extension;		
				
			
			break;
			
			default:
				// Generate Default Filename
				$originalFilename = $file_parts['filename'];
				$newFilename = preg_replace("/[^A-Za-z0-9 ]/", '', $originalFilename);
				$filename= $newFilename.'_'.$userID.'.'.$extension;			
			break;
			
		}	

		$finalSrcPath =  $uploadPath . '/' . $filename;
		
		$fileType = $_POST['fileType'];
		
		// Get the post meta			
		$savedData = get_post_meta( $postID, 'imperialData', true );		
		if(!is_array($savedData) )
		{
			$savedData = array();
		}
		else
		{
			$oldSubmission = $savedData[$userID];

			// Delete the old submission
			$oldSubmissionRef =$uploadPath.'/'.$oldSubmission;
			unlink ($oldSubmissionRef);
			
		}		
		
		$currentDate = date('Y-m-d H:i:s');
		
		$dataArrayStore = array
		(
			"filename" => $filename,
			"dateSubmitted"	=> $currentDate,
		);		
		
				
		$savedData[$userID] = $dataArrayStore;
		
		update_post_meta( $postID, 'imperialData', $savedData );
			
		// Copy the file over
		copy( $tmpName, $finalSrcPath );
				
		echo imperialDocumentUpload::drawDocumentUploadForm($postID);		
		
		die();
	
		
	}
	
	public function documentRemove()
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
			
			$upload_dir   = wp_upload_dir();
			$uploadPath = $upload_dir['basedir'].'/imperial-activity-uploads/'.$postID;

			
			$oldDocument = $savedData[$userID];

			// Handle for submissions PRE saving the date
			if(is_array($oldDocument))
			{
				$oldDocument = $oldDocument['filename'];
			}			
			// Delete the old image
			$oldDocumentRef =$uploadPath.'/'.$oldDocument;
			
			unlink ($oldDocumentRef);
			
		}
		
		$savedData[$userID] = "";
		update_post_meta( $postID, 'imperialData', $savedData );			
		


		echo imperialDocumentUpload::drawDocumentUploadForm($postID);
	
		die();
	
		
	}
	

} // End Class


?>