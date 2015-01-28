<?php
include('init.php');
set_time_limit(4*3600);
$r = mysql_query("SELECT * FROM wg_page WHERE visited=0 ORDER BY id");
while($re = mysql_fetch_array($r)) {
	$url = 'http://en.wikipedia.org/wiki/'.urlencode(strToWiki($re['name']));
	$html = file_get_contents($url);
	$dom = new DOMDocument; $dom->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
	$dom = $dom->getElementById('mw-content-text');
	$cleanHtml = DOMinnerHTML($dom);
	$fh = fopen('data/'.$re['id'].'.txt', 'w'); fwrite($fh, $cleanHtml);

	mysql_query("UPDATE wg_page SET visited='1' WHERE id='".$re['id']."'");
	echo $re['id']." done<Br />";
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