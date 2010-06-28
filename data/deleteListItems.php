<?php
ini_set("display_errors", 1);
require_once('remove_from_xml.php');

$retval = array();

if(!isset($_REQUEST['items'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'No item id is specified.';
  echo json_encode($retval);
  exit;
}
$items = implode($_REQUEST['items']);

if(!isset($_REQUEST['list'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'No list is specified.';
  echo json_encode($retval);
  exit;
}
$list = strtolower($_REQUEST['list']);

$user = 'dyi';
$file = "../sets/".$user."/".$list.".xml";

$tok = strtok($items, ",");
while($tok !== false) {
 //echo $tok;
 remove_from_xml($file, $tok);
 $tok = strtok(","); 
}

$retval['status'] = 'OK';
$retval['statusmsg'] = 'OK';
echo json_encode($retval);

?>