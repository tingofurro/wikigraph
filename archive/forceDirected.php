<?php
 // Based of spring algorithm here: https://cs.brown.edu/~rt/gdhandbook/chapters/force-directed.pdf
	set_time_limit(24*3600);
	include_once('init.php');
	$elems = 10000; $areaPerElem = 1000;
	$width = floor(1.25*sqrt($elems*$areaPerElem));
	$height = floor(0.8*sqrt($elems*$areaPerElem));

	$files = scandir('graphData');
	$lastFile = ''; $maxRound = 0;
	foreach ($files as $file) {
		if(strpos($file, "g".$elems."-r") !== false) {
			$thisRound = str_replace(".txt", "", str_replace("g".$elems."-r", "", $file));
			if($thisRound > $maxRound) {$lastFile = $file; $maxRound = $thisRound;}
		}
	} // try to find if we stopped somewhere nice


	$c1 = 0.10; $c2 = 100; $c3 = 20; $c4 = 0.15;
	$timeStart = time();
	$edges = array();
	$nodes = array();
	$X = array(); $Y = array();
	$labels = array();
	$p = mysql_query("SELECT * FROM wg_page WHERE id<=$elems ORDER BY id");
	while($pa = mysql_fetch_array($p)) {
		array_push($nodes, $pa['id']);
		$labels[$pa['id']] = $pa['name'];
	}

	if($lastFile == '') { // random initialization
		$round = 1;
		foreach ($nodes as $node) {
			$X[$node] = rand(0,$width);
			$Y[$node] = rand(0,$height);		
		}
	}
	else { // get back to the last stage
		$handle = fopen('graphData/'.$lastFile, 'r');
		while (($buffer = fgets($handle, 4096)) !== false) {
			$buffer = str_replace("\n", "", $buffer);
			$toks = explode("|", $buffer);
			if(count($toks) >= 3) {
				$node = $toks[0];
				$X[$node] = $toks[1]; $Y[$node] = $toks[2];
			}
		}
		$round = $maxRound+1;
	}

	$r = mysql_query("SELECT * FROM wg_link WHERE (`from`<=$elems AND `to`<=$elems) ORDER BY id");
	while($re = mysql_fetch_array($r)) {
		if(!isset($edges[$re['to']])) {$edges[$re['to']] = array();}
		array_push($edges[$re['to']], $re['from']);
	}

	// we're going to iterate a number of times ...
	$totalMove = $elems;
	while($totalMove > 0.1*$elems) {
		$totalMove = 0;
		$NX = array(); $NY = array();
		foreach ($nodes as $node) {
			$Fx = 0; $Fy = 0;
			// we're going to calculate total force on this node. First all attracting, then all repelling
			if(isset($edges[$node])) { // there are incoming edges
				foreach ($edges[$node] as $from) {
					$dx = ($X[$from]-$X[$node]); $dy = ($Y[$from]-$Y[$node]);
					$dist = sqrt($dx*$dx + $dy*$dy);
					if($dist > 0) {
						$delta = ($dist-$c2);
						$Fx += $c1*$delta*($dx/$dist); $Fy += $c1*$delta*($dy/$dist);
					}
				} // end of attractive force
				// repulsive force
				foreach ($nodes as $node2) {
					if($node != $node2) {
						$dx = ($X[$node2]-$X[$node]); $dy = ($Y[$node2]-$Y[$node]);
						$dist = sqrt($dx*$dx + $dy*$dy);
						if($dist > 0) {
							$Fx -= ($c3/($dist))*($dx/$dist); $Fy -= ($c3/($dist*$dist))*($dy/$dist);
						}
					}
				}
			}
			$NX[$node] = rangeFunc((round(100*($X[$node]+$c4*$Fx))/100), 0, $width);
			$NY[$node] = rangeFunc((round(100*($Y[$node]+$c4*$Fy))/100), 0, $height);
			$totalMove += abs($NX[$node]-$X[$node])+abs($NY[$node]-$Y[$node]);
		}
		$X = $NX; $Y = $NY; //update at the end of the iteration
		saveTofile($round, $nodes, $X, $Y);
		echo "ROUND ".($round++)."<br />";
		echo "Total move: ".$totalMove." px<br />";
		echo "----------------------<br />";
		flush(); ob_flush(); // force output
	}
	$timeEnd = time();

	function saveTofile($round, $nodes, $X, $Y) {
		$fh = fopen('graphData/g'.count($nodes).'-r'.$round.'.txt', 'w');
		$txt = "";
		foreach ($nodes as $node) {
			$txt .= $node."|".$X[$node]."|".$Y[$node]."\n";
		}
		fwrite($fh, $txt);
		fclose($fh);
	}
	function rangeFunc($val, $min, $max) {
		$val = max($min, $val); return min($max, $val);
	}
?>
Time it took to run: <?php echo floor(($timeEnd-$timeStart)/60); ?>min <?php echo (($timeEnd-$timeStart)%60); ?>seco