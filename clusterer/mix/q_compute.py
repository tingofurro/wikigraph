from sklearn.metrics.pairwise import cosine_similarity
from collections import Counter
import numpy as np

def q_compute(G, G2, membership, simi, tfidf):
	membership = np.array(membership)
	classSet = list(set(membership))
	if len(classSet) == 1:
		return [0,0,0]
	avgScore = [];
	counter = Counter(membership)
	mod = G.modularity(membership)
	mod2 = G2.modularity(membership)
	accuracy = freqArrays(membership, tfidf)
	return [mod, mod2, accuracy, 100*mod*mod2*accuracy]
def freqArrays(membership, tfidf):
	freqMatrix = []
	classSet = list(set(membership))
	for c in classSet:
		goodRows = np.where(membership==c)[0];
		badRows = np.where(membership!=c)[0];
		centroid = np.subtract(tfidf[goodRows, :].mean(axis=0).A[0], tfidf[badRows, :].mean(axis=0).A[0])
		freqMatrix.append(centroid)

	allResults = freqMatrix * tfidf.transpose() #I think this is as yolo as it gets: row = each cluster, column = each article's score
	nMemberships = [classSet[np.argmax(allResults[:,i])] for i in range(0,tfidf.shape[0])]
	differences = np.subtract(membership, nMemberships)
	return np.where(differences==0)[0].size/float(len(membership))
def centroids(membership, tfidf):
	centroids = []
	classSet = list(set(membership))
	for c in classSet:
		goodRows = np.where(membership==c)[0];
		centroids.append(tfidf[goodRows, :].mean(axis=0).A[0])
	centroids = np.array(centroids)
	allResults = cosine_similarity(tfidf, centroids)
	nMemberships = [classSet[np.argmax(allResults[i,:])] for i in range(0,tfidf.shape[0])]
	differences = np.subtract(membership, nMemberships)
	print "Errors: ", np.where(differences!=0)[0].size, " / ", len(membership)