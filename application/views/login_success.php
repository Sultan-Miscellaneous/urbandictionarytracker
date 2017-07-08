<html>
<head>
	<title>Login Form</title>
</head>
	<body>
		<div>
			<?php
				
				echo "login success,<br>","You will be redirected automatically to the homepage page in 3 seconds<br>";
			?>
			<script type="text/javascript">
				setTimeout(function(){
					window.location.href = 'http://localhost:8080/Site_1/index.php/homepage_controller';
				},3000)
			</script>
		</div>
	</form>
</body>
</html>