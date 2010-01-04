<?php

/*
**	insert_person
**
**	This function handles inserting a new entry into the database
*/

function default_view() {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";
	
	$output = '';

	$output .=  	'<div class="wrap"><h2>Manage People</h2>
				<table width="100%" border="0">
					<tr>
						<td width="25%">
							<form method="post" action="">
							<input type="hidden" name="action" value="add" />';
							
							// add a nonce for security
							if( function_exists('wp_nonce_field') )
								wp_nonce_field('people-manager-action-add-button-form');

							$output .=  '
							<select name="type">
								<option value="student">Student or Alum</option>
								<option value="faculty">Faculty</option>
								<option value="staff">Staff</option>
							</select>
							
							<input type="submit" class="button-primary" value="Add" />				
							</form>
						</td>
						
						<td width="50%">
							<form method="post" action="">';
							
							// add a nonce for security
							if( function_exists('wp_nonce_field') )
								wp_nonce_field('people-manager-action-search-button-form');

							$output .=  '<input length="90" type="text" name="searchFor" value="" />
							<input type="hidden" name="action" value="search" />
							<input type="submit" class="button-primary" value="Search" />				
							</form>
						</td>
					</tr>
				</table>';
	
	if($wpdb->blogid != 45) {
		$output .=  "<br /><br /><h2>People Waiting To Be Approved</h2>";
		$output .=  "<p>Only fifty (50) people are shown. Approve or delete these and more will be shown.</p>";
	
		$sql = "SELECT * FROM " . $table_name . " WHERE approved = 'N' ORDER BY gradYear DESC, lname ASC LIMIT 50";	
		$approvals = $wpdb->get_results($sql);
	
		if($approvals) {
			$output .=  "<table width='100%' border='0'><tr>";
			$output .=  "<th align='left'>First Name</th>";
			$output .=  "<th align='left'>Last Name</th>";
			$output .=  "<th align='left'>Type</th>";
			$output .=  "<th align='left'>Major</th>";
			$output .=  "<th align='left'>Graduation Year</th>";
			$output .=  "<th>&nbsp;</th>";
			$output .=  "<th>&nbsp;</th>";
			$output .=  "</tr><tr><td colspan='7'><hr></td></tr>";
	
			foreach($approvals as $approve) {
				$output .=  "<tr>";
					$output .=  "<td>" . stripslashes($approve->fname) . "</td>";
					$output .=  "<td>" . stripslashes($approve->lname) . "</td>";
					$output .=  "<td>" . stripslashes($approve->type) . "</td>";
					$output .=  "<td>" . stripslashes($approve->major) . "</td>";
					$output .=  "<td>" . stripslashes($approve->gradYear) . "</td>";
					
					$output .=  '	<td>
							<form method="post" action="">';
								
								// add a nonce for security
								if( function_exists('wp_nonce_field') )
									wp_nonce_field('people-manager-action-approve-button-form');
	
								$output .=  '<input type="hidden" name="idNum" value="' . $approve->id . '">
								<input type="hidden" name="action" value="approve" />
								<input type="submit" class="button-primary" value="Approve" />				
								</form>
							</td>';
							
					$output .=  '	<td>
							<form method="post" action="">';
								
								// add a nonce for security
								if( function_exists('wp_nonce_field') )
									wp_nonce_field('people-manager-action-delete-button-form');
	
								$output .=  '<input type="hidden" name="idNum" value="' . $approve->id . '">
								<input type="hidden" name="action" value="delete" />
								<input type="submit" class="button-primary" value="Delete" />				
								</form>
							</td>';
							
				$output .=  "</tr>";
			}
			
			$output .=  '</table>';
		}
		
		else {
			$output .=  "<p>No approvals required</p>";
		}
	}

	$sql = "SELECT * FROM " . $table_name . " WHERE approved = 'Y' ORDER BY gradYear DESC, lname ASC LIMIT 50";
	$results = $wpdb->get_results($sql);
	
	$output .=  "<br /><br /><h2>People In Your Database</h2>";
	$output .=  "<p>Only fifty (50) people are shown. Use the search bar to find more.</p>";
	
	if($results) {
		if($wpdb->blogid == "45") {
			$output .=  "<table width='100%' border='0'><tr>";
			$output .=  "<th align='left'>First Name</th>";
			$output .=  "<th align='left'>Last Name</th>";
			$output .=  "<th align='left'>Telephone</th>";
			$output .=  "<th align='left'>Location</th>";
			$output .=  "<th>&nbsp;</th>";
			$output .=  "</tr><tr><td colspan='5'><hr></td></tr>";
		
			foreach($results as $person) {
				$output .=  "<tr>";
					$output .=  "<td>" . stripslashes($person->fname) . "</td>";
					$output .=  "<td>" . stripslashes($person->lname) . "</td>";
					$output .=  "<td>" . stripslashes($person->telephone) . "</td>";
					$output .=  "<td>" . stripslashes($person->office) . "</td>";
					$output .=  '<td>';
						$output .=  '<form method="post" action="">';
								
							// add a nonce for security
							if( function_exists('wp_nonce_field') )
								wp_nonce_field('people-manager-action-edit');
							
							if( $person->type == "Faculty" )
								$editType = 'faculty';
							else if( $person->type == "staff" )
								$editType = 'staff';
							else
								$editType = 'student';
								
							$output .=  '<input type="hidden" name="person-type" value="' . $editType . '" />';
							$output .=  '<input type="hidden" name="person-id" value="' . $person->id . '" />';
							$output .=  '<input type="hidden" name="action" value="edit" />';
							$output .=  '<input type="submit" class="button-primary" value="Edit" />';			
						$output .=  '</form>';
					$output .=  '</td>';
				$output .=  "</tr>";
			}
			
			$output .=  '</table>';		
		}
		
		else {
			$output .=  "<table width='100%' border='0'><tr>";
			$output .=  "<th align='left'>First Name</th>";
			$output .=  "<th align='left'>Last Name</th>";
			$output .=  "<th align='left'>Type</th>";
			$output .=  "<th align='left'>Major</th>";
			$output .=  "<th align='left'>Graduation Year</th>";
			$output .=  "<th>&nbsp;</th>";
			$output .=  "</tr><tr><td colspan='6'><hr></td></tr>";
		
			foreach($results as $person) {
				$output .=  "<tr>";
					$output .=  "<td>" . stripslashes($person->fname) . "</td>";
					$output .=  "<td>" . stripslashes($person->lname) . "</td>";
					$output .=  "<td>" . stripslashes($person->type) . "</td>";
					$output .=  "<td>" . stripslashes($person->major) . "</td>";
					$output .=  "<td>" . stripslashes($person->gradYear) . "</td>";
					$output .=  '<td>';
						$output .=  '<form method="post" action="">';
								
							// add a nonce for security
							if( function_exists('wp_nonce_field') )
								wp_nonce_field('people-manager-action-edit');
							
							if( $person->type == "Faculty" )
								$editType = 'faculty';
							else if( $person->type == "staff" )
								$editType = 'staff';
							else
								$editType = 'student';
								
							$output .=  '<input type="hidden" name="person-type" value="' . $editType . '" />';
							$output .=  '<input type="hidden" name="person-id" value="' . $person->id . '" />';
							$output .=  '<input type="hidden" name="action" value="edit" />';
							$output .=  '<input type="submit" class="button-primary" value="Edit" />';			
						$output .=  '</form>';
					$output .=  '</td>';
				$output .=  "</tr>";
			}
			
			$output .=  '</table>';
		}
	}
	
	else {
		$output .=  "<p>There are no people in your database.</p>";
	}
	
	$output .=  '</div>';
	
	return $output;
}

function display_add_form($type) {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";
	
	$output = '';
	
	if($type == 'faculty') {
		$output .= '<div class="wrap"><h2>Add Faculty</h2>';
								
		$output .= '<table border="0" width="100%">
				<form method="post" action="">
				<input type="hidden" name="action" value="insert">
				<input type="hidden" name="type" value="faculty">
				<input type="hidden" name="visible" value="Y"';

		// add a nonce for security
		if( function_exists('wp_nonce_field') )
			wp_nonce_field('people-manager-action-insert');
				
		$output .= '<tr>
				<td width="40%" align="right"><label for="fname">First Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="fname" name="fname" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="lname">Last Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="lname" name="lname" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="image">A URL To Your Photograph*:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="image" name="image" type="text" length="75" value="" /></td>
			</tr>';
			
		$output .= '<tr>
				<td width="40%" align="right"><label for="email">Your E-Mail Address:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="email" name="email" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="url">Your Web site URL:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="url" name="url" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="telephone">Office Telephone Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="telephone" name="telephone" type="text" length="75" value="" /></td>
			</tr>';
			
		$output .= '<tr>
				<td width="40%" align="right"><label for="office">Office Building and Room Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="office" name="office" type="text" length="75" value="" /></td>
			</tr>';
			
		$output .= '	<tr>
					<td width="40%" align="right"><label for="major">Your Degrees (Limit 75 Words):</label></td>
					<td>&nbsp;</td>
					<td align="left"><textarea id="major" name="major" rows="10" cols="25"></textarea></td>
				</tr>';
			
		$output .= '<tr>
				<td width="40%" align="right"><label for="keywords">Keywords About You and Your Research (separate each keyword with a comma):</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="keywords" name="keywords" type="text" length="75" value="" /></td>
			</tr>';
							
		$output .= '<tr>
				<td width="40%" align="right"><label for="about">Write A Paragraph About Yourself (Limit 150 Words):</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="about" name="about" rows="10" cols="25"></textarea></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="research">Write A Paragraph About Your Research Areas (Limit 150 Words):</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="research" name="research" rows="10" cols="25"></textarea></td>
			</tr>';
			
		$output .= '</table><p>*Please upload your photograph to Flickr or another image storage Web site. You will be given a URL from that Web site to use here.</p>';
		
		$output .= '<input type="submit" class="button-primary" value="Submit Profile" /></form>';
	}
	
	else if( $type == 'student' ) {
		$output .= '<div class="wrap"><h2>Add Person</h2><table border="0" width="100%">
				<form method="post" action="">';
								
				// add a nonce for security
				if( function_exists('wp_nonce_field') )
					wp_nonce_field('people-manager-action-insert');
		
				$output .= '<input type="hidden" name="action" value="insert">';
		
		$output .= '<tr>
				<td width="20%" align="right"><label for="fname">First Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="fname" name="fname" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="20%" align="right"><label for="lname">Last Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="lname" name="lname" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="20%" align="right"><label for="type">Are you:</label></td>
				<td>&nbsp;</td>
				<td align="left">
					<select id="type" name="type">
						<option value="Alum">An Alumnus</option>
						<option value="Current Student">An Undergraduate Student</option>
						<option value="Graduate Student">A Graduate Student</option>
					</select>
				</td>
			</tr>';

		$output .= '<tr>
				<td width="20%" align="right"><label for="major">Your Major:</label></td>
				<td>&nbsp;</td>
				<td align="left">
			';
			
		$peopleManagerOpts = unserialize(get_option('people-manager-options'));
		$allowedMajors = $peopleManagerOpts['allowedMajors'];
		
		if(empty($allowedMajors)) {
			$output .= '<input id="major" name="major" type="text" length="75" value="" />';
		}
		
		else {
			$temp = explode(',', $allowedMajors);
			
			$output .= '<select id="major" name="major">';
			$output .= '<option value=""></option>';
			
			for($x = 0; $x < count($temp); $x++) {
				$output .= '<option value="' . trim($temp[$x]) . '">' . trim($temp[$x]) . '</option>';
			}
			
			$output .= '</select>';
		}
		
		$output .= '</td></tr>';
			
		$output .= '<tr>
				<td width="20%" align="right"><label for="gradYear">Your Graduation Year:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="gradYear" name="gradYear" type="text" maxlength="4" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="20%" align="right"><label for="email">Your E-Mail Address:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="email" name="email" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="20%" align="right"><label for="url">Your Web site URL:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="url" name="url" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="20%" align="right"><label for="image">A URL To Your Photograph*:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="image" name="image" type="text" length="75" value="" /></td>
			</tr>';
			
		$output .= '<tr>
				<td width="20%" align="right"><label for="about">Anything else you would like to share with other alumni?</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="about" name="about" rows="10" cols="50"></textarea></td>
			</tr>';
			
		$output .= '<tr>
				<td width="20%" align="right"><label for="visible">Would you like to share your information with other alumni?</label></td>
				<td>&nbsp;</td>
				<td align="left">
					<select id="visible" name="visible">
						<option value="Y" SELECTED>Yes</option>
						<option value="N">No</option>
					</select>
				</td>
			</tr>';

		$output .= '</table><p>*Please upload your photograph to Flickr or another image storage Web site. You will be given a URL from that Web site to use here.</p>';
		
		$output .= '<input type="submit" class="button-primary" value="Add Student/Alum" /></form></div>';	
	}
	
	else if( $type == 'staff' ) {
		$output .= '<div class="wrap"><h2>Add Staff</h2>';
								
		$output .= '<table border="0" width="100%">
				<form method="post" action="">
				<input type="hidden" name="action" value="insert">
				<input type="hidden" name="type" value="staff">
				<input type="hidden" name="visible" value="Y"';

		// add a nonce for security
		if( function_exists('wp_nonce_field') )
			wp_nonce_field('people-manager-action-insert');
				
		$output .= '<tr>
				<td width="40%" align="right"><label for="fname">First Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="fname" name="fname" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="lname">Last Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="lname" name="lname" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="image">A URL To Your Photograph*:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="image" name="image" type="text" length="75" value="" /></td>
			</tr>';
			
		$output .= '<tr>
				<td width="40%" align="right"><label for="email">Your E-Mail Address:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="email" name="email" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="url">Your Web site URL:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="url" name="url" type="text" length="75" value="" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="telephone">Office Telephone Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="telephone" name="telephone" type="text" length="75" value="" /></td>
			</tr>';
			
		$output .= '<tr>
				<td width="40%" align="right"><label for="office">Office Building and Room Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="office" name="office" type="text" length="75" value="" /></td>
			</tr>';
			
		$output .= '<tr>
				<td width="40%" align="right"><label for="about">Write A Paragraph About Yourself (Limit 150 Words):</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="about" name="about" rows="10" cols="25"></textarea></td>
			</tr>';

		$output .= '</table><p>*Please upload your photograph to Flickr or another image storage Web site. You will be given a URL from that Web site to use here.</p>';
		
		$output .= '<input type="submit" class="button-primary" value="Submit Profile" /></form>';
	}
	
	else {
		$output = "You selected something that doesn't exist. Please contact Tim at the Web Office.";
	}
	
	return $output;
}

function insert_person($array_about_person) {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";
	
	if(!$wpdb->insert($table_name, $array_about_person)) // make sure we inserted into the database.
		return "The person was not inserted in the database. Please contact your support professional.";
	else
		return "The person was successfully inserted in the database.";	
}

function delete_person($id) {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";
	
	$sql = "DELETE FROM " . $table_name . " WHERE id = " . $id;
	if(!$wpdb->query($sql))
		return "Could not delete the person selected.";
	else
		return "Selected person has been deleted from the database.";
}

function update_person($array_about_person, $the_id) {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";
	
	if(!$wpdb->update($table_name, $array_about_person, $the_id))
		return "The person was not updated in the database. Please contact your support professional.";
	else
		return "The person was successfully update in the database.";	
}

function edit_person($personID, $personType) {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";
	
	$sql = "SELECT * FROM " . $table_name . " WHERE id = " . $personID;
	$result = $wpdb->get_row($sql);
	
	$output = '';
	
	if( $personType == "faculty") {
		$output .= '<div class="wrap"><h2>Edit Faculty</h2>';
								
		// add a nonce for security
		if( function_exists('wp_nonce_field') )
			wp_nonce_field('people-manager-action-update');
				
		$output .= '<table border="0" width="100%">
				<form method="post" action="">
				<input type="hidden" name="action" value="update">
				<input type="hidden" name="type" value="faculty">
				<input type="hidden" name="visible" value="Y" />
				<input type="hidden" name="personID" value="' . $personID . '" />';
				
		$output .= '<tr>
				<td width="40%" align="right"><label for="fname">First Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="fname" name="fname" type="text" length="75" value="' . $result->fname . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="lname">Last Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="lname" name="lname" type="text" length="75" value="' . $result->lname . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="image">A URL To Your Photograph*:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="image" name="image" type="text" length="75" value="' . $result->image . '" /></td>
			</tr>';
			
		$output .= '<tr>
				<td width="40%" align="right"><label for="email">Your E-Mail Address:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="email" name="email" type="text" length="75" value="' . $result->email . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="url">Your Web site URL:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="url" name="url" type="text" length="75" value="' . $result->url . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="telephone">Office Telephone Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="telephone" name="telephone" type="text" length="75" value="' . $result->telephone . '" /></td>
			</tr>';
			
		$output .= '<tr>
				<td width="40%" align="right"><label for="office">Office Building and Room Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="office" name="office" type="text" length="75" value="' . $result->office . '" /></td>
			</tr>';
			
		$output .= '	<tr>
					<td width="40%" align="right"><label for="major">Your Degrees (Limit 75 Words):</label></td>
					<td>&nbsp;</td>
					<td align="left"><textarea id="major" name="major" rows="10" cols="25">' . $result->major . '</textarea></td>
				</tr>';
			
		$output .= '<tr>
				<td width="40%" align="right"><label for="keywords">Keywords About You and Your Research (separate each keyword with a comma):</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="keywords" name="keywords" type="text" length="75" value="' . $result->keywords . '" /></td>
			</tr>';
							
		$output .= '<tr>
				<td width="40%" align="right"><label for="about">Write A Paragraph About Yourself (Limit 150 Words):</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="about" name="about" rows="10" cols="25">' . $result->about . '</textarea></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="research">Write A Paragraph About Your Research Areas (Limit 150 Words):</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="research" name="research" rows="10" cols="25">' . $result->research . '</textarea></td>
			</tr>';
			
		$output .= '</table><p>*Please upload your photograph to Flickr or another image storage Web site. You will be given a URL from that Web site to use here.</p>';
		
		$output .= '<input type="submit" class="button-primary" value="Submit Edits" /></form>';
		
		$output .= '<br /><br /><form method="post" action="">
					<input type="hidden" name="action" value="delete">
					<input type="hidden" name="personID" value="' . $personID . '" />
					<input type="submit" class="button-primary" value="Delete Person" /></form></div>';
	}
	
	else if( $personType == "student" ) {	
		$output .= '<div class="wrap"><h2>Add Person</h2><table border="0" width="100%">
				<form method="post" action="">';
								
		// add a nonce for security
		if( function_exists('wp_nonce_field') )
			wp_nonce_field('people-manager-action-update');

		$output .= '<input type="hidden" name="action" value="update">
					<input type="hidden" name="personID" value="' . $personID . '" />';
		
		$output .= '<tr>
				<td width="20%" align="right"><label for="fname">First Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="fname" name="fname" type="text" length="75" value="' . $result->fname . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="20%" align="right"><label for="lname">Last Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="lname" name="lname" type="text" length="75" value="' . $result->lname . '" /></td>
			</tr>';
		
		$aSelect = '';
		$uSelect = '';
		$gSelect = '';
		
		if($result->type == "Alum") {
			$aSelect = "SELECTED";
		}
		
		else if($result->type == "Undergraduate Student") {
			$uSelect = "SELECTED";
		}
		
		else if($result->type == "Graduate Student") {
			$gSelect = "SELECTED";
		}
		
		$output .= '<tr>
				<td width="20%" align="right"><label for="type">Are you:</label></td>
				<td>&nbsp;</td>
				<td align="left">
					<select id="type" name="type">
						<option ' . $aSelect . ' value="Alum">An Alumnus</option>
						<option ' . $uSelect . ' value="Undergraduate Student">An Undergraduate Student</option>
						<option ' . $gSelect . ' value="Graduate Student">A Graduate Student</option>
					</select>
				</td>
			</tr>';

		$output .= '<tr>
				<td width="20%" align="right"><label for="major">Your Major:</label></td>
				<td>&nbsp;</td>
				<td align="left">
			';
			
		$peopleManagerOpts = unserialize(get_option('people-manager-options'));
		$allowedMajors = $peopleManagerOpts['allowedMajors'];
		
		if(empty($allowedMajors)) {
			$output .= '<input id="major" name="major" type="text" length="75" value="' . $result->major . '" />';
		}
		
		else {
			$temp = explode(',', $allowedMajors);
			
			$output .= '<select id="major" name="major">';
			$output .= '<option value=""></option>';
			
			for($x = 0; $x < count($temp); $x++) {
				if($result->major == trim($temp[$x]))
					$output .= '<option SELECTED value="' . trim($temp[$x]) . '">' . trim($temp[$x]) . '</option>';
				else
					$output .= '<option value="' . trim($temp[$x]) . '">' . trim($temp[$x]) . '</option>';
			}
			
			$output .= '</select>';
		}
		
		$output .= '</td></tr>';
			
		$output .= '<tr>
				<td width="20%" align="right"><label for="gradYear">Your Graduation Year:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="gradYear" name="gradYear" type="text" maxlength="4" length="75" value="' . $result->gradYear . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="20%" align="right"><label for="email">Your E-Mail Address:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="email" name="email" type="text" length="75" value="' . $result->email . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="20%" align="right"><label for="url">Your Web site URL:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="url" name="url" type="text" length="75" value="' . $result->url . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="20%" align="right"><label for="image">A URL To Your Photograph*:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="image" name="image" type="text" length="75" value="' . $result->image . '" /></td>
			</tr>';
			
		$output .= '<tr>
				<td width="20%" align="right"><label for="about">Anything else you would like to share with other alumni?</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="about" name="about" rows="10" cols="50">' . $result->about . '</textarea></td>
			</tr>';
		
		
		$output .= '<tr>
				<td width="20%" align="right"><label for="visible">Would you like to share your information with other alumni?</label></td>
				<td>&nbsp;</td>
				<td align="left">
					<select id="visible" name="visible">';
					
					if( $result->visible == "Y" ) {
						$output .= '<option value="Y" SELECTED>Yes</option>';
						$output .= '<option value="N">No</option>';
					}
					
					else if( $result->visible == "N" ) {
						$output .= '<option value="Y">Yes</option>';
						$output .= '<option value="N" SELECTED>No</option>';						
					}
					
					$output .= '</select>
				</td>
			</tr>';

		$output .= '</table><p>*Please upload your photograph to Flickr or another image storage Web site. You will be given a URL from that Web site to use here.</p>';
		
		$output .= '<input type="submit" class="button-primary" value="Submit Edits" /></form>';
		
		$output .= '<br /><br /><form method="post" action="">
					<input type="hidden" name="action" value="delete">
					<input type="hidden" name="personID" value="' . $personID . '" />
					<input type="submit" class="button-primary" value="Delete Person" /></form></div>';
	}
	
	else if( $personType == "staff" ) { 
		$output .= '<div class="wrap"><h2>Edit Staff</h2>';
								
		// add a nonce for security
		if( function_exists('wp_nonce_field') )
			wp_nonce_field('people-manager-action-update');
				
		$output .= '<table border="0" width="100%">
				<form method="post" action="">
				<input type="hidden" name="action" value="update">
				<input type="hidden" name="type" value="staff">
				<input type="hidden" name="visible" value="Y" />
				<input type="hidden" name="personID" value="' . $personID . '" />';
				
		$output .= '<tr>
				<td width="40%" align="right"><label for="fname">First Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="fname" name="fname" type="text" length="75" value="' . $result->fname . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="lname">Last Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="lname" name="lname" type="text" length="75" value="' . $result->lname . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="image">A URL To Your Photograph*:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="image" name="image" type="text" length="75" value="' . $result->image . '" /></td>
			</tr>';
			
		$output .= '<tr>
				<td width="40%" align="right"><label for="email">Your E-Mail Address:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="email" name="email" type="text" length="75" value="' . $result->email . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="url">Your Web site URL:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="url" name="url" type="text" length="75" value="' . $result->url . '" /></td>
			</tr>';

		$output .= '<tr>
				<td width="40%" align="right"><label for="telephone">Office Telephone Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="telephone" name="telephone" type="text" length="75" value="' . $result->telephone . '" /></td>
			</tr>';
			
		$output .= '<tr>
				<td width="40%" align="right"><label for="office">Office Building and Room Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="office" name="office" type="text" length="75" value="' . $result->office . '" /></td>
			</tr>';
							
		$output .= '<tr>
				<td width="40%" align="right"><label for="about">Write A Paragraph About Yourself (Limit 150 Words):</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="about" name="about" rows="10" cols="25">' . $result->about . '</textarea></td>
			</tr>';
			
		$output .= '</table><p>*Please upload your photograph to Flickr or another image storage Web site. You will be given a URL from that Web site to use here.</p>';
		
		$output .= '<input type="submit" class="button-primary" value="Submit Edits" /></form>';
		
		$output .= '<br /><br /><form method="post" action="">
					<input type="hidden" name="action" value="delete">
					<input type="hidden" name="personID" value="' . $personID . '" />
					<input type="submit" class="button-primary" value="Delete Person" /></form></div>';
	}
	
	return $output;
}

function perform_search($searchFor) {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";
	
	$output = '';
	
	$sql = "SELECT * FROM " . $table_name . " WHERE
			LOCATE('" . $searchFor . "', fname) != 0 OR
			LOCATE('" . $searchFor . "', lname) != 0 OR
			LOCATE('" . $searchFor . "', major) != 0 OR
			LOCATE('" . $searchFor . "', gradYear) != 0
			ORDER BY lname DESC";
			
	$results = $wpdb->get_results($sql);		
	
	if($wpdb->blogid == 45) {
		if($results) {
			$output .= "<h2>People Matching Search Term(s): " . $searchFor . "</h2>";
	
			$output .= "<table width='100%' border='0'><tr>";
			$output .= "<th align='left'>First Name</th>";
			$output .= "<th align='left'>Last Name</th>";
			$output .= "<th align='left'>Telephone</th>";
			$output .= "<th align='left'>Location</th>";
			$output .= "<th align='left'>&nbsp;</th>";
			$output .= "</tr><tr><td colspan='5'><hr></td></tr>";
		
			foreach($results as $person) {
				$output .= "<tr>";
					$output .= "<td>" . stripslashes($person->fname) . "</td>";
					$output .= "<td>" . stripslashes($person->lname) . "</td>";
					$output .= "<td>" . stripslashes($person->telephone) . "</td>";
					$output .= "<td>" . stripslashes($person->office) . "</td>";
					$output .= "<td>";
						$output .=  '<form method="post" action="">';
								
							// add a nonce for security
							if( function_exists('wp_nonce_field') )
								wp_nonce_field('people-manager-action-edit');
							
							if( $person->type == "Faculty" )
								$editType = 'faculty';
							else if( $person->type == "staff" )
								$editType = 'staff';
							else
								$editType = 'student';
								
							$output .=  '<input type="hidden" name="person-type" value="' . $editType . '" />';
							$output .=  '<input type="hidden" name="person-id" value="' . $person->id . '" />';
							$output .=  '<input type="hidden" name="action" value="edit" />';
							$output .=  '<input type="submit" class="button-primary" value="Edit" />';			
						$output .=  '</form>';					
					$output .= "</td>";
						
				$output .= "</tr>";
			}
			
			$output .= '</table>';
		}
		
		else {
			$output .= "<h2>Search Results</h2>No people matched your query.<br />";
		}	
	}
	
	else {
		if($results) {
			$output .= "<h2>People Matching Search Term(s): " . $searchFor . "</h2>";
	
			$output .= "<table width='100%' border='0'><tr>";
			$output .= "<th align='left'>First Name</th>";
			$output .= "<th align='left'>Last Name</th>";
			$output .= "<th align='left'>Type</th>";
			$output .= "<th align='left'>Major</th>";
			$output .= "<th align='left'>Graduation Year</th>";
			$output .= "</tr><tr><td colspan='5'><hr></td></tr>";
		
			foreach($results as $person) {
				$output .= "<tr>";
					$output .= "<td>" . stripslashes($person->fname) . "</td>";
					$output .= "<td>" . stripslashes($person->lname) . "</td>";
					$output .= "<td>" . stripslashes($person->type) . "</td>";
					$output .= "<td>" . stripslashes($person->major) . "</td>";
					$output .= "<td>" . stripslashes($person->gradYear) . "</td>";
				$output .= "</tr>";
			}
			
			$output .= '</table>';
		}
		
		else {
			$output .= "<h2>Search Results</h2>No people matched your query.<br />";
		}
	}
	
	return $output;
}
?>