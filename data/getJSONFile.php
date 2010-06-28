<?php
ini_set("display_errors", 1);

$retval = array();
if(!isset($_REQUEST['set'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no set is specified';
  echo json_encode($retval);
  exit;
}
if(!isset($_REQUEST['list'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no list is specified';
  echo json_encode($retval);
  exit;
}

/**
if(!isset($_REQUEST['page'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no page is specified.';
  echo json_encode($retval);
  exit;
}

if(!isset($_REQUEST['rows'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no rows is specified';
  echo json_encode($retval);
  exit;
}

if(!isset($_REQUEST['sidx'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no sidx is specified';
  echo json_encode($retval);
  exit;
}

if(!isset($_REQUEST['sord'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'no order is specified';
  echo json_encode($retval);
  exit;
}
*/
$filename = "../sets/".$_REQUEST['set']."/".$_REQUEST['list'].".xml";
if(!file_exists($filename)) {
    $retval['status'] = 'FAILED';
    $retval['statusmsg'] = 'File does not exist.';
    echo json_encode($retval);
    exit;
}
$fh = @fopen($filename, 'r');
if(!$fh) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'Can not open file.';
  echo json_encode($retval);
  exit;
}

$xml = simplexml_load_file($filename);
//print_r($xml);

$category = $xml->category;
//echo $category;

$lastindex = $xml->lastindex;
$count = count($xml->item);
//echo $count;
$data = array();


$response->page = 1;
$response->total = 1;
$response->records = $count;
//$response->lastindex = $lastindex;

for($i = 0; $i < $count; $i++) {
	$response->rows[$i]['id']= (string)$xml->item[$i]->id;//$i + 1;
	$response->rows[$i]['cell'] = array((string)$xml->item[$i]->id,(string)$xml->item[$i]->title, (string)$xml->item[$i]->author, (string)$xml->item[$i]->price, (string)$xml->item[$i]->rating);
}

$retval['status'] = 'SUCCESS';
$retval['statusmsg'] = 'Success';
$retval['data'] = $data;
//echo json_encode($xml);

echo json_encode($response);
?>