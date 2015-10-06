from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity
from LemmaTokenizer import *
from dbco import *
from timing import *
from myLouvain import *
from myLouvain2 import *

def build_graph(limit):
	cur.execute("SELECT id FROM page ORDER BY betweenness DESC LIMIT "+str(limit))
	arts = [str(r[0]) for r in cur.fetchall()]
	G = Graph()
	G.add_vertices(len(arts))
	findIndex = {}
	for i, a in zip(range(0,len(arts)), arts):
		findIndex[int(a)] = i;

	cur.execute("SELECT `from`, `to` FROM link WHERE `to` IN ("+','.join(arts)+") AND `from` IN ("+','.join(arts)+")")
	edges = [(findIndex[int(r[0])], findIndex[int(r[1])]) for r in cur.fetchall()];
	G.add_edges(edges)
	print "Built edge Graph"
	return G, arts

def buildNLP(nodes, totalEdges):
	texts = []
	for node in nodes:
		f = open('../../crawler/summary/'+str(node)+'.txt', "r"); texts.append(f.read()); f.close();
	count = CountVectorizer(tokenizer=LemmaTokenizer(), stop_words='english', binary=True).fit_transform(texts)
	tfidf = TfidfTransformer().fit_transform(count).asfptype()
	simi = cosine_similarity(tfidf)
	k = (totalEdges/len(nodes)); G = Graph(); G.to_directed()
	G.add_vertices(len(nodes)); edges = []	
	for i in range(0,len(simi)):
		r = simi[i];
		neighbors = r.argsort()[-(k+1):-1]
		edges.extend([(i, n) for n in neighbors])
	G.add_edges(edges)
	print "Built KNN"
	return G


# G, nodes = build_graph(10000)
# G2 = buildNLP(nodes, G.ecount())

# n = 5
# minSize = int(len(nodes)/(n+2))
# pL = constrainedLouvain(G, minSize)
# print "Simple Louvain: ", ((G.modularity(pL)+ G2.modularity(pL))/2), " : ", len(set(pL)), " clusters"
# pL2 = constrainedLouvain2(G, G2, minSize)
# print "Double Louvain: ", ((G.modularity(pL2)+ G2.modularity(pL2))/2), " : ", len(set(pL2)), " clusters"