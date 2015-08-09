width = $(window).width();
height = $(window).height();

frameWidth = width/2;
frameHeight = height;
radius = Math.min(0.8*frameWidth, 0.8*frameHeight) / 2;
stillCenter = true;

var svg = d3.select("body").append("svg").attr("width", width).attr("height", height);

var color = d3.scale.category20();

function loadPie(pieID, level, pieName) {
	myRadius = radius*(1-0.15*(level-1));
	$('#pie'+level).remove();
	size = 0;
	for(var i = 1; i <= level; i ++) size += frameWidth*(1-0.15*(i-1));
	size = Math.max(size, width);
	svg.attr("width", size);
	var arc = d3.svg.arc().outerRadius(myRadius).innerRadius(0.3*myRadius);
	pie = d3.layout.pie().sort(null).value(function(d) { return d.articles; });

	if(level == 2) {
		d3.select('#pie1').transition().duration(400).attr("transform", "translate(" + calcLeft(1)*frameWidth + "," + frameHeight / 2 + ")");
	}
	whereX = calcLeft(level)*frameWidth;
	if(level == 1) whereX = width/2;
	innerSvg = svg.append("g").attr('id', 'pie'+level).attr("transform", "translate(" + whereX + "," + frameHeight / 2 + ")");
	innerSvg.append("text").text("Click to see graph of").attr('class', 'clickMe').attr("transform", "translate(0,-10)").on('click', function(d) {
		openGraph(pieID);
	});
	innerSvg.append("text").text(pieName).attr('class', 'clickMe').attr("transform", "translate(0,7)").on('click', function(d) {
		openGraph(pieID);
	});
	d3.csv(webroot+"pies/"+pieID+".csv", function(error, data) {
		data.forEach(function(d) {
			d.articles = +d.articles;
		});
		var g = innerSvg.selectAll(".arc").data(pie(data)).enter().append("g").attr('id', function(d) {return "clus"+d.data.id;}).on('click', function(d) {
			loadPie(d.data.id, level+1, d.data.clus);
			d3.selectAll('#pie'+level+' path').attr('class', 'invisArc');
			d3.select(this).select('path').attr('class', 'activeArc');
		});
		g.attr("class", "arc").append("path").attr("d", arc).style("fill", function(d) {
			return color(d.data.clus);
		});
		g.append("text").attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")"; }).text(function(d) { return d.data.clus; });
	});	
}
function openGraph(pieID) {
	window.location=prettyroot+'graph/'+pieID;
}
loadPie(0, 1, "All Mathematics");
function calcLeft(level) {
	left = 1.2*radius/frameWidth; i = level;
	while(i > 1) {left += (1-0.15*(i-1)); i --;}
	return left;
}