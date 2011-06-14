<?php
  /****************************************************************************
  *--using ChartDirector software, the following two comments are required--
  *-This software is based in part on the work of the Independent JPEG Group
  *-This software is based in part of the work of the FreeType Team
  ****************************************************************************/
  require_once("phpchartdir.php");
  setLicenseCode("DEVP-29V5-B9CT-DAKJ-ED0A-E037");

  $aurora_status = file("currentStatus.txt");
  for ($i = 0; $i < count($aurora_status); $i++)
  	$aurora_status[$i] = rtrim($aurora_status[$i]);
  
  $brightness=array('No Aurora','IBC 1', 'IBC 2', 'IBC 3');
   
  /* Define Image Dimensions*/
  $imageWidth = 172;
  $imageHeight = 164;
  
  $tag = new XYChart($imageWidth, $imageHeight, 0x00FF00, 0x00FF00, 0);
  $tag->setTransparentColor(0x00FF00);
  
  $tag->setSearchPath(dirname(__FILE__)); 
  $text = $tag->addText(0, 0, "<*img=images/balloon.png*>");
  $text->setAlignment(TopLeft);
  
  $text = $tag->addText(86, 25, "<*font=bold*>Stone Child\nCollege", "", 10, 0x000000);
  $text->setAlignment(Center);
  
  $text = $tag->addText(7, 45, "Current Aurora Status:", "", 10, 0x000000);
  $text->setAlignment(TopLeft);
  
  if ($aurora_status[2] != 0) {
  	$text = $tag->addText(25, 60, "".$brightness[$aurora_status[2]]." Aurora\nDuration: "."1"." hr, "."30"." min", "", 10, 0x000000);
  } else {
  	$text = $tag->addText(25, 60, "".$brightness[$aurora_status[2]],"", 10, 0x000000);
  }
  $text->setAlignment(TopLeft);
  
  $text = $tag->addText(86, 130, "(Click box to hide)", "", 7, 0x000000);
  $text->setAlignment(Center);
  
  /* Create Image */
  header("Content-type: image/png");
  print($tag->makeChart2(PNG)); 

?>