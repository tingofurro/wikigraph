<?php
function whereField($field) {
	return "(`fields`='$field' OR `fields` LIKE '%|".$field."|%' OR `fields` LIKE '".$field."|%' OR `fields` LIKE '%|".$field."')";
}
function topMenu($root, $realRoot) {
?>
	<link rel="stylesheet" type="text/css" href="<?php echo $realRoot;?>css/topMenu.css">
	<div id="topMenu">
		<a href="<?php echo $root;?>"><img src="<?php echo $realRoot;?>images/logo.png" alt="WikiGraph" id="logo" /></a>
		<a href="<?php echo $root."explore";?>"><div class="menuItem firstItem">Explore the Set</div></a>
		<a href="<?php echo $root."fields";?>"><div class="menuItem">Math fields</div></a>
		<!-- <a href="<?php echo $root."subfield/1";?>"><div class="menuItem">Subfields</div></a> -->
		<a href="<?php echo $root."topics/1/1";?>"><div class="menuItem">Topics</div></a>
		<a href="<?php echo $root."keywords";?>"><div class="menuItem">Keywords</div></a>
	</div>
<?php
}
?>