<?php 
ini_set("display_errors", 1);

$retval = array();

if(!isset($_REQUEST['uname'])) {
	$retval['status'] = 'FAILED';
	$retval['statusmsg'] = 'user name is not set.';
	echo json_encode($retval);
	exit;
}

$uname = $_REQUEST['uname'];
$folder = "../sets/".$uname;
mkdir($folder);

$src = "../sets/books.xml";
$dest = $folder."/books.xml";

if(!copy($src, $dest)) {
	$retval['status'] = 'FAILED';
	$retval['statusmsg'] = 'cannot copy default file to new folder.';
	echo json_encode($retval);
	exit;
}
chmod($dest, 0755);

$retval['status'] = 'OK';
$retval['statusmsg'] = 'OK';
echo json_encode($retval);

?>