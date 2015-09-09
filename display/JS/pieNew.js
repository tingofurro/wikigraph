cidList = []; cnameList = []; curPieLevel = 0;
function loadAllPies() {
	curPieLevel = 0;
	cidList = ($('#cidList').html()).split(',');
	cnameList = ($('#cnameList').html()).split('|');
	nextPie();
}
function nextPie() {
	if(curPieLevel < cidList.length) {
		curPieLevel ++;
		loadPie(cidList[curPieLevel-1], curPieLevel, cnameList[curPieLevel-1]);
	}
}
function loadPie(pieID, level, pieName) {
	radius = Math.floor($(window).width()/10);
	myRadius = radius*(1-0.15*(level-1));
	var arc = d3.svg.arc().outerRadius(myRadius).innerRadius(0.3*myRadius);
	pie = d3.layout.pie().sort(null).value(function(d) { return d.articles; });

	whereX = $(window).width()-radius-25;
	innerSvg = svg.append("g").attr('id', 'pie'+level).attr("transform", "translate(" + whereX + "," + (2*(level-1)+1)*(radius+10) + ")");
	innerSvg.append("text").text(pieName).attr('class', 'clickMe').on('click', function(d) {
		reloadGraph(pieID);
	});
	d3.csv(webroot+"pies/"+pieID+".csv", function(error, data) {
		data.forEach(function(d) {
			d.articles = +d.articles;
		});
		var g = innerSvg.selectAll(".arc").data(pie(data)).enter().append("g").attr('id', function(d) {return "clus"+d.data.id;});
		g.attr("class", function(d) {
			if(level < cidList.length && cidList.indexOf(d.data.id)==-1) {return 'arc invisArc';}
			else return 'arc';
		}).append("path").attr("d", arc).style("fill", function(d) {
			return str2color(+d.data.id);
		});
		g.on('click', function(d) {reloadGraph(d.data.id)});
		g.append("text").attr("transform", function(d) { return "translate(" + arc.centroid(d) + ")"; }).text(function(d) { return d.data.clus; });
		nextPie();
	});
}
function reloadGraph(id) {
	window.location=id;
}