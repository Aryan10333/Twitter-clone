	<footer class="footer">
	
		<div class="container">	
			
			<p>&copy; My Website</p>
	
		</div>
	
	</footer>
		
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
		<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js" integrity="sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI" crossorigin="anonymous"></script>
		
		<div class="modal fade" id="staticBackdrop" data-backdrop="static" data-keyboard="false" tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
			
			<div class="modal-dialog">
				
				<div class="modal-content">
					
					<div class="modal-header">
						
						<h5 class="modal-title" id="modalTitle">Login</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						  
							<span aria-hidden="true">&times;</span>
						
						</button>
					
					</div>
					
					<div class="modal-body">
					
						<div class="alert alert-danger" id="loginAlert"></div>
						
						<form>
							
							<input type="hidden" id="loginActive" name="loginActive" value="1">
							<div class="form-group">
								
								<label for="email">Email address</label>
								<input type="email" class="form-control" id="email" placeholder="Email address">
							
							</div>
							
							<div class="form-group">
								
								<label for="password">Password</label>
								<input type="password" class="form-control" id="password" placeholder="Password">
							
							</div>
							
						</form>
					
					</div>
					
					<div class="modal-footer">
					
						<a id="toggle-link" class="mr-3" href="">Sign Up</a>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
						<button type="button" class="btn btn-primary" id="modalButton">Login</button>
					
					</div>
				
				</div>
			
			</div>
		
		</div>
		
		<script>
		
			$("#toggle-link").click(function(e) {
				
				e.preventDefault();
				
				if($("#loginActive").val() == 1)
				{
					
					$("#modalTitle").html("Sign Up");
					$("#modalButton").html("Sign Up");
					$("#toggle-link").html("Login");
					$("#loginActive").val(0);
					
				}
				
				else
				{
					
					$("#modalTitle").html("Login");
					$("#modalButton").html("Login");
					$("#toggle-link").html("Sign Up");
					$("#loginActive").val(1);
					
				}
				
			});
			
			$("#modalButton").click(function() {
				
				$.ajax({
					
					type: "POST",
					url: "actions.php?action=loginSignUp",
					data: "email=" + $("#email").val() + "&password=" + $("#password").val() + "&loginActive=" + $("#loginActive").val(),
					success: function(result) {
						
						if(result == "1")
						{
							
							window.location.assign("http://localhost/aryan/Website/Twitter/");
							
						}
						
						else
						{
							
							$("#loginAlert").html(result).show();
							
						}
						
					}
					
				});
				
			});
			
			$(".toggleFollow").click(function(e) {
				
				e.preventDefault();
				
				var id = $(this).attr("data-userId");
				
				$.ajax({
					
					type: "POST",
					url: "actions.php?action=toggleFollow",
					data: "userId=" + $(this).attr("data-userId"),
					success: function(result) {
						
						if(result == "1")
						{
							
							$("a[data-userId='" + id + "']").html("Follow");
							
						}
						
						else if(result == "2")
						{
							
							$("a[data-userId='" + id + "']").html("Unfollow");
							
						}
						
					}
					
				});
				
			});
			
			$("#postTweetButton").click(function() {
				
				$.ajax({
					
					type: "POST",
					url: "actions.php?action=postTweet",
					data: "tweetContent=" + $("#tweetContent").val(),
					success: function(result) {
						
						if(result == "1")
						{
							
							$("#tweetSuccess").show();
							$("#tweetFail").hide();
							
						}
						
						else if(result != "")
						{
							
							$("#tweetFail").html(result).show();
							$("#tweetSuccess").hide();
							
						}
						
					}
					
				});
				
			});
		
		</script>
		
	</body>

</html>