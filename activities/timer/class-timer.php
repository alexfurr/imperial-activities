<?php

$imperialCountdown = new imperialCountdown();

class imperialCountdown
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
		add_shortcode( 'imperial-countdown', array( $this, 'drawCountdownShortcode' ) );
	}
	

	function frontendEnqueues ()
	{
		//Scripts
		wp_enqueue_script('jquery');
		
		// Global  Styles		
		wp_enqueue_script('imperial-countdown-js', IMPERIAL_ACTIVITIES_URL.'/activities/timer/timer.js' );		

	}	
	
	function drawCountdownShortcode($atts)
	{		
		
		$html='';
		
		$atts = shortcode_atts( 
			array(
				'id'		=> ''
				), 
			$atts
		);
		
		$thisPostID = (int) $atts['id'];
		
		
		// Get the minutes and seconds and calculate how many total seconds		
		$countdown_minutes = get_post_meta($thisPostID, 'countdown_minutes', true);
		$countdown_seconds = get_post_meta($thisPostID, 'countdown_seconds', true);
		$countdown_feedback = get_post_meta($thisPostID, 'countdown_feedback', true);
		$countdown_feedback = nl2br( $countdown_feedback );
		$countdown_feedback = stripslashes($countdown_feedback);

		if($countdown_minutes==""){$countdown_minutes = 0;}
		
		$totalSeconds = ($countdown_minutes*60) + $countdown_seconds;
		
		// Add the feedback as a hidden div
		$html.= '<div id="imperialCountdownTimerFeedback'.$thisPostID.'" style="display:none;">';
		$html.=$countdown_feedback;
		$html.='</div>';
		
		$html.= '<div id="imperialCountdownTimer'.$thisPostID.'" class="icl-countdown">';
		$html.='</div>';
		$html.='<script>imperialCountdown.init('.$thisPostID.', '.$totalSeconds.');</script>';
		
		return $html;
		
	}	
	
}
?>