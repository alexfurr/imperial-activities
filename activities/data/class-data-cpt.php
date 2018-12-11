<?php
$imperialDataCollectionCPT = new imperialDataCollectionCPT();
class imperialDataCollectionCPT
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
		add_action( 'add_meta_boxes_imperial_data', array( $this, 'addMetaBoxes' ));
		
	
		// Remove and add columns in the admin table
		add_filter( 'manage_imperial_data_posts_columns', array( $this, 'my_custom_post_columns' ), 10, 2 );		
		add_action('manage_imperial_data_posts_custom_column', array($this, 'customColumnContent'), 10, 2);	
		
		// Create Results Pages
		add_action( 'admin_menu', array( $this, 'create_AdminPages' ));
				

						
	}
	
	
/*	---------------------------
	ADMIN-SIDE MENU / SCRIPTS 
	--------------------------- */
	function create_CPT ()
	{
		
		$singular = 'Data Collection Item';
		$plural = 'Data Collection';
	
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
			'all_items'          =>		$plural,
			'search_items'       => 'Search '.$plural,
			'parent_item_colon'  => '',
			'not_found'          => 'No '.$plural.' found.',
			'not_found_in_trash' => 'No '.$plural.' found in Trash.'
		);
	
		$args = array(
			'labels'            	=> $labels,
			'public'             	=> true,
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
			'menu_position'     	=> 100,
			'supports'          	=> array( 'title', 'editor' )
			
		);
		
		register_post_type( 'imperial_data', $args );
	}
	
	
	
	
	// Register the metaboxes on  CPT
	function  addMetaBoxes()
	{
		
		global $post;	

		//Main Table editing
		$id 			= 'data_help';
		$title 			= 'How to use this activity';
		$drawCallback 	= array( $this, 'drawContentMetaBox' );
		$screen 		= 'imperial_data';
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
		$id 			= 'data_shortcode';
		$title 			= 'Shortcode';
		$drawCallback 	= array( $this, 'drawSideMetaBox' );
		$screen 		= 'imperial_data';
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
		wp_nonce_field( 'save_imperial_data_metabox_nonce', 'imperial_data_metabox_nonce' );

		echo '<h3>How to add a data input box</h3>
		Simply type<br>
		<div style="padding:3px; margin:10px 0px; border:1px solid #ccc; background:#f7f7f7">
		[data name="input1"]</div>
		into the table cell, or anywhere you want to add an input box.
		<br/><br/>The text "input1" can be anything you wish, but if you want more than one inputs give them all a unique name.
		e.g.
		<hr/>
		
		[data name="input1"]<br/>
		<br/>
		[data name="input2"]';
		
		echo '<h3>How to add a formula box</h3>
		You can add a read only input box that is the result of previously entered data, by using the [data value=""] function. An example is shown below.
		<div style="padding:3px; margin:10px 0px; border:1px solid #ccc; background:#f7f7f7">
		[data value="(input1 + input2)/2"]</div>
		
		The above will take the values of input1 and input2, add them together and divide the result by 2.';
		
		
		echo '<h3>Addtional Options</h3>';
		echo 'You can change the size of the input field  by adding size="5" (for example) so a full example would be<br/>';
		echo '<div style="padding:3px; margin:10px 0px; border:1px solid #ccc; background:#f7f7f7">';
		echo '[data name="input1" size="5"]</div>';
		
	}
	
	
	
	function  drawSideMetaBox($post,$callbackArgs)
	{
		
		global $post;
		
		if($post->ID)
		{
			$thisID = $post->ID;
			echo '[imperial-data id='.$thisID.']';
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
		$columns['submissions'] = 'Submissions';	
		
		 
	  return $columns;
	}		
	
	
		// Content of the custom columns for Topics Page
	function customColumnContent($column_name, $post_ID)
	{
		
		switch ($column_name)
		{
			case "shortcode":			
				echo '[imperial-data id='.$post_ID.']';
			break;


			case "submissions":
			
				// Get the parent ID i.e the pot ID
				//echo '<a href="">Results</a>';
				echo '<a href="options.php?page=imperial-data-submissions&ID='.$post_ID.'"><i class="fas fa-chart-bar"></i> Submission Data</a>';

			
			break;
			
		}
		
			
	}	
		
	
	
		// Save metabox data on edit slide
	function savePostMeta ( $postID )
	{
		global $post_type;
		
		if($post_type=="imperial_data")
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
	
	

	
		
	function create_AdminPages()
	{
	
				
		
		/* Create Results Pages */		
		$parentSlug = "no_parent";
		$page_title="Data Submissions";
		$menu_title="";
		$menu_slug="imperial-data-submissions";
		$function=  array( $this, 'drawResultsPage' );
		$myCapability = "delete_others_pages";
		add_submenu_page($parentSlug, $page_title, $menu_title, $myCapability, $menu_slug, $function);
	}		
	
	function drawResultsPage()
	{
		require_once IMPERIAL_ACTIVITIES_PATH.'activities/data/submissions.php';
	}


	
			
	 
	
} //Close class


?>