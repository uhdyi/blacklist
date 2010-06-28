<?php
ini_set("display_errors", 1);
require_once('AWSService.php');
require_once('YoutubeService.php');
require_once('FlickrService.php');

$retval = array();

if(!isset($_REQUEST['itemId'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no item id is specified';
  echo json_encode($retval);
  exit;
}
if(!isset($_REQUEST['type'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no type is specified';
  echo json_encode($retval);
  exit;
}
$id = $_REQUEST['itemId'];
$type = strtolower($_REQUEST['type']);
//echo $type;


if($type == 'books' || $type == 'music') {
	$retval = searchAWSItemDetail($id, $type);
} elseif ($type == 'video') {
	$retval = searchYoutubeVideoDetail($id, $type);
} else {
	$retval = searchFlickrPhotoDetail($id, $type);
}
//print_r($retval);

echo json_encode($retval);

?>