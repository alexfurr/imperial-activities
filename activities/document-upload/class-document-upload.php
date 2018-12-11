<?php

$imperialDocumentUpload = new imperialDocumentUpload();

class imperialDocumentUpload
{

	//~~~~~
	function __construct ()
	{
		
		$this->addWPActions();		
	}
	

	
/*	---------------------------
	PRIMARY HOOKS INTO WP 
	--------------------------- */	
	function addWPActions ()
	{
		//Frontend
		add_action( 'wp_enqueue_scripts', array( $this, 'frontendEnqueues' ), 1 );
		add_shortcode( 'imperial-document-upload', array( $this, 'drawDocumentUploadShortcode' ) );
		

	}
	

	function frontendEnqueues ()
	{
		//Scripts
		wp_enqueue_script('jquery');
		
		
		
		// Global  Styles		
		wp_enqueue_script('imperial-document-upload-js', IMPERIAL_ACTIVITIES_URL.'/activities/document-upload/document-upload.js' );
		
		
		// Register Ajax script for front end
		//wp_enqueue_script('imperialImageUpload_ajaxJS', IMPERIAL_ACTIVITIES_URL.'/activities/image-upload/ajax.js', array( 'jquery' ) ); #Custom AJAX functions
		
		
		//Localise the JS file
		$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'ajax_nonce' => wp_create_nonce('imperialUpload_frontEndAjax'),
		'plugin_url' => IMPERIAL_ACTIVITIES_URL,
		);
		wp_localize_script( 'imperial-document-upload-js', 'imperialUpload_frontEndAjax', $params );
		
		
		

	}	
	
	function drawDocumentUploadShortcode($atts)
	{
		
		$atts = shortcode_atts( 
			array(
				'id'		=> ''
				), 
			$atts
		);
		
		$postID = (int) $atts['id'];
		$html='<div class="icl-activity-wrap">';		
		
		$activityName = get_the_title($postID);
		$html.= '<h3>'.$activityName.'</h3>';		
		$html.= imperialDocumentUpload::drawDocumentUploadForm($postID);
		
		// Add an edit question link if they have the appopriate permissions
		if(current_user_can('delete_others_pages'))
		{
			// Get the Parent ID (pot)
			$html.='<div class="object-edit-div">';
			
			// Edit Question
			$html.= '<a href="'.get_home_url().'/wp-admin/post.php?post='.$postID.'&action=edit" target="blank">Edit this Object</a>';
			
			// View Results
			$html.=' | <a href="'.get_home_url().'/wp-admin/options.php?page=imperial-doc-submissions&ID='.$postID.'">View Submissions</a>';
			$html.='</div>';
		}	
		
		
		$html.='</div>';
		
		return $html;
		
		
		
		
	}
	
	public static function drawDocumentUploadForm($postID)
	{
		$contentStr='<div id="imperial-upload-content-'.$postID.'">';
		// Get saved Meta if it exist
		$savedData = get_post_meta( $postID, 'imperialData', true );
		
		
		$allowedFileTypes = get_post_meta( $postID, 'allowedFileTypes', true );		
		// Remove spaces
		$allowedFileTypes =  str_replace(' ','',$allowedFileTypes);
		$submissionType = get_post_meta( $postID, 'submissionType', true );
		$submissionStartDate = get_post_meta( $postID, 'submissionStartDate', true );
		$submissionEndDate = get_post_meta( $postID, 'submissionEndDate', true );
		$currentDate = date('Y-m-d H:i:s');
		
		
		// What the delete and upload buttons say
		$submissionText = 'submission';
		if($submissionType=="image")
		{
			$submissionText = 'image';			
		}
				
		$userID = get_current_user_id();
		
		$myDocument = '';
		
		if(isset($savedData[$userID]) )
		{
						
			$myDocument = $savedData[$userID];		
			
		}		
		
		// Handle for submissions PRE saving the date
		if(is_array($myDocument))
		{
			$myDocument = $myDocument['filename'];
		}
		
		if($myDocument<>"")
		{
			
			$randomstring = bin2hex( openssl_random_pseudo_bytes( 16 ) );
			$hash = hash( 'md5', $randomstring );
			
			$upload_dir   = wp_upload_dir();		
			$uploadDir = $upload_dir['baseurl'].'/imperial-activity-uploads/'.$postID;			
			
			
			switch ($submissionType)
			{
				
				case "image":

					$contentStr.= '<img src="'.$uploadDir.'/'.$myDocument.'?hash='.$hash.'">';

				break;

				
				default:
					$contentStr.= '<span style="color:green"><i class="fas fa-check"></i> You\'ve submitted this document</span><br/>';
					$contentStr.= '<a href="'.$uploadDir.'/'.$myDocument.'?hash='.$hash.'" target="blank">'.$myDocument.'</a>';			
				
				break;				
				
			}
			
			
			$allowDelete=true;
			if($submissionEndDate)
			{
				$submissionEndDateTime = new DateTime($submissionEndDate);
				$submissionEndDate = $submissionEndDateTime->format('Y-m-d H:i:s');

				if($currentDate<$submissionEndDateTime)
				{			
					$allowDelete = false;

				}
			}
			
			if($allowDelete==true)
			{
				$contentStr.='<div id="uploadDeleteCheckDiv_'.$postID.'" style="display:none">';
				$contentStr.='Are you sure you want to delete this '.$submissionText.'?<br/>';
				$contentStr.='<button class="icl_delete_upload" id="deleteUploadPostID_'.$postID.'">Yes, delete the '.$submissionText.'</button>';
				$contentStr.='</div>';			
				$contentStr.='<br/><button id="iclDeleteUploadConfirm_'.$postID.'" class="icl_delete_upload_confirm"><i class="fas fa-trash"></i> Delete '.$submissionText.'</button>';
				
			}
			
		}
		else
		{

			// Check the deadlines.
			$allowUpload=true;
			
			if($submissionStartDate)
			{
				$submissionStartDateTime = new DateTime($submissionStartDate);
				$submissionStartDate = $submissionStartDateTime->format('Y-m-d H:i:s');
				$submissionStartDateText = $submissionStartDateTime->format('l jS F, Y, g:i a');
				

				if($currentDate<$submissionStartDate)
				{
					$allowUpload=false;
					$contentStr.='Submissions open on '.$submissionStartDateText;		
					
				}	

				
			}
			
			if($submissionEndDate)
			{
				$submissionEndDateTime = new DateTime($submissionEndDate);
				$submissionEndDate = $submissionEndDateTime->format('Y-m-d H:i:s');

				if($currentDate<$submissionEndDateTime)
				{
					$allowUpload=false;
					$contentStr.='Submissions are now closed';			

				}	

				
			}			
	
			if($allowUpload==true)
			{
				
				if($allowedFileTypes)
				{
					$contentStr.='<div style="font-size:11px; padding:10px 0px;">Allowed File Types : '.$allowedFileTypes.'</div>';
				}
				
				$contentStr.= '<form enctype="multipart/form-data">
				<input type="file" id="fileUpload_'.$postID.'" name="upload">
				<br/><br/><input class="icl_upload_element" name="icl_upload_element" id="docUploadPostID_'.$postID.'" type="button" value="Upload">
				
				<input type="hidden" value="'.$allowedFileTypes.'" name="allowed_ext_'.$postID.'" id="allowed_ext_'.$postID.'" />
				
				</form>';
			}
		}
		
		
		$contentStr.='<div id="imperial-upload-feedback-'.$postID.'"></div>';
		
		
		
		$contentStr.='</div>';
		return $contentStr;
	}
	

	static function drawUploadsTable($postID, $CSV=false)
	{
		
		$html='';
		$csvArray=array();
		
		$thisPost = get_post($postID);
		

		
		$submissions = get_post_meta( $postID, 'imperialData', true );
		$submissionType = get_post_meta( $postID, 'submissionType', true ); // Image or doc
		
		$userArray = imperialActivityUtils::getBlogUsers();
		$html.= '<table class="imperial-table" width="90%">';

		$html.= '<tr><th>Name</th><th>Username</th><th>Role</th><th>Submission Date</th><th>Submission</th>';
		$csvArrayHeaderArray = array("Name", "Username", "Role", "Date", "Submission");

		
		$html.= '</tr>';
		$csvArray[] = $csvArrayHeaderArray;
		
		// now go through all users and add to table, along with how many times they've done the question etc
		foreach ( $userArray as $userID => $userInfo )
		{
			
			$fullname = $userInfo['fullname'];
			$firstName = $userInfo['firstName'];
			$surname = $userInfo['surname'];
			$username = $userInfo['username'];
			$role = $userInfo['role'];
			
			$response = '';
			// Get the Data for this person
			$thisUserDataArray = array();
						
			$UKdate = '-';
			if(isset($submissions[$userID]["dateSubmitted"]) )
			{
				$thisUserDataDate = $submissions[$userID]["dateSubmitted"];
				$UKdate = imperialActivityUtils::getUKdate($thisUserDataDate);
			}	

			
			
			$html.= '<tr>';
			$html.= '<td>'.$fullname.'</td>';
			$html.= '<td>'.$username.'</td>';	
			$html.= '<td>'.$role.'</td>';
			$html.= '<td>'.$UKdate.'</td>';
			
			$tempCSVarray = array ($fullname, $username, $role, $UKdate);
			
			$myDocument = '';
			
			
			$html.='<td>';
			if(isset($submissions[$userID]) )
			{
				$myDocument = $submissions[$userID];
				
				// Handle for submissions PRE saving the date
				if(is_array($myDocument))
				{
					$myDocument = $myDocument['filename'];
				}					
				
				
				$randomstring = bin2hex( openssl_random_pseudo_bytes( 16 ) );
				$hash = hash( 'md5', $randomstring );
				
				$upload_dir   = wp_upload_dir();		
				$uploadDir = $upload_dir['baseurl'].'/imperial-activity-uploads/'.$postID;			

				switch ($submissionType)
				{
					
					case "image":

						$html.= '<a href="'.$uploadDir.'/'.$myDocument.'?hash='.$hash.'" target="blank"><img width="100px" src="'.$uploadDir.'/'.$myDocument.'?hash='.$hash.'"></a>';

					break;

					
					default:
						$html.= '<a href="'.$uploadDir.'/'.$myDocument.'?hash='.$hash.'" target="blank">'.$myDocument.'</a>';			
					
					break;
					
					
				}
				$tempCSVarray[] = $uploadDir.'/'.$myDocument;



				
			}
			else
			{
				$html.= '-';
				$tempCSVarray[] = "";
				
			}
			$html.='</td></tr>';

			
			$csvArray[] = $tempCSVarray;			
			
		}

		$html.= '</table>';		
		
		
		if($CSV==true)
		{
			return $csvArray;
		}
		else
		{
			return $html;
		}		
		
	



	}
	
}
?>