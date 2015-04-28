<?php
include_once('../dbco.php');
include_once('../mainFunc.php');

$python = wherePython();

include_once('1-buildEntireGraph.php');
createGraph();
echo 'Done with 1<br />';

$pyscript = '"'.getDocumentRoot().'/igraph/2-main.py"';
$param1 = '"'.getDocumentRoot().'"';
exec($python.' '.$pyscript." ".$param1, $output);
echo 'Done with 2<br />';

include_once('3-buildSummarySets.php');
buildSummaries();
echo 'Done with 3<br />';

$pyscript = '"'.getDocumentRoot().'/igraph/4-closeness.py"';
exec($python.' '.$pyscript." ".$param1, $output);
echo 'Done with 4<br />';

?>