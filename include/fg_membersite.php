<?PHP

require("gmail.inc");
require_once("class.phpmailer.php");
require_once("class.smtp.php"); 
require_once("formvalidator.php");


class FGMembersite
{
    var $admin_email;
    var $from_address;
    
    var $username;
    var $pwd;
    var $database;
    var $tablename;
    var $connection;
    var $rand_key;
    
    var $error_message;
    
    //-----Initialization -------
    function FGMembersite()
    {
        $this->sitename = 'YourWebsiteName.com';
        $this->rand_key = '0iQx5oBk66oVZep';
    }
    
    function InitDB($host,$uname,$pwd,$database,$tablename)
    {
        $this->db_host  = $host;
        $this->username = $uname;
        $this->pwd  = $pwd;
        $this->database  = $database;
        $this->tablename = $tablename;
        
    }
    function SetAdminEmail($email)
    {
        $this->admin_email = $email;
    }
    
    function SetWebsiteName($sitename)
    {
        $this->sitename = $sitename;
    }
    
    function SetRandomKey($key)
    {
        $this->rand_key = $key;
    }
    
    //-------Main Operations ----------------------
    function RegisterUser()
    {
        if(!isset($_POST['submitted']))
        {
           return false;
        }
        
        $formvars = array();
        
        if(!$this->ValidateRegistrationSubmission())
        {
            return false;
        }
        
        $this->CollectRegistrationSubmission($formvars);
        
        if(!$this->SaveToDatabase($formvars))
        {
            return false;
        }
        
        if(!$this->SendUserConfirmationEmail($formvars))
        {
            return false;
        }

        $this->SendAdminIntimationEmail($formvars);
        
        return true;
    }

    function ConfirmUser()
    {
        if(empty($_GET['code'])||strlen($_GET['code'])<=10)
        {
            $this->HandleError("Please provide the confirm code");
            return false;
        }
        $user_rec = array();
        if(!$this->UpdateDBRecForConfirmation($user_rec))
        {
            return false;
        }
        
        $this->SendUserWelcomeEmail($user_rec);
        
        $this->SendAdminIntimationOnRegComplete($user_rec);
        
        return true;
    }    
    
    function Login()
    {
        if(empty($_POST['username']))
        {
            $this->HandleError("UserName is empty!");
            return false;
        }
        
        if(empty($_POST['password']))
        {
            $this->HandleError("Password is empty!");
            return false;
        }
        
        $username = trim($_POST['username']);
        $password = trim($_POST['password']);
        
        session_start();
        if(!$this->CheckLoginInDB($username,$password))
        {
            return false;
        }
        
        $_SESSION[$this->GetLoginSessionVar()] = $username;
        
        return true;
    }
    
    function CheckLogin()
    {
         session_start();

         $sessionvar = $this->GetLoginSessionVar();
         
         if(empty($_SESSION[$sessionvar]))
         {
            return false;
         }
         return true;
    }
    
    function UserFullName()
    {
        return isset($_SESSION['name_of_user'])?$_SESSION['name_of_user']:'';
    }
    
    function UserEmail()
    {
        return isset($_SESSION['email_of_user'])?$_SESSION['email_of_user']:'';
    }
    
    function LogOut()
    {
        session_start();
        
        $sessionvar = $this->GetLoginSessionVar();
        
        $_SESSION[$sessionvar]=NULL;
        
        unset($_SESSION[$sessionvar]);
    }
	
	function ForgotPassword()
	{
		$user_cred = array();
		
		$user_cred['email'] = $this->SanitizeForSQL($_GET['email']);
		
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        } 
		
		if(!$this->IsFieldUnique($user_cred,'email'))
        {
			$this->HandleError("Email does not exist");
			return false;
		}
		$result = mysql_query("Select name, username from $this->tablename where email='".$user_cred['email']."'",$this->connection);   
        $row = mysql_fetch_assoc($result);
        $user_cred['name'] 		= $row['name'];
		$user_cred['username'] 	= $row['username'];
		require("forgotPasswordConfirmation.inc");
		
		$this->SendUserForgotPasswordEmail($user_cred);
        return true;
	}
	
	function ConfirmForgotPassword()
	{
		if(empty($_GET['code'])||strlen($_GET['code'])<=31)
        {
            $this->HandleError("Please provide the confirm code");
            return false;
        }
		//Check to see if the code exists in the DB
		$username = mysql_query("Select username from $this->tablename where password='".$_GET['code']."'",$this->connection); 
		if ($username == FALSE)
		{
			$this->HandleError("The confirmation code is invalid.");
			return false;
		}
		session_start();
        $_SESSION[$this->GetLoginSessionVar()] = $username;
		
        return true;
	}
    
    //-------Public Helper functions -------------
    function GetSelfScript()
    {
        return htmlentities($_SERVER['PHP_SELF']);
    }    
    
    function SafeDisplay($value_name)
    {
        if(empty($_POST[$value_name]))
        {
            return'';
        }
        return htmlentities($_POST[$value_name]);
    }
    
    function RedirectToURL($url)
    {
        header("Location: $url");
        exit;
    }
    
    function GetSpamTrapInputName()
    {
        return 'sp'.md5('KHGdnbvsgst'.$this->rand_key);
    }
    
    function GetErrorMessage()
    {
        if(empty($this->error_message))
        {
            return '';
        }
        $errormsg = nl2br(htmlentities($this->error_message));
        return $errormsg;
    }    
    //-------Private Helper functions-----------
    
    function HandleError($err)
    {
        $this->error_message .= $err."\r\n";
    }
    
    function HandleDBError($err)
    {
        $this->HandleError($err."\r\n mysqlerror:".mysql_error());
    }
    
    function GetFromAddress()
    {
        if(!empty($this->from_address))
        {
            return $this->from_address;
        }

        $host = $_SERVER['SERVER_NAME'];

        $from ="nobody@$host";
        return $from;
    } 
    
    function GetLoginSessionVar()
    {
        $retvar = md5($this->rand_key);
        $retvar = 'usr_'.substr($retvar,0,10);
        return $retvar;
    }
    
    function CheckLoginInDB($username,$password)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }          
        $username = $this->SanitizeForSQL($username);
        $pwdmd5 = md5($password);
        $qry = "Select name, email from $this->tablename where username='$username' and password='$pwdmd5' and confirmcode='y'";
        
        $result = mysql_query($qry,$this->connection);
        
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Error logging in. The username or password does not match");
            return false;
        }
        
        $row = mysql_fetch_assoc($result);
        
        
        $_SESSION['name_of_user']  = $row['name'];
        $_SESSION['email_of_user'] = $row['email'];
        
        return true;
    }
    
    function UpdateDBRecForConfirmation(&$user_rec)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
        $confirmcode = $this->SanitizeForSQL($_GET['code']);
        
        $result = mysql_query("Select name, email from $this->tablename where confirmcode='$confirmcode'",$this->connection);   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            $this->HandleError("Wrong confirm code.");
            return false;
        }
        $row = mysql_fetch_assoc($result);
        $user_rec['name'] = $row['name'];
        $user_rec['email']= $row['email'];
        
        $qry = "Update $this->tablename Set confirmcode='y' Where  confirmcode='$confirmcode'";
        
        if(!mysql_query( $qry ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$qry");
            return false;
        }      
        return true;
    }
	
	function ReturnSystemUsers()
	{
		if(!$this->DBLogin())
		{
		    $this->HandleError("Database login failed!");
		    return false;
		} 
		
		$qry = "Select name, email, phone_number, carrier, username from ".$this->tablename;
		
		$result = mysql_query($qry,$this->connection);
		
		$returnArray = array();
		
		while ($row = mysql_fetch_assoc($result)) {
			$user = array(
			"name" 			=> $row["name"],
			"email" 		=> $row["email"],
			"phone_number" 	=> $row["phone_number"],
			"carrier"		=> $row["carrier"],
			"username" 		=> $row["username"]
			);
			
			array_push($returnArray, $user);
		}
		
		return json_encode($returnArray);
		
	}
	
	function UpdateDBRecForForgotPassword()
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
		$newpass = $_GET['password'];
		$username = $_SESSION[$this->GetLoginSessionVar()];
        
        $qry = "Update $this->tablename Set password='".md5($newpass)."' Where  username='$username'";
        
        if(!mysql_query($qry ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table.");
            return false;
        }      
        return true;
	}
	
	function UpdateDBRecForData($data)
	{
		if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }   
		if(!$this->CheckLogin())
		{
			$this->HandleError("Not logged in!");
			return false;
		}
		$username = $_SESSION[$this->GetLoginSessionVar()];
        $qry = "Update $this->tablename Set data='$data' Where username='$username'";
        
        if(!mysql_query($qry ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table.");
            return false;
        }      
        return true;
	}
	
    function SendUserWelcomeEmail(&$user_rec)
    {
		global $gAddress, $gPassword, $gPort;
        $mailer = new PHPMailer();
		
		$mailer->IsSMTP();
		$mailer->SMTPAuth   = true;                  // enable SMTP authentication
		$mailer->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$mailer->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
		$mailer->Port       = $gPort;                	// set the SMTP port
		
		$mailer->Username   = $gAddress;  			// GMAIL username
		$mailer->Password   = $gPassword;            // GMAIL password
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($user_rec['email'],$user_rec['name']);
        
        $mailer->Subject = "Welcome to ".$this->sitename;

        $mailer->From = $this->GetFromAddress();        
        
        $mailer->Body ="Hello ".$user_rec['name']."\r\n\r\n".
        "Welcome! Your registration with the Montana Aurora Detector Network is completed.\r\n".
        "\r\n".
		"Please keep in mind that many features of the Montana Aurora Detector Network are still in Beta\r\n".
		"You will be emailed on major updates to the system when they happen, features such as:\r\n".
		"*Email notification\r\n".
		"*Text notification\r\n".
		"*More precise data about the aurora\r\n".
		"*Many more!\r\n\r\n".
		"Thank you very much for signing up to this exciting opportunity!\r\n".
        "Regards,\r\n".
        "The MADN Team\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;
    }
	
	function SendUserForgotPasswordEmail(&$user_cred)
	{
		global $gAddress, $gPassword, $gPort;
        $mailer = new PHPMailer();
		
		$mailer->IsSMTP();
		$mailer->SMTPAuth   = true;                  // enable SMTP authentication
		$mailer->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$mailer->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
		$mailer->Port       = $gPort;                	// set the SMTP port
		
		$mailer->Username   = $gAddress;  			// GMAIL username
		$mailer->Password   = $gPassword;            // GMAIL password
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($user_cred['email'],$user_cred['name']);
        
        $mailer->Subject = "Forgot password to ".$this->sitename;

        $mailer->From = $this->GetFromAddress();
		        
        $mailer->Body ="Hello ".$user_cred['name']."\r\n\r\n".
        "Your username is as follows.\r\n".
        "\r\n".
		"Username:".$user_cred['username']."\r\n".
		"Please click the link below to change your password\r\n".
		"http://aurora.montana.edu".'/confirmpass.php?code='.$forgotcode;
		"\r\n\r\n".
        "Regards,\r\n".
        "The MADN Team\r\n".
        $this->sitename;

        if(!$mailer->Send())
        {
            $this->HandleError("Failed sending user welcome email.");
            return false;
        }
        return true;
	}
    
    function SendAdminIntimationOnRegComplete(&$user_rec)
    {
		global $gAddress, $gPassword, $gPort;
        $mailer = new PHPMailer();
		
		$mailer->IsSMTP();
		$mailer->SMTPAuth   = true;                  // enable SMTP authentication
		$mailer->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$mailer->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
		$mailer->Port       = $gPort;                	// set the SMTP port
		
		$mailer->Username   = $gAddress;  			// GMAIL username
		$mailer->Password   = $gPassword;            // GMAIL password

        $mailer->From = $this->GetFromAddress();  
        if(empty($this->admin_email))
        {
            return false;
        }
        
        $mailer->CharSet = 'utf-8';
        
        $mailer->AddAddress($this->admin_email);
        
        $mailer->Subject = "Registration Completed: ".$user_rec['name'];

        $mailer->From = $this->GetFromAddress();         
        
        $mailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$user_rec['name']."\r\n".
        "Email address: ".$user_rec['email']."\r\n";
        
        if(!$mailer->Send())
        {
            return false;
        }
        return true;
    }

    
    function ValidateRegistrationSubmission()
    {
        //This is a hidden input field. Humans won't fill this field.
        if(!empty($_POST[$this->GetSpamTrapInputName()]) )
        {
            //The proper error is not given intentionally
            $this->HandleError("Automated submission prevention: case 2 failed");
            return false;
        }
        
        $validator = new FormValidator();
        $validator->addValidation("name","req","Please fill in Name");
        //$validator->addValidation("email","email","The input for Email should be a valid email value");
        $validator->addValidation("email","req","Please fill in Email");
        $validator->addValidation("username","req","Please fill in UserName");
        $validator->addValidation("password","req","Please fill in Password");

        
        if(!$validator->ValidateForm())
        {
            $error='';
            $error_hash = $validator->GetErrors();
            foreach($error_hash as $inpname => $inp_err)
            {
                $error .= $inpname.':'.$inp_err."\n";
            }
            $this->HandleError($error);
            return false;
        }        
        return true;
    }
    
    function CollectRegistrationSubmission(&$formvars)
    {
        $formvars['name'] = $this->Sanitize($_POST['name']);
        $formvars['email'] = $this->Sanitize($_POST['email']);
        $formvars['username'] = $this->Sanitize($_POST['username']);
		$formvars['phone'] = $this->Sanitize($_POST['phone']);
		$formvars['carrier'] = $this->Sanitize($_POST['carrier']);
        $formvars['password'] = $this->Sanitize($_POST['password']);
    }
    
    function SendUserConfirmationEmail(&$formvars)
    {
		global $gAddress, $gPassword, $gPort;
        $confirmMailer = new PHPMailer();
		
		$confirmMailer->IsSMTP();
		$confirmMailer->SMTPAuth   = true;                  // enable SMTP authentication
		$confirmMailer->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$confirmMailer->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
		$confirmMailer->Port       = $gPort;                // set the SMTP port
		
		$confirmMailer->Username   = $gAddress;  			// GMAIL username
		$confirmMailer->Password   = $gPassword;            // GMAIL password
        
        $confirmMailer->CharSet = 'utf-8';
        
        $confirmMailer->AddAddress($formvars['email'],$formvars['name']);
        
        $confirmMailer->Subject = "Your registration with ".$this->sitename;

        $confirmMailer->From = $this->GetFromAddress();        
        
        $confirmcode = $formvars['confirmcode'];
        
        //$confirm_url = $this->GetAbsoluteURLFolder().'/confirmreg.php?code='.$confirmcode;
		//$confirm_url = "http://orsl.coe.montana.edu/aurora".'/confirmreg.php?code='.$confirmcode;
        //TODO: This needs to be changed
		
        $confirmMailer->Body ="Hello ".$formvars['name']."\r\n\r\n".
        "Thanks for your registration with the Montana Aurora Detector Network\r\n".
        "Please click the link below to confirm your registration.\r\n".
        "http://aurora.montana.edu".'/confirmreg.php?code='.$confirmcode; //$confirm_url
        "\r\n\r\n".
        "Regards,\r\n".
        "The MADN Team\r\n".
        $this->sitename;

        if(!$confirmMailer->Send())
        {
            $this->HandleError("Failed sending registration confirmation email.");
            return false;
        }
        return true;
    }
    function GetAbsoluteURLFolder()
    {
        $scriptFolder = (isset($_SERVER['HTTPS']) && ($_SERVER['HTTPS'] == 'on')) ? 'https://' : 'http://';
        $scriptFolder .= $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']);
        return $scriptFolder;
    }
    
    function SendAdminIntimationEmail(&$formvars)
    {
        if(empty($this->admin_email))
        {
            return false;
        }
		global $gAddress, $gPassword, $gPort;
        $adminMailer = new PHPMailer();
		
		$adminMailer->IsSMTP();
		$adminMailer->SMTPAuth   = true;                  // enable SMTP authentication
		$adminMailer->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$adminMailer->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
		$adminMailer->Port       = $gPort;                	// set the SMTP port
		
		$adminMailer->Username   = $gAddress;  			// GMAIL username
		$adminMailer->Password   = $gPassword;            // GMAIL password
        
        $adminMailer->CharSet = 'utf-8';
        
        $adminMailer->AddAddress($this->admin_email);
        
        $adminMailer->Subject = "New registration: ".$formvars['name'];

        $adminMailer->From = $this->GetFromAddress();         
        
        $adminMailer->Body ="A new user registered at ".$this->sitename."\r\n".
        "Name: ".$formvars['name']."\r\n".
        "Email address: ".$formvars['email']."\r\n".
        "UserName: ".$formvars['username'];
        
        if(!$adminMailer->Send())
        {
            return false;
        }
        return true;
    }
    
    function SaveToDatabase(&$formvars)
    {
        if(!$this->DBLogin())
        {
            $this->HandleError("Database login failed!");
            return false;
        }
        if(!$this->Ensuretable())
        {
            return false;
        }
        if(!$this->IsFieldUnique($formvars,'email'))
        {
            $this->HandleError("This email is already registered");
            return false;
        }
        
        if(!$this->IsFieldUnique($formvars,'username'))
        {
            $this->HandleError("This UserName is already used. Please try another username");
            return false;
        }        
        if(!$this->InsertIntoDB($formvars))
        {
            $this->HandleError("Inserting to Database failed!");
            return false;
        }
        return true;
    }
    
    function IsFieldUnique($formvars,$fieldname)
    {
        $field_val = $this->SanitizeForSQL($formvars[$fieldname]);
        $qry = "select username from $this->tablename where $fieldname='".$field_val."'";
        $result = mysql_query($qry,$this->connection);   
        if($result && mysql_num_rows($result) > 0)
        {
            return false;
        }
        return true;
    }
    
    function DBLogin()
    {

        $this->connection = mysql_connect($this->db_host,$this->username,$this->pwd);

        if(!$this->connection)
        {   
            $this->HandleDBError("Database Login failed! Please make sure that the DB login credentials provided are correct");
            return false;
        }
        if(!mysql_select_db($this->database, $this->connection))
        {
            $this->HandleDBError('Failed to select database: '.$this->database.' Please make sure that the database name provided is correct');
            return false;
        }
        if(!mysql_query("SET NAMES 'UTF8'",$this->connection))
        {
            $this->HandleDBError('Error setting utf8 encoding');
            return false;
        }
        return true;
    }    
    
    function Ensuretable()
    {
        $result = mysql_query("SHOW COLUMNS FROM $this->tablename");   
        if(!$result || mysql_num_rows($result) <= 0)
        {
            return $this->CreateTable();
        }
        return true;
    }
    
    function CreateTable()
    {
        $qry = "Create Table $this->tablename (".
                "id_user INT NOT NULL AUTO_INCREMENT ,".
                "name VARCHAR( 128 ) NOT NULL ,".
                "email VARCHAR( 64 ) NOT NULL ,".
                "phone_number VARCHAR( 16 ) NOT NULL ,".
                "username VARCHAR( 16 ) NOT NULL ,".
                "password VARCHAR( 32 ) NOT NULL ,".
                "confirmcode VARCHAR(32) ,".
                "PRIMARY KEY ( id_user )".
                ")";
                
        if(!mysql_query($qry,$this->connection))
        {
            $this->HandleDBError("Error creating the table \nquery was\n $qry");
            return false;
        }
        return true;
    }
    
    function InsertIntoDB(&$formvars)
    {
    
        $confirmcode = $this->MakeConfirmationMd5($formvars['email']);
        
        $formvars['confirmcode'] = $confirmcode;
        
        $insert_query = 'insert into '.$this->tablename.'(
                name,
                email,
                username,
				phone_number,
				carrier,
                password,
                confirmcode
                )
                values
                (
                "' . $this->SanitizeForSQL($formvars['name']) . '",
                "' . $this->SanitizeForSQL($formvars['email']) . '",
                "' . $this->SanitizeForSQL($formvars['username']) . '",
				"' . $this->SanitizeForSQL($formvars['phone']) . '",
				"' . $this->SanitizeForSQL($formvars['carrier']) . '",
                "' . md5($formvars['password']) . '",
                "' . $confirmcode . '"
                )';      
        if(!mysql_query( $insert_query ,$this->connection))
        {
            $this->HandleDBError("Error inserting data to the table\nquery:$insert_query");
            return false;
        }        
        return true;
    }
    function MakeConfirmationMd5($email)
    {
        $randno1 = rand();
        $randno2 = rand();
        return md5($email.$this->rand_key.$randno1.''.$randno2);
    }
    function SanitizeForSQL($str)
    {
        if( function_exists( "mysql_real_escape_string" ) )
        {
              $ret_str = mysql_real_escape_string( $str );
        }
        else
        {
              $ret_str = addslashes( $str );
        }
        return $ret_str;
    }
    
 /*
    Sanitize() function removes any potential threat from the
    data submitted. Prevents email injections or any other hacker attempts.
    if $remove_nl is true, newline chracters are removed from the input.
    */
    function Sanitize($str,$remove_nl=true)
    {
        $str = $this->StripSlashes($str);

        if($remove_nl)
        {
            $injections = array('/(\n+)/i',
                '/(\r+)/i',
                '/(\t+)/i',
                '/(%0A+)/i',
                '/(%0D+)/i',
                '/(%08+)/i',
                '/(%09+)/i'
                );
            $str = preg_replace($injections,'',$str);
        }

        return $str;
    }    
    function StripSlashes($str)
    {
        if(get_magic_quotes_gpc())
        {
            $str = stripslashes($str);
        }
        return $str;
    }
	
	
	//////////////////////////////////////
	/////External Use Functions///////////
	//////////////////////////////////////
	
	function GetUserDataValues()
	{
		if(!$this->DBLogin())
		{
		    $this->HandleError("Database login failed!");
		    return false;
		} 
		
		$qry = "Select name, email, phone_number, carrier, username from ".$this->tablename." where username='".$_SESSION[$this->GetLoginSessionVar()]."'";
		
		$result = mysql_query($qry,$this->connection);
		
		$row = mysql_fetch_assoc($result);
		
		return $row;
	}
	
	function getAlertSettings()
	{		
		$output = array();
		if(!$this->DBLogin())
		{
		    $this->HandleError("Database login failed!");
		    return false;
		}
		
		$qry = "Select data from ".$this->tablename." where username='".$_SESSION[$this->GetLoginSessionVar()]."'";
		if(!$result = mysql_query($qry, $this->connection))
		{
			$this->HandleError("Query failed!");
			return false;
		}
		$result = mysql_fetch_assoc($result);
		$output = json_decode($result['data'], TRUE);
		 
		return $output;
	}
	
}
?>