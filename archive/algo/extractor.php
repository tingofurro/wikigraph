<?php
function extractSubcat($category) {
	/* Given a category, will extract the subcategories from Wikipedia */
	$html = file_get_contents('http://en.wikipedia.org/wiki/Category:'.urlencode($category));
	$returnCategories = array();
	$dom = new DOMDocument;
	@$dom->loadHTML($html);
	$dom = $dom->getElementById('mw-subcategories');
	if(!is_null($dom)) {
		foreach ($dom->getElementsByTagName('a') as $link) {
			$thisClass = $link->getAttribute('class');
			if(!empty($thisClass) AND strpos($thisClass, 'CategoryTreeLabel') !== false) {
				$h = explode(":", $link->getAttribute('href')); $category = urldecode($h[1]);
				array_push($returnCategories, $category);
			}
		}
	}
	return $returnCategories;
}
function extractPagesFromCat($category) {
	// Given a category, get a list of pages
	$articleNames = array();
	$dom = new DOMDocument;
	$html = file_get_contents('http://en.wikipedia.org/wiki/Category:'.urlencode($category));
	@$dom->loadHTML($html);
	$dom = $dom->getElementById('mw-pages');
	if(!is_null($dom)) {
		foreach ($dom->getElementsByTagName('a') as $link) {
			$href = $link->getAttribute('href');
			if(strpos($href, "/wiki/") !== false) { // this is an interesting link
				$h = str_replace("/wiki/", "", $href);
				$pieces = explode("#", urldecode(utf8_encode(($h))));
				$name = $pieces[0];// get rid of anchor if there is one
				$try = explode(":", $name);
				if(in_array($try[0], array("Help", "Wikipedia", "Category", "Special", "Template", "Portal", "File", "Template_talk", "Book"))) {} // bad page
				elseif(strpos(utf8_encode($name), '"') !== false) {} // don't keep this page
				else array_push($articleNames, $name);
			}
		}
	}
	return $articleNames;
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
function removeLists($html) { // Remove lists that tend to create complete subgraphs
	$dom = new DOMDocument;
	@$dom->loadHTML(cleanEncoding($html));
	$dom = $dom->getElementsByTagName('body')->item(0);
	$children  = $dom->childNodes;
	$toRemove = array();
	foreach ($children as $child) {
		if(get_class($child) == 'DOMElement') {
			$thisClass = $child->getAttribute('class'); // ambox
			if(!empty($thisClass) AND (strpos($thisClass, 'plainlist') !== false OR strpos($thisClass, 'navbox') !== false OR strpos($thisClass, 'ambox') !== false)) array_push($toRemove, $child);
		}
	}
	foreach ($toRemove as $list) $dom->removeChild($list);
	if(count($toRemove) > 0) echo "<b>Removed something</b>";
	return DOMinnerHTML($dom);
}
function divToSkip(DOMNode $child, $skip) {
	// GOAL: See if we have hit a new Section in the Article (<h2>). If so, verify it's name and see if want to skip that
	$removeHeadline = array('Notes', 'References', 'Historical_references', 'Additional_references', 'External_links', 'Further_reading', 'Notes_and_references', 'Other_resources', 'Bibliography', 'Books', 'Notes_2', 'Sources', 'References_and_further_reading', 'Recommended_reading', 'Additional_reading', 'Literature', 'Footnotes', 'Citations', 'Publications', 'References_and_external_links', 'References_and_notes', 'Textbooks', 'Sources_and_external_links', 'General_references', 'In-line_notes_and_references', 'Selected_bibliography', 'Selected_works', 'Some_publications', 'Biographical_references'); // keep this: 'See_also'
	if(get_class($child) == 'DOMElement') {
		$thisEntity = $child->nodeName;
		if($thisEntity == 'h2') {
			$H2children  = $child->childNodes;
			foreach ($H2children as $couldHeadline) {
				$thisClass = $couldHeadline->getAttribute('class');
				if(!empty($thisClass) AND strpos($thisClass, 'mw-headline') !== false) {
					return in_array($couldHeadline->getAttribute('id'), $removeHeadline); 
				}
			}
		}
	}
	return $skip;
}
function extractLinkArray($pageId) {
	// GOAL: Extract links from a certain page
	$pageNames = array();
	$html = file_get_contents('../data/'.$pageId.'.txt');
	$dom = new DOMDocument;
	@$dom->loadHTML($html);
	foreach ($dom->getElementsByTagName('a') as $link) {
		$href = $link->getAttribute('href');
		// if(($s = strpos($href, "/wiki/") !== false) AND $s === 0) { // this is an interesting link
		if(strpos($href, "/wiki/")===0) { // this is an interesting link
			$h = str_replace("/wiki/", "", $href);
			$toks = explode("#", $h); $cleanName = urldecode($toks[0]);
			$try = explode(":", $cleanName);
			if(in_array($try[0], array("Help", "Wikipedia", "Category", "Special", "Template", "Portal", "File", "Template_talk"))) {}
			else if(!in_array($cleanName, $pageNames)) {array_push($pageNames, $cleanName);}
		}
	}
	return $pageNames;
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
function redirectName($name) {
	$dom = new DOMDocument; @$dom->loadHTML(cleanEncoding(file_get_contents('http://en.wikipedia.org/wiki/'.urlencode($name))));
	$dom = $dom->getElementById('firstHeading');
	if(!empty($dom)) return strToWiki(strip_tags(DOMinnerHTML($dom)));
	return '';
}
function DOMinnerHTML(DOMNode $element) { 
	$innerHTML = "";
	foreach ($element->childNodes as $child) $innerHTML .= $element->ownerDocument->saveHTML($child);
	return $innerHTML; 
}
function cleanEncoding($txt) {
	return mb_convert_encoding($txt, 'HTML-ENTITIES', 'UTF-8');
}
?>