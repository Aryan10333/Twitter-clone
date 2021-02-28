<?php

	session_start();

	$link = mysqli_connect("localhost:3308","root","","twitter");
	
	if(mysqli_connect_errno())
	{
		
		print_r(mysqli_connect_error);
		exit();
		
	}
	
	if(isset($_GET["function"]))
	{
		
		if($_GET["function"] == "logout")
		{
			
			session_unset();
			
		}
		
	}
	
	function time_since($since) {
		
		$chunks = array(
			
			array(60 * 60 * 24 * 365 , 'year'),
			array(60 * 60 * 24 * 30 , 'month'),
			array(60 * 60 * 24 * 7, 'week'),
			array(60 * 60 * 24 , 'day'),
			array(60 * 60 , 'hour'),
			array(60 , 'min'),
			array(1 , 'sec')
		
		);

		for ($i = 0, $j = count($chunks); $i < $j; $i++) {
			
			$seconds = $chunks[$i][0];
			$name = $chunks[$i][1];
			if (($count = floor($since / $seconds)) != 0) {
				
				break;
			
			}
		
		}

		$print = ($count == 1) ? '1 '.$name : "$count {$name}s";
		return $print;
	
	}
	
	function displayTweets($type)
	{
		
		global $link;
		
		if($type == 'public')
		{
			
			$whereClause = "";
			
		}
		
		else if($type == "isFollowing")
		{
			
			if(isset($_SESSION["id"]))
			{
				
				$query = "SELECT * FROM `isFollowing` WHERE `follower` = '".mysqli_real_escape_string($link,$_SESSION["id"])."';";
			
				if($result = mysqli_query($link,$query))
				{
					
					if(mysqli_num_rows($result) > 0)
					{
						
						$whereClause = "";
						
						while($row = mysqli_fetch_array($result))
						{
							
							if($whereClause == "")
							{
								
								$whereClause = " WHERE `userid` = ".$row["isFollowing"]."";
								
							}
							
							else
							{
								
								$whereClause .= " OR ";
								$whereClause .= "`userid` = ".$row["isFollowing"];
								
							}
							
						}
						
					}
					
					else
					{
						
						echo "There are no tweets to display.";
						
						return false;
						
					}
				
				}
				
			}
			
			else
			{
				
				$whereClause = "";
				
			}
			
		}
		
		else if($type == "yourtweets")
		{
			
			if(isset($_SESSION["id"]))
			{
				
				$whereClause = "WHERE `userid` = ".mysqli_real_escape_string($link,$_SESSION["id"]);
				
			}
			
			else
			{
				
				echo "Please first log in to see your tweets.";
				return false;
				
			}
			
		}
		
		else if($type == "search")
		{
			
			if(isset($_SESSION["id"]))
			{
				
				echo "<p>Showing results for '".mysqli_real_escape_string($link,$_GET["q"])."':</p>";
				$whereClause = "WHERE `tweet` LIKE '%".mysqli_real_escape_string($link,$_GET["q"])."%'";
				
			}
			
			else
			{
				
				echo "Please first log in to see other's tweets.";
				return false;
				
			}
			
		}
		
		else if(is_numeric($type))
		{
			
			$userQuery = "SELECT * FROM `users` WHERE `id` = ".mysqli_real_escape_string($link,$type)." LIMIT 1;";
			$userQueryResult = mysqli_query($link,$userQuery);
			$user = mysqli_fetch_array($userQueryResult);
			
			echo "<h2>".mysqli_real_escape_string($link,$user["email"])."'s Tweets</h2>";
			
			$whereClause = "WHERE `userid` = ".mysqli_real_escape_string($link,$type);
			
		}
		
		$query = "SELECT * FROM `tweets` ".$whereClause." ORDER BY `datetime` DESC LIMIT 10;";
		
		$result = mysqli_query($link,$query);
		
		if(mysqli_num_rows($result) == 0)
		{
				
			echo "There are no tweets to display.";
				
		}
		
		else
		{
			
			while($row = mysqli_fetch_array($result))
			{
				
				$userQuery = "SELECT * FROM `users` WHERE `id` = ".mysqli_real_escape_string($link,$row["userid"])." LIMIT 1;";
				$userQueryResult = mysqli_query($link,$userQuery);
				$user = mysqli_fetch_array($userQueryResult);
				
				echo "<div class='tweet'><p><a href='?page=publicprofiles&userid=".$user["id"]."'>".$user["email"].'</a> <span class="timeAgo">'.time_since(time() - strtotime($row["datetime"]))." ago</span>:</p>";
				
				echo "<p>".$row["tweet"]."</p>";
				
				echo "<p><a class='toggleFollow' data-userId='".$row["userid"]."' href=''>";
				
				if(isset($_SESSION["id"]))
				{
				
					$isFollowingQuery = "SELECT * FROM `isFollowing` WHERE `follower` = ".mysqli_real_escape_string($link,$_SESSION["id"])." AND `isFollowing` = ".mysqli_real_escape_string($link,$row["userid"])." LIMIT 1;";
			
					$isFollowingQueryResult = mysqli_query($link,$isFollowingQuery);
						
					if(mysqli_num_rows($isFollowingQueryResult) > 0)
					{
						
						echo "Unfollow";
						
					}
					
					else
					{
						
						echo "Follow";
						
					}
					
				}
				
				else
				{
					
					echo "Follow";
					
				}
				
				echo "</a></p></div>";
				
			}
		
		}
		
	}
	
	function displaySearch() {
		
		echo '<form class="form-inline">
				
				<div class="form-group mb-2">
					
					<input type="hidden" name="page" value="search">
					<input type="text" name = "q" class="form-control" id="search" placeholder="Search">
				
				</div>
				
				<button type="submit" class="btn btn-primary mb-2">Search Tweets</button>
			
			</form>';
		
	}
	
	function displayTweetBox() {
		
		if(isset($_SESSION["id"]))
		{
			
			echo '<div id="tweetSuccess" class="alert alert-success">Your tweet was posted successfully.</div>
			
			<div id="tweetFail" class="alert alert-danger"></div>
			
			<div class="form">
				
				<div class="form-group mb-2">
					
					
					<textarea class="form-control" id="tweetContent"></textarea>
				
				</div>
				
				<button type="submit" class="btn btn-primary mb-2" id="postTweetButton">Post Tweet</button>
			
			</div>';
			
		}
		
	}
	
	function displayUsers() {
		
		global $link;
		
		$query = "SELECT * FROM `users` LIMIT 10;";
		
		$result = mysqli_query($link,$query);
		
		if(mysqli_num_rows($result) == 0)
		{
				
			echo "There are no users to display.";
				
		}
		
		else
		{
			
			while($row = mysqli_fetch_array($result))
			{
				
				echo "<p><a href='?page=publicprofiles&userid=".$row["id"]."'>".$row["email"]."</a></p>";
				
			}
			
		}
		
	}

?>