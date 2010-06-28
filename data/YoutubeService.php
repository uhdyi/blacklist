<?php
ini_set("display_errors", 1);
ini_set('magic_quotes_runtime', 'off');
/**
 * @see Zend_Loader
 */
require_once 'Zend/Loader.php';

/**
 * @see Zend_Gdata_YouTube
 */
Zend_Loader::loadClass('Zend_Gdata');
Zend_Loader::loadClass('Zend_Gdata_Query');
Zend_Loader::loadClass('Zend_Gdata_YouTube');

/**
 * Finds the URL for the flash representation of the specified video
 *
 * @param  Zend_Gdata_YouTube_VideoEntry $entry The video entry
 * @return string|null The URL or null, if the URL is not found
 */
function findFlashUrl($entry)
{
    foreach ($entry->mediaGroup->content as $content) {
        if ($content->type === 'application/x-shockwave-flash') {
            return $content->url;
        }
    }
    return null;
}

/**
 * Returns a feed of top rated videos for the specified user
 *
 * @param  string $user The username
 * @return Zend_Gdata_YouTube_VideoFeed The feed of top rated videos
 */
function getTopRatedVideosByUser($user)
{
    $userVideosUrl = 'http://gdata.youtube.com/feeds/users/' .
                     $user . '/uploads';
    $yt = new Zend_Gdata_YouTube();
    $ytQuery = $yt->newVideoQuery($userVideosUrl);
    // order by the rating of the videos
    $ytQuery->setOrderBy('rating');
    // retrieve a maximum of 5 videos
    $ytQuery->setMaxResults(5);
    // retrieve only embeddable videos
    $ytQuery->setFormat(5);
    return $yt->getVideoFeed($ytQuery);
}

/**
 * Returns a feed of videos related to the specified video
 *
 * @param  string $videoId The video
 * @return Zend_Gdata_YouTube_VideoFeed The feed of related videos
 */
function getRelatedVideos($videoId)
{
    $yt = new Zend_Gdata_YouTube();
    $ytQuery = $yt->newVideoQuery();
    // show videos related to the specified video
    $ytQuery->setFeedType('related', $videoId);
    // order videos by rating
    $ytQuery->setOrderBy('rating');
    // retrieve a maximum of 5 videos
    $ytQuery->setMaxResults(5);
    // retrieve only embeddable videos
    $ytQuery->setFormat(5);
    return $yt->getVideoFeed($ytQuery);
}


/**
 * Echo img tags for the first thumbnail representing each video in the
 * specified video feed.  Upon clicking the thumbnails, the video should
 * be presented.
 *
 * @param  Zend_Gdata_YouTube_VideoFeed $feed The video feed
 * @return void
 */
function echoThumbnails($feed)
{
    foreach ($feed as $entry) {
        $videoId = $entry->getVideoId();
        //echo '<img src="' . $entry->mediaGroup->thumbnail[0]->url . '" ';
        //echo 'width="80" height="72" onclick="ytvbp.presentVideo(\'' . $videoId . '\')">';
    }
}


function echoVideoPlayer($videoId) {
	
	$yt = new Zend_Gdata_YouTube();

  $entry = $yt->getVideoEntry($videoId);
  $videoTitle = $entry->mediaGroup->title->text;
  $videoUrl = findFlashUrl($entry);
  //$relatedVideoFeed = getRelatedVideos($entry->getVideoId());
  //$topRatedFeed = getTopRatedVideosByUser($entry->author[0]->name);
  
  $list = array(
  	'title' => $entry->mediaGroup->title->text,
  	'description' => $entry->mediaGroup->description->text,
  	'author' => (string)$entry->author[0]->name,
  	'authorUrl' => 'http://www.youtube.com/profile?user=' . $entry->author[0]->name,
  	'tags' => (string)$entry->mediaGroup->keywords,
  	'duration' => $entry->mediaGroup->duration->seconds,
  	'watchPage' => $entry->mediaGroup->player[0]->url,
  	'viewCount' => $entry->statistics->viewCount,
  	'rating' => $entry->rating->average,
  	'numRaters' => $entry->rating->numRaters,
  	'videoUrl' => findFlashUrl($entry)
  );
  
  return $list;  
}

function echoVideoList($feed) {

	$i = 0;
	$list = array();
	foreach($feed as $entry) {
	
		$list[$i] = array(
			'videoId' => $entry->getVideoId(),
			'thumbnailUrl' => $entry->mediaGroup->thumbnail[0]->url,
			'videoTitle' => $entry->mediaGroup->title->text,
			'videoDescription' => $entry->mediaGroup->description->text
			);
			$i++;
	}
	return $list;	
}


function searchYoutube($cat, $searchTerm, $page, $queryType, $maxResults, $startIndex) {
	$retval = array();
	$data = array();

if ($queryType === null) {
    /* display the entire interface */
    include '../home.html';
} else if ($queryType == 'show_video') {
    /* display an individual video */
    if (array_key_exists('videoId', $_REQUEST)) {
        $videoId = $_REQUEST['videoId'];
        $data = echoVideoPlayer($videoId);
    }
    /** 
    else if (array_key_exists('videoId', $_REQUEST)) {
        $videoId = $_REQUEST['videoId'];
        echoVideoPlayer($videoId);
    }
    */ 
    else {
        echo 'No videoId found.';
        exit;
    }
} else {
    /* display a list of videos */
    //$searchTerm = $_REQUEST['searchTerm'];
    //$startIndex = $_REQUEST['startIndex'];
    //$maxResults = $_REQUEST['maxResults'];

    $yt = new Zend_Gdata_YouTube();
    $query = $yt->newVideoQuery();
    $query->setQuery($searchTerm);
    $query->setStartIndex($startIndex);
    $query->setMaxResults($maxResults);
		//print_r($query);

    /* check for one of the standard feeds, or list from 'all' videos */
    switch ($queryType) {
    case 'most_viewed':
        $query->setFeedType('most viewed');
        $query->setTime('this_week');
        $feed = $yt->getVideoFeed($query);
        break;
    case 'most_recent':
        $query->setFeedType('most recent');
        $feed = $yt->getVideoFeed($query);
        break;
    case 'recently_featured':
        $query->setFeedType('recently featured');
        $feed = $yt->getVideoFeed($query);
        break;
    case 'top_rated':
        $query->setFeedType('top rated');
        $query->setTime('this_week');
        $feed = $yt->getVideoFeed($query);
        break;
    case 'all':
        $feed = $yt->getVideoFeed($query);
        break;
    default:
        echo 'ERROR - unknown queryType - "' . $queryType . '"';
        break;
    }
    $data = echoVideoList($feed);
    //youtube won't allow search item beyond 1000, namely no page beyond 100 if 10 items/page.
    $totalpages = min(100, $feed->getTotalResults()->text);
}

$retval['status'] = 'OK';
$retval['statusmsg'] = 'OK';
$retval['data'] = $data;
$retval['totalpages'] = $totalpages;

return $retval;
}

function searchYoutubeVideoDetail($id, $type) {
	$retval = array();
	$data = array();
		
	$data = echoVideoPlayer($id);
	
	$retval['status'] = 'OK';
	$retval['statusmsg'] = 'OK';
	$retval['data'] = $data;	
	
	return $retval;
}
?>