<?php
	require ('connect.php');	// require the connection php
	session_start(); 			// start a session

	$comment = '';
	$email = '';				// clear variables to start with

	// Array of strings for captcha
	$string = ["secret!", "captcha", "random",
			   "apso13", "blah", "derp", "j2ola",
			   "2e4tn", "anm2p", "pkaj23"];
    
	// Grab the contents of the email textbox and comment textarea
	if(isset($_POST['content']) || isset($_POST['email']) ) {
		$comment = $_POST['content'];
		$email = $_POST['email'];
	}

	// Constants defined for required values for cookies
	define('START', time() + 30 );   
	define('INVALID_ATTEMPTS', 4);   
	define('LOCK_TIME', time() + (60 * 3) );   
	// Constants placed in variables
	$start = START;
	$attempts = INVALID_ATTEMPTS;
	$lock_time = LOCK_TIME;

	// Insert query
	$insert = "INSERT INTO comments (email, content) VALUES " . 
			  " ('{$email}', '{$comment}')";

	// Success message when the captcha matches
	$success = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN'
				   'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
				<html xmlns='http://www.w3.org/1999/xhtml' lang='en' xml:lang='en'>
				<head>
				    <title>CAPTCHA Assignment</title>
				</head>
				<body>
				    <h1>You are indeed a human! Comment Submitted.</h1>
				    <p><b>Email: </b> $email </p>
				    <p><b>Comment: </b> $comment </p>
				    <p><a href='index.php'>Try Another?</a></p>
				</body>
				</html>";
    
    // If the session 'captcha' is set
	if(isset($_SESSION['captcha']) ){
		// store in a variable
		$captcha = $_SESSION['captcha'];
		
		// When Captcha textbox is set and Submitted
		if( isset($_POST['captcha']) && isset($_POST['submit']) ){
			$captcha_text = $_POST['captcha'];

			// When Captcha entered and Captcha SESSION don't match
	        if( strtoupper($captcha_text) != strtoupper($_SESSION['captcha']) ) {
				// If both cookies are set
				if ( isset($_COOKIE['attempts']) && isset($_COOKIE['start']) ) { 
					// store them in variables
					$attempts = $_COOKIE['attempts'] - 1;
					$start = $_COOKIE['start'];
				}
				else {	 // When COOKIES aren't set, set the timer and counter
					setcookie("start", $start, time() + 30);
					setcookie('attempts', $attempts, $start);
				}
	        	// set the cookie whenever the captcha entered doesn't match
	        	setcookie('attempts', $attempts);
	        	
	        	// error message
	        	$error_msg = "<h1>You are not human!</h1>
		        	  	      <p>You have " . $attempts . " attempts left in the next " . 
		        	  	      ($start - time()) . " seconds</p>";
	        	
	        	// unset the captcha, replace with another random string
	        	$_SESSION['captcha'] = $string[array_rand($string)];
	        	
	        	// When the attempts reaches to 0
	        	if ($attempts == 0) {
	        		// Remove the session, remove cookies
					session_destroy();
					setcookie("start", $start, time() - 1);
					setcookie('attempts', $attempts, time() - 1);

					// If a timer for the lock and lock cookie has been set
					if ( isset($_COOKIE['lock_time']) && isset($_COOKIE['lock']) ) { 
						// store them in a variable
						$locked = $_COOKIE['lock'];
						$locked_time = $_COOKIE['lock_time'];
					}
					else {	 // When COOKIES aren't set
						// set the cookie timer, and a lock cookie
						setcookie("lock_time", $lock_time, time() + (60 * 3));
						setcookie("lock", lock());
						$locked = $_COOKIE['lock'];
					}
					// set the lockdown cookie with the lock time
					setcookie("lockdown", $locked, $lock_time);
				}
				// display the error message when attempts is not 0
	        	echo $error_msg;
	        }
	        else{	// when they match
	        	// Remove all sessions
	        	session_destroy();
	        	
	        	// if both the attempts and start cookies are set, in case the captcha
	        	// matches at first attempt, remove all cookies
	        	if (isset($_COOKIE['attempts']) && isset($_COOKIE['start'])) { 
		        	setcookie("start", $start, time() - 1);
					setcookie('attempts', $attempts, time() - 1);
				}
				// run the query to add the information to the database
				$result = $db->query($insert);
				// die to a markup success
				die($success);
	    	}
	    }
	}
	else{	// If captcha session is not set
		// set the session as a random string from the array defined above
		$captcha = strtoupper($string[array_rand($string)]);
		$_SESSION['captcha'] = $captcha;
	}
	// Set the session to a random string from the array above on
	// every load of the page
	$_SESSION['captcha'] = strtoupper($string[array_rand($string)]);

	// If a counter has been set and is not yet 0, lock the page
	while( isset($_COOKIE['lock_time']) && ($_COOKIE['lock_time'] - time() ) != 0){
		lock();
	}

	// Returns true if the entered captcha does not match the $_SESSION['captcha']
	function captcha_not_matched() {
		if( isset($_POST['captcha']) ){
			$captcha_text = $_POST['captcha'];
		}
		else {
			$captcha_text = '';
		}

		// Return a boolean representation if the text entered and session don't match
		return strtoupper($captcha_text) != strtoupper($_SESSION['captcha']);
	}

	// Function to convert page to error
	function lock() {
		// Array of image src paths
		$cats = [ "images/reading-cat-1.jpg", "images/reading-cat-2.jpg",
			      "images/reading-cat-3.jpg", "images/reading-cat-4.jpg",
			      "images/reading-cat-5.jpg", "images/reading-cat-6.jpg",
			      "images/reading-cat-7.jpg", "images/reading-cat-8.jpg",
			      "images/reading-cat-9.jpg", "images/reading-cat-gif1.gif",
			      "images/reading-cat-gif2.gif", "images/reading-cat-gif3.gif",
			      "images/reading-cat-gif4.gif", "images/reading-cat-gif5.gif" ];

	    // return the html passed into the 'die' function
		return die("<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Strict//EN'
				   'http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd'>
				<html xmlns='http://www.w3.org/1999/xhtml' lang='en' xml:lang='en'>
				<head>
				    <title>CAPTCHA Assignment</title>
				</head>
				<body>
				    <h1>LOCKED OUT!</h1>
				    <p>Until you are allowed back, here is a photo of a cat reading</p>
				    <p><img src='{$cats[array_rand($cats)]}' alt='' /></p>
				</body>
				</html>");
	}
?>