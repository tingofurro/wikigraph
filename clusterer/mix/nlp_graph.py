from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity, euclidean_distances
import numpy as np
from igraph import *
from LemmaTokenizer import *

def buildTFIDF(arts):
	texts = [open('../../crawler/summary/'+a+'.txt', "r").read() for a in arts];
	count = CountVectorizer(tokenizer=LemmaTokenizer(), stop_words='english', binary=True).fit_transform(texts) #, ngram_range=(1,2)
	return TfidfTransformer().fit_transform(count).asfptype()

def tdfidfSimi(tfidf):
	return cosine_similarity(tfidf)

def nlp_graph_KNN(arts, artsName, tfidf, simi):
	k = 27
	G = Graph()
	G.to_directed()
	G.add_vertices(len(arts))
	edges = []	
	for i in range(0,len(simi)):
		r = simi[i];
		neighbors = r.argsort()[-(k+1):-1]
		edges.extend([(i, n) for n in neighbors])
	G.add_edges(edges)
	return G

def nlp_graph_eps(arts, artsName, tfidf, simi):
	eps = np.percentile(simi, 85)
	G = Graph(); G.to_directed()
	G.add_vertices(len(arts))
	edges = []
	for i in range(0,len(simi)):
		r = simi[i];
		toAdd = [(i, u) for u in range(0,len(simi)) if not (i==u) and r[u] >= eps];
		edges.extend(toAdd)
	G.add_edges(edges)
	return G