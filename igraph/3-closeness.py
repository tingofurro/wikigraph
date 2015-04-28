from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity
from StringIO import StringIO
from sklearn.svm import SVC

import numpy as np
import sys, os

def bestIndeces(a,N):
	return np.argsort(a)[::-1][:N]

root = sys.argv[1]

filenames = os.listdir(root+'/igraph/txt')
texts = []; txtIds = []
for filename in filenames:
	toks = filename.split('.')
	txtIds.append(int(toks[0]))
	f = open(root+'/igraph/txt/'+ filename, "r")
	texts.append(f.read())
	f.close()
f = open(root+'/igraph/data/community.txt')
txt = f.read();
f.close();
toks = txt.split('\n');
classes = {};
classesArray = []
classNb = {};
friends = {}; notFriends = {};

for tok in toks:
	infos = tok.split(' ')
	if len(infos) == 2:
		myClass = int(infos[1])
		classes[int(infos[0])] = myClass
		classesArray.append(myClass)
		classNb[myClass] = classNb.get(myClass, 0) + 1
		friends[myClass] = []
		notFriends[myClass] = []

count_vect = CountVectorizer(stop_words='english', ngram_range=(1,3))
tfidf_trans = TfidfTransformer()
trainingTfidf = tfidf_trans.fit_transform(count_vect.fit_transform(texts))

vocabValue = count_vect.vocabulary_.keys()
vocabIndex = count_vect.vocabulary_.values()

clf = SVC(kernel = 'linear').fit(trainingTfidf, classesArray) # train classifier
predicted = clf.predict(trainingTfidf)

distances = cosine_similarity(trainingTfidf, trainingTfidf)

for x1 in range(0,len(distances[0])-1):
	for x2 in range(x1+1,len(distances[0])):
		thisDist = distances[x1][x2]
		class1 = classes[txtIds[x1]]
		class2 = classes[txtIds[x2]]
		if class1 == class2:
			friends[class1].append(thisDist)
		else:
			notFriends[class1].append(thisDist)
			notFriends[class2].append(thisDist)

for c in range(0,len(classNb)):
	print "\n-------------------------\nCluster ", c ," (size: ",classNb[c],"): ", (np.mean(friends[c]) / np.mean(notFriends[c])) ,"\n-------------------------"
	tfidfBuild = []
	for art in range(0,len(classes)):
		if classes[txtIds[art]] == c:
			tfidfBuild.append(trainingTfidf[art].toarray())
	meanTfidf = np.mean(tfidfBuild, axis=0)
	meanTfidf = 100*meanTfidf[0]
	bestIndeces = bestIndeces(meanTfidf, 10)
	for index in bestIndeces:
		print "[",index,"] ", meanTfidf[index], ": ", vocabValue[vocabIndex.index(index)].encode('utf-8')