<?php



$imperialDataCollection = new imperialDataCollection();

class imperialDataCollection
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
		add_shortcode( 'imperial-data', array( $this, 'drawDataCollectionShortcode' ) );
	}
	

	function frontendEnqueues ()
	{
		//Scripts
		wp_enqueue_script('jquery');
		
		// Global  Styles		
		wp_enqueue_script('imperial-table-js', IMPERIAL_ACTIVITIES_URL.'/activities/data/data.js' );
		
		
		// Register Ajax script for front end
		wp_enqueue_script('imperialData_ajaxJS', IMPERIAL_ACTIVITIES_URL.'/activities/data/ajax.js', array( 'jquery' ) ); #Custom AJAX functions
		
		
		//Localise the JS file
		$params = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'ajax_nonce' => wp_create_nonce('imperialData_ajax_nonce')
		);
		wp_localize_script( 'imperialData_ajaxJS', 'imperialData_frontEndAjax', $params );			
		
		

	}	
	
	function drawDataCollectionShortcode($atts)
	{
		
		
		$atts = shortcode_atts( 
			array(
				'id'		=> '',				
				), 
			$atts
		);
		
		$postID = (int) $atts['id'];
		
		
		$html = '<div class="icl-activity-wrap">';
		$html.='<div id="dataCollectionWrap_'.$postID.'">';
		$html.= imperialDataCollection::drawDataCollection($postID);
		$html.='</div>';
		
		// Add an edit question link if they have the appopriate permissions
		if(current_user_can('delete_others_pages'))
		{
			// Get the Parent ID (pot)
			$html.='<div class="object-edit-div">';
			
			// Edit Question
			$html.= '<a href="'.get_home_url().'/wp-admin/post.php?post='.$postID.'&action=edit" target="blank">Edit this Object</a>';
			
			// View Results
			$html.=' | <a href="'.get_home_url().'/wp-admin/options.php?page=imperial-data-submissions&ID='.$postID.'">View Results</a>';
			$html.='</div>';
		}		
		
		$html.='</div>';
		
		return $html;
		
	}
	
	public static function drawDataCollection($postID)
	{
		// Get saved Meta if it exist
		$savedData = get_post_meta( $postID, 'imperialData', true );
		$userID = get_current_user_id();
		$myData = array();
		
		if(isset($savedData[$userID]) )
		{
			
			if(isset($savedData[$userID]["data"]))
			{			
				$myData = $savedData[$userID]["data"];
			}
		}
		

		$thisPost = get_post($postID);
		$content = $thisPost->post_content;
		
		$theTitle = $thisPost->post_title;
		
		$contentStr = '<h3>'.$theTitle.'</h3>';
		
		$contentStr.= apply_filters('the_content', $content);
		$debug = '';
		
		preg_match_all("/\[[^\]]*\]/", $contentStr, $matches);
		$bracketStrings = $matches[0];
		
		// Create array for all the input fields
		$myInputFields = array();
		
		
		foreach ($bracketStrings as $myStr)
		{

			if (strpos($myStr, '[data ') !== false)
			{
				$strContents = substr($myStr, 1, -1);
				
				
				// Check for READ ONLY
				if (strpos($strContents, 'value=') === false)
				{
					
					
					// Get the original ID		
					$originalID = substr($myStr, 0, -1);
					$originalID = str_replace("[data ", "", $originalID);				

					
					$attsArray = shortcode_parse_atts( $originalID);

					$dataName = '';
					$inputSize = '3';
					if(isset($attsArray['name'] ) )
					{
						$dataName = $attsArray['name'];						
						$dataName = preg_replace("/[^A-Za-z0-9 ]/", '', html_entity_decode ($dataName));	
					}
					
					if(isset($attsArray['size'] ) )
					{
						$inputSize = $attsArray['size'];
					}	


					$thisInputID_temp = str_replace(" ", "-", $dataName);
					$thisInputID = "imperial-data-". $thisInputID_temp;
					
									
					$thisValue= '';
					if(isset($myData[$thisInputID]) )
					{
						$thisValue = htmlspecialchars($myData[$thisInputID]);
					}
					
					
					$myInputFields[$thisInputID_temp] = $thisValue; // Add this ID to the replacement array for formulas later on

					
					$replaceStr = '<input size="'.$inputSize.'" type="text" value="'.$thisValue.'" class="imperial-data-input" name="'.$thisInputID.'" id="'.$thisInputID.'" />';
					
					$contentStr = str_replace($myStr, $replaceStr, $contentStr);
				}
				
			}
		}
		

		foreach ($bracketStrings as $myStr)
		{
			
			
			
			
			$formulaCheck = substr($myStr, 1, -1);	
			
			// Check for READ ONLY
			if (strpos($formulaCheck, 'value=') !== false)
			{
				
				$attsArray = shortcode_parse_atts( $formulaCheck);
				
				
				$formula = $attsArray['value'];				
				$formula = html_entity_decode ($formula, ENT_QUOTES); // Remove strange quotes ANNOYING	

				$valuesArray = array();
				$formula = str_replace("data value=", "", $formula);	
				
				
				// Create array of all input names in this formula
				// List all possible math in this forumla
				$delimiters = array(
					"+",
					"-",
					"/",
					"(",
					")",
					"*",
					"'",
				);
				$formulaValues = imperialDataCollection::multiExplode ($delimiters,$formula);	
				
				// Check for any non numeric
				$isValidEquation = true;
				$formulaFieldCount=0;
				foreach ($myInputFields as $fieldID => $fieldValue)
				{	
					// If this field is part of the Forumla check its a numeric value
					if(in_array($fieldID, $formulaValues) )
					{
						$formulaFieldCount++; // up the count by one - we check this in case of typos.If count is zero there is  no valid data
						
						if(!is_numeric ($fieldValue))
						{
							$isValidEquation = false;
						}

					}
					
					$formula = str_replace($fieldID, $fieldValue, $formula);		

				}		
				
				
				// If there are no fields then don't do the forumula				
				if($formulaFieldCount==0){$isValidEquation = false;}
				
				// Only have numbers and equestions
				$formula = preg_replace( "/[^0-9-+.\/()*]/", '', $formula );
				
				// If there left and righ tbrackets don't match counts its not a valid formula
				$leftBrackets = substr_count($formula, '(');
				$rightBrackets = substr_count($formula, ')');
				
				if($leftBrackets<>$rightBrackets){$isValidEquation = false;}
		
				
				if($isValidEquation==true)
				{			
			
					// Do not show divisino by zerp errors
					set_error_handler(function ()
					{
						throw new Exception('Ach!');
					});

					try{
						$formulaSolution = eval('return '.$formula.';');
					}
					catch( Exception $e ){
						//echo "Divide by zero, I don't fear you!".PHP_EOL;
						$formulaSolution = '-';
					}
					


					restore_error_handler();
					

					$formulaSolution = round($formulaSolution, 2);  

				}	
				else
				{
					$formulaSolution = '-';	
				}

				$inputSize = 3;
				if(isset($attsArray['size'] ) )
				{
					$inputSize = $attsArray['size'];
				}	
				
			
				$replaceStr = '<input type="readonly" size="'.$inputSize.'" class="imperial-data-input" type="text" value="'.$formulaSolution.'" readonly>';
				$contentStr = str_replace($myStr, $replaceStr, $contentStr);
				//	$replaceStr = '<input type="text" value="'.$thisValue.'" class="imperial-data-input" name="'.$thisInputID.'" id="'.$thisInputID.'" />';

				
				
			}

		}		
		

		
		$saveButton = '<div><button class="imperial-data-save-button" id="imperial-data-save-'.$postID.'" >Save</button></div>';
		$contentStr = '<div id="imperial-data-wrap-'.$postID.'">'.$contentStr;		
		$contentStr.=$saveButton.'</div>';
		
		return $contentStr;
	}
	
	
	// Split a string on multiple delimeters
	// Useful for extracing names of data inputs in a forumla
	static function multiExplode ($delimiters,$string)
	{    
			
		$ready = str_replace($delimiters, $delimiters[0], $string);
		$launch = explode($delimiters[0], $ready);
		
		foreach ($launch as $KEY => $VALUE)
		{
			// Remove non alpha numberic
			$newValue = preg_replace("/[^A-Za-z0-9 ]/", '', $VALUE);
			$launch[$KEY] = $newValue;
			
		}

		// Remove Blank Values
		$launch = array_filter($launch);

		// Remove non alhpanumeric
		return  $launch;
	}
	
	
	static function drawSubmission($postID, $CSV=false)
	{
		
		$html='';
		$csvArray=array();
		
		$thisPost = get_post($postID);
		
		$content = $thisPost->post_content;
		$contentStr = apply_filters('the_content', $content);

		preg_match_all("/\[[^\]]*\]/", $contentStr, $matches);
		$bracketStrings = $matches[0];

		// Create array for all the input fields
		$myInputFields = array();

		foreach ($bracketStrings as $myStr)
		{

			if (strpos($myStr, '[data ') !== false)
			{
				$strContents = substr($myStr, 1, -1);
				
				$attsArray = shortcode_parse_atts( $strContents);
				

				
				if(isset($attsArray['name'] ) )
				{
					$dataName = $attsArray['name'];						
					$dataName = preg_replace("/[^A-Za-z0-9 ]/", '', html_entity_decode ($dataName));	
					$myInputFields[] = $dataName;
				}	

			}
		}
		$savedData = get_post_meta( $postID, 'imperialData', true );
		$userArray = imperialActivityUtils::getBlogUsers();
		$html.= '<table class="imperial-table" width="90%">';

		$html.= '<tr><th>Name</th><th>Username</th><th>Role</th><th>Submission Date</th>';
		$csvArrayHeaderArray = array("Name", "Username", "Role", "Date");

		foreach ($myInputFields as $inputID)
		{
			$html.= '<th>'.$inputID.'</th>';
			$csvArrayHeaderArray[] = $inputID;
		}
		$html.= '</tr>';
		$csvArray[] = $csvArrayHeaderArray;
		
		
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
			$thisUserDataArray = array();
			
			if(isset($savedData[$userID]["data"]) )
			{
				$thisUserDataArray = $savedData[$userID]["data"];
			}
			
			$UKdate = '-';
			if(isset($savedData[$userID]["dateSubmitted"]) )
			{
				$thisUserDataDate = $savedData[$userID]["dateSubmitted"];
				$UKdate = imperialActivityUtils::getUKdate($thisUserDataDate);

			}	

			
			
			$html.= '<tr>';
			$html.= '<td>'.$fullname.'</td>';
			$html.= '<td>'.$username.'</td>';	
			$html.= '<td>'.$role.'</td>';
			$html.= '<td>'.$UKdate.'</td>';
			
			$tempCSVarray = array ($fullname, $username, $role, $UKdate);
			
			// Get the data
			foreach ($myInputFields as $inputID)
			{
				$thisValue = '-';
				
				
				$dataKey = 'imperial-data-'.$inputID;
				if(isset($thisUserDataArray[$dataKey]) )
				{
					$thisValue = $thisUserDataArray[$dataKey];
					
				}
				
				if($thisValue==""){$thisValue = '-';}
				$html.= '<td>'.$thisValue.'</td>';
				
				$tempCSVarray[] = $thisValue;
			}	
			
			$html.= '</tr>';	
			$csvArray[] = $tempCSVarray;			
			
		}

		$html.= '</table>';		
		
		
		if($CSV==true)
		{
			return $csvArray;
		}
		else
		{
			return $html;
		}
		
		
		
	}
	

}
?>