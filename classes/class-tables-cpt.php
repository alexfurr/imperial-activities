<?php
$imperialActivityTables = new imperialActivityTables();
class imperialActivityTables
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
		add_action( 'add_meta_boxes_imperial_table', array( $this, 'addMetaBoxes' ));
		
	
		// Remove and add columns in the admin table
		add_filter( 'manage_imperial_table_posts_columns', array( $this, 'my_custom_post_columns' ), 10, 2 );		
		add_action('manage_imperial_table_posts_custom_column', array($this, 'customColumnContent'), 10, 2);	
				

						
	}
	
	
/*	---------------------------
	ADMIN-SIDE MENU / SCRIPTS 
	--------------------------- */
	function create_CPT ()
	{
		
		$singular = 'Table';
		$plural = 'Tables';
	
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
			'supports'          	=> array( 'title' )
			
		);
		
		register_post_type( 'imperial_table', $args );
	}
	
	
	
	
	// Register the metaboxes on  CPT
	function  addMetaBoxes()
	{
		
		global $post;	

		//Main Table editing
		$id 			= 'tbale_content';
		$title 			= 'Table Content';
		$drawCallback 	= array( $this, 'drawContentMetaBox' );
		$screen 		= 'imperial_table';
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

		
			
		//Style Metabox
		$id 			= 'table_style';
		$title 			= 'Table Style';
		$drawCallback 	= array( $this, 'drawStyleMetaBox' );
		$screen 		= 'imperial_table';
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
		
			
	}
	
	function  drawContentMetaBox($post,$callbackArgs)
	{
		
		// Add Nonce Field
		wp_nonce_field( 'save_imperial_table_metabox_nonce', 'imperial_table_metabox_nonce' );

		echo 'CONTENT HERE';
		
	}
	
	
	
	function drawStyleMetaBox($post, $callbackArgs)
	{
		
		echo 'STYLES HERE';

		
	}
	
	
	// Remove Date Columns on projects
	function my_custom_post_columns( $columns )
	{
		unset(
			$columns['date']
		);			
		//$columns['qType'] = 'Type';	
		//$columns['qShortcode'] = 'Shortcode';
		//$columns['responses'] = 'Responses';	
		
		 
	  return $columns;
	}		
	
	
		// Content of the custom columns for Topics Page
	function customColumnContent($column_name, $post_ID)
	{
		/*
		switch ($column_name)
		{
			case "qShortcode":			
				echo '[ek-question id='.$post_ID.']';
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
		*/
			
	}	
		
	
	
		// Save metabox data on edit slide
	function savePostMeta ( $postID )
	{
		global $post_type;
		
		if($post_type=="imperial_table")
		{
		
			// Check if nonce is set.
			if ( ! isset( $_POST['imperial_table_metabox_nonce'] ) ) {
				return;
			}
			
			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST['imperial_table_metabox_nonce'], 'save_imperial_table_metabox_nonce' ) ) {
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