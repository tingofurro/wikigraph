var width = 380; height = $(window).height(), duration = 750, root = null;
var tree = d3.layout.tree().size([height, width]);
var diagonal = d3.svg.diagonal().projection(function(d) { return [d.y, d.x]; });

var latestOpen = null;

var svg = d3.select("body").append("svg").attr('id', 'tree').attr("width", width).attr("height", height);
d3.json($('#treeLocation').html(), function(error, data) {
    $('#graph').width($(document).width()-width-1);
    root = data; root.x0 = height / 2; root.y0 = 0;
    function collapse(d) {
        if (d.children) {
            d._children = d.children; d._children.forEach(collapse); d.children = null;
        }
    }
    root.children.forEach(collapse);
    update(root);
});

function update(source) {
    var nodes = tree.nodes(root).reverse(), links = tree.links(nodes);
    nodes.forEach(function(d) { d.y = (d.depth+1) * 120-20; });

    var node = svg.selectAll("g.node").data(nodes, function(d) { return d.id; });

    var nodeEnter = node.enter().append("g").attr("class", "node").attr("transform", function(d) { return "translate(" + source.y0 + "," + source.x0 + ")"; })
            .on("click", click);

    nodeEnter.append("circle").attr("r", 1e-6)
    nodeEnter.append("text").attr("x", -10).attr("dy", ".35em")
    .attr("text-anchor", "end").text(function(d) { return d.name; })
    .attr('fill', function(d) {return str2color(d.id); });

    var nodeUpdate = node.transition().duration(duration).attr("transform", function(d) { return "translate(" + d.y + "," + d.x + ")"; });

    nodeUpdate.select("circle").attr("r", 4).style("fill", function(d) { return str2color(d.id); });
    nodeUpdate.select("text").style("fill-opacity", 1);

    var nodeExit = node.exit().transition().duration(duration).attr("transform", function(d) { return "translate(" + source.y + "," + source.x + ")"; }).remove();
    nodeExit.select("circle").attr("r", 1e-6);
    nodeExit.select("text").style("fill-opacity", 0);

    var link = svg.selectAll("path.link").data(links, function(d) { return d.target.id; });

    link.enter().insert("path", "g").attr("class", "link").attr("d", function(d) {var o = {x: source.x0, y: source.y0};return diagonal({source: o, target: o});});
    link.transition().duration(duration).attr("d", diagonal);
    link.exit().transition().duration(duration).attr("d", function(d) {var o = {x: source.x, y: source.y};return diagonal({source: o, target: o});}).remove();

    nodes.forEach(function(d) {d.x0 = d.x; d.y0 = d.y;});
}

function click(d) {
    if(latestOpen && d.id == latestOpen.id) {
        if (d.children) {d._children = d.children; d.children = null;}
        $('#graph').attr('src', $('#webroot').html()+$('#dbPrefix').html()+'/'+d.parent.id);
        latestOpen = d.parent;
        update(d);
        return true;
    }
    else if(latestOpen && d.level == latestOpen.level) {
        latestOpen._children = latestOpen.children; latestOpen.children = null;
    }
    if (d.children) {d._children = d.children; d.children = null;}
    else {d.children = d._children; d._children = null;}
    $('#graph').attr('src', $('#webroot').html()+$('#dbPrefix').html()+'/'+d.id);
    latestOpen = d;
    update(d);
}
