<?php
	
	include_once 'includes/base.php';
	//$base = new Story();
	
	$header = isset($_REQUEST["action"]) ? $_REQUEST["action"]:"get";
	
	switch($header)
	{
		case "get":
			if($_SERVER['REQUEST_METHOD']=='GET')
			{
				if ($_GET['story'] == "all")
				{
					//$story = $base->get_stories($_GET['story'], "all");
				}
				elseif ($_GET['story'] == "Bar" || $_GET['story'] == "Cafe" || $_GET['story'] == "cafe" || $_GET['story'] == "Café" || $_GET['story'] == "Karoake" || $_GET['story'] == "Karoake Bar" || $_GET['story'] == "Pub" || $_GET['story'] == "Restaurant")
				{
					//$story = $base->get_stories($_GET['story'], "type");
				}
				elseif ($_GET['story'] && $_GET['special'])
				{
					//$story = $base->get_stories($_GET['story'], $_GET['special']);
				}
				elseif (is_numeric($_GET['story']))
				{
					//$story = $base->get_stories($_GET['story'], "id");
				}
				else
				{
					//$story = $base->get_stories($_GET['story'], "name");
				}
			}
			else
			{
				header("HTTP/1.1 400 Bad Request");
			}
			break;
			
	}
?>