<?php
	require 'captcha_generator.php';
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
   "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="en" xml:lang="en">
<head>
    <title>CAPTCHA Assignment</title>
</head>
<body>
	<p><img src="captcha.php" alt="Captcha!" /></p>

    <form method="post" action="">
    	<fieldset>
	    	Email:
	    	<br />
	    	<?php if(captcha_not_matched()): ?>
				<input id="email" name="email" value='<?=$email?>' type="text" />
			<?php else: ?>
				<input id="email" name="email" type="text" />
			<?php endif ?>
			<br />
			
			Comment:
			<br />
			<?php if(captcha_not_matched()): ?>
				<textarea id="content" name="content" rows="5" cols="20">
					<?=$comment?>
				</textarea>
			<?php else: ?>
				<textarea id="content" name="content" rows="5" cols="20"></textarea>
			<?php endif ?>
			<br />

			Please input CAPTCHA:
	    	<br />
			<input id="captcha" name="captcha" type="text" />
			<br />
			<input type="submit" name="submit" value="Submit Comment" />
		</fieldset>
	</form>
</body>
</html>