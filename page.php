<?php
	include_once 'includes/base.php';
	//This is where the main service is stored
	$base = new Story();
	//Initialises the Story Class in the 'includes/base.php' file
	
	$header = isset($_REQUEST["action"]) ? $_REQUEST["action"]:"get";
	//Gets the header sent by the client so request type can be analysed
	
	switch($header)
	{
	
		//Checks the post action method
		case "post":
		
			//Checks request method
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				//Checks to see if username and password has been set via Basic Authentication in the request header and if the name, email and dob data has been sent via a method of post
				if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($_POST['storyTitle']) && isset($_POST['title']) && isset($_POST['content']) && isset($_POST['pageNumber']) && isset($_POST['pageOptionA']) && isset($_POST['pageOptionADest']))
				{
					//If the username and password, name, email and dob has been sent via the correct method then:
					
					//Gets those variables and sends them to the reg_user function to register them
					$user = $_SERVER["PHP_AUTH_USER"];
					$pass = $_SERVER["PHP_AUTH_PW"];
					$storyTitle = $_POST['storyTitle'];
					$title = $_POST['title'];
					$content = $_POST['content'];
					$pageInteraction = $_POST['pageInteraction'];
					$pageEInteraction = $_POST['pageEInteraction'];
					$pageEInteractionAnswer = $_POST['pageEInteractionAnswer'];
					$pageMInteraction = $_POST['pageMInteraction'];
					$pageMInteractionAnswer = $_POST['pageMInteractionAnswer'];
					$pageHInteraction = $_POST['pageHInteraction'];
					$pageHInteractionAnswer = $_POST['pageHInteractionAnswer'];
					$pageJInteraction = $_POST['pageJInteraction'];
					$pageJInteractionAnswer = $_POST['pageJInteractionAnswer'];
					$pageNumber = $_POST['pageNumber'];
					$pageFirst = $_POST['pageFirst'];
					$pageOptionA = $_POST['pageOptionA'];
					$pageOptionB = $_POST['pageOptionB'];
					$pageOptionA_Dest = $_POST['pageOptionADest'];
					$pageOptionB_Dest = $_POST['pageOptionBDest'];
					$pageInteractionOption = $_POST['pageInteractionOption'];
					$pageReward = $_POST['pageReward'];
					$pagedata = $base->new_page($user, $pass, $storyTitle, $title, $content, $pageNumber, $pageInteraction, $pageEInteraction, $pageEInteractionAnswer, $pageMInteraction, $pageMInteractionAnswer, $pageHInteraction, $pageHInteractionAnswer, $pageJInteraction, $pageJInteractionAnswer, $pageOptionA, $pageOptionA_Dest, $pageOptionB, $pageOptionB_Dest, $pageInteractionOption, $pageReward, $pageFirst);
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