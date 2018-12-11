<?php
$imperialDocumentUploadCPT = new imperialDocumentUploadCPT();
class imperialDocumentUploadCPT
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
		
		
		//Admin Menu
		add_action( 'init',  array( $this, 'create_CPT' ) );		
		
		
		// Save additional  meta for the custom post
		add_action( 'save_post', array($this, 'savePostMeta' ), 10 );
		
		// Add metaboxes
		add_action( 'add_meta_boxes_imperial_doc_upload', array( $this, 'addMetaBoxes' ));
		
	
		// Remove and add columns in the admin table
		add_filter( 'manage_imperial_doc_upload_posts_columns', array( $this, 'my_custom_post_columns' ), 10, 2 );		
		add_action('manage_imperial_doc_upload_posts_custom_column', array($this, 'customColumnContent'), 10, 2);	
		
		add_action( 'admin_menu', array( $this, 'create_AdminPages' ));
		
				

						
	}
	
	
/*	---------------------------
	ADMIN-SIDE MENU / SCRIPTS 
	--------------------------- */
	function create_CPT ()
	{
		
		$singular = 'Document/Image Upload';
		$plural = 'Document/Image Uploads';
	
		//Projects
		$labels = array(
			'name'               =>  $plural,
			'singular_name'      =>  $singular,
			'menu_name'          =>  $plural,
			'name_admin_bar'     =>  $plural,
			'add_new'            =>  'Add New '.$singular,
			'add_new_item'       =>  'Add New '.$singular,
			'new_item'           =>  'New '.$singular,
			'edit_item'          =>  'Edit '.$singular,
			'view_item'          => 'View '.$singular,
			'all_items'          => 'All '.$plural,
			'search_items'       => 'Search '.$plural,
			'parent_item_colon'  => '',
			'not_found'          => 'No '.$plural.' found.',
			'not_found_in_trash' => 'No '.$plural.' found in Trash.'
		);
	
		$args = array(
			'labels'            	=> $labels,
			'public'             	=> false,
			'exclude_from_search'	=> true,
			'publicly_queryable' 	=> false,
			'show_ui'            	=> true,
			'show_in_nav_menus'	 	=> false,
			'show_in_menu'       	=> false,
			'query_var'         	=> true,
			'rewrite'           	=> false,
			'capability_type'   	=> 'post',
			'has_archive'       	=> true,
			'hierarchical'      	=> false,
			'menu_position'     	=> 65,
			'supports'          	=> array( 'title' )
			
		);
		
		register_post_type( 'imperial_doc_upload', $args );
	}

	// Register the metaboxes on  CPT
	function  addMetaBoxes()
	{
		
		global $post;	
		
		//Main Table editing
		$id 			= 'doc_settings';
		$title 			= 'Upload Type';
		$drawCallback 	= array( $this, 'drawUploadMetaBoxSettings' );
		$screen 		= 'imperial_doc_upload';
		$context 		= 'normal';
		$priority 		= 'default';
		$callbackArgs 	= array();		
		add_meta_box( 
			$id, 
			$title, 
			$drawCallback, 
			$screen, 
			$context,
			$priority, 
			$callbackArgs 
		);	
		
		//Optional settings Metabox
		$id 			= 'doc_options';
		$title 			= 'Submission Options';
		$drawCallback 	= array( $this, 'drawOptionalSettingsMetaBox' );
		$screen 		= 'imperial_doc_upload';
		$context 		= 'normal';
		$priority 		= 'default';
		$callbackArgs 	= array();		
		add_meta_box( 
			$id, 
			$title, 
			$drawCallback, 
			$screen, 
			$context,
			$priority, 
			$callbackArgs 
		);

		
			
		//Side MetaBox
		$id 			= 'doc_uploads_shortcode';
		$title 			= 'Shortcode';
		$drawCallback 	= array( $this, 'drawSideMetaBox' );
		$screen 		= 'imperial_doc_upload';
		$context 		= 'side';
		$priority 		= 'default';
		$callbackArgs 	= array();		
		add_meta_box( 
			$id, 
			$title, 
			$drawCallback, 
			$screen, 
			$context,
			$priority, 
			$callbackArgs 
		);	
		
			
	}
	
	
	function  drawSideMetaBox($post,$callbackArgs)
	{
		
		global $post;
		
		if($post->ID)
		{
			$thisID = $post->ID;
			echo '[imperial-document-upload id='.$thisID.']';
		}
		
	}	
	
	
	function  drawUploadMetaBoxSettings($post,$callbackArgs)
	{
		$submissionType = get_post_meta($post->ID, 'submissionType', true);	
		$allowedFileTypes = get_post_meta($post->ID, 'allowedFileTypes', true);		
		
		// Submission Types
		echo '<label for="submissionType">Submission Type</label><br/>';
		echo '<select name="submissionType" id="submissionType">';
		echo '<option value=""';
		if($submissionType=="")
		{
			echo ' selected ';
		}
		echo '>Document Upload</option>';
		
		echo '<option value="image"';
		if($submissionType=="image")
		{
			echo ' selected ';
		}
		echo '>Image Upload</option>';	
		
		echo '</select>';		
		
		echo '<hr/>';
		
		echo '<label for="allowedFileTypes">Allowed File Types (<i>Leave blank for any file type, or comma seperated list e.g. doc, docx, ppt</i>)</label><br/>';
		echo '<input type="text" name="allowedFileTypes" id="allowedFileTypes" value="'.$allowedFileTypes.'" />';
		
		
		
		
	}
	
	function  drawOptionalSettingsMetaBox($post,$callbackArgs)
	{
		
		
		// Add Nonce Field
		wp_nonce_field( 'save_imperial_data_metabox_nonce', 'imperial_data_metabox_nonce' );

		$submissionStartDate = get_post_meta($post->ID, 'submissionStartDate', true);
		$submissionEndDate = get_post_meta($post->ID, 'submissionEndDate', true);
		$filenameConvention = get_post_meta($post->ID, 'filenameConvention', true);
		$submissionType = get_post_meta($post->ID, 'submissionType', true);
		
		
		
		
		
		// optional Settings
		echo '<strong><i>All settings below are optional</i></strong><hr/>';
		
		imperialActivityUtils::addDatePicker("submissionStartDate", $label="Submission Start Date", $submissionStartDate);
		imperialActivityUtils::addDatePicker("submissionEndDate", $label="Submission End Date", $submissionEndDate);
		
		
		
		
		echo '<label for="filenameConvention">Filename convention</label><br/>';
		echo '<select name="filenameConvention">';
		echo '<option value=""';
		if($filenameConvention=="")
		{
			echo ' selected ';
		}
		echo '>Keep original filename</option>';
		
		echo '<option value="userFullname"';
		if($filenameConvention=="userFullname")
		{
			echo ' selected ';
		}
		echo '>Use submitter fullname</option>';		
		
		echo '<option value="anonymous"';
		if($filenameConvention=="anonymous")
		{
			echo ' selected ';
		}
		echo '>Anonymous filenames</option>';	
		
		
		echo '</select>';
		
		
	}	
		
	
	
	
	
	// Remove Date Columns on projects
	function my_custom_post_columns( $columns )
	{
		unset(
			$columns['date']
		);			
		//$columns['qType'] = 'Type';	
		$columns['shortcode'] = 'Shortcode';
		$columns['submissions'] = 'Submissions';	
		
		 
	  return $columns;
	}		
	
	
		// Content of the custom columns for Topics Page
	function customColumnContent($column_name, $post_ID)
	{
		
		switch ($column_name)
		{
			case "shortcode":			
			echo '[imperial-document-upload id='.$post_ID.']';
			break;
			
			
			case "submissions":
			

				echo '<a href="options.php?page=imperial-doc-submissions&ID='.$post_ID.'"><i class="fas fa-file-upload"></i> Submissions</a>';

			
			break;			

			
		}
		
			
	}	
		
	
	
		// Save metabox data on edit slide
	function savePostMeta ( $postID )
	{
		global $post_type;
		
		
		if($post_type=="imperial_doc_upload")
		{

			// Check if nonce is set.
			if ( ! isset( $_POST['imperial_data_metabox_nonce'] ) ) {
				return;
			}
			
			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST['imperial_data_metabox_nonce'], 'save_imperial_data_metabox_nonce' ) ) {
				return;
			}
			
			// If this is an autosave, our form has not been submitted, so we don't want to do anything.
			if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
				return;
			}
		
			// Check the user's permissions.
			if ( ! current_user_can( 'edit_post', $postID ) ) {
				return;
			}
			
			// check if there was a multisite switch before
			if ( is_multisite() && ms_is_switched() ) {
				return $postID;
			}			

			
			// Save the Submission Date and Time
			
			imperialActivityUtils::saveDatePickerPostMeta($postID, "submissionStartDate");
			imperialActivityUtils::saveDatePickerPostMeta($postID, "submissionEndDate");
			
			
			// Update Document Submission meta
			update_post_meta( $postID, "filenameConvention", $_POST['filenameConvention'] );
			update_post_meta( $postID, "submissionType", $_POST['submissionType'] );
			update_post_meta( $postID, "allowedFileTypes", $_POST['allowedFileTypes'] );
			

		}		
	
	}	
	
	
	function create_AdminPages()
	{
	
				
		
		/* Create Results Pages */		
		$parentSlug = "no_parent";
		$page_title="Submissions";
		$menu_title="";
		$menu_slug="imperial-doc-submissions";
		$function=  array( $this, 'drawResultsPage' );
		$myCapability = "delete_others_pages";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);
	}		
	
	function drawResultsPage()
	{
		require_once IMPERIAL_ACTIVITIES_PATH.'activities/document-upload/submissions.php';
	}	
	
	
			
	 
	
} //Close class


?>