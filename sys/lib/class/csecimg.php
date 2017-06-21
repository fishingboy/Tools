<?php
   $NO_RETURN_URL = 1;
    include ("../../../common.php");
//session_start();

class CSecimg {

	var $font = 'monofont.ttf';

	function generateCode($characters) 
	{		
		$possible = '123456789';
		$code = '';
		$i = 0;
		while ($i < $characters) { 
			$code .= substr($possible, mt_rand(0, strlen($possible)-1), 1);
			$i++;
		}
		return $code;
	}

	function CSecimg($width='120', $height='40',$characters='6', $sname='') 
	{
		$code = $this->generateCode($characters);		
		$font_size = $height * 0.85;
		$image = @imagecreate($width, $height) or die('Cannot initialize new GD image stream');
		/* set the colours */
		$background_color = imagecolorallocate($image, 255, 255, 255);
		$text_color = imagecolorallocate($image, 20, 40, 100);
		$noise_color = imagecolorallocate($image, 100, 120, 180);
		/* generate random dots in background */
		for( $i=0; $i<($width*$height)/3; $i++ ) 
		{
			imagefilledellipse($image, mt_rand(0,$width), mt_rand(0,$height), 1, 1, $noise_color);
		}
		/* generate random lines in background */
		for( $i=0; $i<($width*$height)/150; $i++ ) 
		{
			imageline($image, mt_rand(0,$width), mt_rand(0,$height), mt_rand(0,$width), mt_rand(0,$height), $noise_color);
		}
		/* create textbox and add text */
		$textbox = imagettfbbox($font_size, 0, $this->font, $code) or die('Error in imagettfbbox function');
		$x = ($width - $textbox[4])/2;
		$y = (($height - $textbox[5])/2) - 2;
		imagettftext($image, $font_size, 0, $x, $y, $text_color, $this->font , $code) or die('Error in imagettftext function');
		/* output captcha image to browser */
		header('Content-Type: image/jpeg');
		imagejpeg($image);
		imagedestroy($image);
		$_SESSION['_sess_scode' . $sname] = $code;
	}

}


$width      = getParam('width', 'int', 120);
$height     = getParam('height', 'int', 40);
$characters = getParam('characters', 'int', 6);
$sname      = getParam('sname', 'string', '');
if ($characters < 1) $characters = 6;

$secimg = new CSecimg($width, $height, $characters, $sname);


?>