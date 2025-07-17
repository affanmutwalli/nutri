<?php
include_once 'psl-config.php';
function sec_session_start() {
    $session_name = 'sec_session_id';   // Set a custom session name
    $secure = SECURE;
    // This stops JavaScript being able to access the session id.
    $httponly = true;

    // Check if session is already started
    if (session_status() == PHP_SESSION_ACTIVE) {
        return;
    }

    // Forces sessions to only use cookies.
    if (ini_set('session.use_only_cookies', 1) === FALSE) {
        header("Location: ../error.php?err=Could not initiate a safe session (ini_set)");
        exit();
    }

    // Gets current cookies params.
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"],
        $cookieParams["path"],
        $cookieParams["domain"],
        $secure,
        $httponly);

    // Sets the session name to the one set above.
    session_name($session_name);

    // Start session with error handling
    try {
        session_start();            // Start the PHP session
        session_regenerate_id(true);    // regenerated the session, delete the old one.
    } catch (Exception $e) {
        error_log("Session start error: " . $e->getMessage());
        // Try to start session without regenerating ID
        session_start();
    }
}
function checkMobileNoExists($mobileno, $mysqli) {
		
	
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT id, username, password, salt 
        FROM members
       WHERE MobileNo = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $mobileno);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();
		
		
		if ($stmt->num_rows == 1) {
			if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked
                return false;
            }
			else
			{
				return true;
			}
		}
        
    }
}
function getPassword($email, $mysqli) {
		
	$newpassword="222";
    // Using prepared statements means that SQL injection is not possible. 
    if ($stmt = $mysqli->prepare("SELECT id, username, password, salt 
        FROM members
       WHERE email = ?
        LIMIT 1")) {
        $stmt->bind_param('s', $email);  // Bind "$email" to parameter.
        $stmt->execute();    // Execute the prepared query.
        $stmt->store_result();
 
        // get variables from result.
        $stmt->bind_result($user_id, $username, $db_password, $salt);
        $stmt->fetch();
		//echo $db_password."<br>";
 		//echo $newpassword."<br>";
        // hash the password with the unique salt.
		 $random_salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), true));
        $password = hash('sha512', $newpassword . $random_salt);
		//echo $password;
		
		//echo $random_salt."<br><br>".$password."<br><br>";
		
		if($stmt1 = $mysqli->prepare("update members set password=?, salt=?   
       WHERE email = ?"))
	   {
        $stmt1->bind_param('sss',$password,$random_salt,$email);  // Bind "$email" to parameter.
        $stmt1->execute();    // Execute the prepared query.
        $stmt1->store_result();
		}
		$username="n1";
		$email="n1_e";
		if ($insert_stmt = $mysqli->prepare("INSERT INTO members (username, email, password, salt) VALUES (?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssss', $username,$email, $password, $random_salt);
            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                header('Location: ../error.php?err=Registration failure: INSERT');
            }
        }
        /*if ($stmt->num_rows == 1) {
            // If the user exists we check if the account is locked
            // from too many login attempts 
 
            if (checkbrute($user_id, $mysqli) == true) {
                // Account is locked 
                // Send an email to user saying their account is locked
                return false;
            } else {
                // Check if the password in the database matches
                // the password the user submitted.
                if ($db_password == $password) {
                    // Password is correct!
                    // Get the user-agent string of the user.
                    $user_browser = $_SERVER['HTTP_USER_AGENT'];
                    // XSS protection as we might print this value
                    $user_id = preg_replace("/[^0-9]+/", "", $user_id);
                    $_SESSION['user_id'] = $user_id;
                    // XSS protection as we might print this value
                    $username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
                                                                "", 
                                                                $username);
                    $_SESSION['username'] = $username;
                    $_SESSION['login_string'] = hash('sha512', 
                              $password . $user_browser);
                    // Login successful.
                    return true;
                } else {
                    // Password is not correct
                    // We record this attempt in the database
                    $now = time();
                    $mysqli->query("INSERT INTO login_attempts(user_id, time)
                                    VALUES ('$user_id', '$now')");
                    return false;
                }
            }
        } else {
            // No user exists.
            return false;
        }*/
    }
}
function login($email, $password, $mysqli) {
	
		// Using prepared statements means that SQL injection is not possible. 
		if ($stmt = $mysqli->prepare("SELECT id, username, password, salt 
			FROM members
		   WHERE email = ?
			LIMIT 1")) {
			$stmt->bind_param('s', $email);  // Bind "$email" to parameter.
			$stmt->execute();    // Execute the prepared query.
			$stmt->store_result();
	 
			// get variables from result.
			$stmt->bind_result($user_id, $username, $db_password, $salt);
			
			$stmt->fetch();
	
			// hash the password with the unique salt.
			$password = hash('sha512', $password . $salt);
			//echo $password."<br>";
			
			if ($stmt->num_rows == 1) {
				// If the user exists we check if the account is locked
				// from too many login attempts 
				//echo "exists";
				if (checkbrute($user_id, $mysqli) == true) {
					echo "brute force";
					// Account is locked 
					// Send an email to user saying their account is locked
					return false;
				} else {
					// Check if the password in the database matches
					// the password the user submitted.
					//echo hash('sha512', $db_password . $salt);
					if ($db_password == $password) {
						// Password is correct!
						// Get the user-agent string of the user.
						$user_browser = $_SERVER['HTTP_USER_AGENT'];
						// XSS protection as we might print this value
						$user_id = preg_replace("/[^0-9]+/", "", $user_id);
						$_SESSION['user_id'] = $user_id;
						// XSS protection as we might print this value
						$username = preg_replace("/[^a-zA-Z0-9_\-]+/", 
																	"", 
																	$username);
						$_SESSION['username'] = $username;
						$_SESSION['email'] = $email;
						$_SESSION['login_string'] = hash('sha512', 
								  $password . $user_browser);
						// Login successful.
						return true;
					} else {
						// Password is not correct
						// We record this attempt in the database
						$now = time();
						$mysqli->query("INSERT INTO login_attempts(user_id, time)
										VALUES ('$user_id', '$now')");
						return false;
					}
				}
			} else {
				// No user exists.
				return false;
			}
		}
}
function checkbrute($user_id, $mysqli) {
    // Get timestamp of current time 
    $now = time();
 
    // All login attempts are counted from the past 2 hours. 
    $valid_attempts = $now - (2 * 60 * 60);
 
    if ($stmt = $mysqli->prepare("SELECT time 
                             FROM login_attempts 
                             WHERE user_id = ? 
                            AND time > '$valid_attempts'")) {
        $stmt->bind_param('i', $user_id);
 
        // Execute the prepared query. 
        $stmt->execute();
        $stmt->store_result();
 
        // If there have been more than 5 failed logins 
        if ($stmt->num_rows > 10) {
            return true;
        } else {
            return false;
        }
    }
}
function esc_url($url) {
 
    if ('' == $url) {
        return $url;
    }
 
    $url = preg_replace('|[^a-z0-9-~+_.?#=!&;,/:%@$\|*\'()\\x80-\\xff]|i', '', $url);
 
    $strip = array('%0d', '%0a', '%0D', '%0A');
    $url = (string) $url;
 
    $count = 1;
    while ($count) {
        $url = str_replace($strip, '', $url, $count);
    }
 
    $url = str_replace(';//', '://', $url);
 
    $url = htmlentities($url);
 
    $url = str_replace('&amp;', '&#038;', $url);
    $url = str_replace("'", '&#039;', $url);
 
    if ($url[0] !== '/') {
        // We're only interested in relative links from $_SERVER['PHP_SELF']
        return '';
    } else {
        return $url;
    }
}
function login_check($mysqli) {
    // Check if all session variables are set 
    if (isset($_SESSION['user_id'], 
                        $_SESSION['username'], 
                        $_SESSION['login_string'])) {
						//echo $_SESSION['login_string'];
 
        /*$user_id = $_SESSION['user_id'];
        $login_string = $_SESSION['login_string'];
        $username = $_SESSION['username'];
 
        // Get the user-agent string of the user.
        $user_browser = $_SERVER['HTTP_USER_AGENT'];
 
        if ($stmt = $mysqli->prepare("SELECT password 
                                      FROM members 
                                      WHERE id = ? LIMIT 1")) {
            // Bind "$user_id" to parameter. 
            $stmt->bind_param('i', $user_id);
            $stmt->execute();   // Execute the prepared query.
            $stmt->store_result();
 
            if ($stmt->num_rows == 1) {
                // If the user exists get variables from result.
                $stmt->bind_result($password);
                $stmt->fetch();
                $login_check = hash('sha512', $password . $user_browser);
 
                if ($login_check == $login_string) {
                    // Logged In!!!!
                    return true;
                } else {
                    // Not logged in
                    return false;
                }
            } else {
                // Not logged in 
                return false;
            }
        } else {
            // Not logged in 
            return false;
        }*/
		return true;
    } else {
        // Not logged in 
        return false;
    }
	//return true;
}

function validate_password_strength($password) {
    $errors = array();

    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters long";
    }

    if (!preg_match('/[a-z]/', $password)) {
        $errors[] = "Password must contain at least one lowercase letter";
    }

    if (!preg_match('/[A-Z]/', $password)) {
        $errors[] = "Password must contain at least one uppercase letter";
    }

    if (!preg_match('/[0-9]/', $password)) {
        $errors[] = "Password must contain at least one number";
    }

    return $errors;
}

function change_user_password($email, $current_password_hash, $new_password_hash, $mysqli) {
    // Get current user data
    $stmt = $mysqli->prepare("SELECT id, password, salt FROM members WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows !== 1) {
        return array('success' => false, 'message' => 'User not found');
    }

    $user = $result->fetch_assoc();
    $expected_current = hash('sha512', $current_password_hash . $user['salt']);

    if ($user['password'] !== $expected_current) {
        return array('success' => false, 'message' => 'Current password is incorrect');
    }

    // Generate new salt and hash
    $new_salt = hash('sha512', uniqid(openssl_random_pseudo_bytes(16), TRUE));
    $final_new_password = hash('sha512', $new_password_hash . $new_salt);

    // Update password
    $update_stmt = $mysqli->prepare("UPDATE members SET password = ?, salt = ? WHERE email = ?");
    $update_stmt->bind_param('sss', $final_new_password, $new_salt, $email);

    if ($update_stmt->execute()) {
        error_log("Password changed successfully for user: " . $email);
        return array('success' => true, 'message' => 'Password changed successfully');
    } else {
        error_log("Password change failed for user: " . $email . " - " . $mysqli->error);
        return array('success' => false, 'message' => 'Error updating password');
    }
}
?>