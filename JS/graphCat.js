var width = $(document).width()-20, height = $(document).height()-20;

var force = d3.layout.force().linkDistance(10).linkStrength(2).size([width, height]);

var svg = d3.select("body").append("svg").attr("width", width).attr("height", height);

var loading = svg.append("text").attr("x", width / 2).attr("y", height / 2).attr("dy", ".35em").style("text-anchor", "middle").text("Simulating. One moment please…");

d3.json(webroot+"json/catGraph.json", function(error, graph) {
  var nodes = graph.nodes.slice(), links = [], bilinks = [];

  graph.links.forEach(function(link) {
    var s = nodes[link.source],
        t = nodes[link.target],
        i = {}; // intermediate node
    nodes.push(i);
    links.push({source: s, target: i}, {source: i, target: t});
    bilinks.push([s, i, t]);
  });

  force.nodes(nodes).links(links).start();

  var link = svg.selectAll(".link").data(bilinks).enter().append("path").attr("class", "link");

    var gnodes = svg.selectAll('g.gnode').data(graph.nodes).enter().append('g').classed('gnode', true).on('mouseover', function(d){
         d3.select(this).select('circle').attr('r', 7);
         d3.select(this).select("text").style({opacity:'1.0'});
         document.title=d.name;
    }).on('mouseout', function(d){
         d3.select(this).select('circle').attr('r', 5);
         d3.select(this).select("text").style({opacity:'0'});
    });
      
    var node = gnodes.append("circle").attr("class", "node").attr("r", 5).style("fill", function(d) { return color[(d.group-1)]; });

    node.append("title").text(function(d) { return d.name; })
    labels = gnodes.append("text").text(function(d) { return d.name; }).attr('class', 'textAbove').attr('y', '-10px');

   force.on("tick", function() {
      console.log('Alpha'+force.getAlpha());
   });
   force.on("end", function() {
      link.attr("d", function(d) {
         return "M" + d[0].x + "," + d[0].y+ "S" + d[1].x + "," + d[1].y+ " " + d[2].x + "," + d[2].y;
      });
      gnodes.attr("transform", function(d) { 
         return 'translate(' + [d.x, d.y] + ')';
      });
      loading.remove();
   });
});