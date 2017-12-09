<?php
$subdir = '';
if($_REQUEST['subdir']) $subdir = $_REQUEST['subdir'];
if($subdir=='/') $subdir = '';

$ftpDir = 'druk';
if(file_exists('config.php')) {include('config.php');}
$printingDir = str_replace(basename(dirname(__FILE__)), $ftpDir.$subdir, dirname(__FILE__));

if($_REQUEST['downloadType'] == 'zippedFolder'){

	$fnames = scandir($printingDir);
	
	chdir($printingDir);

	$download_dir = $_REQUEST['download_dir'];
	$zipname = dirname(__FILE__).'/'.basename($subdir).'.zip';
	
	//single directory, no recursion ---------
    /*$zip = new ZipArchive;
    $zip->open($zipname, ZipArchive::CREATE);
    if ($handle = opendir('.')) {
      while (false !== ($entry = readdir($handle))) {
        if ($entry != "." && $entry != ".." && !strstr($entry,'.php') && !strstr($entry,'.zip')) {
			//echo $entry.'<br>';
            $zip->addFile($entry);
        }
      }
      closedir($handle);
    }

    $zip->close();*/
	//-----------------
	
	
	
	class FlxZipArchive extends ZipArchive {
			/** Add a Dir with Files and Subdirs to the archive;;;;; @param string $location Real Location;;;;  @param string $name Name in Archive;;; @author Nicolas Heimann;;;; @access private  **/
		public function addDir($location, $name) {
			$this->addEmptyDir($name);
			 $this->addDirDo($location, $name);
		 } // EO addDir;
	
			/**  Add Files & Dirs to archive;;;; @param string $location Real Location;  @param string $name Name in Archive;;;;;; @author Nicolas Heimann * @access private   **/
		private function addDirDo($location, $name) {
			$name .= '/';         $location .= '/';
		  // Read all Files in Dir
			$dir = opendir ($location);
			while ($file = readdir($dir))    {
				if ($file == '.' || $file == '..') continue;
			  // Rekursiv, If dir: FlxZipArchive::addDir(), else ::File();
				$do = (filetype( $location . $file) == 'dir') ? 'addDir' : 'addFile';
				$this->$do($location . $file, $name . $file);
			}
		} 
	}
	

	$za = new FlxZipArchive;
	$res = $za->open($zipname, ZipArchive::CREATE);
	if($res === TRUE)    {
		$za->addDir($printingDir, basename($printingDir)); $za->close();
	
		$zipUrl = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/'.basename($subdir).'.zip';
				
		header('Content-Type: application/zip');
		header("Content-Disposition: attachment; filename='".$zipname."'");
		header('Content-Length: ' . filesize($zipname));
		header("Location: ".$zipUrl);
	}
	else 
	{
		echo 'Could not create a zip archive';
	}

}
	
	
if($_REQUEST['downloadType'] == 'singleFile'){
	$printingFile = $printingDir;
	$fname = dirname(__FILE__).'/'.basename($subdir);

	$fileUrl = 'http://'.$_SERVER['HTTP_HOST'].dirname($_SERVER['PHP_SELF']).'/'.basename($subdir);
	header("Content-type: application/multipart");
	header("Content-Disposition: attachment; filename='".$fname."'");
	header('Content-Length: ' . filesize($fname));
	header("Location: ".$fileUrl);
}
?>
