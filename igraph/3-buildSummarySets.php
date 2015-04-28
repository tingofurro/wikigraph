<?php

function buildSummaries() {
	include_once('../dbco.php');
	include_once('../mainFunc.php');
	include_once('../algo/extractor.php');
	$src = getDocumentRoot()."/igraph/data/spinglass.txt";
	$groups = file_get_contents($src); $groups = preg_split('/\r\n|\n|\r/', trim($groups));
	$nodes = array();
	foreach ($groups as $toks) {
		$tok = explode(" ", $toks);
		array_push($nodes, $tok[0]);
	}
	emptyFolder('txt');
	foreach ($nodes as $i => $node) {
		$html = file_get_contents('../data/'.$node.'.txt');
		$summary = extractSummary('<body>'.$html.'</body>');
		$summary = strip_tags($summary);
		$fh = fopen('txt/'.$node.'.txt', 'w');
		fwrite($fh, $summary);
		fclose($fh);
	}
}

function emptyFolder($folderName) {
	foreach(glob($folderName.'/*') as $file) if(is_file($file)) unlink($file);
}
function extractSummary($html) {
	// Given an HTML Wikipedia page, extract the summary (in HTML format still) (~first paragraph)	
	$dom = new DOMDocument;
	@$dom->loadHTML(cleanEncoding($html));
	$dom = $dom->getElementsByTagName('body')->item(0);
	$cleanHtml = ""; $lastHeadline = ''; $skip = false;
	$children  = $dom->childNodes;
	foreach ($children as $child) {
		if(get_class($child) == 'DOMElement' && $child->nodeName == 'h2') {break;}
		$skip = false;
		if(get_class($child) == 'DOMElement') {
			$class = $child->getAttribute('class');
			if(!empty($class) && $class == 'hatnote') $skip = true;
		}
		if(!$skip) {$cleanHtml .= $dom->ownerDocument->saveHTML($child);}
	}
	return $cleanHtml;
}
?>