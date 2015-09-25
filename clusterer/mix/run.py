from build_graph import *
from nlp_graph import *
from q_compute import *
from igraph import *; from dbco import *;
from methods import *

limit = 3000
cur.execute("SELECT id, name FROM page ORDER BY betweenness DESC LIMIT "+str(limit)); res = cur.fetchall();
arts = [str(a[0]) for a in res];
arts.sort();
artsName = {int(a[0]): a[1] for a in res};

tfidf = buildTFIDF(arts)
simi = tdfidfSimi(tfidf)


G = build_graph(arts)
print "Loaded G1"
G2 = nlp_graph_KNN(arts, artsName, tfidf, simi)
print "Loaded G2"
G3 = nlp_graph_eps(arts, artsName, tfidf, simi)
print "Loaded G3"

methods = ['louvainMethod', 'leadingEigenvectors', 'fastGreedy', 'multilevel', 'walktrap', 'philMethod'] # spinglass
methods = ['louvainMethod', 'backLouvainMethod', 'philMethod'] # spinglass
for m in methods:
	print "----------------------------------\n", m
	membership = globals()[m](G, G2, tfidf)
	[mod, mod2, mod3, q] = q_compute(G, G2, G3, membership, simi, tfidf)
	print "mod1: ",mod, ", mod2: ", mod2 ,", mod3: ", mod3 ," => ", q