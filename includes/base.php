<?php
header("Access-Control-Allow-Origin: kshatriya.co.uk");
// Table Of Contents
// 1.0: Config and none class functions
// 1.1: User Class for logining, registering and editing users
// 1.2: Game Date
// 1.3: Generic App Wide Info


// 1.0: Config and none class functions

date_default_timezone_set('Europe/London');

function connect_db(){

	return new PDO("mysql:host=localhost;dbname=fable_project", "user", "pass", array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));

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
	
	public function update_user($username, $password, $name, $email, $website, $avatar, $newPass)
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
		$encrypt = $newPass;
		
		//encrypts the password
		$encrypt = password_hash($newPass, PASSWORD_BCRYPT);
		
		//Checks to see if the username variable is not empty
		if($username != "")
		{
			//If it is not empty:
			
			//Searchs the database for a username matching the username requested by the client
			$queryCheck = $conn->prepare("SELECT * from users WHERE username = :username");
			
			//binds the variables ready for the query to execute
			$queryCheck->bindParam(":username", $username, PDO::PARAM_STR);
			//executes the database query
			$queryCheck->execute();
			
			//verifies that there is a search with at least 1 result
			while($row = $queryCheck->fetch(PDO::FETCH_ASSOC))
			{
				//verifies if the password is the same as the encrypted stored in the database
				if (password_verify($password, $row["password"]))
				{
					// password was correct
					
					// attempts the query and catches any errors
					try
					{
						
						// a prepared statement that should help prevent SQL Injections
						$query = $conn->prepare("UPDATE users SET password = :password, name = :name, email = :email, avatar = :avatar, website = :website WHERE username = :username");	
						$query->bindParam(":username", $username, PDO::PARAM_STR);
						$query->bindParam(":password", $encrypt, PDO::PARAM_STR);
						$query->bindParam(":name", $name, PDO::PARAM_STR);
						$query->bindParam(":email", $email, PDO::PARAM_STR);
						$query->bindParam(":avatar", $avatar, PDO::PARAM_STR);
						$query->bindParam(":website", $website, PDO::PARAM_STR);
						$query->execute();
						
						//If it works, informs the client the new field has been created
						header("HTTP/1.1 200 OK");
					
					}
					catch (PDOException $e)
					{
						echo 'Connection failed: ' . $e->getMessage();
						header("HTTP/1.1 400 Bad Request");
					}
				
				}
				else
				{
					//If the password was incorrect inform the client the user is unauthorised
					header("HTTP/1.1 401 Unauthorized");
				}
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
				$user_ad = $row['admin'];
				
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
				$user_array = array("ID" => $user_id, "username" => $user_nm, "password" => $user_pw, "name" => $user_rnm, "DOB" => $user_dob, "avatar" => $user_av, "Email" => $user_em, "joined" => $user_jd, "website" => $user_ws, "admin" => $user_ad,"author" => $user_au, "stories" => $user_st);
				
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

// 1.2: Game Data

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
						$visible = 1;
						
						// a prepared statement that should help prevent SQL Injections
						$query = $conn->prepare("INSERT INTO stories (title, description, type, owner, image, recommended, visible) VALUES (:title, :description, :type, :owner, :image, :rec, :visible)");	
						$query->bindParam(":title", $title, PDO::PARAM_STR);
						$query->bindParam(":description", $desc, PDO::PARAM_STR);
						$query->bindParam(":type", $type, PDO::PARAM_STR);
						$query->bindParam(":owner", $user_id, PDO::PARAM_STR);
						$query->bindParam(":image", $image, PDO::PARAM_STR);
						$query->bindParam(":rec", $rec, PDO::PARAM_STR);
						$query->bindParam(":visible", $visible, PDO::PARAM_STR);
						$query->execute();
						
						header("HTTP/1.1 201 Created");
					
					}
					catch (PDOException $e)
					{
						//echo 'Connection failed: ' . $e->getMessage();
						header("HTTP/1.1 400 Bad Request");
					}
				}
				else
				{
					try
					{
						$visible = 0;
						
						// a prepared statement that should help prevent SQL Injections
						$query = $conn->prepare("INSERT INTO stories (title, description, type, owner, image, recommended, visible) VALUES (:title, :description, :type, :owner, :image, :rec, :visible)");	
						$query->bindParam(":title", $title, PDO::PARAM_STR);
						$query->bindParam(":description", $desc, PDO::PARAM_STR);
						$query->bindParam(":type", $type, PDO::PARAM_STR);
						$query->bindParam(":owner", $user_id, PDO::PARAM_STR);
						$query->bindParam(":image", $image, PDO::PARAM_STR);
						$query->bindParam(":rec", $rec, PDO::PARAM_STR);
						$query->bindParam(":visible", $visible, PDO::PARAM_STR);
						$query->execute();
						
						header("HTTP/1.1 201 Created");
					
					}
					catch (PDOException $e)
					{
						header("HTTP/1.1 400 Bad Request");
					}
				}
			}
		}
	}
	
	public function new_page($username, $password, $story, $title, $content, $content_2, $number, $question, $type, $easy_interaction, $easy_interaction_answer, $medium_interaction, $medium_interaction_answer, $hard_interaction, $hard_interaction_answer, $humour_interaction, $humour_interaction_answer, $option1, $option1_Dest, $option2, $option2_Dest, $optionSpecial, $optionFailure, $reward, $first)
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
				try
					{
						//enables the variable to be used inside and outside the while loop and if statements
							
						$easyIntID = "";
						$mediumIntID = "";
						$hardIntID = "";
						$humourIntID = "";
						$difficulty = "";

						if($easy_interaction != "" && $easy_interaction_answer != "")
						{

							$difficulty = "Easy";

							$query = $conn->prepare("INSERT INTO interactions (story, page, type, interaction, answer, difficulty, destination, failure) VALUES (:story, :page, :type, :interaction, :answer, :difficulty)");	
							$query->bindParam(":story", $story, PDO::PARAM_STR);
							$query->bindParam(":page", $number, PDO::PARAM_STR);
							$query->bindParam(":type", $type, PDO::PARAM_STR);
							$query->bindParam(":interaction", htmlspecialchars($easy_interaction), PDO::PARAM_STR);
							$query->bindParam(":answer", $easy_interaction_answer, PDO::PARAM_STR);
							$query->bindParam(":difficulty", $difficulty, PDO::PARAM_STR);
							$query->execute();
							
							$interactions = $conn->query("SELECT ID FROM interactions WHERE page = '$number' AND difficulty = 'Easy' AND interaction = '$easy_interaction'");
							
							//only adds a value to the variable if there is a valid value to add
							while($row_inters = $interactions->fetch())
							{
								$easyIntID = $row_inters['ID'];
							}
						}
						
						
						if($medium_interaction != "" && $medium_interaction_answer != "")
						{

							$difficulty = "Normal";

							$query = $conn->prepare("INSERT INTO interactions (story, page, type, interaction, answer, difficulty, destination, failure) VALUES (:story, :page, :type, :interaction, :answer, :difficulty)");
							$query->bindParam(":story", $story, PDO::PARAM_STR);
							$query->bindParam(":page", $number, PDO::PARAM_STR);
							$query->bindParam(":type", $type, PDO::PARAM_STR);
							$query->bindParam(":interaction", $medium_interaction, PDO::PARAM_STR);
							$query->bindParam(":answer", $medium_interaction_answer, PDO::PARAM_STR);
							$query->bindParam(":difficulty", $difficulty, PDO::PARAM_STR);
							$query->bindParam(":destination", $optionSpecial, PDO::PARAM_STR);
							$query->bindParam(":failure", $optionFailure, PDO::PARAM_STR);
							$query->execute();
							
							$interactions = $conn->query("SELECT ID FROM interactions WHERE page = '$number' AND difficulty = 'Normal' AND interaction = '$medium_interaction'");
							
							//only adds a value to the variable if there is a valid value to add
							while($row_inters = $interactions->fetch())
							{
								$mediumIntID = $row_inters['ID'];
							}
						}
						
						
						if($hard_interaction != "" && $hard_interaction_answer != "")
						{

							$difficulty = "Hard";

							$query = $conn->prepare("INSERT INTO interactions (story, page, type, interaction, answer, difficulty, destination, failure) VALUES (:story, :page, :type, :interaction, :answer, :difficulty)");
							$query->bindParam(":story", $story, PDO::PARAM_STR);
							$query->bindParam(":page", $number, PDO::PARAM_STR);
							$query->bindParam(":type", $type, PDO::PARAM_STR);
							$query->bindParam(":interaction", $hard_interaction, PDO::PARAM_STR);
							$query->bindParam(":answer", $hard_interaction_answer, PDO::PARAM_STR);
							$query->bindParam(":difficulty", $difficulty, PDO::PARAM_STR);
							$query->execute();
							
							$interactions = $conn->query("SELECT ID FROM interactions WHERE page = '$number' AND difficulty = 'Hard' AND interaction = '$hard_interaction'");
							
							//only adds a value to the variable if there is a valid value to add
							while($row_inters = $interactions->fetch())
							{
								$hardIntID = $row_inters['ID'];
							}
						}
						
						
						if($humour_interaction != "" && $humour_interaction_answer != "")
						{

							$difficulty = "Humour";

							$query = $conn->prepare("INSERT INTO interactions (story, page, type, interaction, answer, difficulty, destination, failure) VALUES (:story, :page, :type, :interaction, :answer, :difficulty)");
							$query->bindParam(":story", $story, PDO::PARAM_STR);
							$query->bindParam(":page", $number, PDO::PARAM_STR);
							$query->bindParam(":type", $type, PDO::PARAM_STR);
							$query->bindParam(":interaction", $humour_interaction, PDO::PARAM_STR);
							$query->bindParam(":answer", $humour_interaction_answer, PDO::PARAM_STR);
							$query->bindParam(":difficulty", $difficulty, PDO::PARAM_STR);
							$query->execute();
							
							$interactions = $conn->query("SELECT ID FROM interactions WHERE page = '$number' AND difficulty = 'Humour' AND interaction = '$humour_interaction'");
							
							//only adds a value to the variable if there is a valid value to add
							while($row_inters = $interactions->fetch())
							{
								$humourIntID = $row_inters['ID'];
							}
						}
						
						$content = addslashes($content);
						$content_2 = addslashes($content_2);
						$question = addslashes($question);
						
						// a prepared statement that should help prevent SQL Injections
						$query = $conn->prepare("INSERT INTO page (story, title, content, second_content, page_number, interaction, easy_interaction, medium_interaction, hard_interaction, humour_interaction, option1, option1_Dest, option2, option2_Dest, optionSpecial, optionFailure, goodies, first) VALUES (:story, :title, :content, :content_2, :number, :question, :easy_interaction, :medium_interaction, :hard_interaction, :humour_interaction, :option1, :option1Dest, :option2, :option2Dest, :optionSpecial, :optionFailure, :goodies, :first)");	
						$query->bindParam(":story", $story, PDO::PARAM_STR);
						$query->bindParam(":title", $title, PDO::PARAM_STR);
						$query->bindParam(":content", $content, PDO::PARAM_STR);
						$query->bindParam(":content_2", $content_2, PDO::PARAM_STR);
						$query->bindParam(":number", $number, PDO::PARAM_STR);
						$query->bindParam(":question", $question, PDO::PARAM_STR);
						$query->bindParam(":easy_interaction", $easyIntID, PDO::PARAM_STR);
						$query->bindParam(":medium_interaction", $mediumIntID, PDO::PARAM_STR);
						$query->bindParam(":hard_interaction", $hardIntID, PDO::PARAM_STR);
						$query->bindParam(":humour_interaction", $humourIntID, PDO::PARAM_STR);
						$query->bindParam(":option1", $option1, PDO::PARAM_STR);
						$query->bindParam(":option1Dest", $option1_Dest, PDO::PARAM_STR);
						$query->bindParam(":option2", $option2, PDO::PARAM_STR);
						$query->bindParam(":option2Dest", $option2_Dest, PDO::PARAM_STR);
						$query->bindParam(":optionSpecial", $optionSpecial, PDO::PARAM_STR);
						$query->bindParam(":optionFailure", $optionFailure, PDO::PARAM_STR);
						$query->bindParam(":goodies", $reward, PDO::PARAM_STR);
						$query->bindParam(":first", $first, PDO::PARAM_STR);
						$query->execute();
						
						header("HTTP/1.1 201 Created");
					
					}
					catch (PDOException $e)
					{
						header("HTTP/1.1 400 Bad Request");
					}
			}
			else
			{
				header("HTTP/1.1 403 Unauthorized");
			}
			
		}
	}



	public function update_page($username, $password, $story, $title, $content, $content_2, $number, $question, $type, $easy_interaction, $easy_interaction_answer, $medium_interaction, $medium_interaction_answer, $hard_interaction, $hard_interaction_answer, $humour_interaction, $humour_interaction_answer, $option1, $option1_Dest, $option2, $option2_Dest, $optionSpecial, $optionFailure, $reward, $first)
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
				try
					{
						//enables the variable to be used inside and outside the while loop and if statements
							
						$easyIntID = "";
						$mediumIntID = "";
						$hardIntID = "";
						$humourIntID = "";
						$difficulty = "";

						if($easy_interaction != "" && $easy_interaction_answer != "")
						{

							$difficulty = "Easy";

							$query = $conn->prepare("INSERT INTO interactions (story, page, type, interaction, answer, difficulty, destination, failure) VALUES (:story, :page, :type, :interaction, :answer, :difficulty)");	
							$query->bindParam(":story", $story, PDO::PARAM_STR);
							$query->bindParam(":page", $number, PDO::PARAM_STR);
							$query->bindParam(":type", $type, PDO::PARAM_STR);
							$query->bindParam(":interaction", htmlspecialchars($easy_interaction), PDO::PARAM_STR);
							$query->bindParam(":answer", $easy_interaction_answer, PDO::PARAM_STR);
							$query->bindParam(":difficulty", $difficulty, PDO::PARAM_STR);
							$query->execute();
							
							$interactions = $conn->query("SELECT ID FROM interactions WHERE page = '$number' AND difficulty = 'Easy' AND interaction = '$easy_interaction'");
							
							//only adds a value to the variable if there is a valid value to add
							while($row_inters = $interactions->fetch())
							{
								$easyIntID = $row_inters['ID'];
							}
						}
						
						
						if($medium_interaction != "" && $medium_interaction_answer != "")
						{

							$difficulty = "Normal";

							$query = $conn->prepare("INSERT INTO interactions (story, page, type, interaction, answer, difficulty, destination, failure) VALUES (:story, :page, :type, :interaction, :answer, :difficulty)");
							$query->bindParam(":story", $story, PDO::PARAM_STR);
							$query->bindParam(":page", $number, PDO::PARAM_STR);
							$query->bindParam(":type", $type, PDO::PARAM_STR);
							$query->bindParam(":interaction", $medium_interaction, PDO::PARAM_STR);
							$query->bindParam(":answer", $medium_interaction_answer, PDO::PARAM_STR);
							$query->bindParam(":difficulty", $difficulty, PDO::PARAM_STR);
							$query->bindParam(":destination", $optionSpecial, PDO::PARAM_STR);
							$query->bindParam(":failure", $optionFailure, PDO::PARAM_STR);
							$query->execute();
							
							$interactions = $conn->query("SELECT ID FROM interactions WHERE page = '$number' AND difficulty = 'Normal' AND interaction = '$medium_interaction'");
							
							//only adds a value to the variable if there is a valid value to add
							while($row_inters = $interactions->fetch())
							{
								$mediumIntID = $row_inters['ID'];
							}
						}
						
						
						if($hard_interaction != "" && $hard_interaction_answer != "")
						{

							$difficulty = "Hard";

							$query = $conn->prepare("INSERT INTO interactions (story, page, type, interaction, answer, difficulty, destination, failure) VALUES (:story, :page, :type, :interaction, :answer, :difficulty)");
							$query->bindParam(":story", $story, PDO::PARAM_STR);
							$query->bindParam(":page", $number, PDO::PARAM_STR);
							$query->bindParam(":type", $type, PDO::PARAM_STR);
							$query->bindParam(":interaction", $hard_interaction, PDO::PARAM_STR);
							$query->bindParam(":answer", $hard_interaction_answer, PDO::PARAM_STR);
							$query->bindParam(":difficulty", $difficulty, PDO::PARAM_STR);
							$query->execute();
							
							$interactions = $conn->query("SELECT ID FROM interactions WHERE page = '$number' AND difficulty = 'Hard' AND interaction = '$hard_interaction'");
							
							//only adds a value to the variable if there is a valid value to add
							while($row_inters = $interactions->fetch())
							{
								$hardIntID = $row_inters['ID'];
							}
						}
						
						
						if($humour_interaction != "" && $humour_interaction_answer != "")
						{

							$difficulty = "Humour";

							$query = $conn->prepare("INSERT INTO interactions (story, page, type, interaction, answer, difficulty, destination, failure) VALUES (:story, :page, :type, :interaction, :answer, :difficulty)");
							$query->bindParam(":story", $story, PDO::PARAM_STR);
							$query->bindParam(":page", $number, PDO::PARAM_STR);
							$query->bindParam(":type", $type, PDO::PARAM_STR);
							$query->bindParam(":interaction", $humour_interaction, PDO::PARAM_STR);
							$query->bindParam(":answer", $humour_interaction_answer, PDO::PARAM_STR);
							$query->bindParam(":difficulty", $difficulty, PDO::PARAM_STR);
							$query->execute();
							
							$interactions = $conn->query("SELECT ID FROM interactions WHERE page = '$number' AND difficulty = 'Humour' AND interaction = '$humour_interaction'");
							
							//only adds a value to the variable if there is a valid value to add
							while($row_inters = $interactions->fetch())
							{
								$humourIntID = $row_inters['ID'];
							}
						}
						
						$content = addslashes($content);
						$content_2 = addslashes($content_2);
						$question = addslashes($question);
						
						// a prepared statement that should help prevent SQL Injections
						$query = $conn->prepare("UPDATE page (story, title, content, second_content, page_number, interaction, easy_interaction, medium_interaction, hard_interaction, humour_interaction, option1, option1_Dest, option2, option2_Dest, optionSpecial, optionFailure, goodies, first) VALUES (:story, :title, :content, :content_2, :number, :question, :easy_interaction, :medium_interaction, :hard_interaction, :humour_interaction, :option1, :option1Dest, :option2, :option2Dest, :optionSpecial, :optionFailure, :goodies, :first) WHERE story = :story AND number = :number");	
						$query->bindParam(":story", $story, PDO::PARAM_STR);
						$query->bindParam(":title", $title, PDO::PARAM_STR);
						$query->bindParam(":content", $content, PDO::PARAM_STR);
						$query->bindParam(":content_2", $content_2, PDO::PARAM_STR);
						$query->bindParam(":number", $number, PDO::PARAM_STR);
						$query->bindParam(":question", $question, PDO::PARAM_STR);
						$query->bindParam(":easy_interaction", $easyIntID, PDO::PARAM_STR);
						$query->bindParam(":medium_interaction", $mediumIntID, PDO::PARAM_STR);
						$query->bindParam(":hard_interaction", $hardIntID, PDO::PARAM_STR);
						$query->bindParam(":humour_interaction", $humourIntID, PDO::PARAM_STR);
						$query->bindParam(":option1", $option1, PDO::PARAM_STR);
						$query->bindParam(":option1Dest", $option1_Dest, PDO::PARAM_STR);
						$query->bindParam(":option2", $option2, PDO::PARAM_STR);
						$query->bindParam(":option2Dest", $option2_Dest, PDO::PARAM_STR);
						$query->bindParam(":optionSpecial", $optionSpecial, PDO::PARAM_STR);
						$query->bindParam(":optionFailure", $optionFailure, PDO::PARAM_STR);
						$query->bindParam(":goodies", $reward, PDO::PARAM_STR);
						$query->bindParam(":first", $first, PDO::PARAM_STR);
						$query->execute();
						
						header("HTTP/1.1 201 Created");
					
					}
					catch (PDOException $e)
					{
						header("HTTP/1.1 400 Bad Request");
					}
			}
			else
			{
				header("HTTP/1.1 403 Unauthorized");
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
				$cre = $row['created'];
				$vis = $row['visible'];
				$ownerUserName = "";
				
				$queryCheckName = $conn->prepare("SELECT * from users WHERE ID = :id");
			
				$queryCheckName->bindParam(":id", $owner, PDO::PARAM_STR);
				$queryCheckName->execute();
				
				while($rowCheckName = $queryCheckName->fetch(PDO::FETCH_ASSOC))
				{
					$ownerUserName = $rowCheckName['username'];
				}
				
				// collects story data, then prepares the data ready for transport
				
				$stories_array = array("ID" => $id, "title" => $title, "description" => $desc, "type" => $type, "ownerID" => $owner, "ownerName" => $ownerUserName, "image" => $img, "recommended" => $rec, "created" => $cre, "visible" => $vis);
					
				array_push($arr, $stories_array);
					
				echo json_encode($arr);
				
			}
		}
		else
		{
			header("HTTP/1.1 404 Not Found");
		}
		
	}
	
	public function get_stories_auth($username, $password, $search, $method)
	{
		
		$conn = connect_db();
		$arr = array();
		
		$queryCheck = $conn->prepare("SELECT * from users WHERE username = :username");
		
		$queryCheck->bindParam(":username", $username, PDO::PARAM_STR);
		$queryCheck->execute();
		
		while($rowCheck = $queryCheck->fetch(PDO::FETCH_ASSOC))
		{
			if (password_verify($password, $rowCheck["password"]))
			{
				
				$user_id = $rowCheck['ID'];
				
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
				elseif ($method=="titleOnly")
				{
					$results = $conn->query("SELECT title from stories where owner = '$user_id' ORDER BY ID");
				}
				elseif ($method=="allStoryData")
				{
					$results = $conn->query("SELECT * from stories where owner = '$user_id' ORDER BY ID");
				}
				
				$rev_count = 0;
				
				if ($results->rowCount() > 0)
				{
					
					header ("Content-type: application/json");
					header("HTTP/1.1 200 OK");
					
					while($row = $results->fetch()){
						
						if($method == "titleOnly")
						{
							$title = $row['title'];
						
							// collects story data, then prepares the data ready for transport
							
							$stories_array = array("title" => $title);
								
							array_push($arr, $stories_array);
						}
						else
						{
							$id = $row['ID'];
							$title = $row['title'];
							$desc = $row['description'];
							$type = $row['type'];
							$owner = $row['owner'];
							$rec = $row['recommended'];
							$img = $row['image'];
							
							// collects story data, then prepares the data ready for transport
							
							$stories_array = array("ID" => $id, "title" => $title, "description" => $desc, "type" => $type, "ownerID" => $owner, "ownerName" => $username, "image" => $img, "recommended" => $rec);
								
							array_push($arr, $stories_array);
						}
						echo json_encode($arr);
					}
				}
				else
				{
					header("HTTP/1.1 404 Not Found");
				}
			}
			else
			{
				header("HTTP/1.1 403 Unauthorized");
			}
		}
	}

	public function get_pages($page, $story)
	{

		$conn = connect_db();
		$arr = array();


		//If the client is requesting the first page of the book
		if($page == "First")
		{
			$results = $conn->query("SELECT * from page where story ='$story' AND first = TRUE ORDER BY ID");
		}
		elseif($page == "all")
		{
			$results = $conn->query("SELECT * from page where story ='$story' ORDER BY ID");
		}
		//If the client is requesting a specific page of the book
		else
		{
			$results = $conn->query("SELECT * from page where story ='$story' AND page_number = '$page' ORDER BY ID");
		}
		

		//Checks that we have some rows
		if ($results->rowCount() > 0)
		{
			
			//Tell the client to expect JSON
			header ("Content-type: application/json");
			//Set the header that the request was successfull
			header("HTTP/1.1 200 OK");
			
			while($row = $results->fetch()){
			
				//Get every row from the page table
				$id = $row['ID'];
				$story = $row['story'];
				$title = $row['title'];
				$content = stripslashes($row['content']);
				$second_content = stripslashes($row['second_content']);
				$number = $row['page_number'];
				$interaction = stripslashes($row['interaction']);
				$easy_interaction = $row['easy_interaction'];
				$easy_interaction_answer = "";
				$interaction_destination = "";
				$interaction_failure = "";
				$interaction_type = "";
				$medium_interaction = $row['medium_interaction'];
				$medium_interaction_answer = "";
				$hard_interaction = $row['hard_interaction'];
				$hard_interaction_answer = "";
				$humour_interaction = $row['humour_interaction'];
				$humour_interaction_answer = "";
				$option1 = $row['option1'];
				$option1_Dest = $row['option1_Dest'];
				$option2 = $row['option2'];
				$option2_Dest = $row['option2_Dest'];
				$optionSpecialSuccess = $row['optionSpecial'];
				$optionSpecialFailure = $row['optionFailure'];
				$goodies = $row['goodies'];
				$first = $row['first'];

				//Gets the easy difficulty interations from the database
				$interactionE = $conn->query("SELECT * FROM interactions WHERE ID = '$easy_interaction'");
				
				//picks the data
				while($row_intE = $interactionE->fetch())
				{
					$easy_interaction = $row_intE['interaction'];
					$easy_interaction_answer = $row_intE['answer'];
					$interaction_type = $row_intE['type'];
				}

				//Gets the medium difficulty interations from the database
				$interactionM = $conn->query("SELECT * FROM interactions WHERE ID = '$medium_interaction'");
				
				//picks the data
				while($row_intM = $interactionM->fetch())
				{
					$medium_interaction = $row_intM['interaction'];
					$medium_interaction_answer = $row_intM['answer'];
				}

				//Gets the hard difficulty interations from the database
				$interactionH = $conn->query("SELECT * FROM interactions WHERE ID = '$hard_interaction'");
				
				//picks the data
				while($row_intH = $interactionH->fetch())
				{
					$hard_interaction = $row_intH['interaction'];
					$hard_interaction_answer = $row_intH['answer'];
				}

				//Gets the humour difficulty interations from the database
				$interactionHu = $conn->query("SELECT * FROM interactions WHERE ID = '$humour_interaction'");
				
				//picks the data
				while($row_intHu = $interactionHu->fetch())
				{
					$humour_interaction = $row_intHu['interaction'];
					$humour_interaction_answer = $row_intHu['answer'];
				}

				//Makes an array with all the picked data ready to be converted to JSON
				$pages_array = array("ID" => $id, "story" => $story, "title" => $title, "content" => $content, "content_2" => $second_content, "number" => $number, "interaction" => $interaction, "interaction_type" => $interaction_type, "easy_interaction" => $easy_interaction, "easy_interaction_answer" => $easy_interaction_answer, "medium_interaction" => $medium_interaction, "medium_interaction_answer" => $medium_interaction_answer, "hard_interaction" => $hard_interaction, "hard_interaction_answer" => $hard_interaction_answer, "humour_interaction" => $humour_interaction, "humour_interaction_answer" => $humour_interaction_answer, "option1" => $option1, "option1_Dest" => $option1_Dest, "option2" => $option2, "option2_Dest" => $option2_Dest, "optionSpecialSuccess" => $optionSpecialSuccess, "optionSpecialFailure" => $optionSpecialFailure, "goodies" => $goodies, "first" => $first);

				if($page == "all")
				{
					$pages_array = array("number" => $number);
				}
				
				array_push($arr, $pages_array);
				
			}

			//Converts to JSON
			echo json_encode($arr);
		}
		//If we don't tell the client Nothing was found
		else
		{
			header("HTTP/1.1 404 Not Found");
		}

	}
	
}

class GenericInfo
{
	
	public function get_updates($method)
	{
		$conn = connect_db();
		$arr = array();
		
		if($method == "latest")
		{
			$query = $conn->prepare("SELECT * from updates ORDER BY date DESC LIMIT 1");
			$query->execute();
			
			while($row = $query->fetch(PDO::FETCH_ASSOC))
			{
				header ("Content-type: application/json");
				header("HTTP/1.1 200 OK");
				
				$id = $row['ID'];
				$version = $row['version'];
				$content = $row['content'];
				$content_2 = $row['content_2'];
				$content_3 = $row['content_3'];
				$content_4 = $row['content_4'];
				$content_5 = $row['content_5'];
				$content_6 = $row['content_6'];
				$about = $row['about'];
				$date = $row['date'];

				// collects story data, then prepares the data ready for transport
				
				$updates_array = array("ID" => $id, "version" => $version, "content" => $content, "content_2" => $content_2, "content_3" => $content_3, "content_4" => $content_4, "content_5" => $content_5, "content_6" => $content_6, "about" => $about, "date" => $date);
				
				array_push($arr, $updates_array);
				
				echo json_encode($arr);
			}
		}
		elseif($method == "privacy")
		{
			$query = $conn->prepare("SELECT * from policies WHERE type = 'privacy' ORDER BY updated DESC LIMIT 1");
			$query->execute();
			
			while($row = $query->fetch(PDO::FETCH_ASSOC))
			{
				header ("Content-type: application/json");
				header("HTTP/1.1 200 OK");
				
				$id = $row['ID'];
				$title = $row['title'];
				$updated = $row['updated'];
				$content = $row['content'];
				$type = $row['type'];

				// collects story data, then prepares the data ready for transport
				
				$privacy_array = array("ID" => $id, "title" => $title, "updated" => $updated, "content" => $content,  "type" => $type);
				
				array_push($arr, $privacy_array);
				
				echo json_encode($arr);
			}
		}
		else
		{
			$query = $conn->prepare("SELECT * from updates WHERE version = $method ORDER BY date DESC LIMIT 1");
			$query->execute();
			
			while($row = $query->fetch(PDO::FETCH_ASSOC))
			{
				header ("Content-type: application/json");
				header("HTTP/1.1 200 OK");
				
				$id = $row['ID'];
				$version = $row['version'];
				$content = $row['content'];
				$content_2 = $row['content_2'];
				$content_3 = $row['content_3'];
				$content_4 = $row['content_4'];
				$content_5 = $row['content_5'];
				$content_6 = $row['content_6'];
				$about = $row['about'];
				$date = $row['date'];

				// collects story data, then prepares the data ready for transport
				
				$updates_array = array("ID" => $id, "version" => $version, "content" => $content, "content_2" => $content_2, "content_3" => $content_3, "content_4" => $content_4, "content_5" => $content_5, "content_6" => $content_6, "about" => $about, "date" => $date);
				
				array_push($arr, $updates_array);
				
				echo json_encode($arr);
			}
		}
	}

	public function post_updates($username, $password, $version, $about, $date, $content, $content_2, $content_3, $content_4, $content_5, $content_6)
	{
		$conn = connect_db();

		$queryCheck = $conn->prepare("SELECT * from users WHERE username = :username");
		
		$queryCheck->bindParam(":username", $username, PDO::PARAM_STR);
		$queryCheck->execute();
		
		while($rowCheck = $queryCheck->fetch(PDO::FETCH_ASSOC))
		{
			if (password_verify($password, $rowCheck["password"]))
			{
				if($rowCheck['admin'] == TRUE)
				{
					try
					{

						$query = $conn->prepare("INSERT INTO updates (version, content, content_2, content_3, content_4, content_5, content_6, about, date) VALUES (:version, :content, :content_2, :content_3, :content_4, :content_5, :content_6, :about, :date)");
						$query->bindParam(":version", $version, PDO::PARAM_STR);
						$query->bindParam(":content", $content, PDO::PARAM_STR);
						$query->bindParam(":content_2", $content_2, PDO::PARAM_STR);
						$query->bindParam(":content_3", $content_3, PDO::PARAM_STR);
						$query->bindParam(":content_4", $content_4, PDO::PARAM_STR);
						$query->bindParam(":content_5", $content_5, PDO::PARAM_STR);
						$query->bindParam(":content_6", $content_6, PDO::PARAM_STR);
						$query->bindParam(":date", $optionSpecial, PDO::PARAM_STR);
						$query->bindParam(":about", $about, PDO::PARAM_STR);
						$query->execute();

						header("HTTP/1.1 201 Created");
					
					}
					catch (PDOException $e)
					{
						header("HTTP/1.1 400 Bad Request");
					}
				}
			}

		}
	}

	public function tile_update($method)
	{

		$conn = connect_db();

		if($method == "onLoad")
		{
			$query = $conn->prepare("SELECT * from stories ORDER BY ID DESC LIMIT 1");
			$query->execute();
			
			while($row = $query->fetch(PDO::FETCH_ASSOC))
			{
				header('Content-type: application/xml');
				header("HTTP/1.1 200 OK");
				
				$id = $row['ID'];
				$title = $row['title'];
				$image = $row['image'];
				$desc = $row['description'];

				// collects story data, then prepares the data ready for transport
				
					$privacy_array = "<tile>
						<visual version='2'>
							<binding fallback='TileWidePeekImageAndText01' template='TileWide310x150PeekImageAndText01'>

								<image id='1' alt='alt text' src='$image'/>

								<text id='1'>Our Latest Story: $title</text>

							</binding>
						</visual>
					</tile>";
				
				print $privacy_array;
			}
		}
		elseif($method == "latestStory")
		{

			$arr = array();

			$query = $conn->prepare("SELECT * from stories ORDER BY ID DESC LIMIT 1");
			$query->execute();
			
			while($row = $query->fetch(PDO::FETCH_ASSOC))
			{
				header ("Content-type: application/json");
				header("HTTP/1.1 200 OK");
				
				$id = $row['ID'];
				$title = $row['title'];
				$desc = $row['description'];
				$type = $row['type'];
				$owner = $row['owner'];
				$rec = $row['recommended'];
				$img = $row['image'];
				$author = "";
				
				$queryCheckName = $conn->prepare("SELECT * from users WHERE ID = :id");
			
				$queryCheckName->bindParam(":id", $owner, PDO::PARAM_STR);
				$queryCheckName->execute();
				
				while($rowCheckName = $queryCheckName->fetch(PDO::FETCH_ASSOC))
				{
					$author= $rowCheckName['username'];
				}

				// collects story data, then prepares the data ready for transport
				
				$stories_array = array("ID" => $id, "title" => $title, "description" => $desc, "type" => $type, "ownerID" => $owner, "ownerName" => $author, "image" => $img, "recommended" => $rec);
								
				array_push($arr, $stories_array);
					
				echo json_encode($arr);
			}

		}

	}
	
}

?>
