<?php
include('dbco.php');
?>
<html>
<head>
	<title>
		Tree of Math Categories on Wikipedia
	</title>	
</head>
<body>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
	<script src="JS/lib/springy.js"></script>
	<script src="JS/lib/springyui.js"></script>
	<script src="JS/category.js"></script>

	<canvas id="springydemo" width='1300' height='700' />
	<script type="text/javascript">
	var graph = new Springy.Graph();

	<?php
	$r = mysql_query("SELECT * FROM wg_category WHERE distance<=2 ORDER BY distance");
	while($re = mysql_fetch_array($r)) {
		echo "var node".$re['id']." = graph.newNode({label: '".$re['name']."'}); \n";
		if($re['parent'] != 0) {
			echo "graph.newEdge(node".$re['parent'].", node".$re['id'].", {color: '#6A4A3C'});\n";
		}
	}
	?>
	jQuery(function(){
	  var springy = window.springy = jQuery('#springydemo').springy({
	    graph: graph,
	    nodeSelected: function(node){
	      console.log('Node selected: ' + JSON.stringify(node.data));
	    }
	  });
	});
	</script>
</body>
</html>
