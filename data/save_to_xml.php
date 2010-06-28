<?php
function save_to_xml($data = null, $list, $file) {
    
    $doc = new DOMDocument('1.0', 'ISO-8859-1');
    $doc->formatOutput = true;
    
    $r = $doc->createElement("list");
    $doc->appendChild($r);
    
    $category = $doc->createElement("category");
    $category->appendChild($doc->createTextNode($list));
    $r->appendChild($category);
    
    $lastindex = $doc->createElement("lastindex");
    $lastindex->appendChild($doc->createTextNode("0"));
    $r->appendChild($lastindex);
    
    if($data != null ) {
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

        $price = $doc->createElement("price");
        $price->appendChild($doc->createTextNode($data[$i]['price']));
        $b->appendChild($price);
        
        $rating = $doc->createElement("rating");
        $rating->appendChild($doc->createTextNode($data[$i]['rating']));
        $b->appendChild($rating);
    	}
  	}
    $doc->save($file);
}
?>