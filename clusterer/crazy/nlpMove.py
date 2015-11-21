from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import euclidean_distances
from LemmaTokenizer import *
from dbco import *
import numpy as np, heapq, math

def genName(inText, outText, vocabValue, vocabIndex):
	freqIn = inText.mean(axis=0).A[0]
	freqOut = outText.mean(axis=0).A[0]
	freqArray = np.subtract(freqIn, freqOut)
	bestWords = {}
	fiveBestI = freqArray.argsort()[-5:][::-1]
	fiveBest = []
	for i in fiveBestI:
		fiveBest.append(vocabValue[vocabIndex.index(i)].encode('ascii', 'ignore'))
	return {'bestKeywords': fiveBest, 'freqArray': freqArray}

pageNb = 500
classesArray = []; nodes = [];
cur.execute("SELECT id FROM wg_page WHERE PR > 0.2 ORDER BY RAND() LIMIT "+str(pageNb))
numberClasses = 10
i = 0
for row in cur.fetchall():
	i += 1
	classesArray.append(math.floor(numberClasses*i/pageNb))
	nodes.append(row[0])

print len(nodes)

texts = [];
for node in nodes:
	f = open('summary/'+str(node)+'.txt', "r")
	texts.append(f.read())
	f.close()
count_vect = CountVectorizer(tokenizer=LemmaTokenizer(), ngram_range = (1,2), stop_words='english') #binary=True

totalCount = count_vect.fit_transform(texts)

tfidf_trans = TfidfTransformer()
totalCount = tfidf_trans.fit_transform(totalCount)
totalCount = totalCount.asfptype()
distances = euclidean_distances(totalCount, totalCount)

vocabValue = count_vect.vocabulary_.keys()
vocabIndex = count_vect.vocabulary_.values()


r = 0
changed = totalCount.shape[0]
while 1.0*changed > 0.01*totalCount.shape[0]:
	classSet = set(classesArray)
	classesArray = np.array(classesArray)

	classArray = []
	freqMatrix = []
	f = open('clusters.txt','w')
	for clas in classSet:
		goodIndex = np.where(classesArray==clas)[0]
		badIndex = np.where(classesArray!=clas)[0]
		goodRows = totalCount[goodIndex, :]
		badRows = totalCount[badIndex, :]

		results = genName(goodRows, badRows, vocabValue, vocabIndex)
		freqMatrix.append(results['freqArray']) #generating a very cool looking matrix
		classArray.append(clas)

		friends = []; notFriends = [];
		for x1 in range(0,distances.shape[0]-1):
			for x2 in range(x1+1,distances.shape[0]):
				thisDist = distances[x1][x2]
				if x1 in goodIndex and x2 in goodIndex:
					friends.append(thisDist)
				elif x1 in goodIndex or x2 in goodIndex:
					notFriends.append(thisDist)

		score = (np.mean(friends) / np.mean(notFriends))
		f.write(str(clas)+'[]'+str(score)+'[]'+','.join(results['bestKeywords'])+'\n')
		print clas, " | ", ','.join(results['bestKeywords']), " | Size: ", np.where(classesArray==clas)[0].size, " | Score: ", score
	f.close()
	r += 1
	changed = 0
	allResults = freqMatrix * totalCount.transpose() #I think this is as yolo as it gets: row = each cluster, column = each article's score

	cappedFreqMatrix = np.array(freqMatrix)
	cappedFreqMatrix[cappedFreqMatrix<0.05] = 0
	scores = map(int, cappedFreqMatrix.sum(axis=1))
	for articleI in range(0,totalCount.shape[0]):
		bestClas = classArray[np.argmax(allResults[:,articleI])]
		if bestClas != classesArray[articleI]:
			changed += 1
		classesArray[articleI] = bestClas
	print "------------------------------"

f = open('community.txt','w')
for node, member in zip(nodes, classesArray):
	f.write(str(node)+' '+str(member)+'\n')
f.close()