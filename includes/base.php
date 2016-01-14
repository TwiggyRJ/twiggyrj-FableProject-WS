<?php
header("Access-Control-Allow-Origin: kshatriya.co.uk");
// Table Of Contents
// 1.0: Config and none class functions
// 1.1: User Class for logining, registering and editing users
// 1.2: Base Functionality


// 1.0: Config and none class functions

// includes the password library for encrypting the password for storage on the SQL Database

date_default_timezone_set('Europe/London');

function connect_db(){

	return new PDO("mysql:host=localhost;dbname=fable_project", "kshatriy_ntd", "SCcR0KpU6IOw", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

}


// 1.1: User Class for logining, registering and editing users

class User
{
	
	public function reg_user($username, $password, $name, $email, $dob)
	{
		
		// Checks to see if the username variable only contains alpha numeric characters
		if (!ctype_alnum($username))
		{
			
			// error handling for not inputting valid charachters
			$username = "";
			$password = "";
			
			//sends a response informing the client of invalid input
			header("HTTP/1.1 400 Bad Request");
			
		}
			
		// connect to the PDO database
		$conn = connect_db();
		
		//Duplicates the password
		$encrypt = $password;
		
		//encrypts the password
		$encrypt = password_hash($password, PASSWORD_BCRYPT);
		
		//Checks to see if the username variable is not empty
		if($username != "")
		{
			//If it is not empty:
			
			// attempts the query and catches any errors
			try
			{
				
				// a prepared statement that should help prevent SQL Injections
				$query = $conn->prepare("INSERT INTO users (username, password, name, joined, dob, email) VALUES (:username, :password, :name, Now(), :dob, :email)");	
				$query->bindParam(":username", $username, PDO::PARAM_STR);
				$query->bindParam(":password", $encrypt, PDO::PARAM_STR);
				$query->bindParam(":name", $name, PDO::PARAM_STR);
				$query->bindParam(":dob", $dob, PDO::PARAM_STR);
				$query->bindParam(":email", $email, PDO::PARAM_STR);
				$query->execute();
				
				//If it works, informs the client the new field has been created
				header("HTTP/1.1 201 Created");
			
			}
			catch (PDOException $e)
			{
				echo 'Connection failed: ' . $e->getMessage();
				header("HTTP/1.1 400 Bad Request");
			}
		}
		else
		{
			//if it is empty then inform the client the request was invalid
			
			header("HTTP/1.1 400 Bad Request");
		}
		
	}
	
	public function login_user($username, $password)
	{ 
		
		// Checks to see if the username variable only contains alpha numeric characters
		if (!ctype_alnum($username))
		{
			
			// error handling for not inputting valid charachters
			$username = "";
			$password = "";
			
			//sends a response informing the client of invalid input
			header("HTTP/1.1 400 Bad Request");
			
		}
		
		// connect to the PDO database
		$conn = connect_db();
		
		//Created an array ready to store the user data into
		$arr = array();
		
		//Searchs the database for a username matching the username requested by the client
		$query = $conn->prepare("SELECT * from users WHERE username = :username");
		
		//binds the variables ready for the query to execute
		$query->bindParam(":username", $username, PDO::PARAM_STR);
		//executes the database query
		$query->execute();
		
		//verifies that there is a search with at least 1 result
		while($row = $query->fetch(PDO::FETCH_ASSOC))
		{
			//verifies if the password is the same as the encrypted stored in the database
			if (password_verify($password, $row["password"]))
			{
				// password was correct
				
				//Informs the client the request was successfull
				header("HTTP/1.1 200 OK");
				//Informs the client that the response will be a JSON object
				header ("Content-type: application/json");
				
				
				// collects user data, then prepares the data ready for transport
				
				$user_id = $row['ID'];
				$user_nm = $row['username'];
				$user_rnm = $row['name'];
				$user_jd = $row['joined'];
				$user_pw = $row['password'];
				$user_dob = $row['dob'];
				$user_av = $row['avatar'];
				$user_em = $row['email'];
				$user_ws = $row['website'];
				$user_au = $row['author'];
				
				//Gets the number of stories the user has written
				$stories = $conn->query("SELECT COUNT(owner) FROM stories WHERE owner = '$user_id'");
				
				//enables the variable to be used inside and outside the while loop
				$user_st = "";
				
				//only adds a value to the variable if there is a valid value to add
				while($row_stories = $stories->fetch())
				{
					$user_st = $row_stories['COUNT(owner)'];
				}
				
				//adds the user data to an array
				$user_array = array("ID" => $user_id, "username" => $user_nm, "password" => $user_pw, "name" => $user_rnm, "DOB" => $user_dob, "avatar" => $user_av, "Email" => $user_em, "joined" => $user_jd, "website" => $user_ws, "author" => $user_au, "stories" => $user_st);
				
				//adds the user_array to the array created earlier
				array_push($arr, $user_array);
				
				//pushes the array out to the client
				echo json_encode($arr);
			
			}
			else
			{
				//If the password was incorrect inform the client the user is unauthorised
				header("HTTP/1.1 401 Unauthorized");
			}
		}
	}
}

// 1.2: Base Functionality

class Story
{

	public function new_story($title, $desc, $type, $username, $password, $image)
	{
		
		$conn = connect_db();
		
		//Check if the user is real and that they are authorised to add content
		
		$queryCheck = $conn->prepare("SELECT * from users WHERE username = :username");
		
		$queryCheck->bindParam(":username", $username, PDO::PARAM_STR);
		$queryCheck->execute();
		
		while($rowCheck = $queryCheck->fetch(PDO::FETCH_ASSOC))
		{
			if (password_verify($password, $rowCheck["password"]))
			{
				$user_id = $rowCheck['ID'];
				$user_au = $rowCheck['author'];
			
				// changes the session ID to prevent cross site scripting
				session_regenerate_id(); 
				
				// connect to the PDO database
				$rec = 0;
				
				// attempts the query and catches any errors
				if($user_au == 1)
				{
					try
					{
						
						// a prepared statement that should help prevent SQL Injections
						$query = $conn->prepare("INSERT INTO stories (title, description, type, owner, image, recommended) VALUES (:title, :description, :type, :owner, :image, :rec)");	
						$query->bindParam(":title", $title, PDO::PARAM_STR);
						$query->bindParam(":description", $desc, PDO::PARAM_STR);
						$query->bindParam(":type", $type, PDO::PARAM_STR);
						$query->bindParam(":owner", $user_id, PDO::PARAM_STR);
						$query->bindParam(":image", $image, PDO::PARAM_STR);
						$query->bindParam(":rec", $rec, PDO::PARAM_STR);
						$query->execute();
						
						header("HTTP/1.1 201 Created");
					
					}
					catch (PDOException $e)
					{
						echo 'Connection failed: ' . $e->getMessage();
						header("HTTP/1.1 400 Bad Request");
					}
				}
				else
				{
					header("HTTP/1.1 403 Unauthorized");
				}
			}
		}
	}

	public function get_stories($search, $method)
	{
		
		$conn = connect_db();
		$arr = array();
		
			
		if($method=="title")
		{
			$results = $conn->query("SELECT * from stories where title like '$search' ORDER BY ID");
		}
		elseif ($method=="type")
		{
			$results = $conn->query("SELECT * from stories where type = '$search' ORDER BY ID");
		}
		elseif (is_numeric($method))
		{
			$results = $conn->query("SELECT * from stories where title like '$search' AND owner = '$method' ORDER BY ID DESC");
		}
		elseif ($method=="id")
		{
			$results = $conn->query("SELECT * from stories where ID ='$search'");
		}
		elseif ($method=="all")
		{
			$results = $conn->query("SELECT * from stories ORDER BY ID");
		}
		
		$rev_count = 0;
		
		if ($results->rowCount() > 0)
		{
			
			header ("Content-type: application/json");
			header("HTTP/1.1 200 OK");
			
			while($row = $results->fetch()){
			
				$id = $row['ID'];
				$title = $row['title'];
				$desc = $row['description'];
				$type = $row['type'];
				$owner = $row['owner'];
				$rec = $row['recommended'];
				$img = $row['image'];
				$ownerUserName = "";
				
				$queryCheckName = $conn->prepare("SELECT * from users WHERE ID = :id");
			
				$queryCheckName->bindParam(":id", $owner, PDO::PARAM_STR);
				$queryCheckName->execute();
				
				while($rowCheckName = $queryCheckName->fetch(PDO::FETCH_ASSOC))
				{
					$ownerUserName = $rowCheckName['username'];
				}
				
				// collects story data, then prepares the data ready for transport
				
				$stories_array = array("ID" => $id, "title" => $title, "description" => $desc, "type" => $type, "ownerID" => $owner, "ownerName" => $ownerUserName, "image" => $img, "recommended" => $rec);
					
				array_push($arr, $stories_array);
					
				echo json_encode($arr);
				
			}
		}
		else
		{
			header("HTTP/1.1 404 Not Found");
		}
		
	}

}

?>