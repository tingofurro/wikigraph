from build_graph import *
from build_similarities import *
from nlp_graph import *
from q_compute import *
from igraph import *
from dbco import *
from methods import *
from girvan_newman import *

limit = 1000
cur.execute("SELECT id, name FROM page ORDER BY PR DESC LIMIT "+str(limit)); res = cur.fetchall();
arts = [str(a[0]) for a in res]; artsName = [a[1] for a in res];

fileName = 'graph.json'; build_graph(arts, fileName)

G = Graph.Load(fileName, 'ncol')
G2, simi, tfidf = nlp_graph(arts)

methods = ['louvainMethod', 'leadingEigenvectors', 'fastGreedy', 'multilevel', 'spinglass', 'walktrap']
for m in methods:
	print "----------------------------------\n", m
	membership = globals()[m](G, [])
	[mod, mod2, accuracy, q] = q_compute(G, G2, membership, simi, tfidf)
	print "100*",mod, "*", mod2,"*", accuracy, " => ", q