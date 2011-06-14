<?php
	header("Content-type: image/png");
	$ID = $_REQUEST['ID'];
	$garbage = $_REQUEST['date'];
	
	$im = imagecreatefrompng("images/".$ID.".png");
	imagesavealpha($im, true);
	
	imagepng($im);
	imagedestroy($im);
?>