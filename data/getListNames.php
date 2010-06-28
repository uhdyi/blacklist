<?php
ini_set("display_errors", 1);

$retval = array();
$opts = array();
$idx = 0;

if(!isset($_REQUEST['set'])) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'Set Name is not specified.';
  echo json_encoded($retval);
  exit;
}

$set_dir = "../sets/".$_REQUEST['set'];
$d = @dir($set_dir);
if (!$d) {
  $retval['status'] = 'FAILED';
  $retval['statusmsg'] = 'Can not open set directory.';
  echo json_encode($retval);
  exit;
}

while(false !== ($f = $d->read())){

  if(preg_match('/[a-zA-Z0-9]+.xml/', $f)) {
    $name = explode(".xml", $f);		
     $opts[$idx] = $name[0];
     $idx++;
  }
}

$retval['opts'] = $opts;
$retval['status'] = 'OK';
$retval['statusmsg'] = 'OK';
$retval['count'] = $idx;

$x = json_encode($retval);
echo $x;
?>