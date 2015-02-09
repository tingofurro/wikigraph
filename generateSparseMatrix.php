cooucoucou :)
<?php
	include_once('init.php');
	$myLinks = array();
	$r = mysql_query("SELECT * FROM wg_links ORDER BY id");
	while($re = mysql_fetch_array($r)) {
		if(!isset($myLinks[$re['from']])) {$myLinks[$re['from']] = array();}
		array_push($myLinks[$re['from']], $re['to']);
		if(($re['id']%1000)==0) {echo count($myLinks)."<br />";}
	}
	echo 'mat = [<br />';
	$already = false;
	foreach ($myLinks as $from => $links) {
		if($already) {echo ',<br />';}
		echo '{';
		$already2 = false;
		foreach ($links as $to) {
			if($already2) {echo ", ";}
			echo "{".$from.", ".$to.", 1}";
			$already2 = true;
		}
		echo '}';
		$already = true;
	}
	echo '<br />]';
?>