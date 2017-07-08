<html>
<head>
	<title>login Form</title>
</head>
	<body>
		<div>
			<?php echo form_open('User_Authentication/forgot_password_process')?>
			<?php 
				if (isset($message)) {
					echo $message."<br>";
				}
			?>
			Username:
			<input type="text" name="username" value="<?php echo set_value('username');?>">
			<?php echo form_error('username');?>
			<br>
			email:
			<input type="text" name="email" value="<?php echo set_value('email');?>">
			<?php echo form_error('email');?>
			<br>
			<input type="submit" value="Submit" name="submit"><br>			
		</div>
	</form>
</body>
</html>