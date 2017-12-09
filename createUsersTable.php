<?php
include('config.php');

// Connect to server and select databse.
mysql_connect("$host", "$username", "$password")or die("cannot connect");
mysql_select_db("$db_name")or die("cannot select DB");


   $sql = "CREATE TABLE users1 (
    username varchar(30) NOT NULL,
    password varchar(30) NOT NULL,
	userrole varchar(12) DEFAULT 'user',
    PRIMARY KEY (username)
)";
   $result=mysql_query($sql);
    if($result) echo "DB created successfully<br>";
	$sql = "INSERT INTO users1 (username, password, userrole) VALUES ('demo', 'demo123', 'commonuser')";
	$result=mysql_query($sql);
	$sql = "INSERT INTO users1 (username, password, userrole) VALUES ('admin', 'admin123', 'admin')";
	$result=mysql_query($sql);
    if($result) echo "DB data inserted successfully<br>";
?>