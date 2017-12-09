<?php
ob_start();
include('config.php');

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect");
mysql_select_db("$db_name")or die("cannot select DB");

// username and password sent from form
$myusername=$_POST['myusername'];
$mypassword=$_POST['mypassword'];

$trackback = "";
if($_REQUEST["trackback"]) {$trackback = $_REQUEST["trackback"];}

// To protect MySQL injection (more detail about MySQL injection)
$myusername = stripslashes($myusername);
$mypassword = stripslashes($mypassword);
$trackback = stripslashes($trackback);
$myusername = mysql_real_escape_string($myusername);
$mypassword = mysql_real_escape_string($mypassword);
$trackback = mysql_real_escape_string($trackback);

$sql="SELECT * FROM ".$tbl_name." WHERE username='$myusername' and password='$mypassword'";
$result=mysql_query($sql);

// Mysql_num_row is counting table row
$count=mysql_num_rows($result);
// If result matched $myusername and $mypassword, table row must be 1 row

if($count==1){
// Register $myusername, $mypassword and redirect to file "login_success.php"
echo "Passed";

session_start ();
$_SESSION['myusername'] = $myusername;
$_SESSION['mypassword'] = $mypassword;
$_SESSION['myuserrole'] = $result[0]['userrole'];

	
header('location:http://'. $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']).$trackback.'');
}
else {
echo "Wrong Username or Password";
}

ob_end_flush();
?>