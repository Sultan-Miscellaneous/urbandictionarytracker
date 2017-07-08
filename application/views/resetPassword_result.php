<html>
<head>
	<title>Validation</title>
</head>
	<body>
		<div>
			<?php 
				if (isset($message)) {
					echo $message."<br>";
				}
				echo "You will be redirected automatically to the login page in 3 seconds<br>";
			?>
			<script type="text/javascript">
				setTimeout(function(){
					window.location.href = 'http://localhost:8080/Site_1/';
				},3000)
			
			</script>
		</div>
	</form>
</body>
</html>