<?php

class imperialActivityUtils
{

	// Get an array of blog users and their role
	static function getBlogUsers($args=array())
	{
		
		$userArray = array();
		$blogusers = get_users();
		
		// Array of WP_User objects.
		foreach ( $blogusers as $userInfo )
		{
			$userID = $userInfo->ID;
			$fullname = esc_html( $userInfo->display_name );
			$firstName= esc_html( $userInfo->first_name );
			$surname= esc_html( $userInfo->last_name );		
			$username = $userInfo->user_login;
			$roles = $userInfo->roles;
			if($roles)
			{
				$userlevel = $roles[0];
			}
			else
			{
				$userlevel = "";	
			}
			
			$userArray[$userID] = array
			(
				"fullname"	=> esc_html( $userInfo->first_name ).' '.esc_html( $userInfo->last_name ),
				"firstName"	=> esc_html( $userInfo->first_name ),
				"surname"	=> esc_html( $userInfo->last_name ),
				"username"	=> $userInfo->user_login,
				"role"		=> $userlevel,
			);
		}	

		return $userArray;
		
	}
	
	
	
	static function saveDatePickerPostMeta($postID, $objectID)
	{
		$thisDate = $_POST[$objectID];
		$thisMin = $_POST[$objectID.'_thisMin'];
		$thisHour = $_POST[$objectID.'_thisHour'];
		$thisAMPM = $_POST[$objectID.'_thisAMPM'];
		$myTime= $thisHour.':'.$thisMin.' '.$thisAMPM;		
		$myDateTime = $thisDate.' '.$myTime;
		
		
		if($thisDate=="")
		{
			update_post_meta( $postID, $objectID, ""  );
			return;
		}
		
		// Update Document Submission meta
		update_post_meta( $postID, $objectID, $myDateTime  );
	}
	
	static function addDatePicker($objectID, $label="Available From", $thisDate="")
	{
		
		$thisHour = '';
		$thisMin = '';
		$thisAMPM = '';

		if($thisDate)
		{
			$thisDateTime = new DateTime($thisDate);
			$thisDate = $thisDateTime->format('d-m-Y');
			$thisHour = $thisDateTime->format('g');
			$thisMin = $thisDateTime->format('i');
			$thisAMPM = $thisDateTime->format('A');
		}
				
		
			
		echo '<label for="'.$objectID.'">'.$label.'</label><br/>';
		echo '<input type="text" name="'.$objectID.'" id="'.$objectID.'" size="12" value="'.$thisDate.'"/>';	
		echo '<br/><select name="'.$objectID.'_thisHour">';
		$i=1;
		while ($i<=12)
		{
			echo '<option value="'.$i.'" ';
			if($thisHour==$i){echo ' selected';}		
			echo '>'.$i.'</option>';
			$i++;
		}
		echo '</select>';
		
		
		
		
		echo '<select name="'.$objectID.'_thisMin">';
		$i=0;
		while ($i<=55)
		{
			$tempMin = $i;			
			if($tempMin==0 || $tempMin==5)
			{
				$tempMin = '0'.$tempMin;				
			}
			echo '<option value="'.$tempMin.'" ';			
			if($thisMin==$i){echo ' selected';}
			echo '>'.$tempMin.'</option>';
			$i = $i+5;
		}
		echo '</select>';

		echo '<select name="'.$objectID.'_thisAMPM">';
		echo '<option value="AM" ';	
		if($thisAMPM=="AM"){echo ' selected';}
		echo '>AM</option>';
		
		echo '<option value="PM" ';	
		if($thisAMPM=="PM"){echo ' selected';}
		echo '>PM</option>';
		
		echo '</select>';
		echo '<hr/>';

		
		
		// Enable Date Picker
		?>
		<script>
			jQuery( document ).ready( function ()
			{
				jQuery('#<?php echo $objectID;?>').datepicker({
				dateFormat : 'dd-mm-yy'
				});
		
			});
		</script>	
		<?php		
		
	}
	
	static function generateRandomString($length = 10)
	 {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$charactersLength = strlen($characters);
		$randomString = '';
		for ($i = 0; $i < $length; $i++) {
			$randomString .= $characters[rand(0, $charactersLength - 1)];
		}
		return $randomString;
	}
	
	static function getUKdate($inputDate)
	{
		$tz = new DateTimeZone('Europe/London');
		$date = new DateTime($inputDate);
		$date->setTimezone($tz);
		$UKdate = $date->format('Y-m-d H:i:s');
		
		
		return $UKdate;
	}
}
?>