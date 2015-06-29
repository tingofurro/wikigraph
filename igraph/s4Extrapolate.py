from sklearn.feature_extraction.text import CountVectorizer
from LemmaTokenizer import *
import numpy as np

def extrapolate(root):
	f = open(root+'/igraph/data/recommunity.txt')
	txt = f.read()
	f.close()
	toks = txt.split('\n')
	trainingNodes = []
	trainingLabels = []

	for tok in toks:
		infos = tok.split(' ')
		if len(infos) == 2:
			myClass = int(infos[1])
			trainingNodes.append(int(infos[0]))
			trainingLabels.append(myClass)		

	trainingTexts = []
	for node in trainingNodes:
		f = open(root+'/igraph/txt/'+str(node)+'.txt', "r")
		trainingTexts.append(f.read())
		f.close()

	count_vect = CountVectorizer(tokenizer=LemmaTokenizer(), stop_words='english', ngram_range = (1,2), binary=True)
	totalCount = count_vect.fit_transform(trainingTexts)
	totalCount = totalCount.asfptype()

	labelSet = set(trainingLabels)
	labelArray = []
	trainingLabels = np.array(trainingLabels)
	inTexts = {}; outTexts = {};
	freqMatrix = []
	for clas in labelSet:
		goodRows = np.where(trainingLabels==clas)[0]
		badRows = np.where(trainingLabels!=clas)[0]
		freqIn = totalCount[goodRows, :].mean(axis=0).A[0]
		freqOut = totalCount[badRows, :].mean(axis=0).A[0]
		freqMatrix.append(np.subtract(freqIn, freqOut))
		labelArray.append(clas)

	testNodes = []
	testText = []

	f = open(root+'/igraph/data/fullNodeList.txt')
	txt = f.read()
	f.close()
	nodeList = txt.split('\n')
	for node in nodeList:
		if len(node) > 0 and int(node) not in trainingNodes:
			testNodes.append(int(node))

	for node in testNodes:
		f = open(root+'/igraph/txt/'+str(node)+'.txt', "r")
		testText.append(f.read())
		f.close()

	testCounts = count_vect.transform(testText)
	scores = freqMatrix * testCounts.transpose() # this is a big time matrix mult

	f = open(root+'/igraph/data/extrapolate.txt','w')
	for nodeI in range(0,testCounts.shape[0]):
		bestClas = labelArray[np.argmax(scores[:,nodeI])]
		f.write(str(testNodes[nodeI])+' '+str(bestClas)+'\n')
	f.close()