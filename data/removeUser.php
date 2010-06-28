<?php

$retval = array();
$defaultfile = 'Institution.xml';

if(!isset($_REQUEST['uname'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'No user name is specified.';
  echo json_encode($retval);
  exit;
}
$user = $_REQUEST['uname'];

$folder = "../sets/".$user;
if(is_dir($folder)) {
	$scan = glob(rtrim($folder, '/').'/*');
	foreach($scan as $index=>$path) {
		@unlink($path);
	}
}
rmdir($folder);

$retval['status'] = 'OK';
$retval['statusmsg']= 'OK';
echo json_encode($retval);
?>