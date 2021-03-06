var webroot = '';
var isProblem = true;
var screenW = window.innerWidth-12, screenH = window.innerHeight-5;
var alphaF = 0.005, alphaI;
var smallRadius = 3, largeRadius = 4;
var svg, force;
var rectangle, aboveRect, loading;
var gnodes, nodes, links=[],bilinks=[];
var plotKeywords = true;
var graphData;
var transX, transY;

$(document).ready(function() {
	svg = d3.select("body").append("svg").attr("width", screenW).attr("height", screenH);
	startGraph();
});
function startGraph() {
	$("svg").empty();
	toRun = ($('#toRun').html()==1)?true:false;
	plotGraph($('#fileUrl').html(), toRun, $('#whereToSave').html());
}
function plotGraph(graphFile, toRun, toFile) {
	alphaI = (toRun==1)?0.2:0.0052;
	rectangle = svg.append("rect").attr({"x": (screenW/2 - 125), "y": (screenH/2 - 13), 'width': 250, 'height': 26, 'class': 'waitRectangle'});
	aboveRect = svg.append("rect").attr({"x": (screenW/2 - 125), "y": (screenH/2 - 13), 'width': 1, 'height': 26, 'class': 'aboveRect'});
	loading = svg.append("text").attr({"x": (screenW/2), "y": (screenH/2), 'dy': '0.35em'}).style("text-anchor", "middle").text("Simulating. One moment please...");

	force = d3.layout.force().linkStrength(2).friction(0.9).charge(-10).gravity(0.1).theta(0.8).alpha(alphaI).size([screenW, screenH]);
	d3.json(graphFile, function(error, graph) {
		setTimeout(function() { if(isProblem) {isProblem=false; startGraph();}}, 2000);
		nodes = graph.nodes.slice();
		graph.nodes.forEach(function(node) {node.keywords = node.keywords.split(',');});
		graph.links.forEach(function(link) {
			var s = nodes[link.source],
			t = nodes[link.target],
			i = {t1: s, t2: t}; // intermediate node
			nodes.push(i);
			links.push({source: s, target: i, value: link.value}, {source: i, target: t, value: link.value});
			bilinks.push([s, i, t]);
		});
		graphData = graph;
		force.linkDistance(function(d) {return d.value;}).nodes(nodes).links(links).start();

		var link = svg.selectAll(".link").data(bilinks).enter().append("path").attr("class", "link");

	    gnodes = svg.selectAll('g.gnode').data(graph.nodes).enter().append('g').classed('gnode', true).on('mouseover', function(d){
			d3.select(this).select('circle').attr('r', largeRadius);
			openNotif(d);
	    }).on('mouseout', function(d){
			d3.select(this).select('circle').attr('r', smallRadius);
			deleteNotif();
	    });
	    node = gnodes.append("circle").attr("class", "node").attr("r", smallRadius).style("fill", function(d) { return str2color(d.group); });
	    // node.append("title").text(function(d) { return d.name; });

		force.on("tick", function() {
			isProblem = false;
			aboveRect.attr('width', Math.floor(250*(1-((force.getAlpha()-alphaF)/alphaI))));
		});
		 force.on("end", function() {
			var minX = 0, maxX = 0;	var minY = 0, maxY = 0;
			gnodes.attr('transform', function(d) {
				minX = Math.min(minX, d.x); maxX = Math.max(maxX, d.x);
				minY = Math.min(minY, d.y); maxY = Math.max(maxY, d.y);
			});
			scaleX = window.innerWidth/(maxX-minX); scaleY = window.innerHeight/(maxY-minY);
			transX = Math.abs(minX); transY = Math.abs(minY);
			gnodes.attr("transform", function(d) { 
				return 'translate(' + [(d.x+transX)*scaleX, (d.y+transY)*scaleY] + ')';
			});
			link.attr("d", function(d) {
				return "M" + (d[0].x+transX)*scaleX + "," + (d[0].y+transY)*scaleY+ "S" + (d[1].x+transX)*scaleX + "," + (d[1].y+transY)*scaleY+ " " + (d[2].x+transX)*scaleX + "," + (d[2].y+transY)*scaleY;
				// return "M" + (d[0].x+transX) + "," + (d[0].y+transY)+ " L" + (d[2].x+transX) + "," + (d[2].y+transY);
			}).style('stroke', function(d) { return (d[0].group==d[2].group)?str2color(d[0].group):'black'; })
			.style('opacity', function(d) { return (d[0].group==d[2].group)?0.1:0.03; });

			loading.remove(); aboveRect.remove(); rectangle.remove();
			if(toFile!='') {uploadAjax(nodes, links, toFile);}
			placeKeywords(svg);
		});
	});
}
function openNotif(data) {
	deleteNotif();
	noty({text: '<b>'+data.name+'</b>', layout: 'topRight', speed: 300});
}
function deleteNotif() {
	$('.noty_bar').parent().remove();
}
function uploadAjax(nodes, links, toFile) {
	keepNodes = []; keepEdges = [];
	for(node in nodes) {
		oldNode = nodes[node];
		if(oldNode.name) {
			keepNodes.push({'index': oldNode.index, 'id': oldNode.id, 'name': oldNode.name, 'group': oldNode.group, 'x': (Math.floor(10*oldNode.x)/10), 'y': (Math.floor(10*oldNode.y)/10), 'keywords': oldNode.keywords.join(",")});
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
		success: function(dat) { window.location='http://wikigraph.gatech.edu/'+$('#dbPrefix').html()+'/'+$('#nextGraph').html(); }
	});
}