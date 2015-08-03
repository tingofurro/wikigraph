width = $(window).width();
height = $(window).height();

frameWidth = width/2;
frameHeight = height;
radius = Math.min(0.8*frameWidth, 0.7*frameHeight) / 2;

var svg = d3.select("body").append("svg").attr("width", width).attr("height", height);

var color = d3.scale.category20();

function loadPie(pieID, level) {
	myRadius = radius*(1-0.15*(level-1));
	$('#pie'+level).remove();
	svg.attr("width", level*frameWidth);
	var arc = d3.svg.arc().outerRadius(myRadius).innerRadius(0.3*myRadius);
	pie = d3.layout.pie().sort(null).value(function(d) { return d.articles; });

	innerSvg = svg.append("g").attr('id', 'pie'+level).attr("transform", "translate(" + (level-0.5)*frameWidth + "," + frameHeight / 2 + ")");

	d3.csv(webroot+"pies/"+pieID+".csv", function(error, data) {
		data.forEach(function(d) {
			d.articles = +d.articles;
		});
		var g = innerSvg.selectAll(".arc").data(pie(data)).enter().append("g").attr('id', function(d) {return "clus"+d.data.id;}).on('click', function(d) {
			loadPie(d.data.id, level+1);
			d3.selectAll('.activeArc').attr('class', '');
			d3.select(this).select('path').attr('class', 'activeArc');
		});
		g.attr("class", "arc").append("path").attr("d", arc).style("fill", function(d) {
			return color(d.data.clus);
		});
		g.append("text").attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")"; }).text(function(d) { return d.data.clus; });
	});	
}
loadPie(0, 1);