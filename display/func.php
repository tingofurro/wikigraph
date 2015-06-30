<?php
function shorterName($name) {
	$name = explode(",", $name);
	$name = array_slice($name, 0, min(3, count($name)));
	return implode(",", $name);
}
?>