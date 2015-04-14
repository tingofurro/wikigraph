//var node_data = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20, 21, 22, 23, 24, 25, 26, 27, 28, 29, 30, 31, 32, 33, 34, 35, 36, 37, 38,39, 40, 41, 42, 43, 44, 45, 46, 47, 48, 49, 50, 51, 52, 53, 54, 55, 56, 57, 58, 59, 60 , 61, 62, 63, 64, 65 , 66, 67, 68, 69, 70, 71, 72, 73];
//var edge_data = [{"source":5,"target":0,"weight":1.2857142857142856},{"source":8,"target":5,"weight":0.125},{"source":10,"target":5,"weight":0.125},{"source":14,"target":33,"weight":0.2},{"source":16,"target":17,"weight":0.5},{"source":16,"target":57,"weight":0.2},{"source":17,"target":16,"weight":0.5},{"source":17,"target":0,"weight":0.25},{"source":20,"target":38,"weight":0.25},{"source":20,"target":36,"weight":0.8333333333333333},{"source":29,"target":17,"weight":0.5},{"source":32,"target":17,"weight":0.25},{"source":33,"target":2,"weight":0.3333333333333333},{"source":33,"target":4,"weight":0.2},{"source":34,"target":35,"weight":0.75},{"source":34,"target":58,"weight":0.16666666666666666},{"source":34,"target":9,"weight":0.5},{"source":35,"target":34,"weight":0.75},{"source":36,"target":35,"weight":0.3333333333333333},{"source":36,"target":57,"weight":0.2},{"source":38,"target":0,"weight":0.5},{"source":38,"target":20,"weight":0.25},{"source":38,"target":58,"weight":0.16666666666666666},{"source":37,"target":35,"weight":0.5833333333333333},{"source":39,"target":7,"weight":0.2},{"source":40,"target":0,"weight":0.5},{"source":41,"target":21,"weight":0.1111111111111111},{"source":41,"target":52,"weight":0.5},{"source":42,"target":22,"weight":0.5},{"source":43,"target":15,"weight":0.9663059163059161},{"source":44,"target":43,"weight":0.39285714285714285},{"source":45,"target":14,"weight":0.16666666666666666},{"source":45,"target":58,"weight":0.41666666666666663},{"source":46,"target":47,"weight":0.5095238095238095},{"source":47,"target":46,"weight":0.5095238095238095},{"source":48,"target":46,"weight":1.4773809523809522},{"source":49,"target":30,"weight":0.4583333333333333},{"source":50,"target":8,"weight":0.14285714285714285},{"source":51,"target":8,"weight":0.14285714285714285},{"source":51,"target":0,"weight":0.2},{"source":52,"target":41,"weight":0.5},{"source":53,"target":20,"weight":0.25},{"source":54,"target":20,"weight":0.25},{"source":56,"target":54,"weight":0.3333333333333333},{"source":57,"target":58,"weight":1.6666666666666665},{"source":58,"target":0,"weight":1.3666666666666665},{"source":59,"target":0,"weight":0.2},{"source":60,"target":28,"weight":0.16666666666666666},{"source":61,"target":60,"weight":0.16666666666666666},{"source":55,"target":9,"weight":1.3095238095238095},{"source":62,"target":9,"weight":0.39285714285714285},{"source":63,"target":58,"weight":0.5},{"source":64,"target":57,"weight":0.2},{"source":65,"target":64,"weight":0.3333333333333333},{"source":66,"target":15,"weight":0.25},{"source":67,"target":15,"weight":2.2},{"source":67,"target":20,"weight":0.25},{"source":68,"target":15,"weight":0.25},{"source":69,"target":22,"weight":0.6984126984126984},{"source":70,"target":9,"weight":0.14285714285714285},{"source":70,"target":22,"weight":0.3333333333333333},{"source":71,"target":14,"weight":0.3333333333333333},{"source":72,"target":71,"weight":0.3333333333333333},{"source":73,"target":3,"weight":0.2222222222222222}];

var width = 1000, height = 600;
var force = d3.layout.force().charge(-30).linkDistance(600).alpha(0.0051).size([width, height]);
var svg = d3.select("body").append("svg").attr("width", width).attr("height", height);

d3.json('http://localhost/Wikigraph/display/json/field/2.json', function(error, graph) {
	var nodes = graph.nodes, edges = graph.links;
	force.nodes(nodes).links(edges).start();

	var link = svg.selectAll(".link").data(edges).enter().append("line").attr("class", "link").style("stroke-width", 1);
	var node = svg.selectAll(".node").data(force.nodes()).enter().append("circle").attr("class", "node").attr("r", 5).style("fill", '#a30500').call(force.drag);
	force.on("end", function() {
		link.attr("x1", function(d) { return d.source.x; }).attr("y1", function(d) { return d.source.y; }).attr("x2", function(d) { return d.target.x; }).attr("y2", function(d) { return d.target.y; });
		node.attr("cx", function(d) { return d.x; }).attr("cy", function(d) { return d.y; });
	});
	d3.select('#comm_detect').on('click', function(){
		var louvainNodes = [];
		for(i in nodes) louvainNodes.push(nodes[i].index);
		var community = jLouvain().nodes(louvainNodes).edges(edges);  

		var community_assignment_result = community();
		var node_ids = Object.keys(community_assignment_result);
		var max_community_number = 0;
		node_ids.forEach(function(d){
			nodes[d].community = community_assignment_result[d];
			max_community_number = Math.max(max_community_number, community_assignment_result[d]);
		});
		var color = d3.scale.category20().domain(d3.range([0, max_community_number]));
		d3.selectAll('.node').data(nodes).style('fill', function(d){ return color(d.community);})
	});
	d3.select('#reset_btn').on('click', function(){
		d3.selectAll('.node').data(nodes).style('fill', '#a30500');
	});
});