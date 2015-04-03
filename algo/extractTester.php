<?php
include_once('../dbco.php');
include_once('func.php');
include_once('extractor.php');
set_time_limit(3600);
// $r = mysql_query("SELECT * FROM wg_page WHERE id<1000");
$r = mysql_query("SELECT * FROM wg_page WHERE id=306");
while($re = mysql_fetch_array($r)) {
	$cleanHtml = extractSections($re['name']);
	$return = removeLists($cleanHtml);
	if($return[0] > 0) {
		// echo $re['id'].") ".$re['name']." got something removed<br />";
		ob_flush();
	}
	echo $return[1];
}

function extractSections($pageName) { // Given a pagename, extract HTML
	$html = file_get_contents('http://en.wikipedia.org/wiki/'.urlencode(strToWiki($pageName)));
	$dom = new DOMDocument;
	@$dom->loadHTML(cleanEncoding($html));
	$dom = $dom->getElementById('mw-content-text');

    $cleanHtml = ""; $lastHeadline = ''; $skip = false;
    $children  = $dom->childNodes;
    foreach ($children as $child) {
    	$skip = divToSkip($child, $skip); // reload skipping
		if(!$skip) $cleanHtml .= $dom->ownerDocument->saveHTML($child);
    }
    return $cleanHtml;
}
function removeLists($html) {
	$dom = new DOMDocument;
	@$dom->loadHTML($html);
	$dom = $dom->getElementsByTagName('body')->item(0);
	$children  = $dom->childNodes;
    $toRemove = array();
    foreach ($children as $child) {
		if(get_class($child) == 'DOMElement') {
	    	$thisClass = $child->getAttribute('class');
			if(!empty($thisClass) AND strpos($thisClass, 'plainlist') !== false) array_push($toRemove, $child);
		}
    }
	foreach ($toRemove as $list) $dom->removeChild($list);

	return array(count($toRemove), DOMinnerHTML($dom));
}
?>