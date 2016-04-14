<?php

	include_once 'includes/base.php';
	//This is where the main service is stored
	$base = new GenericInfo();
	//Initialises the Story Class in the 'includes/base.php' file
	
	$header = isset($_REQUEST["action"]) ? $_REQUEST["action"]:"get";
	//Gets the header sent by the client so request type can be analysed
	
	switch($header)
	{
	
		//Checks the get action method
		case "get":
			if($_SERVER['REQUEST_METHOD']=='GET')
			{
				if(isset($_GET['method']))
				{
					
					$method = $_GET['method'];
					
					$update = $base->tile_update($method);
				}
				else
				{
					$update = $base->tile_update("onLoad");
				}
			}
			else
			{
				header("HTTP/1.1 400 Bad Request");
			}
			
			break;
	}

?>