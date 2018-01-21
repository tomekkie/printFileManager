<?php 
/*
Copyright (C) 2017 Tomasz Kielski.

PrintFileManager is free software: you can redistribute it and/or modify it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or (at your option) any later version.
printFileManager is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with Print Master.  If not, see <http://www.gnu.org/licenses/>.
*/

session_start();

if(isset($_SESSION['myusername'])){
	$myusername = $_SESSION['myusername'];
	$myuserrole = $_SESSION['myuserrole'];
	}else{
	$myusername = "";
}

set_language();

$extensionsAllowedForImagick = array("jpg", "tif", "png", "pdf");

$alreadyPrinted = array();

if(file_exists('alreadyPrinted.json')) {
	$jsonContent = file_get_contents('alreadyPrinted.json');
	$alreadyPrinted = json_decode($jsonContent,1);
}

$printingAreas = array();
if(file_exists('printingAreas.json')) {
	$jsonContent = file_get_contents('printingAreas.json');
	$printingAreas = json_decode($jsonContent,2);
}

$fileAreas = array();
if(file_exists('fileAreas.json')) {
	$jsonContent = file_get_contents('fileAreas.json');
	$fileAreas = json_decode($jsonContent,2);
}
$fileAreasChanged = false;

if(isset($_REQUEST['status']))
{
	$status = $_REQUEST['status'];
	//echo 'status: '.$status.' | '.$_REQUEST['filepath'].'<br>';
	if($status=='1')
	{
		unset($alreadyPrinted[array_search($_REQUEST['filepath'], $alreadyPrinted)]);
	}
	else if(!in_array($_REQUEST['filepath'],$alreadyPrinted))
	{
		$alreadyPrinted[] = $_REQUEST['filepath'];
	}
}

if(isset($_REQUEST['mode'])){
	$mode = $_REQUEST['mode'];
	
	$printingAreas[$_REQUEST['filepath']]['mode'] = $mode;
}

$subdir = '';
if($_REQUEST['subdir']) $subdir = $_REQUEST['subdir'];
if($subdir=='/') $subdir = '';


$ftpDir = 'druk';
if(file_exists('config.php')) {include('config.php');}
$printingDir = str_replace(basename(dirname(__FILE__)), $ftpDir.$subdir, dirname(__FILE__));
//delete zip previously downloaded files if any ---------
array_map('unlink', glob( '*.zip'));
//---------

$fnames = scandir($printingDir);


?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
<head>
        <title><?php echo _("Printing file list").' | '.(($subdir=='')?_("Main directory"):$subdir) ?></title>
        <meta charset="utf-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
  
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
.loginpanel {
    padding-bottom: 10px;
    width: 240px;
    float: right;
	margin-top: -10px;
}

.helplink {
    text-align: right;
}

#total {
    float: right;
}

#folder {
	width:100%;
	}

form input{ 
	height:100%; 
	width:100%;
	padding:0;
	margin:0
}
td form{ 
	height:100%; 
	width:100%;
	padding:0;
	margin:0

}
.done{ 
	text-align:center;
	background-color:#6F6;
	border: 1px solid #063;
	height:100%; 
	padding:0;
	margin:0
}
.archive{ 
	text-align:center;
	background-color:#999;
	border: 1px solid #666;
	height:100%; 
	width:100%;
	padding:0;
	margin:0
}
.urgent{ 
	text-align:center;
	background-color:#F90;
	border: 1px solid #F60;
	height:100%; 
	width:100%;
	padding:0;
	margin:0
}
.pending{ 
	text-align:center;
	background-color:#FFC;
	border: 1px solid #FC3;
	height:100%; 
	width:100%;
	padding:0;
	margin:0
}
form .done:hover{ 
	background-color:#CFF
}
form .:hover{ 
	background-color:#BFF
}
.footer{
	text-align: left;
}

</style>
<!-- jQuery -->
<script type="text/javascript" charset="utf8" src="//code.jquery.com/jquery-1.10.2.min.js"></script>
  
<!-- DataTables -->
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/1.10.8/js/jquery.dataTables.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/plug-ins/1.10.8/sorting/natural.js"></script>
<script type="text/javascript" charset="utf8" src="//cdn.datatables.net/plug-ins/1.10.8/sorting/date-euro.js"></script>

<script>
$(document).ready( function () {

	$('#folder').dataTable( {
		
		paging:   false,
		stateSave: true,
		 columnDefs: [
		   { type: 'natural', targets: [0,3] },
		   
		 ]
	  } );
} );
</script>
<body>
<div class="content">
<div class="helplink"> <a href ='instructions/'>instructions</a></div>
<div class="loginpanel">
<?php
if($myusername==""){
	echo "<form name='form1' method='post' action='checklogin.php'>";
	}else{
	echo "<form name='form1' method='post' action='logout.php'>";
}
?>
<br>
<table width="250" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#CCCCCC">
<tr>
<td>
<table width="250" border="0" cellpadding="3" cellspacing="1" bgcolor="#FFFFFF">
<tr>
<td colspan="3"><strong>User Login </strong></td>
</tr>
<tr>
<td width="59">Username</td>
<td width="3">:</td>

<td width="164"><input name="myusername" type="text" id="myusername" size="24" value="<?php echo $myusername; ?>"></td>

</tr>
<tr>
<td>Password</td>
<td>:</td>
<td><input name="mypassword" type="password" id="mypassword" size="24"></td>
</tr>
<tr>
<td>&nbsp;</td>
<td>&nbsp;</td>
<?php
if($myusername==""){
	echo "<td><input type='submit' name='Submit' value='Login'></td>";
	}else{
	echo "<td><input type='submit' name='Submit' value='Logout'></td>";	
}
?>
</tr>
</table>
</td>
</tr>
</table>
</form>
</div>
<div class="title">
<h1><?php echo _("Printing file list").' | '.(($subdir=='')?_("Main directory"):$subdir) ?></h1>
</div>

<?php
chdir($printingDir);
if($subdir=='') 
{
	$allFiles = array();
	$arr = get_areas('.');
	$printingAreas = $arr;
	/*
	foreach ($allFiles as $key => $val)
	{
		echo $key." || ".$val.'<br>';
		//echo $key." || total: ".$val['total']." | todo: ".$val['todo']." | done: ".$val['done']." | mode: ".$val['mode'].'<br>';
	}
	*/
	//purge data from json files-
	//echo json_encode($allFiles).' ||all<br>';
	//echo json_encode($fileAreas).' ||4<br>';
	$alreadyPrinted = array_intersect($alreadyPrinted, $allFiles);
	foreach ($fileAreas as $fpath => $value) 
	{
		if(!in_array($fpath, $allFiles)) {echo $fpath. 'trem<br>'; unset($fileAreas[$fpath]);}
	}
	//echo json_encode($fileAreas).' ||5<br>';
	//-purge
}

echo "<table id='folder' class='display' border='1'>";
// printing table headers
    echo "<thead><tr>
	<th width='50px'>num</th>
	<th>file/dir</th>
	<th>date</th>
	<th width='80px'>filesize</th>
	<th width='42px'>status</th> 
	<th width='100px'>"._("print size")."</th>
	<th width='50px'>"._("count")."</th>
	<th width='80px'>download</th>
	</tr></thead>";

// printing table rows
echo "<tbody>";
$totalArea = 0;
$totalAreaDone = 0;
sort($fnames);
$cell = 1;
$folderPrinted = 1;
$folderNotEmpty = false;

foreach ($fnames as $fname) {
	if($fname=='.') continue;
	if($fname=='alreadyPrinted.json') continue;
	if($fname=='..'&&$subdir=='') continue;
	if(strstr($fname,'.zip')) continue;
	echo "<tr><td>".$cell."</td><td>";
	//echo $fname.' | '.date('Y-m-d H:i:s', filectime($fname)).' | '.is_dir($fname);
	$itemPrinted = in_array($subdir."/".$fname,$alreadyPrinted);
	if(is_dir($fname))
	{
		switch ($fname) {
	  	case '..':
			$dn_subdir = dirname($subdir);
			echo "<form name='form_".$cell."' method='post' action='?'>
			<input name='subdir' type='hidden' id='subdir' value='".$dn_subdir."'>
			<input  class='link' type='submit' value='directory_up'></form></td>
			<td>".date('Y-m-d H:i:s', filectime($fname))."</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td><td>n/a</td>";
		  
			break;
		default:
			$folderNotEmpty = true;
			$folderPrinted = $folderPrinted&&$itemPrinted;
			echo "<form name='form_".$cell."' method='post' action='?'>
			<input name='subdir' type='hidden' id='subdir' value='".$subdir."/".$fname."'>
			<input  class='link' type='submit' value='".$fname."'></form></td>";
			$mode = '0';
			if(array_key_exists($subdir."/".$fname,$printingAreas)){
				if(array_key_exists('mode',$printingAreas[$subdir."/".$fname])){
					$mode = $printingAreas[$subdir."/".$fname]['mode'];
				}
				if($mode!=-1){
					$totalArea += $printingAreas[$subdir."/".$fname]['total'];
					$totalAreaDone += $printingAreas[$subdir."/".$fname]['done'];
				}
			}
			$class = 'pending';	$value = 'pending';
			if($mode == 1) {$class = 'urgent'; $value = 'urgent';}
			if($itemPrinted) {$class = 'done'; $value = '*';}
			if($mode == -1) {$class = 'archive'; $value = 'archive';}
			break;	
		}

	if($fname!='..')
		{
			echo "<td>".date('Y-m-d H:i:s', filectime($fname))."</td><td>n/a</td>";
				
			if($myuserrole=="admin")
			{	
				echo "<td>
				
				<form name='set_mode".$cell."' method='post' action='?'>
				<input name='subdir' type='hidden' id='subdir' value='".$subdir."'>
				<input name='filepath' type='hidden' id='filepath' value='".$subdir."/".$fname."'>
				<input name='mode' type='hidden' id='mode' value='".(($mode+2)%3-1)."'>
				<input class='".$class."' type='submit' value='".$value."'></form></td>
	
				<td>n/a</td><td>n/a</td>";
			}
			else
			{
				echo "<td>
				<div class='".$class."'>".$value."</div>
				</td><td>n/a</td><td>n/a</td>";
			}
			if($myusername==""){
				echo "<td>n/a</td>";
			}
			else
			{
				echo "<td>
				<form name='download_".$cell."' method='post' action='download.php'>
				<input name='subdir' type='hidden' id='downloadsubdir' value='".$subdir."/".$fname."'>
				<input name='downloadType' type='hidden' value='zippedFolder'>
				<input  class='download' type='submit' value='download'></form>
				</td>";
			}
		}
	}
	else
	{
		$folderNotEmpty = true;
		$folderPrinted = $folderPrinted && $itemPrinted;
		if($myusername==""){
			echo $fname."</td><td>".date('Y-m-d H:i:s', filectime($fname))."</td><td>".filesize($fname)."</td><td>"
			.($itemPrinted? '<div class="done">*</div>':'')."</td>";
		}else{
			echo $fname."</td><td>".date('Y-m-d H:i:s', filectime($fname))."</td><td>".filesize($fname)."</td><td>
			<form name='confirm_".$cell."' method='post' action='?'>
			<input name='subdir' type='hidden' id='subdir' value='".$subdir."'>
			<input name='filepath' type='hidden' id='filepath' value='".$subdir."/".$fname."'>
			<input name='status' type='hidden' value='".$itemPrinted."'>
			<input ".($itemPrinted? 'class="done"':'class="pending"')." type='submit' value='".($itemPrinted? '*':' ')."'></form></td>";
		}
		$size = '100x100';
		$re = '/\d+(?i)[xX]\d+/';
		$res = preg_match_all($re, $fname, $matches);
		if($res) {
			$size = $matches[0][0];
		}
		else
		{
			if(array_key_exists($subdir."/".$fname,$fileAreas)&&$fileAreas[$subdir."/".$fname]['filectime']==filectime($fname))
			{
				if(array_key_exists('size',$fileAreas[$subdir."/".$fname])) $size = $fileAreas[$subdir."/".$fname]['size'];
			}
			else
			{
				$fileAreas[$subdir."/".$fname]['filectime']=filectime($fname);
				$path_parts = pathinfo($fname);
				if (extension_loaded('imagick')&&in_array($path_parts['extension'],$extensionsAllowedForImagick))
				{
					$imExc = false;
					try{
						//$imagick = new Imagick($fname);
						$imagick = new Imagick();
						$imagick->readImage($fname.'[0]');
					}catch(Exception $e){
						echo 'Caught exception: '.  $e->getMessage(). "\n";
						$imExc = true;
					}
					if(!$imExc)
					{
						$imageResolution = $imagick->getImageResolution();
						$pixelWidth = $imagick->getImageWidth();
						$pixelHeight = $imagick->getImageHeight();
						$size = round($pixelWidth*2.54/$imageResolution['x'],2).'x'.round($pixelHeight*2.54/$imageResolution['y'],2);
						$fileAreas[$subdir."/".$fname]['size']=$size;
					}
				}
				$fileAreasChanged = true;
			}
		}
			
		$itemCount = 1;
		$re = '/\d+(szt|pcs)./';
		$res = preg_match_all($re, $fname, $matches);
		if($res) {
			$itemCount = $matches[0][0];
			$itemCount = str_replace(array('szt.','pcs.'), '', $itemCount);
		}
		$itemDimensions = explode("x", $size);
		
		$re = '/\d+x\d+mm/';
		$mm_units = preg_match_all($re, $fname, $matches);
		if($mm_units)
		{
			$itemDimensions[0] *=0.1;
			$itemDimensions[1] *=0.1;
		}
			
		$itemArea = $itemDimensions[0]*$itemDimensions[1]*$itemCount*0.0001;

		$totalArea += $itemArea;
		if($itemPrinted)$totalAreaDone += $itemArea;
		
		/*	$imgInfo = Array();
		$identify = exec("identify -format '%x x %y, %w x %h' $fname", $imgInfo);
		$exifData[$fname] = $imgInfo;*/


		if($myusername==""){
			echo "<td>".$size."</td><td>".$itemCount."</td><td>n/a</td>";
		}
		else
		{
			echo "<td>".$size."</td><td>".$itemCount."</td>
			<td>
			<!--
			<a href='http://".$_SERVER['HTTP_HOST']."/".$ftpDir."/".$subdir.$fname."' 
			download > download </a>
			-->
			<form name='download_".$cell."' method='post' action='download.php'>
			<input name='subdir' type='hidden' id='downloadsubdir' value='".$subdir."/".$fname."'>
			<input name='downloadType' type='hidden' value='singleFile'>
			<input  class='download' type='submit' value='download'></form>	
			</td>";
		}

	}
//echo " <a href='http://".$_SERVER['HTTP_HOST'] . dirname($_SERVER['REQUEST_URI'])."'>index page</a> ";
 	echo "</tr>\n";
	$cell ++;
}
echo "</tbody></table><br><br>";


chdir(dirname(__FILE__));
$folderPrinted = $folderPrinted&&$folderNotEmpty;
if($folderPrinted==1){
	if((!in_array($subdir,$alreadyPrinted) && ($folderNotEmpty == 1))) $alreadyPrinted[] = $subdir;
}elseif(in_array($subdir,$alreadyPrinted)){
	unset($alreadyPrinted[array_search($subdir, $alreadyPrinted)]);
}
$jsonData = json_encode($alreadyPrinted);
file_put_contents('alreadyPrinted.json', $jsonData);

?>
<table id='total' class='display' border='1'>
<tbody><tr><td width='150px'><?php echo _("printouts in the table"); ?></td><td width='150px'><?php echo number_format((float)$totalArea, 1, '.', ''); ?> m2</td></tr>
<tr><td width='150px'><?php echo _("remains to print"); ?></td><td width='150px'><?php echo number_format((float)($totalArea - $totalAreaDone), 1, '.', ''); //number_format((float)$totalArea, 2, '.', '')?> m2</td></tr>
</tbody></table>

<div class = "footer"><h4><?php echo _("possible folder attributes:"); ?></h4>
<table width="66%" border="0">
  <tr>
    <td width="50px"><div class = "archive">archive</div></td>
    <td width="220px"><?php echo _("archive, skipped in calculations"); ?></td>
    <td width="50px"><div class = "done">*</div></td>
    <td>&nbsp;</td>
    <td width="50px"><div class = "pending">pending</div></td>
    <td>&nbsp;</td>
    <td width="50px"><div class = "urgent">urgent</div></td>
    <td>&nbsp;</td>
  </tr>
</table>
</div>
</div>
</body>
</html>
<?php
$printingAreas[$subdir]['total'] = $totalArea;
$printingAreas[$subdir]['done'] = $totalAreaDone;
$jsonData = json_encode($printingAreas);
file_put_contents('printingAreas.json', $jsonData);

if($fileAreasChanged)
{
	$jsonData = json_encode($fileAreas);
	//echo $jsonData;
	file_put_contents('fileAreas.json', $jsonData);
}

function set_language()
{
	bindtextdomain("pfm", dirname(__FILE__)."/languages");// gettext file/path
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

function get_areas($dir, &$areas=array())
{
	global $allFiles;
	global $alreadyPrinted;
	global $printingAreas;
	global $fileAreas;
	$_dir = ltrim($dir,'.');
	$areas[$_dir]['done'] = 0;
	$areas[$_dir]['todo'] = 0;
	$areas[$_dir]['total'] = 0;
	$ffs = scandir($dir);

    unset($ffs[array_search('.', $ffs, true)]);
    unset($ffs[array_search('..', $ffs, true)]);

    foreach($ffs as $ff){
        
        if(is_dir($dir.'/'.$ff)) 
		{
			$subareas = get_areas($dir.'/'.$ff);
			$areas = $areas + $subareas; 
			$archive = false;
			if(array_key_exists('mode',$areas[$_dir.'/'.$ff])) $archive = ($areas[$_dir.'/'.$ff]['mode']==-1);
			if(!$archive)
			{
				$areas[$_dir]['done'] += $areas[$_dir.'/'.$ff]['done'];
				$areas[$_dir]['todo'] += $areas[$_dir.'/'.$ff]['todo'];
				$areas[$_dir]['total'] += $areas[$_dir.'/'.$ff]['total'];
			}
		}
		else 
		{
			$_area = get_areaFromFile($dir.'/'.$ff);
			if(in_array($_dir.'/'.$ff,$alreadyPrinted))	{$areas[$_dir]['done'] += $_area;}else{$areas[$_dir]['todo'] += $_area;}
			$areas[$_dir]['total'] += $_area;
		}
		$allFiles[] = $_dir.'/'.$ff;
    }
	$folderPrinted = ($areas[$_dir]['done'] != 0 && $areas[$_dir]['todo'] == 0);
	if($folderPrinted)
	{
		if(!in_array($_dir,$alreadyPrinted)) $alreadyPrinted[] = $_dir;
	}
	else
	{
		if(in_array($_dir,$alreadyPrinted)) unset($alreadyPrinted[array_search($_dir, $alreadyPrinted)]);
	}
	if(is_array($printingAreas[$_dir]))
	{
		if(array_key_exists('mode',$printingAreas[$_dir])) $areas[$_dir]['mode'] = $printingAreas[$_dir]['mode'];
	}
	//echo json_encode($fileAreas).' |||<br>';
	return $areas;
}

function get_areaFromFile($path)
{
		global $fileAreas;
		global $extensionsAllowedForImagick;
		global $fileAreasChanged;
		$_path = ltrim($path,'.');
				
		$path_parts = pathinfo($path);
		$fname = $path_parts['basename'];
		
		$size = '100x100';
		$re = '/\d+(?i)[xX]\d+/';
		$res = preg_match_all($re, $fname, $matches);
		if($res) {
			$size = $matches[0][0];
		}
		else
		{
			if(array_key_exists($_path,$fileAreas)&&$fileAreas[$_path]['filectime']==filectime($path))
			{
				
				if(array_key_exists('size',$fileAreas[$_path])) $size = $fileAreas[$_path]['size'];
				//echo 'ake '.$size.'e   <br>';
			}
			else
			{
				if (extension_loaded('imagick')&&in_array($path_parts['extension'],$extensionsAllowedForImagick ))
				{
					$imExc = false;
					try
					{
						$imagick = new Imagick();
						$imagick->readImage($path.'[0]');
					}
					catch(Exception $e)
					{
						echo 'Caught exception: '.  $e->getMessage().' | '.$path.'<br>';
						$imExc = true;
					}
					if($imExc==false)
					{
						$imageResolution = $imagick->getImageResolution();
						$pixelWidth = $imagick->getImageWidth();
						$pixelHeight = $imagick->getImageHeight();
						$size = round($pixelWidth*2.54/$imageResolution['x'],1).'x'.round($pixelHeight*2.54/$imageResolution['y'],1);	
						$fileAreas[$_path] = array('filectime' => filectime($path),'size' => $size);
					}
				}
				$fileAreasChanged = true;
			}
		}
			
		$itemCount = 1;
		$re = '/\d+(szt|pcs)./';
		$res = preg_match_all($re, $fname, $matches);
		if($res) {
			$itemCount = $matches[0][0];
			$itemCount = str_replace('szt.', '', $itemCount);
		}
		$itemDimensions = explode("x", $size);
		
		$re = '/\d+x\d+mm/';
		$mm_units = preg_match_all($re, $fname, $matches);
		if($mm_units)
		{
			$itemDimensions[0] *=0.1;
			$itemDimensions[1] *=0.1;
		}
		$itemArea = $itemDimensions[0]*$itemDimensions[1]*$itemCount*0.0001;
		return $itemArea;
}
?>