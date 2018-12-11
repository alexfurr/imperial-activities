<?php

$imperialImageUpload = new imperialImageUpload();

class imperialImageUpload
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
		add_shortcode( 'imperial-image-upload', array( $this, 'drawImageUploadShortcode' ) );
		

	}
	

	function frontendEnqueues ()
	{
		//Scripts
		wp_enqueue_script('jquery');
		
		
		
		// Global  Styles		
		wp_enqueue_script('imperial-image-upload-js', IMPERIAL_ACTIVITIES_URL.'/activities/image-upload/image-upload.js' );
		
		
		// Register Ajax script for front end
		//wp_enqueue_script('imperialImageUpload_ajaxJS', IMPERIAL_ACTIVITIES_URL.'/activities/image-upload/ajax.js', array( 'jquery' ) ); #Custom AJAX functions
		
		
		//Localise the JS file
		$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'ajax_nonce' => wp_create_nonce('imperialImageUpload_ajax_nonce'),
		'plugin_url' => IMPERIAL_ACTIVITIES_URL,
		);
		wp_localize_script( 'imperial-image-upload-js', 'imperialImageUpload_frontEndAjax', $params );
		
		
		

	}	
	
	function drawImageUploadShortcode($atts)
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
		$html.= imperialImageUpload::drawImageUploadForm($postID);
		
		// Add an edit question link if they have the appopriate permissions
		if(current_user_can('delete_others_pages'))
		{
			// Get the Parent ID (pot)
			$html.='<div class="object-edit-div">';
			
			// Edit Question
			$html.= '<a href="'.get_home_url().'/wp-admin/post.php?post='.$postID.'&action=edit" target="blank">Edit this Object</a>';
			
			// View Results
			$html.=' | <a href="'.get_home_url().'/wp-admin/options.php?page=imperial-data-submissions&ID='.$postID.'">View Submissions</a>';
			$html.='</div>';
		}	
		
		
		$html.='</div>';
		
		return $html;
		
		
		
		
	}
	
	public static function drawImageUploadForm($postID)
	{
		$contentStr='<div id="imperial-upload-content-'.$postID.'">';
		// Get saved Meta if it exist
		$savedData = get_post_meta( $postID, 'imperialData', true );
		$userID = get_current_user_id();
		
		$myData = array();
		$myImage = '';
		
		if(isset($savedData[$userID]) )
		{
			$myImage = $savedData[$userID];			
		}		
		
		if($myImage<>"")
		{
			$randomstring = bin2hex( openssl_random_pseudo_bytes( 16 ) );
			$hash = hash( 'md5', $randomstring );

			
			$upload_dir   = wp_upload_dir();		
			$uploadDir = $upload_dir['baseurl'].'/imperial-activity-uploads/'.$postID;
			$contentStr.= '<img src="'.$uploadDir.'/'.$myImage.'?hash='.$hash.'">';			


			$contentStr.='<div id="uploadDeleteCheckDiv_'.$postID.'" style="display:none">';
			$contentStr.='Are you sure you want to delete this image?<br/>';
			$contentStr.='<button class="icl_delete_upload" id="deleteUploadPostID_'.$postID.'">Yes, delete the image</button>';
			$contentStr.='</div>';			
			$contentStr.='<br/><button id="iclDeleteImageConfirm_'.$postID.'" class="icl_delete_upload_confirm"><i class="fas fa-trash"></i> Delete Image</button>';
			
		}
		else
		{

			$contentStr.= '<form enctype="multipart/form-data">
			<input type="file" id="fileUpload_'.$postID.'" name="upload">
			<br/><br/><input class="icl_upload_element" name="icl_upload_element" id="docUploadPostID_'.$postID.'" type="button" value="Upload">
			</form>';
		}
		
		
		$contentStr.='<div id="imperial-upload-feedback-'.$postID.'"></div>';
		
		
		
		$contentStr.='</div>';
		return $contentStr;
	}
	
}
?>