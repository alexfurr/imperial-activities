<?php

$imperialCanvas = new imperialCanvas();

class imperialCanvas
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
		add_shortcode( 'imperial-drawing', array( $this, 'drawCanvas' ) );
		

	}
	

	function frontendEnqueues ()
	{
		//Scripts
		wp_enqueue_script('jquery');
		wp_enqueue_script('fabric-js', 'https://cdnjs.cloudflare.com/ajax/libs/fabric.js/2.4.1/fabric.min.js' );
		wp_enqueue_style( 'imperial-canvas-style', IMPERIAL_ACTIVITIES_URL . '/activities/canvas/drawing.css' );
		wp_enqueue_script('imperial-canvas-js', IMPERIAL_ACTIVITIES_URL.'/activities/canvas/drawing.js' );		
		
		// Register Ajax script for front end
		wp_enqueue_script('imperialCanvas_ajaxJS', IMPERIAL_ACTIVITIES_URL.'/activities/canvas/ajax.js', array( 'jquery' ) ); #Custom AJAX functions
		
		//Localise the JS file
		$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'ajax_nonce' => wp_create_nonce('imperialCanvas_ajax_nonce')
		);
		wp_localize_script( 'imperialCanvas_ajaxJS', 'imperialCanvas_frontEndAjax', $params );
	}	
	

    
    
//--------------   
    function drawCanvas($atts)
	{
		
		wp_enqueue_script('imperial-canvas-js', IMPERIAL_ACTIVITIES_URL.'/activities/canvas/drawing.js' );
		
		
		$atts = shortcode_atts( 
			array(
				'id'		=> ''
				), 
			$atts
		);
		
		$str='';
		
		$canvasID = (int) $atts['id'];
		$backgroundImage = '';
		
		$canvas_width = get_post_meta($canvasID, 'canvas_width', true);
		$canvas_height = get_post_meta($canvasID, 'canvas_height', true);
		
		// Check for background
		$background = wp_get_attachment_image_src( get_post_thumbnail_id( $canvasID ), 'full' );
		
		if(isset($background[0]) )
		{
			$backgroundImage = $background[0];
		}
		
		$str.='<div class="icl-activity-wrap">';
		$str.='<div class="tab-wrap"><input id="tab1" type="radio" name="tabs"><label for="tab1" id="drawing-mode_' .$canvasID. '">Drawing mode</label>';
		$str.='<input id="tab2" type="radio" name="tabs"><label id="text-canvas_' .$canvasID. '" for="tab2">Text mode</label>';
		//$str.='<div class="tab-wrap"><a href="javascript:return:false;"><span id="drawing-mode_' .$canvasID. '">Cancel drawing mode</span></a><br>';
		$str.='</div>';
		
		$str.= '<div class="canvas-container" style="
		width: '.$canvas_width.'px;
		height: '.$canvas_height.'px;
		position: relative;
		user-select: none;
		background-image: url('.$backgroundImage.');
		
		">';
		
		
		
		$str.='<canvas id="c_' .$canvasID. '" width="'.$canvas_width.'" height="'.$canvas_height.'"
		style="border: 1px solid rgb(170, 170, 170);
		position: absolute;
		width: '.$canvas_width.'px;
		height: '.$canvas_height.'x;
		left: 0px; top: 0px;
		touch-action: none;
		user-select: none;
		" class="lower-canvas"></canvas>';
		$str.='</div>';
		$str.='<div class="btn-wrapper">
		  <button id="save-canvas_' .$canvasID. '" class="btn btn-info" title="Save"><i class="fa fa-floppy-o"></i></button>	
		  <button id="clear-canvas_' .$canvasID. '" class="btn btn-info" title="Clear"><i class="fa fa-trash"></i></button>
		  <button id="redo-canvas_' .$canvasID. '" class="btn btn-info" title="Redo"><i class="fa fa-redo"></i></button>
		  <button id="undo-canvas_' .$canvasID. '" class="btn btn-info" title="Undo"><i class="fa fa-undo"></i></button>
		
		  <br>

		  <div class="drawing-options" id="drawing-mode-options_' .$canvasID. '">
			<label for="drawing-mode-selector_' .$canvasID. '">Mode:</label>
			<select id="drawing-mode-selector_' .$canvasID. '">
			  <option>Pencil</option>
			  <option>Circle</option>
			  <option>Spray</option>
			</select><br>

			<label for="drawing-line-width_' .$canvasID. '">Line width:</label>
			<span class="info">2</span><input type="range" value="2" min="0" max="99" id="drawing-line-width_' .$canvasID. '"><br>

			<label for="drawing-color_' .$canvasID. '">Line color:</label>';
			
			
			//<input type="color" value="#005E7A" id="drawing-color_' .$canvasID. '">
			
			
			
			$str.='<input type="color" id="drawing-color_'.$canvasID.'" style="background-color:#336699" value="#336699" />
				<script>
				jQuery("#drawing-color_'.$canvasID.'").spectrum({
					color: "#336699",
					preferredFormat: "hex"
				});
				</script>			
			
			<br>
		  </div>
		</div>';


		// See if there is any saved data
		$userID = get_current_user_id();
		
		$savedDataValue = '';
		
		if($userID)
		{
			
			// Get the post meta
			$savedData = get_post_meta( $canvasID, 'canvasData', true );
			
			
			if(is_array($savedData) )
			{
				if(isset($savedData[$userID]) )
				{
					$savedDataValue = $savedData[$userID];
					$savedDataValue = urlencode ($savedDataValue);
				}
			}	
		}
		
		$str.='<input type="hidden" value="'. $savedDataValue.'" name="savedDrawing_'.$canvasID.'" id="savedDrawing_'.$canvasID.'">';


		// Initialise the canvas
		$str.='<script>imperialCanvas.init('.$canvasID.');</script>';

	
	
		// Add an edit question link if they have the appopriate permissions
		if(current_user_can('delete_others_pages'))
		{
			// Get the Parent ID (pot)
			$str.='<div class="object-edit-div">';
			
			// Edit Question
			$str.= '<a href="'.get_home_url().'/wp-admin/post.php?post='.$canvasID.'&action=edit" target="blank">Edit this Object</a>';
			
			// View Results
			//$str.=' | <a href="'.get_home_url().'/wp-admin/options.php?page=imperial-data-submissions&ID='.$canvasID.'">View Results</a>';
			$str.='</div>';
		}		
		
	
	
	
		$str.='</div>';
	


		
		return $str;
	}
    
    
	
}
?>