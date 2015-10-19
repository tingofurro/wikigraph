from dbco import *
from igraph import *
from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity
from LemmaTokenizer import *
import numpy as np
from loadKNN import *

def createGraph(level, cluster, db_prefix):
	nodes = []; where = '';
	if(level > 0):
		where = ' AND cluster'+str(level)+'='+str(cluster)

	cur.execute("SELECT id FROM "+db_prefix+"page WHERE badPage=0 "+where+" ORDER BY id");
	dbFetch = cur.fetchall()
	nodes = [str(row[0]) for row in dbFetch];
	nodesDict = {}
	for i, n in zip(range(0,len(nodes)), nodes):
		nodesDict[n] = i

	cur.execute("SELECT `from`, `to` FROM "+db_prefix+"link WHERE (`to` IN ("+','.join(nodes)+") AND `from` IN ("+','.join(nodes)+")) ORDER BY id")
	edges = [(nodesDict[str(r[0])], nodesDict[str(r[1])]) for r in cur.fetchall()];

	G = Graph()
	G.add_vertices(len(nodes));
	G.add_edges(edges)
	print "Built graph for community detection"

	return G, nodes

def buildTFIDF(arts):
	texts = [open('../../crawler/summary/'+a+'.txt', "r").read() for a in arts];
	count = CountVectorizer(tokenizer=LemmaTokenizer(), stop_words='english', binary=True).fit_transform(texts) #, ngram_range=(1,2)
	return TfidfTransformer().fit_transform(count)

def tdfidfSimi(tfidf):
	return (tfidf * tfidf.T).A

def createGraphNLP(arts, totalEdges):
	G = Graph(); G.to_directed(); G.add_vertices(len(arts))
	k = (totalEdges/len(arts));
	edges = []
	if len(arts) < 5000:
		tfidf = buildTFIDF(arts)
		simi = tdfidfSimi(tfidf)
		for i in range(0,len(simi)):
			r = simi[i];
			neighbors = r.argsort()[-(k+1):-1]
			edges.extend([(i, n) for n in neighbors])
	else: # the graph is too big, we just use the cache, it might be imperfect though
		edg = loadKNN(arts, k)
		for i in edg:
			edges.extend([(i, j) for j in edg[i]])
	G.add_edges(edges)
	print "Built NLP Graph"
	return G