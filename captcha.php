<?php 
	require ('captcha_generator.php');

	putenv('GDFONTPATH=' . realpath('.')); 
	header("Content-type: image/png"); 

	// Build an image resource using an existing image as a starting point
	$backgroundimage = "dino.gif"; 
	$im=imagecreatefromgif($backgroundimage); 
	$colour = imagecolorallocate($im, rand(0,255), rand(0,255), rand(0,255)); 

	// Output the string of characters using a true type font. 
	// Above we set the font path to the current directory, this 
	// means that arial.ttf font file must be in this directory. 
	$font = 'arial.ttf'; 
	$angle = rand(-5,5); 
	imagettftext($im, 90, $angle, 50, 250, $colour, $font, $_SESSION['captcha']); 

	// Draw some annoying lines across the image. 
	imagesetthickness($im, 10); 
	for ($i = 0; $i <3; $i++) { 
	    imageline($im, rand(100,50), rand(150,200), rand(450,550), rand(200,250), $colour); 
	} 

	// Output the image as a PNG and the free the used memory. 
	imagepng($im); 
	imagedestroy($im); 
	
?>
