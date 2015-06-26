from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity
from StringIO import StringIO
from sklearn.svm import SVC

from itertools import izip
import numpy as np
import sys, os

def genName(inText, outText, vocabValue, vocabIndex):
	nbIn = inText.shape[0] # rows, number
	nbOut = outText.shape[0] # rows, number
	nbWords = inText.shape[1] #columns
	# print inText.shape
	# print outText.shape
	maxScore = 0
	bestWord = ''
	wordI = 0
	while(wordI < nbWords):
		yes = len(inText[:,wordI].nonzero()[0])
		inFreq = (1.0*yes/(1.0*nbIn))
		if inFreq > maxScore and inFreq > 0.25:
			yes = len(outText[:,wordI].nonzero()[0])
			outFreq = (1.0*yes/(1.0*nbOut))+0.000001
			score = inFreq-outFreq
			word = vocabValue[vocabIndex.index(wordI)]
			if score > maxScore:
				bestWord = word
				maxScore = score
		wordI += 1
	return bestWord

f = open('data/community.txt')
txt = f.read()
f.close()
toks = txt.split('\n')
classes = {}
classesArray = []
nodes = []
classNb = {}

for tok in toks:
	infos = tok.split(' ')
	if len(infos) == 2:
		myClass = int(infos[1])
		myId = int(infos[0])
		classes[myId] = myClass
		classesArray.append(myClass)
		classNb[myClass] = classNb.get(myClass, 0) + 1
		nodes.append(int(infos[0]))

texts = [];
for node in nodes:
	f = open('txt/'+str(node)+'.txt', "r")
	texts.append(f.read())
	f.close()
print "Loaded the articles"
count_vect = CountVectorizer(stop_words='english')
totalCount = count_vect.fit_transform(texts)
print totalCount.shape
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
for clas in classSet:
	print "Cluster", clas, ": ", genName(inTexts[clas], outTexts[clas], vocabValue, vocabIndex)