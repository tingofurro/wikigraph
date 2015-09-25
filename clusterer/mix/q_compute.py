from sklearn.metrics.pairwise import cosine_similarity
from sklearn.metrics import silhouette_score
from collections import Counter
import numpy as np

def q_compute(G1, G2, G3, membership, simi, tfidf):
	membership = np.array(membership)
	classSet = list(set(membership))
	if len(classSet) == 1:
		return [0,0,0]
	avgScore = [];
	counter = Counter(membership)
	mod1 = int(100*G1.modularity(membership))/100.0
	mod2 = int(100*G2.modularity(membership))/100.0
	mod3 = int(100*G3.modularity(membership))/100.0
	# silhouette = silhouette_score(tfidf, membership, metric='cosine')
	return [mod1, mod2, mod3, (2*mod1+mod2+mod3)/4.0]

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