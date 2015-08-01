<?php
function shorterName($name, $total = 3) {
	$name = explode(",", $name);
	$name = array_slice($name, 0, min($total, count($name)));
	return implode(",", $name);
}
?>