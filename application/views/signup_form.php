<html>
<head>
	<title>Signup Form</title>
</head>
	<body>
		<div>
			<?php echo form_open('User_Authentication/user_signup_process')?>
			<?php 
				if (isset($message)) {
					echo $message;
				}
			?>
			Username:
			<input type="text" name="username" value="<?php echo set_value('username');?>">		<?php 
				echo form_error('username'); 
				if(empty(form_error('username'))){
					echo "<br>";
				} 
			?>
			Password:
			<input type="password" name="password">
			<?php 
				echo form_error('password'); 
				if(empty(form_error('password'))){
					echo "<br>";
				} 
			?>
			Confirm Password:
			<input type="password" name="confirm_password">
			<?php 
				echo form_error('confirm_password'); 
				if(empty(form_error('confirm_password'))){
					echo "<br>";
				} 
			?>
			Email:
			<input type="text" name="email" value = "<?php echo set_value('email');?>">			<?php 
				echo form_error('email'); 
				if(empty(form_error('email'))){
					echo "<br>";
				} 
			?>
			<input type="submit" value="Submit" name="submit"><br>
		</div>
	</form>
</body>
</html>