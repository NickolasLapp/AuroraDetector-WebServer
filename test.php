<?php
<<<<<<< HEAD
<<<<<<< HEAD
include_once 'dataParsing.php';
echo getRecentPMTAuroraData("BZN2");
=======
include "constants.php";
$array = get_data("2011-07-18", TRUE);
print_r($array);
>>>>>>> origin/master
=======
	include("constants.php");
	$array = get_data("2011-07-18");
	print_r($array);
>>>>>>> origin/master
?>