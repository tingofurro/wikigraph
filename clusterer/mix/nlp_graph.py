from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity, euclidean_distances
import numpy as np
from igraph import *

def buildTFIDF(arts):
	texts = [open('../../crawler/summary/'+a+'.txt', "r").read() for a in arts];
	count = CountVectorizer(stop_words='english', binary=True, ngram_range=(1,2)).fit_transform(texts)
	return TfidfTransformer().fit_transform(count).asfptype()

def tdfidfSimi(tfidf):
	return cosine_similarity(tfidf)

def nlp_graph_KNN(arts, artsName, tfidf, simi):
	k = 10
	G = Graph()
	G.to_directed()
	G.add_vertices(len(arts))
	edges = []	
	for i in range(0,len(simi)):
		r = simi[i];
		neighbors = r.argsort()[-(k+1):-1][::-1]
		edges.extend([(i, n) for n in neighbors if (n,i) not in edges])
	G.add_edges(edges)
	return G

def nlp_graph_eps(arts, artsName, tfidf, simi):
	G = Graph()
	G.to_directed()
	G.add_vertices(len(arts))
	edges = []	
	for i in range(0,len(simi)):
		r = simi[i];
		neighbors = r.argsort()[-(k+1):-1][::-1]
		toAdd = [(i, n) for n in neighbors if (n,i) not in edges]
		edges.extend(toAdd)
	G.add_edges(edges)
	print G.has_multiple()
	return G, simi, tfidf