var width = $(document).width()-20, height = $(document).height()-20;
var alphaF = 0.005;
var smallRadius = 4, largeRadius = 5;
var force = d3.layout.force()
    .linkStrength(2).friction(0.9)
    .linkDistance(1).charge(-10)
    .gravity(0.1).theta(0.8)
    .alpha(alphaI).size([width, height]);

var svg = d3.select("body").append("svg").attr("width", width).attr("height", height);

var rectangle = svg.append("rect").attr("x", (width/2 - 125)).attr("y", (height/2 - 13)).attr("width", 250).attr("height", 26).attr('class', 'waitRectangle');
var aboveRect = svg.append("rect").attr("x", (width/2 - 125)).attr("y", (height/2 - 13)).attr("width", 1).attr("height", 26).attr('class', 'aboveRect');

var loading = svg.append("text").attr("x", width / 2).attr("y", height / 2).attr("dy", ".35em").style("text-anchor", "middle").text("Simulating. One moment please...");
d3.json(fileFrom, function(error, graph) {
  var nodes = graph.nodes.slice(), links = [], bilinks = [];

  graph.links.forEach(function(link) {
    var s = nodes[link.source],
        t = nodes[link.target],
        i = {}; // intermediate node
        nodes.push(i);
        links.push({source: s, target: i}, {source: i, target: t});
        bilinks.push([s, i, t]);
  });
  if(nodes.length < 1500) {force.charge(-15);}
  force.nodes(nodes).links(links).start();

  var link = svg.selectAll(".link").data(bilinks).enter().append("path").attr("class", "link");

    var gnodes = svg.selectAll('g.gnode').data(graph.nodes).enter().append('g').classed('gnode', true).on('mouseover', function(d){
         d3.select(this).select('circle').attr('r', largeRadius);
         document.title=d.name;
    }).on('mouseout', function(d){
         d3.select(this).select('circle').attr('r', smallRadius);
    });
      
    var node = gnodes.append("circle").attr("class", "node").attr("r", smallRadius).style("fill", function(d) { return color[(d.group-1)]; }).attr('alt', function(d) {return d.name;});

    node.append("title").text(function(d) { return d.name; });

   force.on("tick", function() {
      var alphaC = force.getAlpha();
      aboveRect.attr('width', Math.floor(250*(1-((alphaC-alphaF)/alphaI))));
   });
   var pointsSave = [];
   force.on("end", function() {
      var minX = 0, maxX = 0;
      var minY = 0, maxY = 0;
      gnodes.attr('transform', function(d) {
        minX = Math.min(minX, d.x); maxX = Math.max(maxX, d.x);
        minY = Math.min(minY, d.y); maxY = Math.max(maxY, d.y);
        pointsSave.push((d.id+'|'+Math.floor(10*d.x)/10)+'|'+(Math.floor(10*d.y)/10)+'|'+d.name+'|'+d.group);
      });
      transX = Math.abs(minX);
      transY = Math.abs(minY);
      svg.attr("width", Math.max(width, (maxX+transX))).attr("height", Math.max(height, (maxY+transY)));
      gnodes.attr("transform", function(d) { 
         return 'translate(' + [d.x+transX, d.y+transY] + ')';
      });
      link.attr("d", function(d) {
         return "M" + (d[0].x+transX) + "," + (d[0].y+transY)+ "S" + (d[1].x+transX) + "," + (d[1].y+transY)+ " " + (d[2].x+transX) + "," + (d[2].y+transY);
      });
      loading.remove();
      aboveRect.remove();
      rectangle.remove();
      if(saveGraph) {uploadAjax();}
   });
    function uploadAjax() {
      postData = {};
      postData.graphType = graphType;
      postData.cleanField = fieldId;
      postData.d = pointsSave.join('[]');
      $.ajax({type: "POST",
        url: webroot+'ajax/savePositions.php',
        data: postData,
        success: function(dat) { // alert(dat);
        }
      });
    }
});