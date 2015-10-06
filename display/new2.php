<!DOCTYPE html>
<meta charset="utf-8">
<title>Force-Directed Graph</title>
<style>
    .node {
        cursor: pointer;
        stroke: #3182bd;
        stroke-width: 1.5px;
    }

    .link {
        fill: none;
        stroke: #9ecae1;
        stroke-width: 1.5px;
    }
</style>
<body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/d3/3.5.5/d3.min.js"></script>
<script>

var width = 960, height = 500, root;

var force = d3.layout.force()
    .size([width, height])
    .on("end", tick);

var svg = d3.select("body").append("svg").attr("width", width).attr("height", height);

var link = svg.selectAll(".link"),
    node = svg.selectAll(".node");

d3.json("<?php echo getRealRoot(); ?>tree.json", function(json) {
    root = json;
    update();
});

function update() {
    var nodes = flatten(root), links = d3.layout.tree().links(nodes);

    force.nodes(nodes).links(links).start();

    link = link.data(links, function(d) { return d.target.id; });
    link.exit().remove();
    link.enter().insert("line", ".node")
        .attr("class", "link")
        .attr("x1", function(d) { return d.source.x; }).attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; }).attr("y2", function(d) { return d.target.y; });

    node = node.data(nodes, function(d) { return d.id; }).style("fill", color);
    node.exit().remove();
    node.enter().append("circle").attr("class", "node")
    .attr("cx", function(d) { return d.x; }).attr("cy", function(d) { return d.y; })
    .attr("r", 4.5).style("fill", color)
    .append("title").text(function(d) {return d.name});
}

function tick() {
    link.attr("x1", function(d) { return d.source.x; })
        .attr("y1", function(d) { return d.source.y; })
        .attr("x2", function(d) { return d.target.x; })
        .attr("y2", function(d) { return d.target.y; });

    node.attr("cx", function(d) { return d.x; })
        .attr("cy", function(d) { return d.y; });
}

// Color leaf nodes orange, and packages white or blue.
function color(d) {
    return d._children ? "#3182bd" : d.children ? "#c6dbef" : "#fd8d3c";
}
function flatten(root) {
    var nodes = [], i = 0;

    function recurse(node) {
    if (node.children) node.children.forEach(recurse);
    if (!node.id) node.id = ++i;
    nodes.push(node);
    }
    recurse(root);
    return nodes;
}
</script>