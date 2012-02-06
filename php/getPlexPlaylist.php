<?php


$id = $_GET["id"]; 
$prevkey = $_GET["key"]; 
$port = $_GET["port"]; 

if (strlen($port) == 0){
	$port = 80;
}

$hosturl = "http://pakee.hopto.org/pakee";

if (strlen($id) == 0){die ("No argument passed in");}

if (strlen($prevkey) == 0)
{
	$url = 'http://' . $id . '/library/sections';
}
else
{
	$url = 'http://' . $id . $prevkey;
}


#write rss reader using general info
echo "<?xml version='1.0'?>" ."\n";
echo "<rss version='2.0' xmlns:atom='http://www.w3.org/2005/Atom' xmlns:boxee='http://boxee.tv/spec/rss/' xmlns:dcterms='http://purl.org/dc/terms/' xmlns:media='http://search.yahoo.com/mrss/'>"."\n";
echo "<channel>"."\n";
echo "  <title>$title</title>"."\n";
echo "  <description>$description</description>"."\n";
//echo "  <image>$image</image>"."\n";
echo "  <language>en_us</language>"."\n";
//echo "  <lastBuildDate>$lastBuildDate</lastBuildDate>"."\n";
echo "  <webmaster>pakeeapp@gmail.com</webmaster>"."\n";
echo "  <link>http://www.plex.com</link>"."\n";


# Parse dunyanews feed to find links of youtube videos
$xml = parseFeed ($url, $port);

//var_dump($xml);

#extract general feed info
$title = make_safe($xml->attributes()->title1);
$description = make_safe($xml->attributes()->viewGroup);
//$image = make_safe($xml->channel[0]->image[0]->url);
//$lastBuildDate = make_safe($xml->channel[0]->lastBuildDate);




#traverse through each item of the feed
$i = 0;
while ($xml->Directory[$i])
{
    $item = $xml->Directory[$i];

    if ($description === "show"){
	    //get show attributes
	    $programname = make_safe($item->attributes()->title);
	    $year = $item->attributes()->year;
	    $summary = make_safe($item->attributes()->summary);
	    $rating = $item->attributes()->rating;
	    $thumb = $item->attributes()->thumb;
	    $art = $item->attributes()->art;
	    $banner = $item->attributes()->banner;
	    $theme = $item->attributes()->theme;
	    $leafCount  = $item->attributes()->leafCount;
	    $viewedLeafCount = $item->attributes()->viewedLeafCount;
	    $key  = $item->attributes()->key;
	    $contentRating  = $item->attributes()->contentRating;
	

	echo "   <item>"."\n";
	echo "      <title>$programname</title>"."\n";
	echo "      <link>$hosturl/getPlexPlaylist.php?id=$id&amp;key=$key</link>"."\n";
	echo "      <description>$summary</description>"."\n";
      	echo "      <media:thumbnail url='http://".$id.$thumb."' />"."\n";
      	echo "      <boxee:media-type expression='full' name='Full Episode' type='episode' />"."\n";
      	echo "      <boxee:release-date>$year</boxee:release-date>"."\n";
      	echo "      <boxee:user-rating>$rating</boxee:user-rating>"."\n";
      	echo "      <boxee:property name='custom:episodes'>$leafCount</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:viewedEpisodes'>$viewedLeafCount</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:art'>http://".$id.$art."</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:banner'>http://".$id.$banner."</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:theme'>http://".$id.$theme."</boxee:property>"."\n";
     	echo "      <media:rating schema='urn:v-chip'>$contentRating</media:rating>"."\n";
      	echo "      <media:community>"."\n";
      	echo "        <media:starRating average='$rating'/>"."\n";
      	echo "      </media:community>"."\n";
	echo "   </item>"."\n";

	}

    else if ($description === "season"){
	    //get season attributes
	    $programname = $item->attributes()->title;
	    $thumb = $item->attributes()->thumb;
	    $summary = $xml->attributes()->summary;
	    $key  = $item->attributes()->key;
	    $leafCount  = $item->attributes()->leafCount;
	    $viewedLeafCount = $item->attributes()->viewedLeafCount;
	

	echo "   <item>"."\n";
	echo "      <title>$programname</title>"."\n";
	echo "      <link>$hosturl/getPlexPlaylist.php?id=$id&amp;key=$key</link>"."\n";
	echo "      <description>$summary</description>"."\n";
      	echo "      <media:thumbnail url='http://".$id.$thumb."' />"."\n";
      	echo "      <boxee:property name='custom:episodes'>$leafCount</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:viewedEpisodes'>$viewedLeafCount</boxee:property>"."\n";
	echo "   </item>"."\n";

	}

    else if ($description === "artist"){
	    //get show attributes
	    $programname = make_safe($item->attributes()->title);
	    $summary = make_safe($item->attributes()->summary);
	    $thumb = $item->attributes()->thumb;
	    $key  = $item->attributes()->key;
	

	echo "   <item>"."\n";
	echo "      <title>$programname</title>"."\n";
	echo "      <link>$hosturl/getPlexPlaylist.php?id=$id&amp;key=$key</link>"."\n";
	echo "      <description>$summary</description>"."\n";
      	echo "      <media:thumbnail url='http://".$id.$thumb."' />"."\n";
	echo "   </item>"."\n";

	}

    else if ($description === "album"){
	    //get show attributes
	    $programname = make_safe($item->attributes()->title);
	    $thumb = $item->attributes()->thumb;
	    $key  = $item->attributes()->key;
	    $leafCount  = $item->attributes()->leafCount;
	    $viewedLeafCount = $item->attributes()->viewedLeafCount;	
	    $originallyAvailableAt = $item->attributes()->originallyAvailableAt;
	    $summary = make_safe($xml->attributes()->summary);


	echo "   <item>"."\n";
	echo "      <title>$programname</title>"."\n";
	echo "      <link>$hosturl/getPlexPlaylist.php?id=$id&amp;key=$key</link>"."\n";
      	echo "      <media:thumbnail url='http://".$id.$thumb."' />"."\n";
      	echo "      <boxee:property name='custom:episodes'>$leafCount</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:viewedEpisodes'>$viewedLeafCount</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:releasedate'>$originallyAvailableAt</boxee:property>"."\n";
	echo "      <description>$summary</description>"."\n";
	echo "   </item>"."\n";

	}


    //choose sort criteria
    else if ($description === "secondary" ){
	    $title = $item->attributes()->title;
	    $key  = $item->attributes()->key;
	

	echo "   <item>"."\n";
	echo "      <title>$title</title>"."\n";
	echo "      <link>$hosturl/getPlexPlaylist.php?id=$id&amp;key=$prevkey/$key</link>"."\n";
	echo "   </item>"."\n";

	}

    else if ($description == null || strlen($description) == 0 ){
	    $title = $item->attributes()->title;
	    $key  = $item->attributes()->key;
	

	echo "   <item>"."\n";
	echo "      <title>$title</title>"."\n";
	echo "      <link>$hosturl/getPlexPlaylist.php?id=$id&amp;key=/library/sections/$key</link>"."\n";
	echo "   </item>"."\n";

	}



	$i++;
}



#traverse through each item of the feed
$i = 0;
while ($xml->Video[$i])
{
    $item = $xml->Video[$i];

    if ($description === "episode"){
	    //get show attributes
	    $programname = make_safe($item->attributes()->title);
	    $summary = make_safe($item->attributes()->summary);
	    $rating = $item->attributes()->rating;
	    $index = $item->attributes()->index;
	    $thumb = $item->attributes()->thumb;
	    $duration  = $item->attributes()->duration;
	    $originallyAvailableAt = $item->attributes()->originallyAvailableAt;
	    $key  = $item->Media[0]->Part[0]->attributes()->key;
	
	    $duration = sprintf("%0.0f", $duration/(60*1000));

	echo "   <item>"."\n";
	echo "      <title>$index  $programname</title>"."\n";
	echo "      <link>http://".$id.$key."</link>"."\n";
	echo "      <description>$summary</description>"."\n";
      	echo "      <media:thumbnail url='http://".$id.$thumb."' />"."\n";
      	echo "      <boxee:media-type expression='full' name='Full Episode' type='episode' />"."\n";
      	echo "      <boxee:user-rating>$rating</boxee:user-rating>"."\n";
      	echo "      <boxee:property name='custom:duration'>$duration min</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:releasedate'>$originallyAvailableAt</boxee:property>"."\n";
      	echo "      <boxee:release-date>$originallyAvailableAt</boxee:release-date>"."\n"; 
      	echo "      <media:community>"."\n";
      	echo "        <media:starRating average='$rating'/>"."\n";
      	echo "      </media:community>"."\n";
	echo "   </item>"."\n";

	}

    else if ($description === "movie"){
	    //get show attributes
	    $programname = make_safe($item->attributes()->title);
	    $year = $item->attributes()->year;
	    $summary = make_safe($item->attributes()->summary);
	    $rating = $item->attributes()->rating;
	    $thumb = $item->attributes()->thumb;
	    $art = $item->attributes()->art;
	    $originallyAvailableAt = $item->attributes()->originallyAvailableAt;
	    //$banner = $item->attributes()->banner;
	    //$theme = $item->attributes()->theme;
	    $contentRating  = $item->attributes()->contentRating;
	    $duration  = $item->attributes()->duration;
	    $duration = sprintf("%0.0f", $duration/(60*1000));
	    $tagline = $item->attributes()->tagline;
	    $key  = $item->Media[0]->Part[0]->attributes()->key;

	

	echo "   <item>"."\n";
	echo "      <title>$programname</title>"."\n";
	echo "      <link>http://".$id.$key."</link>"."\n";
	echo "      <description>$summary</description>"."\n";
      	echo "      <media:thumbnail url='http://".$id.$thumb."' />"."\n";
      	echo "      <boxee:media-type expression='full' name='Full Episode' type='episode' />"."\n";
      	echo "      <boxee:release-date>$year</boxee:release-date>"."\n";
      	echo "      <boxee:property name='custom:duration'>$duration min</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:releasedate'>$originallyAvailableAt</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:tagline'>$tagline</boxee:property>"."\n";
      	echo "      <boxee:user-rating>$rating</boxee:user-rating>"."\n";
      	echo "      <boxee:property name='custom:art'>http://".$id.$art."</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:banner'>http://".$id.$banner."</boxee:property>"."\n";
      	echo "      <boxee:property name='custom:theme'>http://".$id.$theme."</boxee:property>"."\n";
     	echo "      <media:rating schema='urn:v-chip'>$contentRating</media:rating>"."\n";
      	echo "      <media:community>"."\n";
      	echo "        <media:starRating average='$rating'/>"."\n";
      	echo "      </media:community>"."\n";
	echo "   </item>"."\n";

	}



$i++;
}

$i = 0;
while ($xml->Track[$i])
{
    $item = $xml->Track[$i];

    if ($description === "track"){
	    //get show attributes
	    $programname = $item->attributes()->title;
	    $summary = $item->attributes()->summary;
	    $index = $item->attributes()->index;
	    $duration  = $item->attributes()->duration;
	    $thumb = $xml->attributes()->thumb;
	    $key  = $item->Media[0]->Part[0]->attributes()->key;
	
	    $duration = sprintf("%0.0f", $duration/(60*1000));

	    if (strlen($summary) == 0){
		$summary = $xml->attributes()->grandparentTitle.': '.$xml->attributes()->parentTitle;
	    }

	echo "   <item>"."\n";
	echo "      <title>$index  $programname</title>"."\n";
	echo "      <link>http://".$id.$key."</link>"."\n";
	echo "      <description>$summary</description>"."\n";
      	echo "      <media:thumbnail url='http://".$id.$thumb."' />"."\n";
      	echo "      <boxee:property name='custom:duration'>$duration min</boxee:property>"."\n";
	echo "   </item>"."\n";

	}

$i++;
}

echo "</channel>"."\n";
echo "</rss>"."\n";

    




#####################################################################

function make_safe($string) {
    $string = preg_replace('#<!\[CDATA\[.*?\]\]>#s', '', $string);
    $string = strip_tags($string);
    // The next line requires PHP 5.2.3, unfortunately.
    $string = htmlentities($string, ENT_QUOTES, 'UTF-8', false);
    $string = htmlspecialchars($string, ENT_QUOTES, 'UTF-8', true);
    $string = str_replace(' & ', ' &amp; ', $string);

      $string = str_replace('&amp;#039;', '\'', $string);
      $string = str_replace('&amp;reg;', '', $string);
      $string = str_replace('&eacute;', 'e', $string);
      $string = str_replace('&ouml;', 'o', $string);
      $string = str_replace('&mdash;', '-', $string);
      $string = str_replace('&prime;', '\'', $string);
      $string = str_replace('&amp;prime;', '\'', $string);
      $string = str_replace('&nbsp;', ' ', $string);

    return $string;
}

function parseFeed($url, $port){

	echo ("<!-- Sending request to url: $url on port $port -->\n");

	// Use cURL to get the RSS feed into a PHP string variable.
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_PORT, $port);
	curl_setopt($ch, CURLOPT_TIMEOUT, 300);
	//curl_setopt($ch, CURLOPT_HEADER, false);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

	$xmlstring = curl_exec($ch);
	curl_close($ch);
	if (strlen($xmlstring) == 0 ){
		die ("<!-- No data returned from server-->\n");
	}
	return (simplexml_load_string($xmlstring));
	
}

?>
