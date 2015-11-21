from sklearn.feature_extraction.text import CountVectorizer
from sklearn.metrics.pairwise import cosine_similarity
from LemmaTokenizer import *
from dbco import *
import numpy as np
import sys

cluster = int(sys.argv[1])
cur.execute("SELECT level FROM cluster WHERE id="+str(cluster))
row = cur.fetchall()[0]; level = row[0];

cur.execute("SELECT id, cluster"+str(level+1)+" FROM page WHERE cluster"+str(level)+"="+str(cluster)+" ORDER BY cluster"+str(level+1))
pageText = []; pageId = []; clusters = [];
for row in cur.fetchall():
	f = open('../../crawler/summary/'+str(row[0])+'.txt')
	pageText.append(f.read()); f.close();
	pageId.append(row[0]);
	clusters.append(row[1]);
clusters = np.array(clusters)

cur.execute("SELECT id, name FROM cluster WHERE parent="+str(cluster)+" ORDER BY id");
clusterNames = {}; clusterList = [];
for row in cur.fetchall():
	clusterNames[row[0]] = row[1]

count_vect = CountVectorizer(tokenizer=LemmaTokenizer(), ngram_range = (1,2), binary=True)
totalCount = count_vect.fit_transform(pageText)
totalCount = totalCount.asfptype()
print "Done count vectorizing"

clusterSet = list(set(clusters))
freqMatrix = []

for c in clusterSet:
	goodIndex = np.where(clusters==c)[0]
	goodCounts = totalCount[goodIndex, :]
	freqMatrix.append(goodCounts.mean(axis=0).A[0])

freqMatrix = np.array(freqMatrix)

minDists = {};
distances = cosine_similarity(freqMatrix, freqMatrix)
print distances.shape
for c, row in zip(clusterSet, distances):
	print "-----------------------"
	print clusterNames[c], len(np.where(clusters==c)[0]), " / ", len(clusters)
	print row