<?php

if ( ! defined( 'ABSPATH' ) ) 
{
	die();	// Exit if accessed directly
}

// Only let them view if admin		
	if(!current_user_can( 'delete_others_pages'))
{
	die();
}	

$postID = $_GET['ID'];
$postTitle = get_the_title($postID);

echo '<h1>'.$postTitle.' : Data Submissions</h1>';
echo '<a href="edit.php?post_type=imperial_data" class="button-secondary"><i class="fas fa-chevron-left"></i> Back to Data Collection</a>';

echo '<a href="?page=imperial-data-submissions&ID='.$postID.'&myAction=activitiesDownloadData" class="button-secondary"><i class="fas fa-download"></i> Download this Data</a>';




// Get all the submission inputs
$postID = $_GET['ID'];
echo imperialDataCollection::drawSubmission($postID);
		

?>