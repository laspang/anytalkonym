<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;
use OTPHP\TOTP;
use ParagonIE\ConstantTime\Base32;

session_start();
class dbConfig
{
    protected $serverName;
    protected $userName;
    protected $password;
    protected $dbName;
    public function dbConfig()
    {
        $this -> serverName = '';
        $this -> userName = '';
        $this -> password = '';
        $this -> dbName = '';
    }
}
class User extends Dbconfig
{
    protected $hostName;
    protected $userName;
    protected $password;
    protected $dbName;
    private $userTable = 'user';
    private $dbConnect = false;
    public function __construct()
    {
        if (str_contains(dirname("__file__"), "notify")||str_contains(dirname("__file__"), "admin")||str_contains(dirname("__file__"), "2fa"||str_contains(dirname("__file__"), "lang"))||str_contains(dirname("__file__"), "include")) {
            require "../settings.php";
        } else {
            require "settings.php";
        }
        
        if (!$this->dbConnect) {
            $database = new dbConfig();
            $this -> hostName = $db_hostname;
            $this -> userName = $db_username;
            $this -> password =$db_password;
            $this -> dbName = $db_dbname;
        }
    }
    public function getconn()
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        return $conn;
    }

    private function getData($sqlQuery)
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $result = pg_query($coninfo, $sqlQuery);
        if (!$result) {
            die('Error in query: '. pg_error());
        }
        $data= array();
        while ($row = pg_fetch_array($result, PGSQL_ASSOC)) {
            $data[]=$row;
        }
        return $data;
    }
    private function getNumRows($sqlQuery)
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $result = pg_query($coninfo, $sqlQuery);
        if (!$result) {
            die('Error in query: '. pg_error());
        }
        $numRows = pg_num_rows($result);
        return $numRows;
    }
    public function loginStatus()
    {
        if (empty($_SESSION["userid"])) {
            header("Location: /login.php");
        } //
    }
    public function loginreally()
    {
        if (!empty($_SESSION["userid"])) {
            return true;
        } else {
            return false;
        }
    }
    public function login()
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $errorMessage = '';
        if ($_SERVER['REQUEST_METHOD']=='POST') {
            if (!empty($_POST['g-recaptcha-response'])) {
                if ($this->verifycaptcha($_POST['g-recaptcha-response'])) {
                    if (!empty($_POST["loginId"]) && $_POST["loginId"]!=''&& $_POST["loginPass"]!='') {
                        $loginId = $_POST['loginId'];
                        $password = $_POST['loginPass'];
                        //encrypt function
                        function encrypt($key, $payload)
                        {
                            $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
                            $encrypted = openssl_encrypt($payload, 'aes-256-cbc', $key, 0, $iv);
                            return base64_encode($encrypted . '::' . $iv);
                        }

                        function decrypt($key, $garble)
                        {
                            list($encrypted_data, $iv) = explode('::', base64_decode($garble), 2);
                            return openssl_decrypt($encrypted_data, 'aes-256-cbc', $key, 0, $iv);
                        }
                                            if (isset($_COOKIE["loginPass"]) && $_COOKIE["loginPass"] == $password) {
                            $password =decrypt($key, $_COOKIE["loginPass"]) ;
                        } else {
                            $password = $password;
                            
                        }
                        $sqlQuery = "SELECT * FROM \"".$this->userTable."\"
				WHERE email='".$loginId."' AND status = 'active'";
                        $resultSet = pg_query($coninfo, $sqlQuery);
                        $isValidLogin = pg_num_rows($resultSet);
                        if ($isValidLogin) {
                            $row = pg_fetch_row($resultSet);
                            if (password_verify($password, $row[4])) {
                                if (!empty($_POST["remember"]) && $_POST["remember"] != '') {
                                    setcookie("loginId", $loginId, time()+ (10 * 365 * 24 * 60 * 60));
                                    setcookie("loginPass", encrypt($key, $password), time()+ (10 * 365 * 24 * 60 * 60));
                                } else {
                                    $_COOKIE['loginId' ]='';
                                    $_COOKIE['loginPass'] = '';
                                }
                                //TOTP verify
                                $checktotpenablequery = "SELECT \"TOTPenable\" FROM \"user\"  WHERE id=$row[0]";
                                $enablestatus=pg_query($coninfo, $checktotpenablequery);
                                $status = pg_fetch_row($enablestatus);
                                if ($status[0]=="f") {
                                    $_SESSION["userid"] = $row[0];
                                    $_SESSION["name"] = $row[1]." ".$row[2];
                                    header("location: /2fa/park.php");
                                } elseif ($status[0]=="t") {
                                    //opps totp is enabled
                                    $_SESSION["totpuserid"] = $row[0];
                                    $_SESSION["totpname"] = $row[1]." ".$row[2];
                                    $_SESSION["passwordverifyed"]=true;
                                    header("Location: 2fa/verift.html");
                                } else {
                                    exit(1);
                                }
                            } else {
                                $errorMessage .= "Invalid login!";
                            }
                        } else {
                            $errorMessage = "Invalid login!";
                        }
                    } elseif (!empty($_POST["loginId"])) {
                        $errorMessage = "Enter Both user and password!";
                    }
                } else {
                    $errorMessage = "Robot? Sorry, you might need to solve the captcha first.";
                }
            } else {
                $errorMessage = "Robot? Sorry, you might need to solve the captcha first.";
            }
        } else {
            $errorMessage="Please login";
        }
        return $errorMessage;
    }
    public function adminLoginStatus()
    {
        if (empty($_SESSION["adminUserid"])) {
            header("Location: index.php");
        }
    }
    public function adminLogin()
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $errorMessage = '';
        if (!empty($_POST["login"]) && $_POST["email"]!=''&& $_POST["password"]!='') {
            $email = $_POST['email'];
            $password = $_POST['password'];
            $sqlQuery = "SELECT * FROM \"".$this->userTable."\"
				WHERE email='".$email."' AND status = 'active' AND type = 'administrator'";//finish auth process replace m d 5 with password_hash (bcrypt)
            $resultSet = pg_query($coninfo, $sqlQuery);
            $isValidLogin = pg_num_rows($resultSet);
            if ($isValidLogin) {
                $row = pg_fetch_row($resultSet);
                if (password_verify($password, $row[4])) {
                    $_SESSION["adminUserid"] = $row[0];
                    $_SESSION["admin"] = $row[1]." ".$row[2];

                    header("location: dashboard.php");
                } else {
                    $errorMessage = "Invalid login!";
                }
            } else {
                $errorMessage = "Invalid login!";
            }
        } elseif (!empty($_POST["login"])) {
            $errorMessage = "Enter Both user and password!";
        }
        return $errorMessage;
    }
    public function register()
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $message = '';
        if (!empty($_POST['g-recaptcha-response'])) {
            if ($this->verifycaptcha($_POST['g-recaptcha-response'])) {
                if ($_POST["email"] !='') {
                    $sqlQuery = "SELECT * FROM \"user\"
				WHERE email='".$_POST["email"]."'";
                    $conn = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
                    $result = pg_query($conn, $sqlQuery);
                    $isUserExist = pg_num_rows($result);
                    if ($isUserExist) {
                        $message = "User already exist with this email address.";
                    } else {
                        $regex = '/[0-9]*\\.[0-9]+@ijs\\.freeddns\\.org/i';//regular expression for email here
                        if (preg_match($regex, $_POST["email"])) {
                            //checkemail
                            $authtoken = $this->getAuthtoken($_POST["email"]);
                            $insertQuery = "INSERT INTO \"user\" (username, first_name, last_name, email, password, authtoken)
				VALUES ('".$_POST["username"]."', '".$_POST["firstname"]."', '".$_POST["lastname"]."', '".$_POST["email"]."', '".password_hash($_POST["passwd"], PASSWORD_DEFAULT)."', '".$authtoken."')";
                            $userSaved = pg_query($conn, $insertQuery);
                            if ($userSaved) {
                                require 'vendor/autoload.php';
                                $mail = new PHPMailer(true);

                                try {
                                    switch ($smtp_auth) {
      case "SMTPS":    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;break;
      case "STARTTLS":    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;break;
      case "none":    $mail->SMTPSecure = false;break;
      
    }
                                    $mail->isSMTP();
                                    $mail->Host       = $smtp_hostname;
                                    $mail->SMTPAuth   = $smtp_authme;
                                    $mail->Username   = $smtp_username;
                                    $mail->Password   = $smtp_password;
                                    $mail->Port       = $smtp_port;
                                    //Recipients
                                    $mail->setFrom($smtp_senderaddress, $smtp_sendername);
                                    $mail->addAddress($_POST["email"], $_POST['firstname'].' '.$_POST['lastname']);
                                    //Content
                                    $mail->isHTML(false);
                                    $link = "<".$servername."/verify.php?authtoken=".$authtoken.">Verify Email";
                                    $mail->Subject = 'Verify email to complete registration';
                                    $mail->Body    = 'Hi there, click on this '.$link.' to verify email to complete registration.';
                                    $mail->send();
                                    
                                    $message = "Verification email send to your email address. Please check email and verify to complete registration.";
                                } catch (Exception $e) {
                                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                                }
                            } else {
                                $message = "User register request failed.";
                            }
                        } else {
                            $message= "Invalided email";
                        }
                    }
                }
            } else {
                $errorMessage = "Robot? Sorry, you might need to solve the captcha first.";
            }
        } else {
            $errorMessage = "Robot? Sorry, you might need to solve the captcha first.";
        }
        return $message;
    }
    public function getAuthtoken($email)
    {
        $code = $this->gensalt();
        $authtoken = $code."".hash("sha512", $email);
        return $authtoken;
    }
    public function verifyRegister()
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $verifyStatus = 0;
        if (!empty($_GET["authtoken"]) && $_GET["authtoken"] != '') {
            $sqlQuery = "SELECT * FROM \"".$this->userTable."\"
				WHERE authtoken='".$_GET["authtoken"]."'";
            $resultSet = pg_query($coninfo, $sqlQuery);
            $isValid = pg_num_rows($resultSet);
            if ($isValid) {
                $userDetails = pg_fetch_assoc($resultSet);
                if ($_GET["authtoken"]) {
                    $updateQuery = "UPDATE \"".$this->userTable."\" SET status = 'active'
						WHERE id='".$userDetails['id']."'";
                    $isUpdated = pg_query($coninfo, $updateQuery);
                    if ($isUpdated) {
                        $verifyStatus = 1;
                        $authtoken = $this->getAuthtoken($userDetails['email']);
                        $sql = 'UPDATE "user" SET authtoken = \''.$authtoken.'\' WHERE email=\''.$userDetails["email"].'\'';
                        $re = pg_query($coninfo, $sql);//skip result only should be great
                    }
                }
            }
        }
        return $verifyStatus;
    }
    public function userDetails()
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $sqlQuery = "SELECT * FROM \"".$this->userTable."\"
			WHERE id ='".$_SESSION["userid"]."'";
        $result = pg_query($coninfo, $sqlQuery);
        $userDetails = pg_fetch_assoc($result);
        return $userDetails;
    }
    public function editAccount()
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $message = '';
        $updatePassword = '';
        if (!empty($_POST["passwd"]) && $_POST["passwd"] != '' && $_POST["passwd"] != $_POST["cpasswd"]) {
            $message = "Confirm passwords do not match.";
        } elseif (!empty($_POST["passwd"]) && $_POST["passwd"] != '' && $_POST["passwd"] == $_POST["cpasswd"]) {
            $updatePassword = ", password='".password_hash($_POST["passwd"], PASSWORD_DEFAULT)."' ";
        }
        $updateQuery = "UPDATE \"".$this->userTable."\"
			SET username='".$_POST["username"]."' first_name = '".$_POST["firstname"]."', last_name = '".$_POST["lastname"]."', email = '".$_POST["email"]."', mobile = '".$_POST["mobile"]."' , designation = '".$_POST["designation"]."', gender = '".$_POST["gender"]."' $updatePassword
			WHERE id ='".$_SESSION["userid"]."'";
        $isUpdated = pg_query($coninfo, $updateQuery);
        if ($isUpdated) {
            $_SESSION["name"] = $_POST['firstname']." ".$_POST['lastname'];
            $message = "Account details saved.";
        }
        return $message;
    }
    public function resetPassword()
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $message = '';
        if ($_POST['email'] == '') {
            $message = "Please enter email to proceed with password reset";
        } else {
            $sqlQuery = "
				SELECT email
				FROM \"".$this->userTable."\"
				WHERE email='".$_POST['email']."'";
            $result = pg_query($coninfo, $sqlQuery);
            $numRows = pg_num_rows($result);
            if ($numRows) {
                $user = pg_fetch_assoc($result);
                $authtoken = $this->getAuthtoken($user['email']);
                $sql = 'UPDATE "user" SET authtoken = \''.$authtoken.'\' WHERE email=\''.$user["email"].'\'';
                $re = pg_query($coninfo, $sql);//skip result only should be great
                require 'vendor/autoload.php';
                $mail = new PHPMailer(true);
                
                try {
                    switch ($smtp_auth) {
      case "SMTPS":    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;break;
      case "STARTTLS":    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;break;
      case "none":    $mail->SMTPSecure = false;break;
      
    }
                    $mail->isSMTP();
                    $mail->Host       = $smtp_hostname;
                    $mail->SMTPAuth   = $smtp_authme;
                    $mail->Username   = $smtp_username;
                    $mail->Password   = $smtp_password;
                    $mail->Port       = $smtp_port;
                    //Recipients
                    $mail->setFrom($smtp_senderaddress, $smtp_sendername);

                    $mail->addAddress($user['email'], $_POST['firstname'].' '.$_POST['lastname']);
                    //Content
                    $mail->isHTML(false);
                    $link="<".$servername."/reset_password.php?authtoken=".$authtoken.">Reset Password";
                    $mail->Subject = 'Reset your password on examplesite.com';
                    $mail->Body    = "Hi there, click on this ".$link." to reset your password.";
                    $mail->send();
                    echo 'Message has been sent';
                    $message =  "Password reset link send. Please check your mailbox to reset password.";
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
            } else {
                $message = "No account exist with entered email address.";
            }
        }
        return $message;
    }
    public function savePassword()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $message = '';
        if ($_POST['password'] != $_POST['cpassword']) {
            $message = "Password does not match the confirm password.";
        } elseif ($_POST['authtoken']) {
            $sqlQuery = "
				SELECT email, authtoken
				FROM \"".$this->userTable."\"
				WHERE authtoken='".$_POST['authtoken']."'";
            $result = pg_query($coninfo, $sqlQuery);
            $numRows = pg_num_rows($result);
            if ($numRows) {
                $userDetails = pg_fetch_assoc($result);
                $authtoken = $userDetails['authtoken'];
                if ($authtoken == $_POST['authtoken']) {
                    $nauthtoken = $this->getAuthtoken($userDetails['email']);
                    $sqlUpdate = "
						UPDATE \"".$this->userTable."\"
						SET password='".password_hash($_POST['password'], PASSWORD_DEFAULT)."', authtoken ='".$nauthtoken."'
						WHERE email='".$userDetails['email']."' AND authtoken='".$authtoken."'";
                    $isUpdated = pg_query($coninfo, $sqlUpdate);
                    if ($isUpdated) {
                        $message = "Password saved successfully. Please <a href='login.php'>Login</a> to access account.";
                    }
                } else {
                    $message = "Invalid password change request1.";
                }
            } else {
                $message = "Invalid password change request2.";
            }
        }
        return $message;
    }
    public function getUserList()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $sqlQuery = "SELECT * FROM \"".$this->userTable."\" WHERE id <>".$_SESSION['adminUserid']." ";
        if (!empty($_POST["search"]["value"])) {
            $sqlQuery .= '(id LIKE \'%'.$_POST["search"]["value"].'%\' ';
            $sqlQuery .= ' OR first_name LIKE \'%'.$_POST["search"]["value"].'%\' ';
            $sqlQuery .= ' OR last_name LIKE \'%'.$_POST["search"]["value"].'%\' ';
            $sqlQuery .= ' OR designation LIKE \'%'.$_POST["search"]["value"].'%\' ';
            $sqlQuery .= ' OR status LIKE \'%'.$_POST["search"]["value"].'%\' ';
            $sqlQuery .= ' OR username LIKE \'%'.$_POST["search"]["value"].'%\' ';
            $sqlQuery .= ' OR mobile LIKE \'%'.$_POST["search"]["value"].'%\') ';
        }
        if (!empty($_POST["order"])) {
            $sqlQuery .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
        } else {
            $sqlQuery .= 'ORDER BY id DESC ';
        }
        if ($_POST["length"] != -1) {
            $sqlQuery .= 'LIMIT ' . $_POST['length']. ' OFFSET ' . $_POST['start'] . '';
        }
        $result = pg_query($coninfo, $sqlQuery);

        $sqlQuery1 = "SELECT * FROM \"".$this->userTable."\" WHERE id !='".$_SESSION['adminUserid']."' ";
        $result1 = pg_query($coninfo, $sqlQuery1);
        $numRows = pg_num_rows($result1);

        $userData = array();
        while ($users = pg_fetch_assoc($result)) {
            $userRows = array();
            $status = '';
            if ($users['status'] == 'active') {
                $status = '<span class="label label-success">Active</span>';
            } elseif ($users['status'] == 'pending') {
                $status = '<span class="label label-warning">Inactive</span>';
            } elseif ($users['status'] == 'deleted') {
                $status = '<span class="label label-danger">Deleted</span>';
            }
            $userRows[] = $users['id'];
            $userRows[] = ucfirst($users['first_name']." ".$users['last_name']);
            $userRows[] = $users['username'];
            $userRows[] = $users['gender'];
            $userRows[] = $users['email'];
            $userRows[] = $users['mobile'];
            $userRows[] = $users['type'];
            $userRows[] = $status;
            $userRows[] = '<button type="button" name="update" id="'.$users["id"].'" class="btn btn-warning btn-xs update">Update</button>';
            $userRows[] = '<button type="button" name="delete" id="'.$users["id"].'" class="btn btn-danger btn-xs delete" >Delete</button>';
            $userData[] = $userRows;
        }
        $output = array(
            "draw"				=>	intval($_POST["draw"]),
            "recordsTotal"  	=>  $numRows,
            "recordsFiltered" 	=> 	$numRows,
            "data"    			=> 	$userData
        );
        echo json_encode($output);
    }
    public function deleteUser()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        if ($_POST["userid"]) {
            $sqlUpdate = "
				UPDATE \"".$this->userTable."\" SET status = 'deleted'
				WHERE id = '".$_POST["userid"]."'";
            pg_query($coninfo, $sqlUpdate);
        }
    }
    public function getUser()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $sqlQuery = "
			SELECT * FROM \"".$this->userTable."\"
			WHERE id = '".$_POST["userid"]."'";
        $result = pg_query($coninfo, $sqlQuery);
        $row = pg_fetch_array($result, PGSQL_ASSOC);
        echo json_encode($row);
    }
    public function updateUser()
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        if ($_POST['userid']) {
            $updateQuery = "UPDATE \"".$this->userTable."\"
			SET username= '".$_POST['username']."'first_name = '".$_POST["firstname"]."', last_name = '".$_POST["lastname"]."', email = '".$_POST["email"]."', mobile = '".$_POST["mobile"]."' , designation = '".$_POST["designation"]."', gender = '".$_POST["gender"]."', status = '".$_POST["status"]."', type = '".$_POST['user_type']."'
			WHERE id ='".$_POST["userid"]."'";
            $isUpdated = pg_query($coninfo, $updateQuery);
        }
    }
    public function saveAdminPassword()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $message = '';
        if ($_POST['password'] && $_POST['password'] != $_POST['cpassword']) {
            $message = "Password does not match the confirm password.";
        } else {
            $sqlUpdate = "
				UPDATE \"".$this->userTable."\"
				SET password='".password_hash($_POST['password'], PASSWORD_DEFAULT)."'
				WHERE id='".$_SESSION['adminUserid']."' AND type='administrator'";
            $isUpdated = pg_query($coninfo, $sqlUpdate);
            if ($isUpdated) {
                $message = "Password saved successfully.";
            }
        }
        return $message;
    }
    public function adminDetails()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $sqlQuery = "SELECT * FROM \"".$this->userTable."\"
			WHERE id ='".$_SESSION["adminUserid"]."'";
        $result = pg_query($coninfo, $sqlQuery);
        $userDetails = pg_fetch_assoc($result);
        return $userDetails;
    }
    public function addUser()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        if ($_POST["email"]) {
            $authtoken = $this->getAuthtoken($_POST['email']);
            $insertQuery = "INSERT INTO \"".$this->userTable."\"(username,first_name, last_name, email, gender, password, mobile, designation, type, status, authtoken)
				VALUES ('".$_POST['username']."''".$_POST["firstname"]."', '".$_POST["lastname"]."', '".$_POST["email"]."', '".$_POST["gender"]."', '".password_hash($_POST["password"], PASSWORD_DEFAULT)."', '".$_POST["mobile"]."', '".$_POST["designation"]."', '".$_POST['user_type']."', 'active', '".$authtoken."')";
            $userSaved = pg_query($coninfo, $insertQuery);
        }
    }
    public function totalUsers($status)
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $query = '';
        if ($status) {
            $query = " AND status = '".$status."'";
        }
        $sqlQuery = "SELECT * FROM \"".$this->userTable."\"
		WHERE id !='".$_SESSION["adminUserid"]."' $query";
        $result = pg_query($coninfo, $sqlQuery);
        $numRows = pg_num_rows($result);
        return $numRows;
    }
    public function gensalt()
    {
        $str=("1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM");
        $i=1;
        $word="$";
        while ($i<=50) {
            $word .=substr($str, rand(0, 61), 1);
            $i+=1;
        }
        return $word;
    }
    public function verifycaptcha($capatcharesult)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array("response"=>$capatcharesult, "secret"=>"")));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($ch);

        $resonse = json_decode($output, true);
        curl_close($ch);
        if ($resonse['success']=="true") {
            return true;
        } else {
            return false;
        }
    }
    public function totpgensalt()
    {
        $str=("1234567890qwertyuiopasdfghjklzxcvbnmQWERTYUIOPASDFGHJKLZXCVBNM");
        $i=1;
        $word="";
        while ($i<=16) {
            $word .=substr($str, rand(0, 62), 1);
            $i+=1;
        }
        return $word;
    }
    public function gentotp($id)
    {
       
        require '../vendor/autoload.php';
        $secret=trim(Base32::encodeUpper(random_bytes(128)), '=');
        $totp = TOTP::create($secret, 30, 'sha1', 10);
        $_SESSION["gentotp"]=$secret;
        $totp->setLabel('anytalkonym user'); 
        $totp->setIssuer('3AN Service');
        $grCodeUri = $totp->getQrCodeUri(
            'https://api.qrserver.com/v1/create-qr-code/?data=[DATA]&size=300x300&ecc=M',
            '[DATA]'
        );

        return "<img src='{$grCodeUri}'>".$totp->getSecret().$totp->getProvisioningUri();
    }
    public function vertotp($id, $code)
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $secret=$_SESSION['gentotp'];
        var_dump($secret);
        echo"<br>";
        require '../vendor/autoload.php';
        $totp = TOTP::create($secret, 30, 'sha1', 10);
        var_dump($totp->now());
        if ($totp->now()==$code) {
            $sqlQuery="UPDATE \"user\" SET \"TOTPenable\"=true, \"TOTP\"='$session' WHERE id='$id'";
            $go=pg_query($coninfo, $sqlQuery);
            if ($go) {
                $_SESSION['gentotp']="";
                echo "good";
            } else {
                echo "SERVER INTERNEL ERROR";
            }
        } else {
            echo "code error";
        }
    }
    public function verifytotp($id, $code)
    {
        if ($_SESSION["passwordverifyed"]) {
            $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
            if ($id) {
                $insertQuery = "SELECT TOTP FROM \"".$this->userTable."\" WHERE id='$id'";
                $getdata = pg_query($coninfo, $insertQuery);
                $row=pg_num_rows($getdata);
                $totp = TOTP::create($row[0], 30, 'sha512', 10);
                if ($totp->verify($code)) {
                    $_SESSION["userid"]=$_SESSION["totpuserid"];
                    $_SESSION["name"]=$_SESSION["totpname"];
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            http_response_code(403);
            exit(1);
        }
    }
    public function notify($uid, $type, $content, $from)
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        if (!isset($from)) {
            $from="System";
        }
        if (!isset($type)) {
            $type="General";
        }
        $time= date("Y-m-d h:i:sa");
        $sqlQuery="INSERT INTO \"notification\"(uid,type,content,time,\"from\") VALUES($uid,'$type','$content','$time','$from')";
        $exec=pg_query($coninfo, $sqlQuery);
        if ($exec) {
            return true;
        } else {
            return false;
        }
    }
    public function readnotify($uid)
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $sqlQuery="SELECT * FROM \"notification\" WHERE uid=$uid ORDER BY nid DESC";
        ($notifyresult=pg_query($coninfo, $sqlQuery))or die("SERVER INTERNEL ERROR");//special
        $i=0;
        while ($row = pg_fetch_row($notifyresult)) {
            $realquery[0]=$row[5];
            $realquery[1]=$row[3];
            $realquery[2]=$row[4];
            $realquery[3]=$row[2];
            $realquery[4]=$row[6];
            $realquery[5]=$row[0];
            $query[$i]=$realquery;
            $i++;
        }
        return json_encode($query);
    }
    public function readednotify($uid)
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $sqlQuery="UPDATE notification SET status=true WHERE uid=$uid";
        if (pg_query($coninfo, $sqlQuery)) {
            return true;
        } else {
            return false;
        }
    }
    public function countnotify($uid)
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $Sqlquery="SELECT COUNT (*) FROM notification WHERE uid=$uid AND status=false";
        $return=pg_query($coninfo, $Sqlquery);
        $data = pg_fetch_row($return);
        return $data[0];
        //should return the unread notify count
    }
    public function getuserid($username)
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $sqlquery="SELECT id FROM \"user\" WHERE username='$username'";
        $result=pg_query($coninfo, $sqlquery);
        $rresult=pg_fetch_row($result);
        return $rresult[0];
    }
    public function updatebio($bio, $userid)
    {
        $coninfo = pg_connect("host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $sqlquery="UPDATE \"user\" SET bios='$bio' WHERE id=$userid";
        $reusult=pg_query($coninfo, $sqlquery);
        if ($reusult) {
            return true;
        } else {
            return false;
        }
    }
}
