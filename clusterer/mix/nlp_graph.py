from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity, euclidean_distances
import numpy as np
from igraph import *

def nlp_graph(arts):
	texts = [open('../../crawler/summary/'+a+'.txt', "r").read() for a in arts];
	count = CountVectorizer(stop_words='english', binary=True).fit_transform(texts)
	tfidf = TfidfTransformer().fit_transform(count).asfptype()
	simi = cosine_similarity(tfidf)
	B = simi
	treshold = np.percentile(B, 95)
	B[B<treshold] = 0
	B[B>treshold] = 1
	np.savetxt('nlpadja.json', B, '%d')
	G = Graph.Load('nlpadja.json', "adjacency")
	return G, simi, tfidf