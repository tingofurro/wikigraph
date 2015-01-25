<?php
include('init.php');
set_time_limit(7200);
$r = mysql_query("SELECT * FROM wg_page WHERE visited=0 ORDER BY id");
while($re = mysql_fetch_array($r)) {
	$html = file_get_contents('http://en.wikipedia.org/wiki/'.$re['name']);
	$dom= new DOMDocument(); $dom->load($html);
	$domContent = $dom->getElementById('content');
	$content = DOMinnerHTML($domContent);

	mysql_query("UPDATE wg_page SET html='".mysql_real_escape_string($content)."', visited='1' WHERE id='".$re['id']."'");
}

function DOMinnerHTML(DOMNode $element) { 
    $innerHTML = ""; 
    $children  = $element->childNodes;
    foreach ($children as $child) { 
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }
    return $innerHTML; 
} 

?>