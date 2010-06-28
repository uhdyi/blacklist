<?php
ini_set("display_errors", 1);
require_once('save_to_xml.php');

/**
function save_to_xml($data, $list, $file) {
    
    $doc = new DOMDocument('1.0', 'ISO-8859-1');
    $doc->formatOutput = true;
    
    $r = $doc->createElement("list");
    $doc->appendChild($r);
    
    $category = $doc->createElement("category");
    $category->appendChild($doc->createTextNode($list));
    $r->appendChild($category);
    
    for($i = 0; $i < sizeof($data); $i++) {
        $b = $doc->createElement("item");
        $attr = $r->appendChild($b);
        $attr->setAttribute('id', $data[$i]['id']);
        
        $title = $doc->createElement("title");
        $title->appendChild($doc->createTextNode($data[$i]['title']));
        $b->appendChild($title);
        
        $author = $doc->createElement("author");
        $author->appendChild($doc->createTextNode($data[$i]['author']));
        $b->appendChild($author);

        $publisher = $doc->createElement("publisher");
        $publisher->appendChild($doc->createTextNode($data[$i]['publisher']));
        $b->appendChild($publisher);        
    }
    $doc->save($file);
}
*/
$retval = array();
if(!isset($_REQUEST['uname'])) {
    $retval['status'] = 'FAILED';
    $retval['statusmsg'] = 'no user name is set.';
    echo json_encode($retval);
    exit;
}

if(!isset($_REQUEST['lname'])) {
    $retval['status'] = 'FAILED';
    $retval['statusmsg'] = 'no list name is set.';
    echo json_encode($retval);
    exit;
}

if(!isset($_REQUEST['data'])) {
    $retval['status'] = 'FAILED';
    $retval['statusmsg'] = 'no data is set.';
    echo json_encode($retval);
    exit;
}
$user = $_REQUEST['uname'];
$list = $_REQUEST['lname'];
$data = $_REQUEST['data'];

$file = "../sets/".$user."/".$list.".xml";
save_to_xml($data, $list, $file);

$retval['status'] = 'OK';
$retval['statusmsg'] = 'OK';
echo json_encode($retval);
?>