maxLvl = 3;
windowHeight = 0, windowWidth = 0;
tree = {};
$(document).ready(function() {
	windowHeight = screen.height-50;
	windowWidth = screen.width-17;
	svg = d3.select("body").append("svg").attr('id', 'svg1').attr("width", windowWidth).attr("height", windowHeight);
	// loadFirstGraph();
	restart();
});
function restart() {
	loadTopics();
	colorObject = [];
}
function loadPie(lvl, maxLvl) {
	myArray = [];
	fetchLevel(myArray, tree.children, 1, lvl, 1);
	myRadius = radius(lvl); innerRadius = radius(lvl-1); textRadius = radius(lvl);
	if(lvl == maxLvl) {myRadius = Math.max($(window).width(), $(window).height()); textRadius = radius(lvl)+10;}

	var arc = d3.svg.arc().outerRadius(myRadius).innerRadius(innerRadius);
	var textArc = d3.svg.arc().outerRadius(textRadius).innerRadius(innerRadius);
	pie = d3.layout.pie().sort(null).value(function(d) { return d.weight; });
	innerSvg = svg.append("g").attr('id', 'pie'+lvl).attr("transform", "translate(" + windowWidth/2 + "," + windowHeight/2 + ")");
	var g = innerSvg.selectAll(".arc").data(pie(myArray)).enter().append("g");
	g.on('click', function(d) {openGraph(d.data.id);});
	g.attr("class", 'arc').append("path").attr("d", arc).style("fill", function(d) {return d.data.col;});

	g.append("text").attr("transform", function(d) {
		myAngle = Math.floor(180*(d.startAngle+d.endAngle)/(2*3.1415));
		if(myAngle < 180) myAngle -= 90;
		else myAngle += 90;
		return "translate(" + textArc.centroid(d) + ") rotate("+myAngle+")"; 
	}).attr("dy", ".35em").style("text-anchor", "middle").text(function(d) { return d.data.name.substr(0,24).replace('_', ' '); });
}
function fetchLevel(myArray, treeObj, level, targetLevel, currentWeight) {
	if(level == targetLevel) {
		for(u in treeObj) {
			treeObj[u].weight = currentWeight/treeObj.length;
			myArray.push(treeObj[u]);
		}
	}
	else {
		for(u in treeObj) {
			if(treeObj[u].children) {
				fetchLevel(myArray, treeObj[u].children, level+1, targetLevel, treeObj[u].weight);
			}
			else {
				fetchLevel(myArray, [{'col': treeObj[u].col, 'weight': treeObj[u].weight, 'name': ''}], level+1, targetLevel, treeObj[u].weight);
			}
		}
	}
}
function radius(lvl) {
	return 0.3*Math.min($(window).width(), (screen.height-50))*lvl;
}
function loadTopics() {
	alert(webroot+ "tree.json");
	$.getJSON(webroot+ "tree.json", function( json ) {
		tree = json;
		console.log(json)
		setColor(tree.children, colors);
		for(var i = 1; i <= 3; i ++) loadPie(i, maxLvl);
	});
}
function setColor(children, colors) {
	for(c in children) {
		if(colors[c]) children[c].col = colors[c].c;
		else children[c].col = 'black';
		if(children[c].children && colors[c]) setColor(children[c].children, colors[c].sub);
	}
}
function openGraph(graphId) {
	$.getJSON(webroot+'loadGraph/'+graphId, function(json) {
		plotGraph(json.graph, (json.preloaded==1)?false:true, json.toFile)
	});
	$('html, body').animate({scrollTop:screen.height}, 500);
}