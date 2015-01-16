<?php
function strToWiki($oldStr) {
	return str_replace(" ", "_", $oldStr);
}
function wikiToName($oldStr) {
	return str_replace("_", " ", $oldStr);
}
?>