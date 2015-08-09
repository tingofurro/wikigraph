from sklearn.feature_extraction.text import CountVectorizer
from LemmaTokenizer import *
from dbco import *
import numpy as np
import heapq, sys

level = 2

minLevel = 90000000000000; maxLevel = 0;
cur.execute("SELECT id FROM wg_cluster WHERE level="+str(level)+" ORDER BY id")
for row in cur.fetchall():
	minLevel = min(minLevel, row[0])
	maxLevel = max(maxLevel, row[0])

cur.execute("SELECT class1 FROM wg_cdist WHERE level="+str(level)+" ORDER BY class1 DESC LIMIT 1")
minLevel = cur.fetchall()[0][0]+1

savedText = {};

for class1 in range(minLevel, maxLevel):
	toInsert = []
	for class2 in range(class1+1, maxLevel+1):
		cur.execute("SELECT id, cluster2 FROM wg_page WHERE cluster"+str(level)+"="+str(class1)+" OR cluster"+str(level)+"="+str(class2))

		classesArray = []; nodes = [];
		for row in cur.fetchall():
			nodes.append(row[0])
			classesArray.append(row[1])
		classesArray = np.array(classesArray)

		texts = []
		for node in nodes:
			if node in savedText:
				texts.append(savedText[node])
			else:
				f = open('summary/'+str(node)+'.txt', "r")
				thisText = f.read()
				texts.append(thisText)
				f.close()
				savedText[node] = thisText

		count_vect = CountVectorizer(tokenizer=LemmaTokenizer(), stop_words='english', binary=True)
		totalCount = count_vect.fit_transform(texts)
		totalCount = totalCount.asfptype()

		classSet = set(classesArray)

		rows1 = np.where(classesArray==class1)[0]
		rows2 = np.where(classesArray==class2)[0]

		matrix1 = totalCount[rows1, :]
		matrix2 = totalCount[rows2, :]

		freq1 = matrix1.mean(axis=0).A[0]
		cappedFreq1 = freq1
		cappedFreq1[cappedFreq1<0.05] = 0

		freq2 = matrix2.mean(axis=0).A[0]
		cappedFreq2 = freq2
		cappedFreq2[cappedFreq2<0.05] = 0

		freqArray = np.subtract(cappedFreq1, cappedFreq2)
		freqArray = np.absolute(freqArray)

		distance = np.sum(freqArray)
		toInsert.append("(NULL, '"+str(class1)+"', '"+str(class2)+"', '"+str(distance)+"', "+str(level)+")")

		print class1, " vs. ", class2, " = ", distance

	cur.execute("INSERT INTO `wg_cdist` (`id`, `class1`, `class2`, `distance`, `level`) VALUES "+", ".join(toInsert)+";")