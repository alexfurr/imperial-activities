<?php
$imperialCountdownCPT = new imperialCountdownCPT();
class imperialCountdownCPT
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
		add_action( 'add_meta_boxes_imperial_countdown', array( $this, 'addMetaBoxes' ));
		
	
		// Remove and add columns in the admin table
		add_filter( 'manage_imperial_countdown_posts_columns', array( $this, 'my_custom_post_columns' ), 10, 2 );		
		add_action('manage_imperial_countdown_posts_custom_column', array($this, 'customColumnContent'), 10, 2);	
				

						
	}
	
	
/*	---------------------------
	ADMIN-SIDE MENU / SCRIPTS 
	--------------------------- */
	function create_CPT ()
	{
		
		$singular = 'Countdown Timer';
		$plural = 'Countdown Timers';
	
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
		
		register_post_type( 'imperial_countdown', $args );
	}
	
	
	
	
	// Register the metaboxes on  CPT
	function  addMetaBoxes()
	{
		
		global $post;	

		//Main Table editing
		$id 			= 'countdown_help';
		$title 			= 'Countdown Options';
		$drawCallback 	= array( $this, 'drawContentMetaBox' );
		$screen 		= 'imperial_countdown';
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
		$id 			= 'countdown_shortcode';
		$title 			= 'Shortcode';
		$drawCallback 	= array( $this, 'drawSideMetaBox' );
		$screen 		= 'imperial_countdown';
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
		wp_nonce_field( 'save_imperial_countdown_metabox_nonce', 'imperial_countdown_metabox_nonce' );

		
		//$hours = get_post_meta($post->ID, 'countdown_hours', true);
		$countdown_minutes = get_post_meta($post->ID, 'countdown_minutes', true);
		$countdown_seconds = get_post_meta($post->ID, 'countdown_seconds', true);		
		$countdown_feedback = get_post_meta($post->ID, 'countdown_feedback', true);		
		
		if($countdown_minutes==""){$countdown_minutes=0;}
		
		
		
		echo '<input type"text" size="2" id="countdown_minutes" name="countdown_minutes" value="'.$countdown_minutes.'"> minutes';
		
		echo '<hr/>';
		
		echo '<select name="countdown_seconds" id="countdown_seconds">';
		$i=0;
		while($i<60)
		{
			echo '<option value="'.$i.'"';
			if($countdown_seconds==$i)
			{
				echo 'selected';
			}
			
			echo '>'.$i;
			echo '</option>';
			$i++;
		}
		echo '</select> seconds';	
		
		echo '<hr/>';
		
		
		echo '<h3>Feedback Message</h3>';
		echo 'What do you want to display after the countodwn ends.';		
		
		wp_editor($countdown_feedback, 'countdown_feedback', array(
		'wpautop'		=>      true,
		'media_buttons' =>      true,
		'textarea_name' =>      'countdown_feedback',
		'textarea_rows' =>      5,
		//'teeny'                 =>      true
		)); 
		
	}
	
	
	
	function  drawSideMetaBox($post,$callbackArgs)
	{
		
		global $post;
		
		if($post->ID)
		{
			$thisID = $post->ID;
			echo '[imperial-countdown id='.$thisID.']';
		}
		
	}	
	
	
	// Remove Date Columns on projects
	function my_custom_post_columns( $columns )
	{
		unset(
			$columns['date']
		);			
		//$columns['qType'] = 'Type';	
		$columns['qShortcode'] = 'Shortcode';
		//$columns['responses'] = 'Responses';	
		
		 
	  return $columns;
	}		
	
	
		// Content of the custom columns for Topics Page
	function customColumnContent($column_name, $post_ID)
	{
		
		switch ($column_name)
		{
			case "qShortcode":			
				echo '[imperial-countdown id='.$post_ID.']';
			break;
			case "qType":

			
		}
		
			
	}	
		
	
	
		// Save metabox data on edit slide
	function savePostMeta ( $postID )
	{
		global $post_type;
		
		if($post_type=="imperial_countdown")
		{
		
			// Check if nonce is set.
			if ( ! isset( $_POST['imperial_countdown_metabox_nonce'] ) ) {
				return;
			}
			
			// Verify that the nonce is valid.
			if ( ! wp_verify_nonce( $_POST['imperial_countdown_metabox_nonce'], 'save_imperial_countdown_metabox_nonce' ) ) {
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

			
			
			$countdown_seconds = $_POST['countdown_seconds'];
			$countdown_minutes = $_POST['countdown_minutes'];
			$countdown_feedback = isset( $_POST['countdown_feedback'] ) 	?  		$_POST['countdown_feedback']  		: 'Finished';					
			
			
			update_post_meta( $postID, 'countdown_seconds', $countdown_seconds );        
			update_post_meta( $postID, 'countdown_minutes', $countdown_minutes );
			update_post_meta( $postID, 'countdown_feedback', $countdown_feedback );			
			

			}		
	
	}	
	
	
			
			
	 
	
} //Close class


?>