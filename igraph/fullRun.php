<?php
include_once('../dbco.php');
include_once('../mainFunc.php');
include_once('../algo/func.php');

$python = wherePython();
$limit = 500;

include_once('1-buildGraph.php');
createGraph($limit);
buildSummaries($limit);
echo 'Done with 1<br />';

$pyscript = '"'.getDocumentRoot().'/igraph/2-main.py"';
$param1 = '"'.getDocumentRoot().'"';
exec($python.' '.$pyscript." ".$param1, $output);
echo 'Done with 2<br />';

$pyscript = '"'.getDocumentRoot().'/igraph/4-closeness.py"';
exec($python.' '.$pyscript." ".$param1, $output);
echo 'Done with 3<br />';

?>