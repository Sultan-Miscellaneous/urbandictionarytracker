<html>
<head>
	<title>login Form</title>
</head>
	<body>
		<div>
			<?php echo form_open('User_Authentication/user_login_process')?>
			<?php 
				if (isset($message)) {
					echo $message."<br>";
				}
			?>
			Username:
			<input type="text" name="username" value="<?php echo set_value('username');?>">
			<?php echo form_error('username');?>
			<br>
			Password:
			<input type="password" name="password">
			<?php echo form_error('password');?>
			<br>
			<input type="submit" value="Submit" name="submit"><br>
			<a href="http://localhost:8080/Site_1/index.php/User_Authentication/show_signup_form">Sign Up</a>
			<a href="http://localhost:8080/Site_1/index.php/User_Authentication/show_forgotPassword_form"> Forgot your password?</a>
			
		</div>
	</form>
</body>
</html>