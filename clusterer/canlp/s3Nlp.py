from sklearn.feature_extraction.text import CountVectorizer
from sklearn.metrics.pairwise import euclidean_distances, cosine_similarity
from LemmaTokenizer import *
import numpy as np

def genName(inText, outText, vocabValue, vocabIndex):
	freqIn = inText.mean(axis=0).A[0]
	freqOut = outText.mean(axis=0).A[0]
	freqArray = np.subtract(freqIn, freqOut)

	bestWords = {}
	fiveBestI = freqArray.argsort()[-5:][::-1]
	fiveBest = [vocabValue[vocabIndex.index(i)].encode('ascii', 'ignore') for i in fiveBestI]
	return {'bestKeywords': fiveBest, 'freqArray': freqArray}

def useNLP(summaryFolder):
	f = open('data/community.txt','r')
	txt = f.read()
	f.close()
	toks = txt.split('\n')

	classesArray = []; nodes = [];
	for tok in toks:
		infos = tok.split(' ')
		if len(infos) == 2:
			classesArray.append(int(infos[1]))
			nodes.append(int(infos[0]))

	texts = [];
	for node in nodes:
		f = open(summaryFolder+'/'+str(node)+'.txt', "r")
		texts.append(f.read())
		f.close()
	count_vect = CountVectorizer(tokenizer=LemmaTokenizer(), stop_words='english', ngram_range = (1,2), binary=True)
	totalCount = count_vect.fit_transform(texts)
	totalCount = totalCount.asfptype()

	distances = cosine_similarity(totalCount, totalCount)

	vocabValue = count_vect.vocabulary_.keys()
	vocabIndex = count_vect.vocabulary_.values()

	changed = totalCount.shape[0]
	classSet = []; nameDict = {}
	while 1.0*changed > 0.01*totalCount.shape[0]:
		classSet = list(set(classesArray))
		classesArray = np.array(classesArray)

		freqMatrix = []

		for clas in classSet:
			goodRows = np.where(classesArray==clas)[0]
			badRows = np.where(classesArray!=clas)[0]

			results = genName(totalCount[goodRows, :], totalCount[badRows, :], vocabValue, vocabIndex)
			nameDict[clas] = ','.join(results['bestKeywords'])
			freqMatrix.append(results['freqArray']) #generating a very cool looking matrix

		changed = 0
		allResults = freqMatrix * totalCount.transpose() #I think this is as yolo as it gets: row = each cluster, column = each article's score

		cappedFreqMatrix = np.array(freqMatrix)
		cappedFreqMatrix[cappedFreqMatrix<0.05] = 0
		for articleI in range(0,totalCount.shape[0]):
			bestClas = classSet[np.argmax(allResults[:,articleI])]
			if bestClas != classesArray[articleI]:
				changed += 1
			classesArray[articleI] = bestClas

	f = open('data/clusters.txt','w')
	for clas in classSet:
		goodRows = np.where(classesArray==clas)[0]
		friends = []; notFriends = [];
		for x1 in range(0,distances.shape[0]-1):
			for x2 in range(x1+1,distances.shape[0]):
				thisDist = distances[x1][x2]
				if x1 in goodRows and x2 in goodRows:
					friends.append(thisDist)
				elif x1 in goodRows or x2 in goodRows:
					notFriends.append(thisDist)
		score = float(int(100*(np.mean(friends) / np.mean(notFriends))))/100.0
		f.write(str(clas)+'[]'+str(score)+'[]'+nameDict[clas]+'\n')
	f.close()

	f = open('data/recommunity.txt','w')
	for node, member in zip(nodes, classesArray):
		f.write(str(node)+' '+str(member)+'\n')
	f.close()