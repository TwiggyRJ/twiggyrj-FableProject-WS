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
		
	}
//Checks end	
?>