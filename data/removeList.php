<?php

$retval = array();

if(!isset($_REQUEST['uname'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'No user name is specified.';
  echo json_encode($retval);
  exit;
}
$user = $_REQUEST['uname'];

if(!isset($_REQUEST['lname'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'No list name is specified.';
  echo json_encode($retval);
  exit;
}
$list = $_REQUEST['lname'];

$file = "../sets/".$user."/".$list.".xml";
if(file_exists($file)) {
	@unlink($file);
}
else {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'No such list exists.';
  echo json_encode($retval);
  exit;	
}

$retval['status'] = 'OK';
$retval['statusmsg'] = 'OK';
echo json_encode($retval);
?>