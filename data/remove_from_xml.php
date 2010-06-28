<?php
function remove_from_xml($file, $id) {
	
	$xml = simplexml_load_file($file);
	$item_cnt = count($xml->item);
	
	for($i = 0; $i < $item_cnt; $i++) {
		
		if($id == (string) $xml->item[$i]->id) {
			unset($xml->item[$i]);
			break;
		}		
	}
	file_put_contents($file, $xml->saveXML());

}


?>