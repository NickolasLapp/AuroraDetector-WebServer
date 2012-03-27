<?php

include_once("./include/membersite_config.php");

if(!$fgmembersite->CheckLogin())
{
	$fgmembersite->RedirectToURL("./login.php");
}

$row = $fgmembersite->GetUserDataValues();

$name = $row['name'];
$email = $row['email'];
$phoneNumber = $row['phone_number'];
$carrier = $row['carrier'];
$username = $row['username'];

?>