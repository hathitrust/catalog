<?php

/*

This demo is to illustrate how to use feedcreator
using outputFeed function, ATOM1.0 and enclosure
support.



<enclosure> support is useful if you are into
podcasting or publishing photoblog.

the required parameter for enclosure is url, length
and type (as in MIME-type)

*/


include ("feedcreator.class.php");

//define channel
$feed = new UniversalFeedCreator();
$feed->useCached();
$feed->title="Personal News Site";
$feed->description="daily news from me";
$feed->link="http://mydomain.net/";
$feed->syndicationURL="http://60.50.40.155:1000/fctest/demo.php?type=atom";
$feed->category="Entertainment";
$feed->copyright = "feedcreator (c) 2006";
$feed->language = "en";//optional language used in ATOM feeds
$feed->generator = "http://www.phpgedview.net v4.1 beta 3"; // optionally add a secondary generator


//channel items/entries
$item = new FeedItem();
$item->title = "test berita pertama";
$item->link = "http://mydomain.net/news/somelinks.html";
$item->guid = "urn:feeds-archive-org:validator:1";
$item->description = "<p><strong>hahaha aku berjaya!</strong></p>";
$item->source = "http://mydomain.net";
$item->author = "John Doe";
$item->authorEmail = "JohnDoe@example.com";
$item->authorURL = "http://example.com/profile/john";
$item->descriptionHtmlSyndicated = true;
//$item->descriptionTruncSize = 500; // will be ignored if "$item->descriptionHtmlSyndicated = true" since it will result in potentially unpredictable and invalid output
$item->category="normal";



//optional enclosure support
$item->enclosure = new EnclosureItem();
$item->enclosure->url='http://mydomain.net/news/picture.jpg';
$item->enclosure->length="65905";
$item->enclosure->type='image/jpeg';
$item->enclosure->title = "A picture"; ////optional enclosure title used in ATOM feeds
$item->enclosure->language = "en";//optional language used in ATOM feeds

$feed->addItem($item);


//Valid parameters are RSS0.91, RSS1.0, RSS2.0, PIE0.1 (deprecated),
// MBOX, OPML, ATOM, ATOM1.0, ATOM0.3, HTML, JS



if ($_GET['type'] == 'atom') {
	$feed->outputFeed("ATOM1.0");
	//$feed->saveFeed("ATOM1.0", "news/feed.xml");
} else if ($_GET['type'] == 'atom0'){
	$feed->outputFeed("ATOM0.3");
} else if ($_GET['type'] == 'rss2.0'){
	$feed->outputFeed("RSS2.0");
}  else  {
	$feed->outputFeed("RSS");
}


?>