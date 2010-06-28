<?php
ini_set("display_errors", 1);
require_once('lib/phpFlickr.php');

function searchFlickr($keywords, $page) {

	$retval = array();
	$data = array();

	$f = new phpFlickr('5bc169cff7b9121c0c93f9b8804b1116');
	$args = array(
		"tags" => $keywords, 
		"tag_mode" => "all",
		"sort" => "relevance",
		"per_page" => "10",
		"page" => $page
	);
	       
	$photos = $f->photos_search($args);
		
	//decide not to show the photo beyond page 400;
	$totalPages = min($photos['pages'], 400);
	
	if($totalPages > 0) {
		$i = 0;
		foreach ($photos['photo'] as $photo) {
			$user= $f->people_getInfo($photo['owner']);
	
			$data[$i] = array(
				'id' => $photo['id'],
				'title' => $photo['title'],
				'author' => $user['username'],
				'image' => $f->buildPhotoURL($photo, "Square")
			);
			$i++;
		}
		
	$retval['status'] = 'OK';
	$retval['statusmsg'] = 'OK';
	$retval['totalpages'] = $totalPages;
	$retval['currentpage'] = $page;
	$retval['data']= $data;
			
	} else {
		$retval['status'] = 'NA';
		$retval['statusmsg'] = 'NA';
	}

	return $retval;
}


function searchFlickrPhotoDetail($id, $type) {
	$retval = array();
	$data = array();

	$f = new phpFlickr('5bc169cff7b9121c0c93f9b8804b1116');	       
	$photo = $f->photos_getInfo($id);
	

	$tags = '';
	foreach($photo['tags']['tag'] as $tag) {
		$tags .= $tag['raw'].','; //$tag['raw']
	}
	
	$notes = array();	
	foreach($photo['notes']['note'] as $note) {
		$notes[] = $note['_content'] ;
	}
	
	$urls = array();
	foreach($photo['urls']['url'] as $url) {
		$urls = $url['_content'] ;
	}
	
	$data = array(
		'title' => $photo['title'],
		'description' => $photo['description'],
		'image' => $f->buildPhotoURL($photo),
		'date' => $photo['dates']['taken'],
		'author' => $photo['owner']['username'],
		'authorUrl' => 'http://flickr.com/photos/' . $photo['owner']['nsid'],
		'tags' => $tags,
		'note' => $notes,
		'url' => $urls		
	);

	$retval['status'] = 'OK';
	$retval['statusmsg'] = 'OK';
	$retval['data'] = $data;	
	
	return $retval;	
}
?>