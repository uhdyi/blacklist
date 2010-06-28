<?php
require_once 'Zend/Loader.php'; // the Zend dir must be in your include_path
Zend_Loader::loadClass('Zend_Gdata_Books');
//Zend_Loader::loadClass('Zend_Gdata_AuthSub');
//Zend_Loader::loadClass('Zend_Gdata_ClientLogin'); 

/**
$username = 'marsgirl@gmail.com';
$password = '847w5rfp';
$service = 'print';

$httpClient = Zend_Gdata_ClientLogin::getHttpClient($username, $password, $service);
$books = new Zend_Gdata_Books($httpClient);
*/

$books = new Zend_Gdata_Books();
$query = $books->newVolumeQuery();
$query->setQuery('jquery');
$query->setMinViewability('partial_view');
$query->setStartIndex(11);
$query->setMaxResults(10);
$feed = $books->getVolumeFeed($query);

foreach ($feed as $entry) {
	//print_r($entry);
	echo '<br><br>';
  echo $entry->getVolumeId().'</t>';
  echo $entry->getTitle().'</t>';
  echo $entry->getViewability().'<br>';
}

?>