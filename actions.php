<?php

	include("functions.php");
	
	if($_GET["action"] == "loginSignUp")
	{
		
		$message = "";
		
		if($_POST["email"] == "")
		{
			
			$message = "An email address is required.";
			
		}
		
		else if($_POST["password"] == "")
		{
			
			$message = "A password is required.";
			
		}
		
		else if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) 
		{
			
			$message = "Invalid email format.";
		
		}
		
		if($message != "")
		{
			
			echo $message;
			exit();
			
		}
		
		else
		{
			
			if($_POST["loginActive"] == "0")
			{
				
				$query = "SELECT `id` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link,$_POST["email"])."' LIMIT 1;";
						
				if($result = mysqli_query($link,$query))
				{
							
					if(mysqli_num_rows($result) > 0)
					{
						
						$message = "That email address is already taken.";
					
					}
					
					else
					{
						
						$query = "INSERT INTO `users` (`email`,`password`) VALUES ('".mysqli_real_escape_string($link,$_POST["email"])."','".mysqli_real_escape_string($link,password_hash($_POST["password"],PASSWORD_DEFAULT))."');";
						if($result = mysqli_query($link,$query))
						{
							
							$_SESSION["id"] = mysqli_insert_id($link);
							echo 1;
							
						}
						
						else
						{
							
							$message = "Could not sign up the user! Please try again!";
							
						}
						
					}
				
				}
				
				else
				{
					
					$message = "Could not sign up the user! Please try again!";
					
				}
				
			}
			
			else
			{
				
				$query = "SELECT `id`,`password` FROM `users` WHERE `email` = '".mysqli_real_escape_string($link,$_POST["email"])."' LIMIT 1;";
				
				if($result = mysqli_query($link,$query))
				{
					
					if(mysqli_num_rows($result) > 0)
					{
						
						$row = mysqli_fetch_array($result);
						
						if(password_verify($_POST["password"],$row["password"]))
						{
							
							$_SESSION["id"] = $row["id"];
							echo 1;
							
						}
						
						else
						{
							
							echo "Incorrect email/password combination.";
							
						}
						
					}
					
					else
					{
						
						echo "Incorrect email/password combination.";
						
					}
					
				}
				
				else
				{
					
					$message = "Could not login the user! Please try again!";
					
				}
				
			}
			
		}
		
		if($message != "")
		{
			
			echo $message;
			exit();
			
		}

	
	}
	
	if($_GET["action"] == "toggleFollow")
	{
		
		$query = "SELECT * FROM `isFollowing` WHERE `follower` = '".mysqli_real_escape_string($link,$_SESSION["id"])."' AND `isFollowing` = '".mysqli_real_escape_string($link,$_POST["userId"])."' LIMIT 1;";
		
		if($result = mysqli_query($link,$query))
		{
		
			if(mysqli_num_rows($result) > 0)
			{
				
				$row = mysqli_fetch_array($result);
				
				mysqli_query($link,"DELETE FROM `isFollowing` WHERE `id` = ".mysqli_real_escape_string($link,$row["id"])." LIMIT 1;");
				
				echo "1";
				
			}
			
			else
			{
				
				mysqli_query($link,"INSERT INTO `isFollowing` (`follower`,`isFollowing`) VALUES ('".mysqli_real_escape_string($link,$_SESSION["id"])."','".mysqli_real_escape_string($link,$_POST["userId"])."');");
				
				echo "2";
				
			}

		}
		
	}
	
	if($_GET["action"] == "postTweet")
	{
		
		if($_POST["tweetContent"] == "")
		{
			
			echo "Your tweet is empty!";
			
		}
		
		else if(strlen($_POST["tweetContent"]) > 140)
		{
			
			echo "Your tweet is too long!";
			
		}
		
		else
		{
			
			mysqli_query($link,"INSERT INTO `tweets` (`tweet`,`userid`,`datetime`) VALUES ('".mysqli_real_escape_string($link,$_POST["tweetContent"])."',".mysqli_real_escape_string($link,$_SESSION["id"]).",NOW());");
			echo 1;
			
		}
		
	}

?>