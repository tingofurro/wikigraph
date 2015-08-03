from LemmaTokenizer import *
from numpy import *
from dbco import *

import numpy as np
from sklearn.feature_extraction.text import CountVectorizer
from sklearn.feature_extraction.text import TfidfTransformer

continueRunning = True
while continueRunning:
	count_vect = CountVectorizer(tokenizer=LemmaTokenizer(), stop_words='english') #initialize the vectorizer
	tfidf_trans = TfidfTransformer() #initialize our tfidf transformer

	idList = []
	textList = []
	nameList = []
	keywordList = []

	cur.execute("SELECT id, name FROM wg_page WHERE keywords='' ORDER BY RAND() LIMIT 1000")

	for row in cur.fetchall():
		idList.append(str(row[0]))
		nameList.append(row[1])

	for i in idList:
		f = open('summary/'+str(i)+'.txt', "r")
		content = f.read()
		f.close()
		textList.append(content)

	wordCounts = count_vect.fit_transform(textList)
	wordTfidf = tfidf_trans.fit_transform(wordCounts)

	wordTfidf = wordTfidf.toarray()

	ind = 0

	vocabValue = count_vect.vocabulary_.keys()
	vocabIndex = count_vect.vocabulary_.values()

	while ind < len(idList):
		thisTfidf = wordTfidf[ind]
		keywords = []
		keywordScores = []
		i = 0
		for col in thisTfidf:
			if col > 0.15:
				thisWord = vocabValue[vocabIndex.index(i)].encode('utf-8')
				keywords.append(thisWord)
				keywordScores.append(col)
			i += 1
		sortingTransit = sorted(zip(keywordScores, keywords), reverse=True)
		keywordsSorted = [e[1] for e in sortingTransit]
		keywordList.append(keywordsSorted)
		ind += 1
		if ind%200 == 0:
			print ind

	query = "UPDATE wg_page SET keywords = CASE id "

	for i, nam, keyL in zip(idList, nameList, keywordList):
		keyString = ",".join(keyL)
		query += 'WHEN '+str(i)+' THEN "'+keyString+'" '

	query += 'END WHERE id IN ('+','.join(idList)+')'
	cur.execute(query)


	cur.execute("SELECT COUNT(*) FROM wg_page WHERE keywords=''")
	rows = cur.fetchall()
	if rows[0][0] == 0:
		continueRunning = False