from sklearn.feature_extraction.text import CountVectorizer
from sklearn.metrics.pairwise import cosine_similarity, euclidean_distances
from LemmaTokenizer import *
from dbco import *
import numpy as np
import sys

level = 2

cur.execute("SELECT id, cluster"+str(level)+" FROM page WHERE cluster2!=0 ORDER BY cluster"+str(level))
pageText = []; pageId = []; cluster = [];
for row in cur.fetchall():
	f = open('../../crawler/summary/'+str(row[0])+'.txt')
	pageText.append(f.read()); f.close();
	pageId.append(row[0]);
	cluster.append(row[1]);

cur.execute("SELECT id, name FROM cluster WHERE level="+str(level)+" ORDER BY id")
clusterNames = {};
clusterList = [];
for row in cur.fetchall():
	clusterNames[row[0]] = row[1]
	clusterList.append(row[0])

print "Loaded it all"

count_vect = CountVectorizer(tokenizer=LemmaTokenizer(), ngram_range = (1,2), binary=True)
totalCount = count_vect.fit_transform(pageText)
totalCount = totalCount.asfptype()
print "Done count vectorizing"

clusterSet = list(set(cluster))
cluster = np.array(cluster)
freqMatrix = []

for c in clusterSet:
	goodIndex = np.where(cluster==c)[0]
	goodCounts = totalCount[goodIndex, :]
	freqMatrix.append(goodCounts.mean(axis=0).A[0])

freqMatrix = np.array(freqMatrix)

print "Before cosine"

minDists = {};
distances = euclidean_distances(freqMatrix, freqMatrix)
print len(clusterList)
print distances.shape

for x in range(0,len(distances)):
	mindist = 120; minIndex = -1;
	print "--------------------------------------"
	for y in range(0,len(distances[0])):
		if cluster[x] != cluster[y]:
			if distances[x][y] < mindist:
				mindist = distances[x][y]
				print y, "=> ", mindist
				minIndex = y;
	minDists[x] = minIndex;

for c in clusterSet:
	print c
	print clusterNames[c], "=> ", clusterNames[clusterList[minDists[c]]]