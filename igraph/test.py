from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity
from StringIO import StringIO
from sklearn.svm import SVC

from itertools import izip
import numpy as np
import sys, os
import heapq

def genName(inText, outText, vocabValue, vocabIndex):
	freqIn = inText.mean(axis=0).A[0]
	freqOut = outText.mean(axis=0).A[0]
	bestWords = {}
	freqArray = []
	for wordI in range(0,inText.shape[1]):
		inFreq = freqIn[wordI]
		outFreq = freqOut[wordI]
		freqArray.append((inFreq-outFreq))
		if inFreq > 0.25:
			word = vocabValue[vocabIndex.index(wordI)]
			bestWords[word] = (inFreq-outFreq)
	fiveBest = heapq.nlargest(5, bestWords, key=bestWords.get)

	return {'bestKeywords': fiveBest, 'freqArray': freqArray}

f = open('data/recommunity3.txt')
txt = f.read()
f.close()
toks = txt.split('\n')
classes = {}
classesArray = []
nodes = []

for tok in toks:
	infos = tok.split(' ')
	if len(infos) == 2:
		myClass = int(infos[1])
		myId = int(infos[0])
		classes[myId] = myClass
		classesArray.append(myClass)
		nodes.append(int(infos[0]))

texts = [];
for node in nodes:
	f = open('txt/'+str(node)+'.txt', "r")
	texts.append(f.read())
	f.close()
print "Loaded the articles"
count_vect = CountVectorizer(stop_words='english', binary=True)
totalCount = count_vect.fit_transform(texts)
totalCount = totalCount.asfptype()
print "Prepared word count"
print "Wordcount to array"

vocabValue = count_vect.vocabulary_.keys()
vocabIndex = count_vect.vocabulary_.values()
classSet = set(classesArray)
inTexts = {}; outTexts = {}
classesArray = np.array(classesArray)
for clas in classSet:
	goodRows = np.where(classesArray==clas)[0]
	badRows = np.where(classesArray!=clas)[0]
	inTexts[clas] = totalCount[goodRows, :]
	outTexts[clas] = totalCount[badRows, :]

print "Built the ins and outs"
freqArray = {}
for clas in classSet:
	results = genName(inTexts[clas], outTexts[clas], vocabValue, vocabIndex)
	freqArray[clas] = np.matrix(results['freqArray']).transpose()
	print "Cluster", clas, " (Size: ", inTexts[clas].shape[0],"): ", results['bestKeywords']
err = 0

f = open('data/recommunity4.txt','w')
for articleI in range(0,totalCount.shape[0]):
	bestScore = -1000
	bestClas = -1
	for clas in classSet:
		arti = totalCount[articleI,:]
		oneScore = arti.dot(freqArray[clas])
		if oneScore > bestScore:
			bestScore = oneScore
			bestClas = clas
	if bestClas == classesArray[articleI]:
		print "Right answer: ", classesArray[articleI], " ", bestScore
	else:
		err += 1
		print "Wrong answer: ", bestClas, "!= ", classesArray[articleI], " ", bestScore
	f.write(str(nodes[articleI])+' '+str(bestClas)+'\n')
f.close()
print err