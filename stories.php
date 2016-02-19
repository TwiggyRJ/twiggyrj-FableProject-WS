<?php
	
	include_once 'includes/base.php';
	$base = new Story();
	
	$header = isset($_REQUEST["action"]) ? $_REQUEST["action"]:"get";
	
	switch($header)
	{
		case "get":
			if(isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($_GET['story']))
			{
				$user = $_SERVER["PHP_AUTH_USER"];
				$pass = $_SERVER["PHP_AUTH_PW"];
				$title = $_POST['story'];
				
				if($_GET['story'] == "titleOnly")
				{
					$story = $base->get_stories_auth($user, $pass, $title, "titleOnly");
				}
				if($_GET['story'] == "allStoryData")
				{
					$story = $base->get_stories_auth($user, $pass, $_GET['story'], "all");
				}
			}
			elseif($_SERVER['REQUEST_METHOD']=='GET' && isset($_GET['story']))
			{
				if ($_GET['story'] == "all")
				{
					$story = $base->get_stories($_GET['story'], "all");
				}
				elseif ($_GET['story'] == "Fantasy Adventure" || $_GET['story'] == "Puzzle" || $_GET['story'] == "Magic")
				{
					$story = $base->get_stories($_GET['story'], "type");
				}
				elseif (isset($_GET['story']) && isset($_GET['special']) && isset($_GET['title']))
				{
					$story = $base->get_stories($_GET['story'], $_GET['special']);
				}
				elseif (is_numeric($_GET['story']))
				{
					$story = $base->get_stories($_GET['story'], "id");
				}
				else
				{
					$story = $base->get_stories($_GET['story'], "title");
				}
			}
			else
			{
				header("HTTP/1.1 400 Bad Request");
			}
			break;
			
			
		case "post":
		
			if($_SERVER['REQUEST_METHOD']=='POST')
			{
				if (isset($_SERVER['PHP_AUTH_USER']) && isset($_SERVER['PHP_AUTH_PW']) && isset($_POST['title']) && isset($_POST['description']) && isset($_POST['type']) && isset($_POST['image']))
				{
					
					$user = $_SERVER["PHP_AUTH_USER"];
					$pass = $_SERVER["PHP_AUTH_PW"];
					$title = $_POST['title'];
					$desc = $_POST['description'];
					$type = $_POST['type'];
					$image = $_POST['image'];
					$storydata = $base->new_story($title, $desc, $type, $user, $pass, $image);
				}
				else
				{
					header("HTTP/1.1 400 Bad Request");
				}
			}
			else
			{
				header("HTTP/1.1 400 Bad Request");
			}
			break;		
	}	
?>