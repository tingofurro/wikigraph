<?php
  include_once('init.php');
  include_once('graphJsonCreator.php');
  $field =  5;
  if(isset($_GET['field'])) {$field = mysql_real_escape_string($_GET['field']);}
  generateGraph($field);
?>
<!DOCTYPE html>
<meta charset="utf-8">
<link rel="stylesheet" type="text/css" href="css/graphCat.css">
<body>
<script src="http://d3js.org/d3.v3.min.js"></script>
<script src="JS/graphCat.js"></script>