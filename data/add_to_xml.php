<?php
function add_to_xml($file, $title, $author, $price, $rating) {
    
    $doc = new DOMDocument;
    $doc->Load($file);
    
    $list = $doc->getElementsByTagName("list")->item(0);
    $newid = (int)$list->getElementsByTagName("lastindex")->item(0)->textContent + 1;
    //echo $newid;
    
    $lastindex =  $list->getElementsByTagName("lastindex")->item(0);
		$list->removeChild($lastindex);
		
		$newLastIndex = $doc->createElement("lastindex");
		$newLastIndex->appendChild($doc->createTextNode($newid));
		$list->appendChild($newLastIndex);
		
    //$list->getElementsByTagName("lastindex")->item(0)->appendChild($doc->createTextNode($newid));
        
    $newElement = $doc->createElement("item");
    $list->appendChild($newElement);
    
    $idd = $doc->createElement("id");
    $idd->appendChild($doc->createTextNode($newid));
    $newElement->appendChild($idd);
    
    $t = $doc->createElement("title");
    $t->appendChild($doc->createTextNode($title));
    $newElement->appendChild($t);
 
    $au = $doc->createElement("author");
    $au->appendChild($doc->createTextNode($author));
    $newElement->appendChild($au);

    $pri = $doc->createElement("price");
    $pri->appendChild($doc->createTextNode($price));
    $newElement->appendChild($pri);
        
    $ra = $doc->createElement("rating");
    $ra->appendChild($doc->createTextNode($rating));
    $newElement->appendChild($ra);
        
    $doc->save($file);
}
?>