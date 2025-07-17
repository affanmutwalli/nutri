<?php
include_once 'db_connect.php';
include_once 'psl-config.php';
	$error_msg = "";
	 
if (isset($_POST['username'])) {

	// Sanitize and validate the data passed in
	$username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
	$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
	$email = filter_var($email, FILTER_VALIDATE_EMAIL);
	if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
		// Not a valid email
		$error_msg .= '<p class="error">The email address you entered is not valid</p>';
	}
 
	$password = filter_input(INPUT_POST, 'p', FILTER_SANITIZE_STRING);
	if (strlen($password) != 128) {
		// The hashed pwd should be 128 characters long.
		// If it's not, something really odd has happened
		$error_msg .= '<p class="error">Invalid password configuration.</p>';
	}
 
	// Username validity and password validity have been checked client side.
	// This should should be adequate as nobody gains any advantage from
	// breaking these rules.
	//
 
	$prep_stmt = "SELECT id FROM members WHERE email = ? LIMIT 1";
	$stmt = $mysqli->prepare($prep_stmt);
 
   // check existing email  
	if ($stmt) {
		$stmt->bind_param('s', $email);
		$stmt->execute();
		$stmt->store_result();
 
		if ($stmt->num_rows == 1) {
			// A user with this email address already exists
			$error_msg .= '<p class="error">A user with this email address already exists.</p>';
						$stmt->close();
		}
				$stmt->close();
	} else {
		$error_msg .= '<p class="error">Database error Line 39</p>';
				$stmt->close();
	}
 
	// check existing username
	$prep_stmt = "SELECT id FROM members WHERE username = ? LIMIT 1";
	$stmt = $mysqli->prepare($prep_stmt);
 
	if ($stmt) {
		$stmt->bind_param('s', $username);
		$stmt->execute();
		$stmt->store_result();
 
				if ($stmt->num_rows == 1) {
						// A user with this username already exists
						$error_msg .= '<p class="error">A user with this username already exists</p>';
						$stmt->close();
				}
				$stmt->close();
		} else {
				$error_msg .= '<p class="error">Database error line 55</p>';
				$stmt->close();
		}
 
	// TODO: 
	// We'll also have to account for the situation where the user doesn't have
	// rights to do registration, by checking what type of user is attempting to
	// perform the operation.
 
	if (empty($error_msg)) {
		// Create a random salt
		//$random_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE)); // Did not work
		$random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
 
		// Create salted password 
		$password = hash('sha512', $password . $random_salt);
 
		// Insert the new user into the database 
		if ($insert_stmt = $mysqli->prepare("INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)")) {
			$insert_stmt->bind_param('ssss', $username, $email, $password, $random_salt);
			// Execute the prepared query.
			if (! $insert_stmt->execute()) {
				header('Location: ../error.php?err=Registration failure: INSERT');
			}
			else
			{
								
				if($_POST["RSMName"]!="" && $_POST["username"]!="" && $_POST["MobileNoSMS"]!="")
				{	
					$BirthDateParts=explode("/",$_POST["BirthDate"]);
					$NewBirthDate=$BirthDateParts[2]."/".$BirthDateParts[1]."/".$BirthDateParts[0];
					if($_POST["RSMId"]!="")
					{
							$IsActive="N";
							if(isset($_POST) && array_key_exists("IsActive",$_POST))
							{
								$IsActive="Y";
							}
							mysql_query("SET NAMES 'utf8'");
						
							$obj->fupdate("update rsm_master set RSMName='".(mysql_real_escape_string($_POST["RSMName"]))."',RSMCode='".(mysql_real_escape_string($_POST["username"]))."',BirthDate='".(mysql_real_escape_string($NewBirthDate))."',AdharcardNo='".(mysql_real_escape_string($_POST["AdharcardNo"]))."',MobileNoSMS='".(mysql_real_escape_string($_POST["MobileNoSMS"]))."',WhatsAppMobileNo='".(mysql_real_escape_string($_POST["WhatsAppMobileNo"]))."',AdditionalMobileNo='".(mysql_real_escape_string($_POST["AdditionalMobileNo"]))."',Address='".(mysql_real_escape_string($_POST["Address"]))."' , CityId='".(mysql_real_escape_string($_POST["CityId"]))."', EmailId='".(mysql_real_escape_string($_POST["email"]))."' ,IsActive='$IsActive' where RSMId='".$_POST["RSMId"]."'");

					}
					else
					{
							$IsActive="N";
							if(isset($_POST) && array_key_exists("IsActive",$_POST))
							{
								$IsActive="Y";
							}
							
							$MaxId=$obj->fSelectString("Select MAX(RSMId) from  rsm_master")+1;
															
							mysql_query("insert into rsm_master values('".$MaxId."','".(mysql_real_escape_string($_POST["username"]))."','".(mysql_real_escape_string($_POST["RSMName"]))."','".(mysql_real_escape_string($NewBirthDate))."','".(mysql_real_escape_string($_POST["AdharcardNo"]))."','".(mysql_real_escape_string($_POST["MobileNoSMS"]))."','".(mysql_real_escape_string($_POST["WhatsAppMobileNo"]))."','".(mysql_real_escape_string($_POST["AdditionalMobileNo"]))."','".(mysql_real_escape_string($_POST["Address"]))."','".(mysql_real_escape_string($_POST["CityId"]))."','".(mysql_real_escape_string($_POST["email"]))."','$IsActive')");
					}
				}
				else
			
				{
			
					echo "Product Name, Product name in marathi, Product Code, MRP ,Retailer Price, Distributor Price  compulsary";
			
				}
				                  
			}
		}
		//header('Location: ./add_r_rsm.php');
	}
}
?>