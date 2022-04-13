<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

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
        if (!$this->dbConnect) {
            $db_hostname = '';
            $db_username = '';
            $db_password ='';
            $db_dbname = '';
            $set_recaptchakey='';
            $database = new dbConfig();
            $this -> hostName = $db_hostname;
            $this -> userName = $db_username;
            $this -> password =$db_password;
            $this -> dbName = $db_dbname;
        }
    }
    public function getconn()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        return $conn;
    }

    private function getData($sqlQuery)
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $result = pg_query($coninfo, $sqlQuery);
        if (!$result) {
            die('Error in query: '. pg_error());
        }
        $data= array();
        while ($row = pg_fetch_array($result, MYSQLI_ASSOC)) {
            $data[]=$row;
        }
        return $data;
    }
    private function getNumRows($sqlQuery)
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
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
            header("Location: login.php");
        } //
    }
    public function login()
    {//useless i guess
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $errorMessage = '';
        if (!empty($_POST["login"]) && $_POST["loginId"]!=''&& $_POST["loginPass"]!='') {
            $loginId = $_POST['loginId'];
            $password = $_POST['loginPass'];
            if (isset($_COOKIE["loginPass"]) && $_COOKIE["loginPass"] == $password) {
                $password = $_COOKIE["loginPass"];
            } else {
                $password = md5($password);
            }
            $sqlQuery = "SELECT * FROM \"".$this->userTable."\"
				WHERE email='".$loginId."' AND password='".$password."' AND status = 'active'";
            $resultSet = pg_query($coninfo, $sqlQuery);
            $isValidLogin = pg_num_rows($resultSet);
            if ($isValidLogin) {
                if (!empty($_POST["remember"]) && $_POST["remember"] != '') {
                    setcookie("loginId", $loginId, time()+ (10 * 365 * 24 * 60 * 60));
                    setcookie("loginPass", $password, time()+ (10 * 365 * 24 * 60 * 60));
                } else {
                    $_COOKIE['loginId' ]='';
                    $_COOKIE['loginPass'] = '';
                }
                $userDetails = pg_fetch_assoc($resultSet);
                $_SESSION["userid"] = $userDetails['id'];
                $_SESSION["name"] = $userDetails['first_name']." ".$userDetails['last_name'];
                header("location: index.php");
            } else {
                $errorMessage = "Invalid login!";
            }
        } elseif (!empty($_POST["loginId"])) {
            $errorMessage = "Enter Both user and password!";
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
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
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
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $message = '';
        if (!empty($_POST["register"]) && $_POST["email"] !='') {
            $sqlQuery = "SELECT * FROM \"user\"
				WHERE email='".$_POST["email"]."'";
            $conn = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
            $result = pg_query($conn, $sqlQuery);
            $isUserExist = pg_num_rows($result);
            if ($isUserExist) {
                $message = "User already exist with this email address.";
            } else {
                $regex = '/[0-9]*\\.[0-9]+@tmjh\\.tp\\.edu\\.tw/i';
                if (preg_match($regex, $_POST["email"])) {
                    $regex = '/[0-9]*\\.[0-9]+@tmjh\\.tp\\.edu\\.tw/i';


                    //checkemail
                    $authtoken = $this->getAuthtoken($_POST["email"]);
                    $insertQuery = "INSERT INTO \"user\" (first_name, last_name, email, password, authtoken)
				VALUES ('".$_POST["firstname"]."', '".$_POST["lastname"]."', '".$_POST["email"]."', '".md5($_POST["passwd"])."', '".$authtoken."')";
                    $userSaved = pg_query($conn, $insertQuery);
                    if ($userSaved) {


                    //Load Composer's autoloader
                        require 'vendor/autoload.php';

                        //Create an instance; passing `true` enables exceptions
                        $mail = new PHPMailer(true);

                        try {
                            //Server settings
                            $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                            $mail->isSMTP();                                            //Send using SMTP
                        $mail->Host       = '';                     //Set the SMTP server to send through
                        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
                        $mail->Username   = '';                     //SMTP username
                        $mail->Password   = '';                               //SMTP password
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
                        $mail->Port       = 587;                                             //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

                        //Recipients
                            $mail->setFrom('', '');
                            $mail->addAddress($_POST["email"], 'User');     //Add a recipient
                        //Content
                        $mail->isHTML(false);                                  //Set email format to HTML
                        $link = "<a href='http://webdamn.com/demo/user-management-system/verify.php?authtoken=".$authtoken."'>Verify Email</a>";
                            $mail->Subject = 'Verify email to complete registration';
                            $mail->Body    = 'Hi there, click on this '.$link.' to verify email to complete registration.';

                            $mail->send();
                            echo 'Message has been sent';
                            $message = "Verification email send to your email address. Please check email and verify to complete registration.";
                        } catch (Exception $e) {
                            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                        }
                        //					$link = "<a href='http://webdamn.com/demo/user-management-system/verify.php?authtoken=".$authtoken."'>Verify Email</a>";
//					$toEmail = $_POST["email"];
//					$subject = "Verify email to complete registration";
//					$msg = "Hi there, click on this ".$link." to verify email to complete registration.";
//					$msg = wordwrap($msg,70);
//					$headers = "From: info@webdamn.com";
//					if(mail($toEmail, $subject, $msg, $headers)) {
//						$message = "Verification email send to your email address. Please check email and verify to complete registration.";
//					}
                    } else {
                        $message = "User register request failed.";
                    }
                } else {
                    $message= "You must use your school email AND must me a student at tmjh.tp.edu.tw";
                }
            }
        }
        return $message;
    }
    public function getAuthtoken($email)
    {
        $code = md5(889966);
        $authtoken = $code."".md5($email);
        return $authtoken;
    }
    public function verifyRegister()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $verifyStatus = 0;
        if (!empty($_GET["authtoken"]) && $_GET["authtoken"] != '') {
            $sqlQuery = "SELECT * FROM \"".$this->userTable."\"
				WHERE authtoken='".$_GET["authtoken"]."'";
            $resultSet = pg_query($coninfo, $sqlQuery);
            $isValid = pg_num_rows($resultSet);
            if ($isValid) {
                $userDetails = pg_fetch_assoc($resultSet);
                $authtoken = $this->getAuthtoken($userDetails['email']);
                if ($authtoken == $_GET["authtoken"]) {
                    $updateQuery = "UPDATE \"".$this->userTable."\" SET status = 'active'
						WHERE id='".$userDetails['id']."'";
                    $isUpdated = pg_query($coninfo, $updateQuery);
                    if ($isUpdated) {
                        $verifyStatus = 1;
                    }
                }
            }
        }
        return $verifyStatus;
    }
    public function userDetails()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $sqlQuery = "SELECT * FROM \"".$this->userTable."\"
			WHERE id ='".$_SESSION["userid"]."'";
        $result = pg_query($coninfo, $sqlQuery);
        $userDetails = pg_fetch_assoc($result);
        return $userDetails;
    }
    public function editAccount()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $message = '';
        $updatePassword = '';
        if (!empty($_POST["passwd"]) && $_POST["passwd"] != '' && $_POST["passwd"] != $_POST["cpasswd"]) {
            $message = "Confirm passwords do not match.";
        } elseif (!empty($_POST["passwd"]) && $_POST["passwd"] != '' && $_POST["passwd"] == $_POST["cpasswd"]) {
            $updatePassword = ", password='".md5($_POST["passwd"])."' ";
        }
        $updateQuery = "UPDATE \"".$this->userTable."\"
			SET first_name = '".$_POST["firstname"]."', last_name = '".$_POST["lastname"]."', email = '".$_POST["email"]."', mobile = '".$_POST["mobile"]."' , designation = '".$_POST["designation"]."', gender = '".$_POST["gender"]."' $updatePassword
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
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $message = '';
        if ($_POST['email'] == '') {
            $message = "Please enter username or email to proceed with password reset";
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
                //Load Composer's autoloader
                require 'vendor/autoload.php';

                //Create an instance; passing `true` enables exceptions
                $mail = new PHPMailer(true);
                $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
                try {
                    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = '';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = '';                     //SMTP username
    $mail->Password   = '';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 587;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
                    $mail->setFrom('', '');
                    $mail->addAddress($user['email'], 'User');     //Add a recipient
    //Content
    $mail->isHTML(false);                                  //Set email format to HTML
    $link="<a href='/reset_password.php?authtoken=".$authtoken."'>Reset Password</a>";
                    $mail->Subject = 'Reset your password on examplesite.com';
                    $mail->Body    = "Hi there, click on this ".$link." to reset your password.";
                    //$mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

                    $mail->send();
                    echo 'Message has been sent';
                    $message =  "Password reset link send. Please check your mailbox to reset password.";
                } catch (Exception $e) {
                    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
                }
                //				$link="<a href='https://www.webdamn.com/demo/user-management-system/reset_password.php?authtoken=".$authtoken."'>Reset Password</a>";
//				$toEmail = $user['email'];
//				$subject = "Reset your password on examplesite.com";
//				$msg = "Hi there, click on this ".$link." to reset your password.";
//				$msg = wordwrap($msg,70);
//				$headers = "From: info@webdamn.com";
//				if(mail($toEmail, $subject, $msg, $headers)) {
//					$message =  "Password reset link send. Please check your mailbox to reset password.";
//				}
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
                $authtoken = $this->getAuthtoken($userDetails['email']);
                if ($authtoken == $_POST['authtoken']) {
                    $sqlUpdate = "
						UPDATE \"".$this->userTable."\"
						SET password='".md5($_POST['password'])."'
						WHERE email='".$userDetails['email']."' AND authtoken='".$authtoken."'";
                    $isUpdated = pg_query($coninfo, $sqlUpdate);
                    if ($isUpdated) {
                        $message = "Password saved successfully. Please <a href='login.php'>Login</a> to access account.";
                    }
                } else {
                    $message = "Invalid password change request.";
                }
            } else {
                $message = "Invalid password change request.";
            }
        }
        return $message;
    }
    public function getUserList()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        $sqlQuery = "SELECT * FROM \"".$this->userTable."\" WHERE id <>".$_SESSION['adminUserid']." ";
        if (!empty($_POST["search"]["value"])) {
            $sqlQuery .= '(id LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR first_name LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR last_name LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR designation LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR status LIKE "%'.$_POST["search"]["value"].'%" ';
            $sqlQuery .= ' OR mobile LIKE "%'.$_POST["search"]["value"].'%") ';
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
        $row = pg_fetch_array($result, MYSQLI_ASSOC);
        echo json_encode($row);
    }
    public function updateUser()
    {
        $coninfo = pg_connect("  host=$this->hostName user=$this->userName password=$this->password dbname=$this->dbName sslmode=require");
        if ($_POST['userid']) {
            $updateQuery = "UPDATE \"".$this->userTable."\"
			SET first_name = '".$_POST["firstname"]."', last_name = '".$_POST["lastname"]."', email = '".$_POST["email"]."', mobile = '".$_POST["mobile"]."' , designation = '".$_POST["designation"]."', gender = '".$_POST["gender"]."', status = '".$_POST["status"]."', type = '".$_POST['user_type']."'
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
				SET password='".md5($_POST['password'])."'
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
            $insertQuery = "INSERT INTO \"".$this->userTable."\"(first_name, last_name, email, gender, password, mobile, designation, type, status, authtoken)
				VALUES ('".$_POST["firstname"]."', '".$_POST["lastname"]."', '".$_POST["email"]."', '".$_POST["gender"]."', '".password_hash($_POST["password"], PASSWORD_DEFAULT)."', '".$_POST["mobile"]."', '".$_POST["designation"]."', '".$_POST['user_type']."', 'active', '".$authtoken."')";
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
}
