<?php
require_once("./include/membersite_config.php");

echo $fgmembersite->UnsubscribeUser($_GET['signature'], $_GET['phone_number'], $_GET['expiration'], $_GET['email']);


?>