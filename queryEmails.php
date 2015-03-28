<?php
require_once("./include/membersite_config.php");

if ($fgmembersite->CheckLogin() && $fgmembersite->UserFullName() == 'AuroraUser' && $fgmembersite->UserEmail() == 'aurora.montana@gmail.com') {
    echo $fgmembersite->ReturnSystemUsers();
} else {
    echo "Access Denied";
}
?>