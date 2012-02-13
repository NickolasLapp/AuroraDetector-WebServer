<?PHP
require_once("./include/fg_membersite.php");
require("password.inc");

$fgmembersite = new FGMembersite();

//Provide your site name here
$fgmembersite->SetWebsiteName('aurora.montana.edu');

//Provide the email address where you want to get notifications
$fgmembersite->SetAdminEmail($adminEmail);

//Provide your database login details here:
//hostname, user name, password, database name and table name
//note that the script will create the table (for example, fgusers in this case)
//by itself on submitting register.php for the first time
$fgmembersite->InitDB(/*hostname*/$host,
                      /*username*/$user,
                      /*password*/$pass,
                      /*database name*/$db,
                      /*table name*/$tbl);

//For better security. Get a random string from this link: http://tinyurl.com/randstr
// and put it here
$fgmembersite->SetRandomKey($passkey);

?>