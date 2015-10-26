from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from LemmaTokenizer import *
import numpy as np

def genFreqArray(inText, outText):
	return np.subtract(inText.mean(axis=0).A[0], outText.mean(axis=0).A[0])

def useNLP(nodes, classesArray, summaryFolder):
	classesArray = np.array(classesArray)

	texts = [];
	for node in nodes:
		f = open(summaryFolder+'/'+str(node)+'.txt', "r"); texts.append(f.read()); f.close();
	count_vect = CountVectorizer(tokenizer=LemmaTokenizer(), stop_words='english', ngram_range = (1,2), binary=True)
	totalCount = count_vect.fit_transform(texts)

	tfidf_trans = TfidfTransformer() #initialize our tfidf transformer
	totalCount = tfidf_trans.fit_transform(totalCount)

	totalCount = totalCount.asfptype()

	changed = totalCount.shape[0]
	round = 0
	while round < 2:
		classSet = list(set(classesArray))
		freqMatrix = []

		for clas in classSet:
			goodRows = np.where(classesArray==clas)[0]
			badRows = np.where(classesArray!=clas)[0]

			freqArray = genFreqArray(totalCount[goodRows, :], totalCount[badRows, :])
			freqMatrix.append(freqArray) #generating a very cool looking matrix

		allResults = freqMatrix * totalCount.transpose() #I think this is as yolo as it gets: row = each cluster, column = each article's score

		nClassesArray = [classSet[np.argmax(allResults[:,i])] for i in range(0,totalCount.shape[0])]
		changed = np.count_nonzero(nClassesArray-classesArray)
		classesArray = np.array(nClassesArray)
		round += 1
	return classesArray