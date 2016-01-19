<?php
	include_once 'includes/base.php';
	//This is where the main service is stored
	$base = new User();
	//Initialises the User Class in the 'includes/base.php' file
	
	$header = isset($_REQUEST["action"]) ? $_REQUEST["action"]:"get";
	//Gets the header sent by the client so request type can be analysed
	
	switch($header)
	{
		
		//Checks the get action method
		case "get":
			
			//Checks request method
			if($_SERVER['REQUEST_METHOD']=='GET')
			{
				//Checks to see if username and password has been set via Basic Authentication in the request header
				if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']))
				{
					//If the username and password has been sent via the correct method then:
					
					//Gets those variables and sends them to the login_user function to check if the details are valid
					$user = $_SERVER["PHP_AUTH_USER"];
					$pass = $_SERVER["PHP_AUTH_PW"];
					$userdata = $base->login_user($user, $pass);
				}
				else
				{
					//If the username and password has not been sent correctly, inform the client the request was not was expected which makes it "wrong"
					
					header("HTTP/1.1 400 Bad Request");
				}
			}
			else
			{
				//If Request Method was not what was expected, tell the client the request was wrong
				
				header("HTTP/1.1 400 Bad Request");
			}
			break;
	
		//Checks the post action method
		case "put":
		
			//Checks request method
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				//Checks to see if username and password has been set via Basic Authentication in the request header and if the name, email and dob data has been sent via a method of post
				if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['avatar']) && isset($_POST['newPassword']) && isset($_POST['website']))
				{
					//If the username and password, name, email and dob has been sent via the correct method then:
					
					//Gets those variables and sends them to the reg_user function to register them
					$user = $_SERVER["PHP_AUTH_USER"];
					$pass = $_SERVER["PHP_AUTH_PW"];
					$name = $_POST['name'];
					$email = $_POST['email'];
					$avatar = $_POST['avatar'];
					$newPass = $_POST['newPassword'];
					$website = $_POST['website'];
					$userdata = $base->update_user($user, $pass, $name, $email, $avatar, $website, $newPass);
				}
				else
				{
					//If the username and password has not been sent correctly and the name, email and dob variables not sent correctly or missing then: inform the client the request was not was expected which makes it "wrong"
					
					header("HTTP/1.1 400 Bad Request");
				}
			}
			else
			{
				//If Request Method was not what was expected, tell the client the request was wrong
				header("HTTP/1.1 400 Bad Request");
			}
			break;
			
	
		//Checks the post action method
		case "post":
		
			//Checks request method
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				//Checks to see if username and password has been set via Basic Authentication in the request header and if the name, email and dob data has been sent via a method of post
				if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($_POST['name']) && isset($_POST['email']) && isset($_POST['dob']))
				{
					//If the username and password, name, email and dob has been sent via the correct method then:
					
					//Gets those variables and sends them to the reg_user function to register them
					$user = $_SERVER["PHP_AUTH_USER"];
					$pass = $_SERVER["PHP_AUTH_PW"];
					$name = $_POST['name'];
					$email = $_POST['email'];
					$dob = $_POST['dob'];
					$userdata = $base->reg_user($user, $pass, $name, $email, $dob);
				}
				else
				{
					//If the username and password has not been sent correctly and the name, email and dob variables not sent correctly or missing then: inform the client the request was not was expected which makes it "wrong"
					
					header("HTTP/1.1 400 Bad Request");
				}
			}
			else
			{
				//If Request Method was not what was expected, tell the client the request was wrong
				header("HTTP/1.1 400 Bad Request");
			}
			break;	

		
	}
//Checks end	
?>