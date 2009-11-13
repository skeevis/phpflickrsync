<?php
require_once('phpFlickr/phpFlickr.php');
$key = "";
$secret = "";
$frob = "";
$token = "";
$f = new phpFlickr($key,$secret);
$read_directory = "/tmp/Snapshots/";
$write_directory = "/tmp/Snapshots_Bak/";
$title="FlickrSync";
$desc="FlickrSync";
$tags="flickrsync";


if(empty($frob))
{
$frob = $f->auth_getFrob();
$api_sig=md5($secret."api_key".$key."frob".$frob."permswrite");
$auth_url = "http://flickr.com/services/auth/?api_key=$key&perms=write&frob=$frob&api_sig=$api_sig";
echo "TWO STEPS YOU MUST NOW TAKE:\n\n";
echo "1. SET THE frob VARIABLE TO: $frob\n\n";
echo "2. VISIT $auth_url AND APPROVE THE APPLICATION!\n\n";
echo "RERUN WHEN YOU HAVE SET FROB\n\n";
}
elseif(empty($token))
{
	$token = $f->auth_getToken($frob);
	echo "PLEASE SET THE TOKEN VALUE TO THE TOKEN MENTIONED BELOW, SAVE, AND RERUN THE APPLICATION:";
	echo print_r($token)."\n\n\n";
}
else
{


$f->setToken($token);

echo "ENTERING MASTER LOOP\n";
while(1)
{
	echo "I'm awake!!!\n";
	$filenames_to_upload = array();
	if ($handle = opendir($read_directory)) {
	 while (($file = readdir($handle)) !== false) {
	   echo "NEW FILE: $file \n\n";
		array_push($filenames_to_upload,$file);
	 }
	 closedir($handle);
	}
	foreach($filenames_to_upload as $filename)
	{
		if($filename != "." && $filename != ".."){
		echo "PROCESSING $filename \n";
		$ticket = $f->sync_upload($read_directory.$filename,$title, $desc,$tags);
		echo "UPLOADED TO FLICKR! NOW MOVING\n";
		rename($read_directory.$filename, $write_directory.$filename);
		echo "MOVED!\n\n";
		sleep(1);
		}
	}
	$filenames_to_upload=array();
	echo "Sleeping....\n\n\n";
	sleep(5);
}
}


