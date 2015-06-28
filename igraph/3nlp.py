from sklearn.feature_extraction.text import CountVectorizer

from LemmaTokenizer import *

import numpy as np
import sys, os, heapq

root = sys.argv[1]

def genName(inText, outText, vocabValue, vocabIndex):
	freqIn = inText.mean(axis=0).A[0]
	freqOut = outText.mean(axis=0).A[0]
	freqArray = np.subtract(freqIn, freqOut)
	bestWords = {}
	for wordI in range(0,inText.shape[1]):
		if freqArray[wordI] > 0.25:
			word = vocabValue[vocabIndex.index(wordI)]
			bestWords[word] = freqArray[wordI]
	fiveBest = heapq.nlargest(5, bestWords, key=bestWords.get)

	return {'bestKeywords': fiveBest, 'freqArray': freqArray}

f = open(root+'/igraph/data/community.txt','r')
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
	f = open(root+'/igraph/txt/'+str(node)+'.txt', "r")
	texts.append(f.read())
	f.close()
count_vect = CountVectorizer(tokenizer=LemmaTokenizer(), stop_words='english', ngram_range = (1,2), binary=True)
totalCount = count_vect.fit_transform(texts)
totalCount = totalCount.asfptype()

vocabValue = count_vect.vocabulary_.keys()
vocabIndex = count_vect.vocabulary_.values()

r = 0
changed = totalCount.shape[0]
while 1.0*changed > 0.01*totalCount.shape[0]:
	classSet = set(classesArray)
	classesArray = np.array(classesArray)

	classArray = []
	freqMatrix = []
	f = open(root+'/igraph/data/clusters.txt','w')
	for clas in classSet:
		goodRows = np.where(classesArray==clas)[0]
		badRows = np.where(classesArray!=clas)[0]

		results = genName(totalCount[goodRows, :], totalCount[badRows, :], vocabValue, vocabIndex)
		freqMatrix.append(results['freqArray']) #generating a very cool looking matrix
		classArray.append(clas)
		f.write(str(clas)+'[]5[]'+','.join(results['bestKeywords'])+'\n')
		# print "Cluster", clas, " (Size: ", inTexts[clas].shape[0],"): ", results['bestKeywords']
	f.close()
	r += 1
	changed = 0
	allResults = freqMatrix * totalCount.transpose() #I think this is as yolo as it gets: row = each cluster, column = each article's score

	cappedFreqMatrix = np.array(freqMatrix)
	cappedFreqMatrix[cappedFreqMatrix<0.05] = 0
	scores = map(int, cappedFreqMatrix.sum(axis=1))
	print scores
	for articleI in range(0,totalCount.shape[0]):
		bestClas = classArray[np.argmax(allResults[:,articleI])]
		if bestClas != classesArray[articleI]:
			changed += 1
		classesArray[articleI] = bestClas

f = open(root+'/igraph/data/recommunity.txt','w')
for node, member in zip(nodes, classesArray):
	f.write(str(node)+' '+str(member)+'\n')
f.close()