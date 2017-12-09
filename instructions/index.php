<?php
/*
Copyright (C) 2017 Tomasz Kielski.

This file is part of printFileManager.

PrintFileManager is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
printFileManager is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Print Master.  If not, see <http://www.gnu.org/licenses/>.
*/

set_language();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Instructions</title>
</head>
<style type="text/css">
.content {
    margin-top: 0%;
    margin-bottom: 0%;
    margin-left: auto;
    right: auto;
    text-align: center;
    left: 50%;
    width: 920px;
    margin-right: auto;
}

p {
	text-align: left;
}
.content h3 {
	text-align: left;
}
.content h2 {
	text-align: left;
}
.content ol li {
	text-align: left;
}
.content ul li {
	text-align: left;
}
.content subp {
	padding-top: 0px;
	padding-right: 0px;
	padding-bottom: 0px;
	padding-left: 5em;
}
.subp {
	padding-left: 2em;
	text-align: left;
}
</style>
<body>
<div class="content">
<h1><a href = "../"><?php echo _("back"); ?></a></h1>
<h2><?php echo _("Instructions of use"); ?></h2>
<p><?php echo _("This php app serves to sum the areas of printing files. "); ?> </p>
<p><?php echo _("The printing files are supposed to be placed in folders and subfolders contained within printing files folder which should be the 
  &quot;<em>public_html</em>&quot; subfolder. "); ?> </p>
<p><?php echo _("The printing areas are calculated automatically on entering the application page. The calculations are based on dimensions encoded in file names like &quot;<em>p5_120x95cm.jpg</em>&quot; or &quot;<em>P01_1536x2420mm.tif</em>&quot;. If the file name doesn't contain dimensions encoded like that, the default 100x100cm printing size will be assumed. If the server php contains Imagemagick extension, then from files with .tif or .jpg extension the printing size will be decoded by Imagemagick. The current application data is stored in json files automatically created inside the application folder. "); ?></p>
<p><?php echo _("The count of printouts should be encoded by appending to file name together with &quot;pcs&quot;, like &quot;<em>P21_297x210mm_67pcs.jpg</em>&quot;."); ?></p>
<p><?php echo _("When logged as a common user you can browse through the printing folders and mark files as printed. The parent foders get automatically marked as printed when all the contained files are marked as printed. When logged as admin you can also change folder attributes. Folders marked as archive are skipped in area calculations.<br />
  <br />
"); ?></p>
<h2><?php echo _("Instalation instructions "); ?></h2>
<p>1. <?php echo _("Create a ftp account with main folder being a &quot;<em>public_html</em>&quot; subfolder. This will be your main printing files folder. You can put all your printing files as a loose collection or structured in subfolders there."); ?></p>
<p>2. <?php echo _("Edit the configuration file &quot;<em>config.php</em>&quot; by entering the printing files folder name and the database settings"); ?>:</p>
<p>3. <?php echo _("Create an application folder in &quot;<em>public_html</em>&quot; folder. Copy the content of &quot;<em>printFileManager</em>&quot; there. "); ?></p>

<ul>
  <li>$ftpDir = 'print';  // <?php echo _("main printing files folder "); ?></li>
</ul>
<div class="subp"><?php echo _("mysql database parameters: "); ?></div>
<ul>
  <li>$host=&quot;localhost&quot;; // <?php echo _("Host name "); ?></li>
  <li>$username=&quot;*********&quot;; // Mysql username</li>
  <li>$password=&quot;*********&quot;; // Mysql password</li>
  <li>$db_name=&quot;printFileManager&quot;; // <?php echo _("Database name "); ?></li>
  <li>$tbl_name=&quot;users&quot;; // <?php echo _("Table name "); ?></li>
</ul>
<p>4. <?php echo _('Open your app in the web browser and run the script <em>createUsersTable.php</em> from the main application folder. This will create  a database table for storing user names, passwords, and roles. The table  contains columns named "username", "password" and "userrole". The demo users values will be inserted into the table. '); ?><br />
  <br />
</p>
<h2>Demo users</h2>
<p><?php echo _("There are two demo users defined"); ?>:</p>
<ul>
  <li> user: demo, password: demo123 (<?php echo _("user role as common user"); ?>)</li>
  <li> user: admin, password: admin123 (<?php echo _("user role as admin, enabled to edit folder statuses"); ?>)<br />
  </li>
</ul>
<p>&nbsp;</p>
<h1><a href = "../"><?php echo _("back"); ?></a></h1>
</div>
</body>
</html>
<?php 
function set_language()
{
	bindtextdomain("pfm", dirname(dirname(__FILE__))."/languages");// gettext file/path
	textdomain("pfm");
	$lang = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
	switch ($lang){
		case "pl":
			putenv("LANGUAGE=pl_PL");
			// translations will be searched in languages/pl_PL/LC_MESSAGES/pfm.mo	
			break;
		 
		default:
			putenv("LANGUAGE=en_EN");
			break;
	}
}
?>