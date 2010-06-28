<?php
ini_set("display_errors", 1);
require_once('add_to_xml.php');

$retval = array();
if(!isset($_REQUEST['list'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no list is specified';
  echo json_encode($retval);
  exit;
}
if(!isset($_REQUEST['title'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no title is specified';
  echo json_encode($retval);
  exit;
}
if(!isset($_REQUEST['author'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no author is specified';
  echo json_encode($retval);
  exit;
}
if(!isset($_REQUEST['price'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no price is specified';
  echo json_encode($retval);
  exit;
}
if(!isset($_REQUEST['rating'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no rating is specified';
  echo json_encode($retval);
  exit;
}
$list = strtolower($_REQUEST['list']);
$title = $_REQUEST['title'];
$author = $_REQUEST['author'];
$price = $_REQUEST['price'];
$rating = $_REQUEST['rating'];
$user = 'dyi';

$file = "../sets/".$user."/".$list.".xml";
add_to_xml($file, $title, $author, $price, $rating);

$retval['status'] = 'OK';
$retval['statusmsg'] = 'OK';
$retval['file']= $file;
echo json_encode($retval);
?>