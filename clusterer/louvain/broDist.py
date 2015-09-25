from sklearn.feature_extraction.text import CountVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from LemmaTokenizer import *
from dbco import *
from s1Build import *
import numpy as np
import sys

cluster = int(sys.argv[1]); level = 0;
if cluster > 0:
	cur.execute("SELECT level FROM cluster WHERE id="+str(cluster))
	row = cur.fetchall()[0]; level = row[0];
	cur.execute("SELECT id, cluster"+str(level+1)+" FROM page WHERE cluster"+str(level)+"="+str(cluster)+" ORDER BY cluster"+str(level+1))
else:
	cur.execute("SELECT id, cluster1 FROM page WHERE cluster1!=0")
	
pageText = []; pageId = []; clusters = [];
for row in cur.fetchall():
	f = open('../../crawler/summary/'+str(row[0])+'.txt'); pageText.append(f.read()); f.close();
	pageId.append(row[0]); clusters.append(row[1]);

G, blabl = createGraph(level, cluster, '')

clusters = np.array(clusters)

cur.execute("SELECT id, name FROM cluster WHERE parent="+str(cluster)+" ORDER BY id");
names = {r[0]: r[1] for r in cur.fetchall()}

count_vect = CountVectorizer(tokenizer=LemmaTokenizer(), binary=True)
totalCount = count_vect.fit_transform(pageText)
totalCount = totalCount.asfptype()
print "Done count vectorizing"
print "Initial modularity: ", G.modularity(clusters)

clusterSet = list(set(clusters))
bestFriends = [1,2,2];
while (len(clusterSet) > 6 and len(bestFriends) > 0):
	print clusterSet
	freqMatrix = []

	for c in clusterSet:
		goodIndex = np.where(clusters==c)[0]
		goodCounts = totalCount[goodIndex, :]
		freqMatrix.append(goodCounts.mean(axis=0).A[0])

	freqMatrix = np.array(freqMatrix)

	minDists = {};
	distances = cosine_similarity(freqMatrix, freqMatrix)
	K = 1
	neighbors = {}

	for c, row in zip(clusterSet, distances):
		kNeighbors = row.argsort()[-(K+1):-1][::-1]
		for n in kNeighbors:
			neighbors[(c, clusterSet[n])] = row[n]

	bestFriends = []
	for n in neighbors:
		if n[0] < n[1] and (n[1], n[0]) in neighbors and (len(np.where(clusters==n[0])[0]) < len(clusters)/6.0 or len(np.where(clusters==n[1])[0]) < len(clusters)/6.0):
			bestFriends.append(n)

	for weChange in bestFriends:
		s1 = len(np.where(clusters==weChange[0])[0]); s2 = len(np.where(clusters==weChange[1])[0])

		toKeep = weChange[0]; toChange = weChange[1]
		if s2 > s1:
			toKeep = weChange[1]; toChange = weChange[0]
		clusters[np.where(clusters==toChange)] = toKeep
		print 'Merged: ', names[weChange[0]], s1," to ", names[weChange[1]], s2
	print "Modularity: ", G.modularity(clusters)
	clusterSet = list(set(clusters))

print "Exited with: ", len(clusterSet), "clusters"