<?php
ini_set("display_errors", 1);
require_once('save_to_xml.php');

$retval = array();
if(!isset($_REQUEST['uname'])) {
	$retval['status'] = 'FAILED';
	$retval['statusmsg'] = 'no user name is set.';
	echo json_encoded($retval);
	exit;
}

if(!isset($_REQUEST['lname'])) {
	$retval['status'] = 'FAILED';
	$retval['statusmsg'] = 'no list name is set.';
	echo json_encoded($retval);
	exit;
}

$uname = $_REQUEST['uname'];
$lname = $_REQUEST['lname'];

$fname = "../sets/".$uname."/".$lname.".xml";

save_to_xml(null, $lname,$fname);

$retval['status'] = 'OK';
$retval['statusmsg'] = 'OK';
echo json_encode($retval);

?>