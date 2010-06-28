<?php
ini_set("display_errors", 1);
require_once('lib/aws_signed_request.php');

function searchAWS($cat, $keywords, $page) {

$public_key = "AKIAJDNZSMGIHLN7E2IQ";
$private_key = "xXiE5eQr+ugOOh5QCFJYSXlZykdZh1PVapwTzdEY";
$retval = array();
$data = array();

$string_to_sign = array(
	"Operation" => "ItemSearch",
	"SearchIndex" => $cat,
	"Keywords" => $keywords,
	"ItemPage" => $page,
	"ResponseGroup" => "Small, Offers, Images, Reviews"
);

$xml = aws_signed_request("com", $string_to_sign, $public_key, $private_key);
// seems amazon wont' allow acess item beyond 4000, namely no page beyond 400 if 10 item/page.
$totalPages = min((integer)$xml->Items->TotalPages, 400);

if($totalPages > 0) {
	$items = $xml->Items->Item;
	
	$endpage = ($page == $totalPages) ? sizeof($items) : 10;
	for($i=0; $i<$endpage; $i++) {
		$item = $items[$i];
		//print_r($item);
			
		$data[$i] = array('asin' => (string)$item->ASIN,
			  'title' => (string)$item->ItemAttributes->Title,
			  //'detailpage' => (string)$item->DetailPageURL,
			  'author' => (strtolower($cat) == 'books'? (string)$item->ItemAttributes->Author:(string)$item->ItemAttributes->Artist),
			  'price' => ((string)$item->Offers->TotalOffers == 0 ? 'discontinued by the manufacturer':(string)$item->Offers->Offer->OfferListing->Price->FormattedPrice),
			  'image' => (string)$item->MediumImage->URL,
			  'rating' => (string)$item->CustomerReviews->AverageRating,
			  'itemtype' => $cat);
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

function searchAWSItemDetail($asin, $type) {
$public_key = "AKIAJDNZSMGIHLN7E2IQ";
$private_key = "xXiE5eQr+ugOOh5QCFJYSXlZykdZh1PVapwTzdEY";
$retval = array();
$data = array();

$string_to_sign = array(
	"Operation" => "ItemLookup",
	"ItemId" =>$asin, //"0596159773"
	"ResponseGroup" => "Small, Offers, Images, EditorialReview,Reviews"
);

$xml = aws_signed_request("com", $string_to_sign, $public_key, $private_key);
//print_r($xml);

$i = 0;
$items = $xml->Items->Item;
foreach($items as $item) {
	
	$reviews = array();
	$j = 0;
	
	if($item->CustomerReviews) {
	foreach($item->CustomerReviews->Review as $review) {
		$reviews[$j] = array(
				'rating' => (string)$review->Rating,
				'reviewer' => ((string)$review->Reviewer->Nickname == '' ? 'You-Know-Who':(string)$review->Reviewer->Nickname),
				'location' => ((string)$review->Reviewer->Location == '' ? 'Nowhere':(string)$review->Reviewer->Location),
				'date' => (string)$review->Date,
				'summary' => (string)$review->Summary,
				'content' => (string)$review->Content
				     );
		$j++;
	}
}

	if((int)$item->Offers->TotalOffers == 0) {
		$price = 'N/A';
	} else {
		$price = (string)$item->Offers->Offer->OfferListing->Price->FormattedPrice;
	}

	if($item->EditorialReviews) {
		$description = ($type == 'books' ? (string)$item->EditorialReviews->EditorialReview->Content : '');
	} else {
		$description = 'N/A';
	}
	
	$data[$i] = array(
				'itemId' => $asin,
				'title' => (string)$item->ItemAttributes->Title,
			  //'detailpage' => (string)$item->DetailPageURL,
			  'author' => ($type == 'books' ? (string)$item->ItemAttributes->Author : (string)$item->ItemAttributes->Artist),
			  'price' => $price,
			  'image' => (string)$item->LargeImage->URL,
    		'averating' => (string)$item->CustomerReviews->AverageRating,
			  'totalreviews' => (string)$item->CustomerReviews->TotalReviews,
			  'totalreviewpages' => (string)$item->CustomerReviews->TotalReviewPages,
			  'review' => $reviews,
			  'description' => $description);
	$i++;	
}
	
$retval['status'] = 'OK';
$retval['statusmsg'] = 'OK';
$retval['data']= $data;

return $retval;
}
?>