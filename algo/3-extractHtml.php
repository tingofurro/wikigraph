<?php
/*
OBJECTIVE:
From the list of page names in DB, load the pages, extract clean HTML into txt files in wikigraph/data
*/
include('../dbco.php');
include('func.php');
set_time_limit(4*3600);
$r = mysql_query("SELECT * FROM wg_page");
while($re = mysql_fetch_array($r)) {
	if(!file_exists('../data/'.$re['id'].'.txt')) {
		$url = 'http://en.wikipedia.org/wiki/'.urlencode(strToWiki($re['name']));
		$html = file_get_contents($url);
		$dom = new DOMDocument; $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
		$dom = $dom->getElementById('mw-content-text');
		$cleanHtml = DOMinnerHTML($dom);
		$fh = fopen('../data/'.$re['id'].'.txt', 'w'); fwrite($fh, $cleanHtml);
	}
}
function DOMinnerHTML(DOMNode $element) { 
    $innerHTML = ""; $lastHeadline = ''; $skip = false;
    $children  = $element->childNodes;
    foreach ($children as $child) {
    	$skip = shouldISkip($child, $skip); // reload skipping
		if(!$skip) {
	        $innerHTML .= $element->ownerDocument->saveHTML($child);
		}
    }
    return $innerHTML; 
}
function shouldISkip(DOMNode $child, $skip) {
	$childType = get_class($child);
	if($childType == 'DOMElement') {
		$thisEntity = $child->nodeName;
		if($thisEntity == 'h2') {
		    $H2children  = $child->childNodes;
		    foreach ($H2children as $couldHeadline) {
				$thisClass = $couldHeadline->getAttribute('class');
				if(!empty($thisClass) AND strpos($thisClass, 'mw-headline') !== false) {
					$thisId = $couldHeadline->getAttribute('id');
					return in_array($thisId, array('See_also', 'Notes', 'References', 'External_links', 'Further_reading'));
				}			    	
		    }
		}
	}
	return $skip;
}
?>