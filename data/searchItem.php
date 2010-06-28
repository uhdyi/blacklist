<?php
ini_set("display_errors", 1);
require_once('AWSService.php');
require_once('YoutubeService.php');
require_once('FlickrService.php');

$retval = array();
$data = array();
$page = 1;


if(!isset($_REQUEST['cat'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no category is specified';
  echo json_encode($retval);
  exit;
}
if(!isset($_REQUEST['keywords'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no keywords is specified';
  echo json_encode($retval);
  exit;
}
if(isset($_REQUEST['page'])) {
  $page = urldecode($_REQUEST['page']);
} 
$cat = urldecode($_REQUEST['cat']);
$keywords = urldecode($_REQUEST['keywords']);

if($cat == 'Books' || $cat == 'Music') { 
  $retval = searchAWS($cat, $keywords, $page);
} elseif ($cat == 'Video') {
	$qtype = 'all';
	$mresults = 10;
	$sindex = ($page - 1) * 10 + 1;
  $retval = searchYoutube($cat, $keywords, $page, $qtype, $mresults, $sindex);
} else if ($cat == 'Photo') {
	$retval = searchFlickr($keywords, $page);
}else {
}

echo json_encode($retval);

?>