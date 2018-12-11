<?php


if ( isset($_GET['myAction'] ) )
{
	
	

	
	$myAction = $_GET['myAction'];
	
	switch ($myAction)
	{
		
		case "activitiesDownloadData":


				// Handle CSV Export for data
				add_action( 'init', array('activitiesDownloadCSV', 'activitiesDownloadCSVData') );
			
		break;		
		
		case "activitiesDownloadUploads":


				// Handle CSV Export for uploads
				add_action( 'init', array('activitiesDownloadCSV', 'activitiesDownloadUploadsCSV') );
			
		break;			
		
		
	}


	
}


class activitiesDownloadCSV
{

	
	
	public static function activitiesDownloadCSVData()
	{
		// Check for current user privileges 
		if(!current_user_can('delete_others_pages') )
		{		
			return;
		}



		$activityID = $_GET['ID'];
		
		$postTitle = get_the_title($activityID);
	
		
		$CSV_array = imperialDataCollection::drawSubmission($activityID, true);
		
		$fileNameStart = preg_replace("/[^A-Za-z0-9 ]/", '', $postTitle).'-'.$activityID;
		
		
		$fileName = $fileNameStart.'.csv';
		
		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		ob_end_clean();		 // Remove unwanted blank spaces / line breaks
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$fileName}");
		header("Expires: 0");
		header("Pragma: public");
		
		$fh = @fopen( 'php://output', 'w' );
		
		foreach ($CSV_array as $fields) {
			fputcsv($fh, $fields);
		}				
		
		// Close the file
		fclose($fh);
		// Make sure nothing else is sent, our file is done
		die();
	}	
	
	
	
	public static function activitiesDownloadUploadsCSV()
	{
		// Check for current user privileges 
		if(!current_user_can('delete_others_pages') )
		{		
			return;
		}



		$activityID = $_GET['ID'];
		
		$postTitle = get_the_title($activityID);
	
		
		$CSV_array = imperialDocumentUpload::drawUploadsTable($activityID, true);
		
		
		$fileNameStart = preg_replace("/[^A-Za-z0-9 ]/", '', $postTitle).'-'.$activityID;
		
		
		$fileName = $fileNameStart.'.csv';
		
		
		header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
		header('Content-Description: File Transfer');
		ob_end_clean();		 // Remove unwanted blank spaces / line breaks
		header("Content-type: text/csv");
		header("Content-Disposition: attachment; filename={$fileName}");
		header("Expires: 0");
		header("Pragma: public");
		
		$fh = @fopen( 'php://output', 'w' );
		
		foreach ($CSV_array as $fields) {
			fputcsv($fh, $fields);
		}				
		
		// Close the file
		fclose($fh);
		// Make sure nothing else is sent, our file is done
		die();
	}		
	

	
	
} //Close class
?>