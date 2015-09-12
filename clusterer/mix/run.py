from build_graph import *
from nlp_graph import *
from q_compute import *
from igraph import *; from dbco import *;
from methods import *

limit = 500
cur.execute("SELECT id, name FROM page ORDER BY PR DESC LIMIT "+str(limit)); res = cur.fetchall();
arts = [str(a[0]) for a in res];
arts.sort();
artsName = {int(a[0]): a[1] for a in res};

tfidf = buildTFIDF(arts)
simi = tdfidfSimi(tfidf)


G = build_graph(arts)

G2 = nlp_graph_KNN(arts, artsName, tfidf, simi)

methods = ['louvainMethod', 'leadingEigenvectors', 'fastGreedy', 'multilevel']
for m in methods:
	print "----------------------------------\n", m
	membership = globals()[m](G, G2)
	[mod, mod2, q] = q_compute(G, G2, membership, simi, tfidf)
	print "mod1: ",mod, ", mod2: ", mod2 ," => ", q