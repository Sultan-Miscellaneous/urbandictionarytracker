<html>
<head>
	<title>Homepage</title>
</head>
	<body>
		<div>
			<?php
				echo 'Hello ',$user_name,'<br>';
			?>
			<?php echo form_open('Homepage_controller/addDefinitionLink')?>
			Add Urban Dictionary Link:
			<input type="text" name="link" value="<?php echo set_value('link');?>">
			<?php 
				echo form_error('link'); 
			?>
			<input type="submit" value="Submit" name="submit"><br>
			<a href="http://localhost:8080/Site_1/index.php/User_Authentication/logout">LogOut</a>
			<?php
				echo $definitions;
			?>
		</div>
	</form>
</body>
</html>