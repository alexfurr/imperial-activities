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

echo '<h1>Document Submissions</h1>';
echo '<a href="edit.php?post_type=imperial_doc_upload" class="button-secondary"><i class="fas fa-chevron-left"></i> Back to Document Uploads</a>';

echo '<a href="?page=imperial-doc-submissions&ID='.$postID.'&myAction=activitiesDownloadUploads" class="button-secondary"><i class="fas fa-download"></i> Download this table</a>';

echo imperialDocumentUpload::drawUploadsTable($postID);


die();


/*










$userArray = imperialActivityUtils::getBlogUsers();

$submissions = get_post_meta( $postID, 'imperialData', true );
$submissionType = get_post_meta( $postID, 'submissionType', true ); // Image or doc
echo '<table class="imperial-table" width="90%">';

echo '<thead><th>Name</th><th>Username</th><th>Role</th><th></th>';

echo '</thead>';
// now go through all users and add to table, along with how many times they've done the question etc
foreach ( $userArray as $userID => $userInfo )
{
	$fullname = $userInfo['fullname'];
	$firstName = $userInfo['firstName'];
	$surname = $userInfo['surname'];
	$username = $userInfo['username'];
	$role = $userInfo['role'];
	
	$response = '';
	// Get the Data for this person
				
	
	echo '<tr>';
	echo '<td>'.$fullname.'</td>';
	echo '<td>'.$username.'</td>';	
	echo '<td>'.$role.'</td>';
	echo '<td width="100px">';
		
	$myDocument = '';
	
	if(isset($submissions[$userID]) )
	{
		$myDocument = $submissions[$userID];			
	}


	if($myDocument<>"")
	{
		
		$randomstring = bin2hex( openssl_random_pseudo_bytes( 16 ) );
		$hash = hash( 'md5', $randomstring );
		
		$upload_dir   = wp_upload_dir();		
		$uploadDir = $upload_dir['baseurl'].'/imperial-activity-uploads/'.$postID;			
		
		
		switch ($submissionType)
		{
			
			case "image":

				echo '<a href="'.$uploadDir.'/'.$myDocument.'?hash='.$hash.'" target="blank"><img width="100px" src="'.$uploadDir.'/'.$myDocument.'?hash='.$hash.'"></a>';

			break;

			
			default:
				echo '<a href="'.$uploadDir.'/'.$myDocument.'?hash='.$hash.'" target="blank">'.$myDocument.'</a>';			
			
			break;
			
			
		}
	}
	else
	{
		echo '-';
	}
	echo '</td>';
	
	
	echo '</tr>';		
	
}

echo '</table>';
		*/

?>