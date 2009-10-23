<?php
/*
Plugin Name: People Manager
Plugin URI: http://feyreal.com/wordpress/wordpress-plugins/people-manager/
Description: This plugin provides functionality for listing people associated with your Web site.
Author: Tim Moore (support@feyreal.com)
Version: 1.0
Author URI: http://feyreal.com
*/

/*
**	display_people_to_world SHORTCODE
**
**	Creates a shortcode and some parameters to view the data on a word-visible page
**	Available: [do_display_people_to_world view='alumni'] --> change alumni to different types
*/

function display_people_to_world($attr) {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";
	
	$view = $_POST['view'];
	
	if(empty($view))
		$view = $_GET['view'];
		
	if($view == "single") {
		$id = $_POST['id'];
		
		if(empty($id))
			$id = $_GET['id'];
			
		$sql = "SELECT * FROM " . $table_name . " WHERE id = " . $id;
				
		$result = $wpdb->get_row($sql);
		
		if(!empty($result->image)) {
			if( $result->type == "faculty" ) {
				echo '	<p><img valign="top" align="right" src="' . $result->image . '" alt="Image of ' . $result->fname . ' ' . $result->lname . '">
						<strong><a href="' . $result->url . '" target="new">' . $result->fname . ' ' . $result->lname . '</a></strong> (' . $result->email . ')<br /><br />
						<strong>Degree(s):</strong> ' . $result->major . '<br /><br />
						<strong>Office:</strong> ' . $result->office . '</p>
						<strong>Telephone:</strong> ' . $result->telephone . '</p>
						<p><strong>About:</strong> ' . $result->about . '</p>
						<p><strong>Resarch:</strong> ' . $result->research . '</p>
						<p><strong>Keywords:</strong> ' . $result->keywords . '</p>
						';
			}
			
			else {
				echo '	<p><img valign="top" align="right" src="' . $result->image . '" alt="Image of ' . $result->fname . ' ' . $result->lname . '">
						<strong><a href="' . $result->url . '" target="new">' . $result->fname . ' ' . $result->lname . '</a></strong> (' . $result->email . ')<br /><br />
						<strong>Major:</strong> ' . $result->major . '<br /><br />
						<strong>Grad. Year:</strong> ' . $result->gradYear . '<br /><br />
						' . $result->about . '</p>';			
			}
		}
		
		else {
			if( $result->type == "faculty" ) {
				echo '	<table border="0" width="100%">
							<tr>
								<td><a href="' . $result->url . '" target="new">' . $result->fname . ' ' . $result->lname . '</a> (' . $result->email . ')</td>
							</tr>
							
							<tr>
								<td><strong>Degree(s):</strong> ' . $result->major . '</td>
							</tr>

							<tr>
								<td><strong>Office:</strong> ' . $result->office . '</td>
							</tr>
							
							<tr>
								<td><strong>Telephone:</strong> ' . $result->telephone . '</td>
							</tr>
							
							<tr>
								<td><strong>About:</strong> ' . $result->about . '</td>
							</tr>
							
							<tr>
								<td><strong>Research:</strong> ' . $result->research . '</td>
							</tr>
							
							<tr>
								<td><strong>Keywords:</strong> ' . $result->keywords . '</td>
							</tr>
						</table>';			
			}
			
			else {
				echo '	<table border="0" width="100%">
							<tr>
								<td><a href="' . $result->url . '" target="new">' . $result->fname . ' ' . $result->lname . '</a> (' . $result->email . ')</td>
							</tr>
							
							<tr>
								<td>' . $result->major . ' ' . $result->gradYear . '</td>
							</tr>
							
							<tr>
								<td>' . $result->about . '</td>
							</tr>
						</table>';	
			}
		}
		
		echo '<p><a href="?view=directory"><-- Back to People Directory</a></p><br /><br /><br /><br />';
	}
	
	else {
		// grab the specific attributes we are interested in
		$queryType = strtoupper($attr['querytype']);
		
		$type = $attr['type'];
		
		if(!empty($type)) {
			$tempType = explode(',', $type);
			
			$newType = "(";
			
			for($x = 0; $x < count($tempType); $x++) {
				if( $x > 0 && $x != count( $tempType ) ) {
					$newType .= "OR";
				}
				
				$newType .= ' type = "' . trim( $tempType[ $x ] ) . '" ';
			}
			
			$newType .= ")";
			
			$type = $newType;
		}
		
		$sort = $attr['sort'];
		$sortDirection = strtoupper($attr['sortdirection']);
		$major = $attr['major'];
		$gradYear = $attr['gradyear'];
		$tempColumns = $attr['columnstodisplay'];
		
		if(empty($tempColumns)) {
			$columns[0]['display_name'] = 'First Name';
			$columns[0]['field'] = 'fname';
			$columns[1]['display_name'] = 'Last Name';
			$columns[1]['field'] = 'lname';
			$columns[2]['display_name'] = 'Graduation Year';
			$columns[2]['field'] = 'gradYear';
			$columns[3]['display_name'] = 'Major';
			$columns[3]['field'] = 'major';
			$columns[4]['display_name'] = 'Type';
			$columns[4]['field'] = 'type';
		}
		
		else {
			$temp = explode('|', $tempColumns);
			
			for($x = 0; $x < count($temp); $x++) {
				$column = explode(',', $temp[$x]);
				$columns[$x]['display_name'] = $column[0];
				$columns[$x]['field'] = $column[1];
			}
		}
		
		if(empty($queryType))
			$queryType = "OR";
			
		// build the SQL query based on the above attributes
		$sql = "SELECT * FROM " . $table_name;
		$args = array();
		
		if(!empty($type))
			$args[] = $type;
		
		if(!empty($major))
			$args[] = "LOCATE('" . $major . "', major) != 0";
		
		if(!empty($gradYear))
			$args[] = "gradYear = '" . $gradYear . "'";
		
		for($x = 0; $x < count($args); $x++) {
			if($x > 0)
				$sql .= " AND ";
			else
				$sql .= " WHERE ";
	
			$sql .= $args[$x];
		}
		
		if(!empty($sort))
			$sql .= " ORDER BY " . $sort . " " . $sortDirection;
				
		$results = $wpdb->get_results($sql);
		
		if($results) {
			echo "<table border='0' width='100%'><tr>";
			
			for($x = 0; $x < 5; $x++) {
				echo '<th align="left">' . $columns[$x]['display_name'] . '</th>';
			}
	
			echo "</tr>";
					
			foreach($results as $result) {
				echo "<tr>";
					echo "<td><a href='?view=single&id=" . $result->id . "'>" . $result->$columns[0]['field'] . "</a></td>";
					echo "<td>" . $result->$columns[1]['field'] . "</td>";
					echo "<td>" . $result->$columns[2]['field'] . "</td>";
					echo "<td>" . $result->$columns[3]['field'] . "</td>";
					echo "<td>" . $result->$columns[4]['field'] . "</td>";
				echo "</tr>";
			}
			
			echo "</table>";
		}
		
		else {
			echo "No people to display.";
		}
	}
}

add_shortcode('do_display_people_to_world', 'display_people_to_world');

/*
**	add_faculty_public_form SHORTCODE
**
**	Creates a shortcode that display a form for people to add new people
**	Available: [do_add_person_public_form]
*/

function add_faculty_public_form() {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";
	
	// grab the variable for what action we are performing
	// make sure to get it even if we decide to POST it or GET it
	$action = $_POST['action'];

	if(empty($action)) {
		$action = $_GET['action'];
	}
	
	if($action == 'insert') {
	
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$major = $_POST['major'];
		$email = strtolower($_POST['email']);
		$url = strtolower($_POST['url']);
		$about = $_POST['about'];
		$image = strtolower($_POST['image']);
		$type = $_POST['type'];
		$visible = $_POST['visible'];		
		$telephone = $_POST['telephone'];
		$office = $_POST['office'];
		$keywords = $_POST['keywords'];
		$research = $_POST['research'];
		
		$urlCheck = substr($url, 0, 7);
		if($urlCheck != 'http://' && !empty($url))
			$url = 'http://' . $url;
		
		$imageCheck = substr($image, 0, 7);
		if($imageCheck != 'http://' && !empty($image))
			$image = 'http://' . $image;
		
		$about_person = array(	'fname' => $fname,
								'lname' => $lname,
								'major' => $major,
								'email' => $email,
								'url' => $url,
								'about' => $about,
								'image' => $image,
								'type' => $type,
								'visible' => $visible,
								'telephone' => $telephone,
								'office' => $office,
								'keywords' => $keywords,
								'research' => $research,
								'updated' => time(),
								'approved' => 'N');

		$insert_person = insert_person($about_person);
		
		echo $insert_person;	
	}
	
	else {
		echo '<table border="0" width="100%">
				<form method="post" action="">
				<input type="hidden" name="action" value="insert">
				<input type="hidden" name="type" value="faculty">
				<input type="hidden" name="visible" value="Y"';
				
		echo '<tr>
				<td width="40%" align="right"><label for="fname">First Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="fname" name="fname" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="lname">Last Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="lname" name="lname" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="image">A URL To Your Photograph*:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="image" name="image" type="text" length="75" value="" /></td>
			</tr>';
			
		echo '<tr>
				<td width="40%" align="right"><label for="email">Your E-Mail Address:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="email" name="email" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="url">Your Web site URL:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="url" name="url" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="telephone">Office Telephone Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="telephone" name="telephone" type="text" length="75" value="" /></td>
			</tr>';
			
		echo '<tr>
				<td width="40%" align="right"><label for="office">Office Building and Room Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="office" name="office" type="text" length="75" value="" /></td>
			</tr>';
			
		echo '	<tr>
					<td width="40%" align="right"><label for="major">Your Degrees (Limit 75 Words):</label></td>
					<td>&nbsp;</td>
					<td align="left"><textarea id="major" name="major" rows="10" cols="25"></textarea></td>
				</tr>';
			
		echo '<tr>
				<td width="40%" align="right"><label for="keywords">Keywords About You and Your Research (separate each keyword with a comma):</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="keywords" name="keywords" type="text" length="75" value="" /></td>
			</tr>';
							
		echo '<tr>
				<td width="40%" align="right"><label for="about">Write A Paragraph About Yourself (Limit 150 Words):</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="about" name="about" rows="10" cols="25"></textarea></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="research">Write A Paragraph About Your Research Areas (Limit 150 Words):</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="research" name="research" rows="10" cols="25"></textarea></td>
			</tr>';
			
		echo '</table><p>*Please upload your photograph to Flickr or another image storage Web site. You will be given a URL from that Web site to use here.</p>';
		
		echo '<input type="submit" class="button-primary" value="Submit Profile" /></form>';	
	}
}

add_shortcode('do_add_faculty_public_form', 'add_faculty_public_form');

/*
**	add_student_public_form SHORTCODE
**
**	Creates a shortcode that display a form for people to add new people
**	Available: [do_add_person_public_form]
*/

function add_student_public_form() {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";
	
	// grab the variable for what action we are performing
	// make sure to get it even if we decide to POST it or GET it
	$action = $_POST['action'];

	if(empty($action)) {
		$action = $_GET['action'];
	}
	
	if($action == 'insert') {
	
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$major = $_POST['major'];
		$gradYear = $_POST['gradYear'];
		$email = strtolower($_POST['email']);
		$url = strtolower($_POST['url']);
		$about = $_POST['about'];
		$image = strtolower($_POST['image']);
		$type = $_POST['type'];
		$visible = $_POST['visible'];
		
		$urlCheck = substr($url, 0, 7);
		if($urlCheck != 'http://' && !empty($url))
			$url = 'http://' . $url;
		
		$imageCheck = substr($image, 0, 7);
		if($imageCheck != 'http://' && !empty($image))
			$image = 'http://' . $image;
		
		$about_person = array(	'fname' => $wpdb->escape($fname),
								'lname' => $wpdb->escape($lname),
								'major' => $wpdb->escape($major),
								'gradYear' => $wpdb->escape($gradYear),
								'email' => $wpdb->escape($email),
								'url' => $wpdb->escape($url),
								'about' => $wpdb->escape($about),
								'image' => $wpdb->escape($image),
								'type' => $wpdb->escape($type),
								'visible' => $wpdb->escape($visible),
								'updated' => time(),
								'approved' => 'N');

		$insert_person = insert_person($about_person);
		
		echo $insert_person;	
	}
	
	else {
		echo '<table border="0" width="100%">
				<form method="post" action="">
				<input type="hidden" name="action" value="insert">';
				
		echo '<tr>
				<td width="40%" align="right"><label for="fname">First Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="fname" name="fname" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="lname">Last Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="lname" name="lname" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="type">Are you:</label></td>
				<td>&nbsp;</td>
				<td align="left">
					<select id="type" name="type">
						<option value="Alum">An Alumnus</option>
						<option value="Current Student">A Current Student</option>
					</select>
				</td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="major">Your Major:</label></td>
				<td>&nbsp;</td>
				<td align="left">
			';
			
		$peopleManagerOpts = unserialize(get_option('people-manager-options'));
		$allowedMajors = $peopleManagerOpts['allowedMajors'];
		
		if(empty($allowedMajors)) {
			echo '<input id="major" name="major" type="text" length="75" value="" />';
		}
		
		else {
			$temp = explode(',', $allowedMajors);
			
			echo '<select id="major" name="major">';
			echo '<option value=""></option>';
			
			for($x = 0; $x < count($temp); $x++) {
				echo '<option value="' . trim($temp[$x]) . '">' . trim($temp[$x]) . '</option>';
			}
			
			echo '</select>';
		}
		
		echo '</td></tr>';
			
		echo '<tr>
				<td width="40%" align="right"><label for="gradYear">Your Graduation Year:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="gradYear" name="gradYear" type="text" maxlength="4" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="email">Your E-Mail Address:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="email" name="email" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="url">Your Web site URL:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="url" name="url" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="image">A URL To Your Photograph*:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="image" name="image" type="text" length="75" value="" /></td>
			</tr>';
			
		echo '<tr>
				<td width="40%" align="right"><label for="about">Anything else you would like to share with other alumni?</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="about" name="about" rows="10" cols="25"></textarea></td>
			</tr>';
			
		echo '<tr>
				<td width="40%" align="right"><label for="visible">Would you like to share your information with other alumni?</label></td>
				<td>&nbsp;</td>
				<td align="left">
					<select id="visible" name="visible">
						<option value="Y" SELECTED>Yes</option>
						<option value="N">No</option>
					</select>
				</td>
			</tr>';

		echo '</table><p>*Please upload your photograph to Flickr or another image storage Web site. You will be given a URL from that Web site to use here.</p>';
		
		echo '<input type="submit" class="button-primary" value="Submit Profile" /></form>';	
	}
}

add_shortcode('do_add_student_public_form', 'add_student_public_form');

/*
**	insert_person
**
**	This function handles inserting a new entry into the database
*/

function insert_person($array_about_person) {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";
	
	if(!$wpdb->insert($table_name, $array_about_person)) // make sure we inserted into the database.
		return "The person was not inserted in the database. Please contact your support professional.";
	else
		return "The person was successfully inserted in the database.";	
}

/*
**	Menu Builder
**
**	Creates and adds a menu page where you go to manage the entries in your people management database
*/

add_action('admin_menu', 'people_manager_add_menu_pages');

function people_manager_add_menu_pages() {
	add_pages_page( 'Manage People', 'Manage People', 10, __FILE__, 'display_people_management_page');
	add_options_page('Manage People Options', 'Manage People Options', 10, __FILE__, 'people_management_options_page');
}

/*
**	Function Name: people_management_options_page
**
**	This function builds an options page for the People Manager Plugin
*/
function people_management_options_page() {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";

	if( !is_admin() )
		wp_die("You are not authorized to be here.");
		
	if( $_POST['action'] == 'options-save' ) {
		// get $_POST values
		$allowedMajors = $_POST['allowedMajors'];
		
		$people_manager_opts_array = array('allowedMajors' => $allowedMajors);
		
		update_option('people-manager-options', serialize($people_manager_opts_array));
		?>
		<div class="updated"><p><strong><?php _e('Options saved.', 'mt_trans_domain' ); ?></strong></p></div>
		<?php
	}
	
	else if( $_POST['action'] == 'generate-shortcode' ) {
		$faculty = $_POST['faculty'];
		$student = $_POST['student'];
		$alumni = $_POST['alumni'];
		$gradYear = $_POST['gradYear'];
		$gradYearText = $_POST['gradYearText'];
		$major = $_POST['major'];
		$majorText = $_POST['majorText'];
		$sortOption = $_POST['sortOption'];
		$sortDirection = $_POST['sortDirection'];
		
		$atts = "";
		
		if( empty( $faculty) && empty( $student ) && empty( $alumni ) ) {
			$type = "";
		}
		
		else {
			$type = " type='";
			
			if( !empty( $faculty ) )
				$type .= "Faculty,";
			
			if( !empty( $student ) )
				$type .= "Current Student,";
			
			if( !empty( $alumni ) )
				$type .= "Alum,";
		
			$type = substr($type, 0, ( strlen( $type ) - 1 ) );
			$type .= "' ";
		}		

		$atts .= $type;
		
		if( !empty( $gradYear ) ) {
			$atts .= " gradYear='" . $gradYearText . "' ";
		}
		
		if( !empty( $major ) ) {
			$atts .= " major='" . $majorText . "' ";
		}
		
		if( !empty( $sortOption ) ) {
			$atts .= " sort='" . $sortOption . "' ";
		
			if( !empty( $sortDirection ) ) {
				$atts .= " sortdirection='" . $sortDirection . "' ";
			}
		}
		
		$customShortcode = "[do_display_people_to_world" . $atts . "]";		
		?>
		<div class="updated"><p><strong><?php _e('Your shortcode is: ', 'mt_trans_domain' ); echo "<p><blockquote>" . $customShortcode . "</blockquote></p>"; ?></strong></p></div>
		<?php
	}
	
	$peopleManagerOpts = unserialize(get_option('people-manager-options'));
	$allowedMajors = $peopleManagerOpts['allowedMajors'];
	
	echo '<div class="wrap">';
	
	echo '<h2>Adding Signup Forms</h2>';
	
	echo '	<p>There are two signup forms available. They are:
				<blockquote>
					<code>[do_add_faculty_public_form]</code>: Adds a signup to the page specific to faculty members.<br />
					<code>[do_add_student_public_form]</code>: Adds a signup to the page specific to students and alumni.<br />
				</blockquote>
			</p>
	
			<p>To use these forms, create a new page, copy everything between the brackets (including the brackets), paste the code into your page, then publish the page.</p>
	';
	echo '<h2>Generate Shortcode</h2>';

	echo '<form method="post" action = "">';
	echo '	<input type="hidden" name="action" value="generate-shortcode">';
	
	// add a nonce for security
	if( function_exists('wp_nonce_field') )
		wp_nonce_field('people-manager-action-options-generate');
		
	echo '	<strong>Include the following types of people:</strong><br />
				<blockquote>
					<input type="checkbox" id="faculty" name="faculty" value="on"> <label for="faculty">Faculty</label><br />
					<input type="checkbox" id="student" name="student" value="on"> <label for="student">Current Students</label><br />
					<input type="checkbox" id="alumni" name="alumni" value="on"> <label for="alumni">Alumni</label><br />
				</blockquote>
			
			<strong>Filter results by:</strong><br />
				<blockquote>
					<table border="0" width="100%">
						<tr>
							<td width="10%"><input type="checkbox" id="gradYear" name="gradYear" value="on"> <label for="gradYear">Grad. Year:</label></td>
							<td><input type="text" name="gradYearText" value = ""> (ex. 2010)</td>
						</tr>
						
						<tr>
							<td width="10%"><input type="checkbox" id="major" name="major" value="on"> <label for="major">Major:</label></td>
							<td><input type="text" name="majorText" value = ""> (ex. English)</td>
						</tr>
					</table>
				</blockquote>
				
			<strong>Sort results by:</strong> 
					<select name="sortOption">
						<option value=""></option>
						<option value="lname">Last Name</option>
						<option value="gradYear">Grad. Year</option>
						<option value="major">Major</option>
					</select><br /><br />
				
			<strong>Sort direction:</strong> 
					<select name="sortDirection">
						<option value=""></option>
						<option value="ASC">Alphabetical</option>
						<option value="DESC">Descending (ex. 2010, 2009, 2008, etc)</option>
					</select>';

	?>
		<p class="submit">
		<input type="submit" name="Submit" value="<?php _e('Generate Shortcode', 'mt_trans_domain' ) ?>" />
		</p>
		</form>
	<?php					
	echo '		<h2>People Management Options</h2>

				<form method="post" action="">';
								
				// add a nonce for security
				if( function_exists('wp_nonce_field') )
					wp_nonce_field('people-manager-action-options-save');				
				
				echo '	<input type="hidden" name="action" value="options-save">
						
						<table border="0" width="100%">
							<tr>
								<td width="12.5%" valign="top"><label for="allowedMajors">Allowable Majors*:</label></td>
								<td align="left"><textarea id="allowedMajors" name="allowedMajors" rows="5" cols="25">' . $allowedMajors . '</textarea></td>
							</tr>
						</table><br />
						* List the majors you want people to choose, separated by a comma. For example: type in <code>MBA, MSA, Finance</code> to allow only those three majors. Leave the field empty if you do not want to restrict major selection.<br />';
				?>
							<p class="submit">
							<input type="submit" name="Submit" value="<?php _e('Update People Manager Options', 'mt_trans_domain' ) ?>" />
							</p>
				<?php			
				
				echo '</form>';
				
	echo '</div>';
}

/*
**	Function Name: display_people_management_page
**
**	This function outputs the management page for the People Manager Plugin
*/
function display_people_management_page() {
	global $wpdb;
	$table_name = $wpdb->prefix . "people";

	// grab the variable for what action we are performing
	// make sure to get it even if we decide to POST it or GET it
	$action = $_POST['action'];

	if(empty($action)) {
		$action = $_GET['action'];
	}
	
	if($action == 'insert') {
		check_admin_referer('people-manager-action-insert');
		
		if( !is_admin() )
			wp_die("You are not authorized to be here.");
			
		$fname = $_POST['fname'];
		$lname = $_POST['lname'];
		$major = $_POST['major'];
		$gradYear = $_POST['gradYear'];
		$email = strtolower($_POST['email']);
		$url = strtolower($_POST['url']);
		$about = $_POST['about'];
		$image = strtolower($_POST['image']);
		$type = $_POST['type'];
		$visible = $_POST['visible'];
		$telephone = $_POST['telephone'];
		$office = $_POST['office'];
		$keywords = $_POST['keywords'];
		$research = $_POST['research'];
			
		$urlCheck = substr($url, 0, 7);
		if($urlCheck != 'http://' && !empty($url))
			$url = 'http://' . $url;
		
		$imageCheck = substr($image, 0, 7);
		if($imageCheck != 'http://' && !empty($image))
			$image = 'http://' . $image;
		
		$about_person = array(	'fname' => $fname,
								'lname' => $lname,
								'major' => $major,
								'gradYear' => $gradYear,
								'email' => $email,
								'url' => $url,
								'about' => $about,
								'image' => $image,
								'type' => $type,
								'visible' => $visible,
								'telephone' => $telephone,
								'office' => $office,
								'keywords' => $keywords,
								'research' => $research,
								'updated' => time(),
								'approved' => 'Y');

		$insert_person = insert_person($about_person);
		
		echo $insert_person;
	}
	
	else if($action == 'search') {
		check_admin_referer('people-manager-action-search-button-form');
		
		if( !is_admin() )
			wp_die("You are not authorized to be here.");
			
		$searchFor = $_POST['searchFor'];
		
		$sql = "SELECT * FROM " . $table_name . " WHERE
				LOCATE('" . $searchFor . "', fname) != 0 OR
				LOCATE('" . $searchFor . "', lname) != 0 OR
				LOCATE('" . $searchFor . "', major) != 0 OR
				LOCATE('" . $searchFor . "', gradYear) != 0
				ORDER BY lname DESC";
				
		$results = $wpdb->get_results($sql);		

		if($results) {
			echo "<h2>People Matching Search Term(s): " . $searchFor . "</h2>";

			echo "<table width='100%' border='0'><tr>";
			echo "<th align='left'>First Name</th>";
			echo "<th align='left'>Last Name</th>";
			echo "<th align='left'>Type</th>";
			echo "<th align='left'>Major</th>";
			echo "<th align='left'>Graduation Year</th>";
			echo "</tr><tr><td colspan='5'><hr></td></tr>";
		
			foreach($results as $person) {
				echo "<tr>";
					echo "<td>" . $person->fname . "</td>";
					echo "<td>" . $person->lname . "</td>";
					echo "<td>" . $person->type . "</td>";
					echo "<td>" . $person->major . "</td>";
					echo "<td>" . $person->gradYear . "</td>";
				echo "</tr>";
			}
			
			echo '</table>';
		}
		
		else {
			echo "<h2>Search Results</h2>No people matched your query.<br />";
		}
	}
	
	else if($action == 'add-faculty') {
		check_admin_referer('people-manager-action-add-faculty-button-form');
		
		if( !is_admin() )
			wp_die("You are not authorized to be here.");

		echo '<div class="wrap"><h2>Add Faculty</h2>';
								
		// add a nonce for security
		if( function_exists('wp_nonce_field') )
			wp_nonce_field('people-manager-action-insert');
				
		echo '<table border="0" width="100%">
				<form method="post" action="">
				<input type="hidden" name="action" value="insert">
				<input type="hidden" name="type" value="faculty">
				<input type="hidden" name="visible" value="Y"';

		// add a nonce for security
		if( function_exists('wp_nonce_field') )
			wp_nonce_field('people-manager-action-insert');
				
		echo '<tr>
				<td width="40%" align="right"><label for="fname">First Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="fname" name="fname" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="lname">Last Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="lname" name="lname" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="image">A URL To Your Photograph*:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="image" name="image" type="text" length="75" value="" /></td>
			</tr>';
			
		echo '<tr>
				<td width="40%" align="right"><label for="email">Your E-Mail Address:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="email" name="email" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="url">Your Web site URL:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="url" name="url" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="telephone">Office Telephone Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="telephone" name="telephone" type="text" length="75" value="" /></td>
			</tr>';
			
		echo '<tr>
				<td width="40%" align="right"><label for="office">Office Building and Room Number:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="office" name="office" type="text" length="75" value="" /></td>
			</tr>';
			
		echo '	<tr>
					<td width="40%" align="right"><label for="major">Your Degrees (Limit 75 Words):</label></td>
					<td>&nbsp;</td>
					<td align="left"><textarea id="major" name="major" rows="10" cols="25"></textarea></td>
				</tr>';
			
		echo '<tr>
				<td width="40%" align="right"><label for="keywords">Keywords About You and Your Research (separate each keyword with a comma):</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="keywords" name="keywords" type="text" length="75" value="" /></td>
			</tr>';
							
		echo '<tr>
				<td width="40%" align="right"><label for="about">Write A Paragraph About Yourself (Limit 150 Words):</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="about" name="about" rows="10" cols="25"></textarea></td>
			</tr>';

		echo '<tr>
				<td width="40%" align="right"><label for="research">Write A Paragraph About Your Research Areas (Limit 150 Words):</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="research" name="research" rows="10" cols="25"></textarea></td>
			</tr>';
			
		echo '</table><p>*Please upload your photograph to Flickr or another image storage Web site. You will be given a URL from that Web site to use here.</p>';
		
		echo '<input type="submit" class="button-primary" value="Submit Profile" /></form>';		
	}
	
	else if($action == 'add-student') {
		check_admin_referer('people-manager-action-add-student-button-form');
		
		if( !is_admin() )
			wp_die("You are not authorized to be here.");
			
		echo '<div class="wrap"><h2>Add Person</h2><table border="0" width="100%">
				<form method="post" action="">';
								
				// add a nonce for security
				if( function_exists('wp_nonce_field') )
					wp_nonce_field('people-manager-action-insert');
		
				echo '<input type="hidden" name="action" value="insert">';
		
		echo '<tr>
				<td width="20%" align="right"><label for="fname">First Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="fname" name="fname" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="20%" align="right"><label for="lname">Last Name:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="lname" name="lname" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="20%" align="right"><label for="type">Are you:</label></td>
				<td>&nbsp;</td>
				<td align="left">
					<select id="type" name="type">
						<option value="Alum">An Alumnus</option>
						<option value="Current Student">A Current Student</option>
						<option value="Faculty">A Faculty Member</option>
					</select>
				</td>
			</tr>';

		echo '<tr>
				<td width="20%" align="right"><label for="major">Your Major:</label></td>
				<td>&nbsp;</td>
				<td align="left">
			';
			
		$peopleManagerOpts = unserialize(get_option('people-manager-options'));
		$allowedMajors = $peopleManagerOpts['allowedMajors'];
		
		if(empty($allowedMajors)) {
			echo '<input id="major" name="major" type="text" length="75" value="" />';
		}
		
		else {
			$temp = explode(',', $allowedMajors);
			
			echo '<select id="major" name="major">';
			echo '<option value=""></option>';
			
			for($x = 0; $x < count($temp); $x++) {
				echo '<option value="' . trim($temp[$x]) . '">' . trim($temp[$x]) . '</option>';
			}
			
			echo '</select>';
		}
		
		echo '</td></tr>';
			
		echo '<tr>
				<td width="20%" align="right"><label for="gradYear">Your Graduation Year:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="gradYear" name="gradYear" type="text" maxlength="4" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="20%" align="right"><label for="email">Your E-Mail Address:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="email" name="email" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="20%" align="right"><label for="url">Your Web site URL:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="url" name="url" type="text" length="75" value="" /></td>
			</tr>';

		echo '<tr>
				<td width="20%" align="right"><label for="image">A URL To Your Photograph*:</label></td>
				<td>&nbsp;</td>
				<td align="left"><input id="image" name="image" type="text" length="75" value="" /></td>
			</tr>';
			
		echo '<tr>
				<td width="20%" align="right"><label for="about">Anything else you would like to share with other alumni?</label></td>
				<td>&nbsp;</td>
				<td align="left"><textarea id="about" name="about" rows="10" cols="50"></textarea></td>
			</tr>';
			
		echo '<tr>
				<td width="20%" align="right"><label for="visible">Would you like to share your information with other alumni?</label></td>
				<td>&nbsp;</td>
				<td align="left">
					<select id="visible" name="visible">
						<option value="Y" SELECTED>Yes</option>
						<option value="N">No</option>
					</select>
				</td>
			</tr>';

		echo '</table><p>*Please upload your photograph to Flickr or another image storage Web site. You will be given a URL from that Web site to use here.</p>';
		
		echo '<input type="submit" class="button-primary" value="Add Student/Alum" /></form></div>';
	}
	
	else if($action == "approve") {
		check_admin_referer('people-manager-action-approve-button-form');
		
		if( !is_admin() )
			wp_die("You are not authorized to be here.");
			
		$id = $_POST['idNum'];
		$sql = "UPDATE " . $table_name . " SET approved = 'Y' WHERE id = " . id;
		
		if(!$wpdb->query($sql))
			echo "Could not approve entry. Please try again later or contact your support professional.";
		else
			echo "Entry approved!";
	}
	
	else if($action == "delete") {
		check_admin_referer('people-manager-action-delete-button-form');
		
		if( !is_admin() )
			wp_die("You are not authorized to be here.");
			
		$id = $_POST['idNum'];
		$sql = "DELETE FROM " . $table_name . " WHERE id = " . id;
		
		if(!$wpdb->query($sql))
			echo "Could not delete entry. Please try again later or contact your support professional.";
		else
			echo "Entry deleted!";	
	}
	
	else {
		if( !is_admin() )
			wp_die("You are not authorized to be here.");
			
		echo 	'<div class="wrap"><h2>Manage People</h2>
					<table width="100%" border="0">
						<tr>
							<td width="25%">
								<form method="post" action="">';
								
								// add a nonce for security
								if( function_exists('wp_nonce_field') )
									wp_nonce_field('people-manager-action-add-student-button-form');

								echo '<input type="hidden" name="action" value="add-student" />
								<input type="submit" class="button-primary" value="Add New Student/Alum" />				
								</form>
							</td>

							<td width="25%">
								<form method="post" action="">';
								
								// add a nonce for security
								if( function_exists('wp_nonce_field') )
									wp_nonce_field('people-manager-action-add-faculty-button-form');

								echo '<input type="hidden" name="action" value="add-faculty" />
								<input type="submit" class="button-primary" value="Add New Faculty" />				
								</form>
							</td>
							
							<td width="50%">
								<form method="post" action="">';
								
								// add a nonce for security
								if( function_exists('wp_nonce_field') )
									wp_nonce_field('people-manager-action-search-button-form');

								echo '<input length="90" type="text" name="searchFor" value="" />
								<input type="hidden" name="action" value="search" />
								<input type="submit" class="button-primary" value="Search" />				
								</form>
							</td>
						</tr>
					</table>';

		echo "<br /><br /><h2>People Waiting To Be Approved</h2>";
		echo "<p>Only fifty (50) people are shown. Approve or delete these and more will be shown.</p>";

		$sql = "SELECT * FROM " . $table_name . " WHERE approved = 'N' ORDER BY gradYear DESC, lname ASC LIMIT 50";	
		$approvals = $wpdb->get_results($sql);
		
		if($approvals) {
			echo "<table width='100%' border='0'><tr>";
			echo "<th align='left'>First Name</th>";
			echo "<th align='left'>Last Name</th>";
			echo "<th align='left'>Type</th>";
			echo "<th align='left'>Major</th>";
			echo "<th align='left'>Graduation Year</th>";
			echo "<th>&nbsp;</th>";
			echo "<th>&nbsp;</th>";
			echo "</tr><tr><td colspan='7'><hr></td></tr>";

			foreach($approvals as $approve) {
				echo "<tr>";
					echo "<td>" . $approve->fname . "</td>";
					echo "<td>" . $approve->lname . "</td>";
					echo "<td>" . $approve->type . "</td>";
					echo "<td>" . $approve->major . "</td>";
					echo "<td>" . $approve->gradYear . "</td>";
					
					echo '	<td>
							<form method="post" action="">';
								
								// add a nonce for security
								if( function_exists('wp_nonce_field') )
									wp_nonce_field('people-manager-action-approve-button-form');

								echo '<input type="hidden" name="idNum" value="' . $approve->id . '">
								<input type="hidden" name="action" value="approve" />
								<input type="submit" class="button-primary" value="Approve" />				
								</form>
							</td>';
							
					echo '	<td>
							<form method="post" action="">';
								
								// add a nonce for security
								if( function_exists('wp_nonce_field') )
									wp_nonce_field('people-manager-action-delete-button-form');

								echo '<input type="hidden" name="idNum" value="' . $approve->id . '">
								<input type="hidden" name="action" value="delete" />
								<input type="submit" class="button-primary" value="Delete" />				
								</form>
							</td>';
							
				echo "</tr>";
			}
			
			echo '</table>';
		}
		
		else {
			echo "<p>No approvals required</p>";
		}

		$sql = "SELECT * FROM " . $table_name . " WHERE approved = 'Y' ORDER BY gradYear DESC, lname ASC LIMIT 50";
		$results = $wpdb->get_results($sql);
		
		echo "<br /><br /><h2>People In Your Database</h2>";
		echo "<p>Only fifty (50) people are shown. Use the search bar to find more.</p>";
		
		if($results) {	
			echo "<table width='100%' border='0'><tr>";
			echo "<th align='left'>First Name</th>";
			echo "<th align='left'>Last Name</th>";
			echo "<th align='left'>Type</th>";
			echo "<th align='left'>Major</th>";
			echo "<th align='left'>Graduation Year</th>";
			echo "</tr><tr><td colspan='5'><hr></td></tr>";
		
			foreach($results as $person) {
				echo "<tr>";
					echo "<td>" . $person->fname . "</td>";
					echo "<td>" . $person->lname . "</td>";
					echo "<td>" . $person->type . "</td>";
					echo "<td>" . $person->major . "</td>";
					echo "<td>" . $person->gradYear . "</td>";
				echo "</tr>";
			}
			
			echo '</table>';
		}
		
		else {
			echo "<p>There are no people in your database.</p>";
		}
		
		echo '</div>';
	}
}

/*	Create/Update Database
**	This function allows us to maintain the database. If the plugin is being installed for the first time
**	this function inserts a table into the database for the current blog. If this plugin is being
**	updated, it checks that and upgrades the table if needed.
*/
register_activation_hook(__FILE__, 'people_manager_install'); // when the plugin is activated, the DB install function is called

function people_manager_install() {
	if( !is_admin() )
		wp_die("You are not authorized to be here.");
			
	global $wpdb;
	$people_db_version = '0.4';
	$table_name = $wpdb->prefix . "people";
	
	if($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
				id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
				updated BIGINT(11) DEFAULT '0' NOT NULL,
				fname VARCHAR(512) NULL,
				lname VARCHAR(512) NULL,
				major VARCHAR(512) NULL,
				gradYear VARCHAR(4) NULL,
				email VARCHAR(512) NULL,
				url TEXT NULL,
				about VARCHAR(1024) NULL,
				type VARCHAR(64) NULL,
				image VARCHAR(256) NULL,
				visible VARCHAR(1) DEFAULT 'Y' NOT NULL,
				approved VARCHAR(1) DEFAULT 'N' NOT NULL,
				telephone VARCHAR(25) NULL,
				office VARCHAR(128) NULL,
				keywords VARCHAR(512) NULL,
				research VARCHAR(1024) NULL,
				UNIQUE KEY id (id));";
		
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		add_option("people_db_version", $people_db_version);
	}

		// this section is used to update the plugin.
		$installed_ver = get_option("people_db_version");
		
		if($installed_ver != $people_db_version) {
			$sql = "CREATE TABLE " . $table_name . " (
					id MEDIUMINT(9) NOT NULL AUTO_INCREMENT,
					updated BIGINT(11) DEFAULT '0' NOT NULL,
					fname VARCHAR(512) NULL,
					lname VARCHAR(512) NULL,
					major VARCHAR(512) NULL,
					gradYear VARCHAR(4) NULL,
					email VARCHAR(512) NULL,
					url TEXT NULL,
					about VARCHAR(1024) NULL,
					type VARCHAR(64) NULL,
					image VARCHAR(256) NULL,
					visible VARCHAR(1) DEFAULT 'Y' NOT NULL,
					approved VARCHAR(1) DEFAULT 'N' NOT NULL,
					telephone VARCHAR(25) NULL,
					office VARCHAR(128) NULL,
					keywords VARCHAR(512) NULL,
					research VARCHAR(1024) NULL,
					UNIQUE KEY id (id));";
			
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		
			update_option("people_db_version", $people_db_version);
		}
}
?>