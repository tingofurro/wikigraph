<?php
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
		<a href="<?php echo $root."subfield/1";?>"><div class="menuItem">Subfields</div></a>
		<a href="<?php echo $root."keywords";?>"><div class="menuItem">Keywords</div></a>
		<!-- <a href="<?php echo $root."clustering";?>"><div class="menuItem">Clustering Work</div></a> -->
	</div>
<?php
}
function cleanFieldColor() {
	return array('#FF0000', '#FF0074', '#FF00E8', '#A200FF', '#2D00FF', '#0046FF', '#00BAFF', '#00FFD0', '#00FF5B', '#18FF00', '#8CFF00', '#FFFD00', '#FF8900');
}
?>