<?php
/*
Plugin Name: Imperial Activities
Description: Freehand drawing, tables and more
Version: 0.1.1
Author: Alex Furr
GitHub Plugin URI: https://github.com/ImperialCollegeLondon/imperial-activities
*/


define( 'IMPERIAL_ACTIVITIES_URL', plugins_url('imperial-activities' , dirname( __FILE__ )) );
define( 'IMPERIAL_ACTIVITIES_PATH', plugin_dir_path(__FILE__) );

include_once( IMPERIAL_ACTIVITIES_PATH . '/classes/class-wp.php');
include_once( IMPERIAL_ACTIVITIES_PATH . '/classes/class-utils.php');

// Drawing
include_once( IMPERIAL_ACTIVITIES_PATH . 'activities/canvas/class-canvas.php');
include_once( IMPERIAL_ACTIVITIES_PATH . 'activities/canvas/class-ajax.php');
include_once( IMPERIAL_ACTIVITIES_PATH . 'activities/canvas/class-canvas-cpt.php');

// Document UPload
include_once( IMPERIAL_ACTIVITIES_PATH . 'activities/document-upload/includes.php');

// Data
include_once( IMPERIAL_ACTIVITIES_PATH . 'activities/data/includes.php');

// Download options
include_once( IMPERIAL_ACTIVITIES_PATH . 'classes/class-download.php');

// Countdown Timers
include_once( IMPERIAL_ACTIVITIES_PATH . 'activities/timer/class-timer.php');
include_once( IMPERIAL_ACTIVITIES_PATH . 'activities/timer/class-timer-cpt.php');

// Icons
include_once( IMPERIAL_ACTIVITIES_PATH . 'classes/class-icons.php');



?>
