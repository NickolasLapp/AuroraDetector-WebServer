<?php
	$font = 'arial.ttf';
	$bold = 'arialbd.ttf';
	
	// Grab the image width and height
	$image_width = (integer) $_REQUEST['width'];
	$image_height = (integer) $_REQUEST['height'];
	$legend_height = $image_height-40;
	$line_width = $image_width-35;

	// Create a image
	$image  = imagecreatetruecolor($image_width, $image_height);
	
	$orange_boundary = floor($legend_height/3)+40;
	$yellow_boundary = floor($legend_height/2)+40;
	$white_boundary = floor(2*($legend_height)/3)+40;

	// Make the legend
	for($i=0; $i<$legend_height; $i++)
	{
		if ($i < $orange_boundary) {
			$color_r = floor($i*15)/$orange_boundary+230;
			$color_g = floor($i*120)/$orange_boundary+50;
			$color_b = floor($i-20)/$orange_boundary+40;
		} elseif ($i <$yellow_boundary) {
			$color_r = floor(($i-$orange_boundary)*10)/($yellow_boundary-$orange_boundary)+245;
			$color_g = floor(($i-$orange_boundary)*80)/($yellow_boundary-$orange_boundary)+170;
			$color_b = floor(($i-$orange_boundary)*20)/($yellow_boundary-$orange_boundary)+20;	
		} elseif ($i < $white_boundary) {
			$color_r = 255;
			$color_g = floor(($i-$yellow_boundary)*5)/($white_boundary-$yellow_boundary)+250;
			$color_b = floor(($i-$yellow_boundary)*215)/($white_boundary-$yellow_boundary)+40;
		} else {
			$color_r = floor(($i-$white_boundary)*-255)/($image_height-$white_boundary)+255;
			$color_g = floor(($i-$white_boundary)*-255)/($image_height-$white_boundary)+255;
			$color_b = floor(($i-$white_boundary)*-65)/($image_height-$white_boundary)+255;	
		}
		
		$color = ImageColorAllocate($image, $color_r, $color_g, $color_b);
		$white = ImageColorAllocate($image, 255, 255, 255);
		
		imageline($image, 0, $i+40, $line_width, $i+40, $color);
		imageline($image, $line_width+1, $i+40, $image_width, $i+40, $white);
	}
	
	$text = ImageColorAllocate($image, 0, 99, 147);
	
	// Make the top label
	imagefilledrectangle($image, 0, 0, $image_width, 40, $white);
	imagettftext($image, 12, 0, 5, 14, $text, $bold, 'Power Flux');
	imagettftext($image, 12, 0, 20, 32, $text, $bold, 'mW/m');
	imagettftext($image, 8, 0, 68, 26, $text, $bold, '2');
	
	// Draw the legend labels
	imagettftext($image, 10, 0, $line_width+1, 54, $text, $font, '10.0');
	imagettftext($image, 10, 0, $line_width+2, $orange_boundary, $text, $font, '1.0');
	imagettftext($image, 10, 0, $line_width+2, $white_boundary, $text, $font, '0.1');
	imagettftext($image, 10, 0, $line_width+2, $image_height-2, $text, $font, '0.0');
	
	# Prints out all the figures and picture and frees memory
	header('Content-type: image/png');
	
	ImagePNG($image);
	imagedestroy($image); 
?>