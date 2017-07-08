<html>
<head>
	<title>login Form</title>
</head>
	<body>
		<div>
			<?php echo form_open('User_Authentication/resetPassword')?>
			<?php 
				if (isset($message)) {
					echo $message."<br>";
				}
			?>
			Old Password:
			<input type="password" name="oldPassword">
			<?php echo form_error('oldPassword');?>
			<br>
			New Password:
			<input type="password" name="newPassword">
			<?php echo form_error('newPassword');?>
			Confirm New Password:
			<input type="password" name="ConfirmNewPassword">
			<?php echo form_error('ConfirmNewPassword');?>
			<br>
			<input type="text" name="resetCode" value="<?php echo $resetCode?>" hidden>
			<br>
			<input type="submit" value="Submit" name="submit"><br>			
		</div>
	</form>
</body>
</html>