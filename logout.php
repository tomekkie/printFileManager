<?php
session_start();
$trackback = "";
if($_REQUEST["trackback"]) {$trackback = $_REQUEST["trackback"];}
session_unset();
header('location:http://'. $_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI']).$trackback.'');
exit();
?>