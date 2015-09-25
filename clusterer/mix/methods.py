from sklearn.metrics.pairwise import cosine_similarity
import numpy as np
from igraph import *
from dbco import *
import louvain

arpack_options.maxiter=300000;
def leadingEigenvectors(G, G2, tfidf):
	return G.community_leading_eigenvector().membership
def fastGreedy(G, G2, tfidf):
	G.to_undirected("collapse")
	G.simplify()
	return G.community_fastgreedy().as_clustering().membership
def infomap(G, G2, tfidf):
	return G.community_infomap().membership
def labelPropagation(G, G2, tfidf):
	return G.community_label_propagation().membership
def multilevel(G, G2, tfidf):
	return G.community_multilevel().membership
def edge_betweenness(G, G2, tfidf):
	return G.community_edge_betweenness().as_clustering().membership
def spinglass(G, G2, tfidf):
	return G.community_spinglass().membership
def walktrap(G, G2, tfidf):
	return G.community_walktrap().as_clustering().membership
def louvainMethod(G, G2, tfidf):
	G2 = G
	G2.to_undirected()
	return louvain.find_partition(G2, method='Modularity').membership
def backLouvainMethod(G, G2, tfidf):
	G2.to_undirected()
	return louvain.find_partition(G2, method='Modularity').membership

def genFreqArray(inText, outText):
	return np.subtract(inText.mean(axis=0).A[0], outText.mean(axis=0).A[0])

def philMethod(G, G2, tfidf):
	G.to_undirected();
	membership = louvain.find_partition(G, method='Modularity').membership;
	membership = np.array(membership)
	# membership = np.array(G.community_multilevel().membership)

	changed = tfidf.shape[0]
	while 1.0*changed > 1:
		classSet = list(set(membership))
		freqMatrix = []

		for clas in classSet:
			goodRows = np.where(membership==clas)[0]
			badRows = np.where(membership!=clas)[0]

			inRows = tfidf[goodRows, :];
			outRows = tfidf[badRows, :];
			freqArray = inRows.mean(axis=0).A[0]-outRows.mean(axis=0).A[0]
			freqMatrix.append(freqArray) #generating a very cool looking matrix

		allResults = freqMatrix * tfidf.transpose() #I think this is as yolo as it gets: row = each cluster, column = each article's score
		# allResults = cosine_similarity(freqMatrix, tfidf)

		nClassesArray = [classSet[np.argmax(allResults[:,i])] for i in range(0,tfidf.shape[0])]
		changed = np.count_nonzero(nClassesArray-membership)
		membership = np.array(nClassesArray)

	return membership

# def leadingEigenvectorsNaive(G, G2, tfidf):
# 	return G.community_leading_eigenvector_naive().membership
# def optimal_modularity(G, G2, tfidf):
# 	return G.community_optimal_modularity().membership
