var screenW = $(document).width()-20, screenH = $(document).height()-20;
var alphaF = 0.005, alphaI;
var smallRadius = 4, largeRadius = 5;
var svg, force;
var rectangle, aboveRect, loading;
var nodes, links=[],bilinks=[];

function plotGraph(graphFile, toRun, toFile) {
	// toRun: do the nodes already have positions or not?
	// toFile: where to save. If == '' then no saving
	alphaI = (toRun==1)?0.2:0.0052;
	svg = d3.select("body").append("svg").attr("width", screenW).attr("height", screenH);
	rectangle = svg.append("rect").attr({"x": (screenW/2 - 125), "y": (screenH/2 - 13), 'width': 250, 'height': 26, 'class': 'waitRectangle'});
	aboveRect = svg.append("rect").attr({"x": (screenW/2 - 125), "y": (screenH/2 - 13), 'width': 1, 'height': 26, 'class': 'aboveRect'});
	loading = svg.append("text").attr({"x": (screenW/2), "y": (screenH/2), 'dy': '0.35em'}).style("text-anchor", "middle").text("Simulating. One moment please...");

	force = d3.layout.force().friction(0.9).linkDistance(function(d) {return d.value; }).charge(-120).gravity(0.1).theta(0.8).alpha(alphaI).size([screenW, screenH]);
	d3.json(graphFile, function(error, graph) {
		nodes = graph.nodes;
		links = graph.links;

		force.nodes(graph.nodes).links(graph.links).start();

		var link = svg.selectAll(".link").data(graph.links).enter().append("line").attr("class", "link");

	    var gnodes = svg.selectAll('g.gnode').data(graph.nodes).enter().append('g').classed('gnode', true).on('mouseover', function(d){
	         d3.select(this).select('circle').attr('r', largeRadius);
	         document.title=d.name;
	    }).on('mouseout', function(d){
	         d3.select(this).select('circle').attr('r', smallRadius);
	    });
	      
	    var node = gnodes.append("circle").attr("class", "node").attr("r", smallRadius).style("fill", function(d) { return color(d.group); });
	    node.append("title").text(function(d) { return d.name; });

		force.on("tick", function() {
			aboveRect.attr('width', Math.floor(250*(1-((force.getAlpha()-alphaF)/alphaI))));
		});
		 force.on("end", function() {
	      var minX = 0, maxX = 0;
	      var minY = 0, maxY = 0;
	      gnodes.attr('transform', function(d) {
	        minX = Math.min(minX, d.x); maxX = Math.max(maxX, d.x);
	        minY = Math.min(minY, d.y); maxY = Math.max(maxY, d.y);
	      });
	      transX = Math.abs(minX);
	      transY = Math.abs(minY);
	      svg.attr("width", Math.max(screenW, (maxX+transX))).attr("height", Math.max(screenH, (maxY+transY)));
	      gnodes.attr("transform", function(d) { 
	         return 'translate(' + [d.x+transX, d.y+transY] + ')';
	      });
		link.attr("x1", function(d) { return d.source.x; })
		.attr("y1", function(d) { return d.source.y; })
		.attr("x2", function(d) { return d.target.x; })
		.attr("y2", function(d) { return d.target.y; });
	      loading.remove(); aboveRect.remove(); rectangle.remove();
		  if(toFile!='') {uploadAjax(nodes, links, toFile);}
		});
	});
}
function uploadAjax(nodes, links, toFile) {
	keepNodes = []; keepEdges = [];
	for(node in nodes) {
		oldNode = nodes[node];
		if(oldNode.name) {
			keepNodes.push({'index': oldNode.index, 'id': oldNode.id, 'name': oldNode.name, 'group': oldNode.group, 'x': (Math.floor(10*oldNode.x)/10), 'y': (Math.floor(10*oldNode.y)/10)});
		}
	}
	for(link in links) {
		oldLink = links[link];
		if(oldLink.source.name) {
			center = oldLink.target; // not the real node
			keepEdges.push({'source': center.t1.index, 'target': center.t2.index, 'value': 2});
		}
	}
	postData = {nodes: JSON.stringify(keepNodes), links: JSON.stringify(keepEdges), toFile: toFile};
	$.ajax({type: "POST",
		url: webroot+'ajax/saveGraph.php',
		data: postData,
		success: function(dat) { /* alert(dat); */ }
	});
}