<?php
	include_once 'includes/base.php';
	//This is where the main service is stored
	$base = new GenericInfo();
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
				if (isset($_GET['method']))
				{
					//If the username and password has been sent via the correct method then:
					
					//Gets those variables and sends them to the login_user function to check if the details are valid
					$updatedata = $base->get_updates($_GET['method']);
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

		case "post":
			
			//Checks request method
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				//Checks to see if username and password has been set via Basic Authentication in the request header
				if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($_POST['version']) && isset($_POST['about']) && isset($_POST['updated']) && isset($_POST['content_1']))
				{
					
					$user = $_SERVER["PHP_AUTH_USER"];
					$pass = $_SERVER["PHP_AUTH_PW"];
					$version = $_POST['version'];
					$about = $_POST['about'];
					$updated = $_POST['updated'];
					$content_1 = $_POST['content_1'];
					$content_2 = $_POST['content_2'];
					$content_3 = $_POST['content_3'];
					$content_4 = $_POST['content_4'];
					$content_5 = $_POST['content_5'];
					$content_6 = $_POST['content_6'];
					$changelogdata = $base->post_updates($user, $pass, $version, $about, $updated, $content_1, $content_2, $content_3, $content_4, $content_5, $content_6);
				}
				else
				{
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