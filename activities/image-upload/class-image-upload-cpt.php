<?php
$imperialImageUploadCPT = new imperialImageUploadCPT();
class imperialImageUploadCPT
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
		//add_action( 'save_post', array($this, 'savePostMeta' ), 10 );
		
		// Add metaboxes
		add_action( 'add_meta_boxes_imperial_img_upload', array( $this, 'addMetaBoxes' ));
		
	
		// Remove and add columns in the admin table
		add_filter( 'manage_imperial_img_upload_posts_columns', array( $this, 'my_custom_post_columns' ), 10, 2 );		
		add_action('manage_imperial_img_upload_posts_custom_column', array($this, 'customColumnContent'), 10, 2);	
				

						
	}
	
	
/*	---------------------------
	ADMIN-SIDE MENU / SCRIPTS 
	--------------------------- */
	function create_CPT ()
	{
		
		$singular = 'Image Upload';
		$plural = 'Image Uploads';
	
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
		
		register_post_type( 'imperial_img_upload', $args );
	}
	
	
	
	
	// Register the metaboxes on  CPT
	function  addMetaBoxes()
	{
		
		global $post;	
			
		//Side MetaBox
		$id 			= 'img_upload_shortcode';
		$title 			= 'Shortcode';
		$drawCallback 	= array( $this, 'drawSideMetaBox' );
		$screen 		= 'imperial_img_upload';
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
			echo '[imperial-image-upload id='.$thisID.']';
		}
		
	}	
	
	
	// Remove Date Columns on projects
	function my_custom_post_columns( $columns )
	{
		unset(
			$columns['date']
		);			
		//$columns['qType'] = 'Type';	
		$columns['shortcode'] = 'Shortcode';
		//$columns['responses'] = 'Responses';	
		
		 
	  return $columns;
	}		
	
	
		// Content of the custom columns for Topics Page
	function customColumnContent($column_name, $post_ID)
	{
		
		switch ($column_name)
		{
			case "shortcode":			
			echo '[imperial-image-upload id='.$post_ID.']';
			break;

			
		}
		
			
	}	
		
	
	
		// Save metabox data on edit slide
	function savePostMeta ( $postID )
	{
		global $post_type;
		
		if($post_type=="imperial_img_upload")
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
				return $post_id;
			}			

			
			
			//update_post_meta( $postID, 'responseOptions', $options_data );        
			//update_post_meta( $postID, 'autoIncrementOptionKeyID', $next_key );
			

			}		
	
	}	
		
			
	 
	
} //Close class


?>