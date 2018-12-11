<?php
$imperialActivityDrawing = new imperialActivityDrawing();
class imperialActivityDrawing
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
		add_action( 'add_meta_boxes_imperial_drawing', array( $this, 'addMetaBoxes' ));
		
		// Remove and add columns in the admin table
		add_filter( 'manage_imperial_drawing_posts_columns', array( $this, 'my_custom_post_columns' ), 10, 2 );		
		add_action('manage_imperial_drawing_posts_custom_column', array($this, 'customColumnContent'), 10, 2);	
		

						
	}
	

	
	
	
/*	---------------------------
	ADMIN-SIDE MENU / SCRIPTS 
	--------------------------- */
	function create_CPT ()
	{
		
		$singular = 'Drawing';
		$plural = 'Drawings';
	
		//Projects
		$labels = array(
			'name'               =>  $plural,
			'singular_name'      =>  $singular,
			'menu_name'          =>  $plural,
			'name_admin_bar'     =>  'Tables',
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
			'supports'          	=> array( 'title', 'thumbnail' )
			
		);
		
		register_post_type( 'imperial_drawing', $args );
	}

	
	
	
	// Register the metaboxes on  CPT
	function  addMetaBoxes()
	{
		
		global $post;	

		//Main MetaBox
		$id 			= 'canvas_options';
		$title 			= 'Canvas Options';
		$drawCallback 	= array( $this, 'drawContentMetaBox' );
		$screen 		= 'imperial_drawing';
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

		

		//Main MetaBox
		$id 			= 'canvas_shortcode';
		$title 			= 'Shortcode';
		$drawCallback 	= array( $this, 'drawSideMetaBox' );
		$screen 		= 'imperial_drawing';
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
	
	function  drawContentMetaBox($post,$callbackArgs)
	{
		
		// Add Nonce Field
		wp_nonce_field( 'save_imperial_drawing_metabox_nonce', 'imperial_drawing_metabox_nonce' );

		
		$canvas_width = get_post_meta($post->ID, 'canvas_width', true);
		$canvas_height = get_post_meta($post->ID, 'canvas_height', true);
		
		if($canvas_width==""){$canvas_width = "500";}
		if($canvas_height==""){$canvas_height = "500";}
		
		
		echo '<label for="canvas_height">Canvas Width</label><br/>';
		echo '<input type"text" size="3" id="canvas_width" name="canvas_width" value="'.$canvas_width.'">px';
		
		echo '<hr/>';
		echo '<label for="canvas_height">Canvas Height</label><br/>';
		echo '<input type"text" size="3" id="canvas_height" name="canvas_height" value="'.$canvas_height.'">px';
		
	}
	
	function  drawSideMetaBox($post,$callbackArgs)
	{
		
		global $post;
		
		if($post->ID)
		{
			$thisID = $post->ID;
			echo '[imperial-drawing id='.$thisID.']';
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
				echo '[imperial-drawing id='.$post_ID.']';
			break;
			case "qType":
				// Get the qType
				$qType = get_post_meta($post_ID, 'qType', true);
				
				// GEt the Actual description of this qType //
				$qTypeName = '';
				$qTypeClass = 'ek_'.$qType;
				$thisClass = 'ek_'.$qType;
		
				$qTypeMeta = $thisClass::questionMeta();
				
				echo $qTypeMeta['qString'];
			break;	

			case "responses":
			
				// Get the parent ID i.e the pot ID
				$parentPotID = wp_get_post_parent_id($post_ID);
				//echo '<a href="">Results</a>';
				echo '<a href="options.php?page=ek-question-results&questionID='.$post_ID.'&potID='.$parentPotID.'"><i class="fas fa-chart-pie"></i> Results</a>';

			
			break;
			
		}
		
			
	}	
		
	
	
		// Save metabox data on edit slide
	function savePostMeta ( $postID )
	{
		global $post_type;
		
		if($post_type=="imperial_drawing")
		{
			

		
			// Check if nonce is set.
			if ( ! isset( $_POST['imperial_drawing_metabox_nonce'] ) ) {
			
				return;
			}
			
			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST['imperial_drawing_metabox_nonce'], 'save_imperial_drawing_metabox_nonce' ) ) {
				
				

				
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

							
				


			
			$canvas_width = $_POST['canvas_width'];
			$canvas_height = $_POST['canvas_height'];
			
			update_post_meta( $postID, 'canvas_width', $canvas_width );        
			update_post_meta( $postID, 'canvas_height', $canvas_height );
			

			

			}		
	
	}	
	
	

	
		
	function create_AdminPages()
	{
		
		/* Create Question Results Pages */		
		$parentSlug = "admin.php?page=imperial_activities";
		$page_title="Tables";
		$menu_title="";
		$menu_slug="edit.php?post_type=imperial_table";
		$function=  array( $this, 'drawResultsPage' );
		$myCapability = "delete_others_pages";
		//add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);		
	}			
			
	 
	
} //Close class


?>