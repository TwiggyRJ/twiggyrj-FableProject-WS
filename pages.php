<?php
	
	include_once 'includes/base.php';
	$base = new Story();
	
	$header = isset($_REQUEST["action"]) ? $_REQUEST["action"]:"get";
	
	switch($header)
	{
		case "get":
			if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($_GET['story'] && isset($_GET['page']))
			{
				$user = $_SERVER["PHP_AUTH_USER"];
				$pass = $_SERVER["PHP_AUTH_PW"];
				$title = $_POST['story'];
				
				if($_GET['story'] == "titleOnly")
				{
					$story = $base->get_pages_auth($user, $pass, $title, "titleOnly");
				}
				if($_GET['story'] == "allStoryData")
				{
					$story = $base->get_pages_auth($user, $pass, $_GET['story'], "all");
				}
			}
			elseif($_SERVER['REQUEST_METHOD']=='GET' && isset($_GET['story'] && isset($_GET['page']))
			{
				if (is_numeric($_GET['page']) && is_numeric($_GET['story']))
				{
					$story = $base->get_pages($_GET['page'], $_GET['story']);
				}
			}
			else
			{
				header("HTTP/1.1 400 Bad Request");
			}
			break;
			
	}	
?>