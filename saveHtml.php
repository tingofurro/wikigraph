<?php
include('init.php');
set_time_limit(7200);
$r = mysql_query("SELECT * FROM wg_page WHERE visited=0 ORDER BY id");
while($re = mysql_fetch_array($r)) {
	$url = 'http://en.wikipedia.org/wiki/'.strToWiki($re['name']);
	$html = file_get_contents($url);
	$dom = new DOMDocument; $dom->loadHTML($html);
	$dom = $dom->getElementById('mw-content-text');
	$cleanHtml = DOMinnerHTML($dom);
	$cleanHtml = urldecode(utf8_encode($cleanHtml));
	mysql_query("UPDATE wg_page SET html='".mysql_real_escape_string($cleanHtml)."', visited='1' WHERE id='".$re['id']."'");
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
					return in_array($thisId, array('See_also', 'Notes', 'References', 'External_links'));
				}			    	
		    }
		}
	}
	return $skip;
}
?>