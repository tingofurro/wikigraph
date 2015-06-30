<?php
set_time_limit(180);
include_once('dbco.php');
include_once('mainFunc.php');
include_once('createJsonGraph.php');
$realRoot = getRealRoot();

?>
<!DOCTYPE html>
<html>
	<head>
		<title>What is Wikigraph?</title>
		<link rel="stylesheet" type="text/css" href="<?php echo $realRoot; ?>css/whatisthis.css">
	</head>
	<?php include_once('header.php'); ?>
	<body>
		<div id="faq">
			<h1>Wikiwhat?</h1>
			<p>
				Here are some of the questions Wikigraph tries to answer:<br />
				<div class="question">"Given a set of webpages, can you determine the inherent structure?"<br /></div>
				<div class="question">"Is it possible to automate course syllabi generation from the structure of the subject on Wikipedia?"<br /></div>
				<div class="question">"Can you represent the Wikipedia graph and clusters in a meaningful way?"</div>
			</p>

			<h1>Technicalities</h1>
			<p>
				<b>How is the data obtained?</b><br />
				<p>It is directly grabbed on Wikipedia. Latest crawl was in 05/2015. Therefore this is not the most up to date data.</p>
				<b>How are the clusters obtained?</b><br />
				<p>
					<li> Links (edges) between the pages (nodes), to generate a big graph. We run a community detection algorithm on graphs (we use Spinglass) to build "communities".</li>
					<li>Natural Language Processing (NLP), to refine the results, name the clusters, and assess of the "quality of the clusters".</li>
					We might write a paper on the recursive method we built.
				</p>
				<b>Can this be applied just to Mathematics?</b>
				<p>
					Absolutely not. This was built to be applied to any graph of webpages.<br />
					We chose Mathematics because to see what Wikipedia would say natural Mathematics should be.
				</p>
				<b>What's the dataset like?</b>
				<p>
					There is about 32.000 mathematics pages and represent about 500MB of raw HTML.<br />
				</p>
				<p>
					<b>What do the graph represent?</b>
					Each node is Wikipedia page and it is colored with what cluster it is in.<br />
					As this is a multi-level clustering, you can click on a cluster and "zoom-in" just that cluster.<br />
					For sanity of the browser, only the 800 most central nodes are displayed at each layer. (centrality is determined using pagerank)
				</p>
				<b>I like this, can I see the code?</b>
				<p>
					Of course, here's the <a href="https://github.com/tingofurro/wikigraph">Github link.</a><br />
					It contains the main algorithms (in Python) and the visualization (PHP, JavaScript, HTML).<br />
					The visualization is done using D3.
				</p>
			</p>
			<h1>The Team</h1>
			<p>
				This project is a Senior Design project at the Georgia Institute of Technology.<br />
				The supervisor is <a href="http://people.math.gatech.edu/~harrell/" target="_new">Professor Harrell</a>. The student is Philippe Laban (plaban3@gatech.edu). Feel free to contact us with questions!<br />
				Special thanks to the school of Mathematics of Georgia Tech for letting host the display of the project on GT servers!
			</p>
		</div>
	</body>
</html>