<?php

$imperialActivities = new imperialActivities();

class imperialActivities
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
		add_action( 'admin_menu', array( $this, 'createAdminMenu' ) );	
		add_action( 'admin_enqueue_scripts', array( $this, 'adminEnqueues' ) );	
		

	}
	

	function frontendEnqueues ()
	{
		//Scripts
		wp_enqueue_script('jquery');
		
		// Global  Styles
		wp_enqueue_style( 'imperial-activity-styles', IMPERIAL_ACTIVITIES_URL . '/css/styles.css' );
		wp_enqueue_script('imperial-colourpicker-js', IMPERIAL_ACTIVITIES_URL. '/libs/spectrum/spectrum.js' );
		wp_enqueue_style('imperial-colourpicker-css', IMPERIAL_ACTIVITIES_URL. '/libs/spectrum/spectrum.css' );
	}

	function adminEnqueues ()
	{

		// Global  Styles
		wp_enqueue_style( 'imperial-activity-admin-styles', IMPERIAL_ACTIVITIES_URL . '/css/admin-styles.css' );


		// Font Awesome
		wp_register_style( 'imperial-activities-font-awesome', '//use.fontawesome.com/releases/v5.2.0/css/all.css' );
		

	}		
	
	
	function createAdminMenu ()
	{
		
		// Main Page
		$page_title="Activites";
		$menu_title = "Activities";
		$capability = "delete_others_pages";
		$menu_slug = "imperial_activities";
		$drawFunction = "drawActivitiesHome";
		$icon = "dashicons-media-interactive";
		$position = 22;		
		add_menu_page($page_title, $menu_title, $capability, $menu_slug, array($this, $drawFunction), $icon, $position);	

		
		// Add Sub activities
		
		
		/* Add Submenu */		
		$activitiesArray = imperialActivities::getActivityTypes();
		
				
		
		foreach ($activitiesArray as $activityInfo)
		{
			$activityName = $activityInfo[0];			
			$activityCPT = $activityInfo[1];
			add_submenu_page(
				'imperial_activities',                 // parent slug
				$activityName,             // page title
				$activityName,             // sub-menu title
				'delete_others_pages',                 // capability
				'edit.php?post_type='.$activityCPT //your menu menu slug
			);		
		}
		
		
	}
	
	function drawActivitiesHome ()
	{
		

		wp_enqueue_style( 'imperial-activities-font-awesome' );		
		
		echo '<h1>Activities</h1>';

		$activitiesArray = imperialActivities::getActivityTypes();
		
				
		echo '<div class="activities_home_wrap">';
		
		foreach ($activitiesArray as $activityInfo)
		{
			$activityName = $activityInfo[0];			
			$activityCPT = $activityInfo[1];
			$activityIcon = $activityInfo[2];
			
			echo '<a href="edit.php?post_type='.$activityCPT.'">';
			echo '<div>';
			echo '<div class="activityName">'.$activityName.'</div>';
			
			echo '<div class="activityIcon">';
			echo '<span class="fa-stack fa-2x">';
			echo '<i class="fas fa-circle fa-stack-2x"></i>';
			echo '<i class="fa-inverse fa-stack-1x fas fa-'.$activityIcon.'" style="color:#fff"></i>';			
			echo '</span>';
			echo '</div>';			
			
			
			
			echo '</div>';
			echo '</a>';
		}
		
		echo '</div>';

	}
	
	
	public static function getActivityTypes()
	{
		$activitiesArray = array(
		
		array("Data Collection", "imperial_data", "table"),
		array("Drawing / Annotation", "imperial_canvas", "pen-fancy"),
		array("Countdown Timer", "imperial_countdown", "hourglass-half"),
		//array("Picture Upload", "imperial_img_upload", "image"),
		array("Document/Image Upload", "imperial_doc_upload", "file-upload"),		
		
		);

		return $activitiesArray;		
		
		
		
	}
	
}
?>