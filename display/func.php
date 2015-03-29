<?php
function strToWiki($oldStr) {
	return str_replace(" ", "_", $oldStr);
}
function wikiToName($oldStr) {
	return urldecode(str_replace("_", " ", $oldStr));
}
function whereField($field) {
	return "(`fields`='$field' OR `fields` LIKE '%|".$field."|%' OR `fields` LIKE '".$field."|%' OR `fields` LIKE '%|".$field."')";
}
function topMenu($root, $realRoot) {
?>
	<link rel="stylesheet" type="text/css" href="<?php echo $realRoot;?>css/topMenu.css">
	<div id="topMenu">
		<a href="<?php echo $root;?>"><img src="<?php echo $realRoot;?>images/logo.png" alt="WikiGraph" id="logo" /></a>
		<a href="<?php echo $root."category";?>"><div class="menuItem firstItem">Category Tree</div></a>
		<a href="<?php echo $root."fields";?>"><div class="menuItem">Math fields</div></a>
		<a href="<?php echo $root."explore";?>"><div class="menuItem">Explore the Set</div></a>
		<a href="<?php echo $root."clustering";?>"><div class="menuItem">Clustering Work</div></a>
	</div>
<?php
}
function getRoot() {
	$link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	if(strpos($link, "/Wikigraph")) {$pos = strpos($link, "/Wikigraph"); $root = substr($link, 0, ($pos+10))."/";}
	if(strpos($link, "/wikigraph")) {$pos = strpos($link, "/wikigraph"); $root = substr($link, 0, ($pos+10))."/";}
	return $root;
}
function getRealRoot() {
	return getRoot().'display/';
}
function getDocumentRoot() {

	$link = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'];
	if(strpos($link, "/Wikigraph")) {$root = $_SERVER['DOCUMENT_ROOT']."/Wikigraph";}
	if(strpos($link, "/wikigraph")) {$root = $_SERVER['DOCUMENT_ROOT']."/wikigraph";}
	return $root;
}
function cleanFieldColor() {
	return array('#FF0000', '#FF0074', '#FF00E8', '#A200FF', '#2D00FF', '#0046FF', '#00BAFF', '#00FFD0', '#00FF5B', '#18FF00', '#8CFF00', '#FFFD00', '#FF8900');
}
?>