from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from numpy import *
from dbco import *
import numpy as np
import sys

def computeKeywords(prefix):
	try:
		cur.execute("ALTER TABLE "+prefix+"page ADD `keywords` TEXT NOT NULL AFTER `category`")
	except:
		pass
	continueRunning = True
	while continueRunning:
		count_vect = CountVectorizer(stop_words='english') #initialize the vectorizer
		tfidf_trans = TfidfTransformer() #initialize our tfidf transformer

		cur.execute("SELECT id, name FROM "+prefix+"page WHERE keywords='' ORDER BY RAND() LIMIT 1000"); res = cur.fetchall();
		idList = [str(r[0]) for r in res]; nameList = [r[1] for r in res];

		keywordList = []; textList = [];
		for i in idList:
			f = open('summary/'+prefix+str(i)+'.txt', "r")
			content = f.read();	f.close(); textList.append(content)

		wordCounts = count_vect.fit_transform(textList)
		wordTfidf = tfidf_trans.fit_transform(wordCounts)

		vocab = {v: k for k,v in count_vect.vocabulary_.items()}

		for ind in range(0,len(idList)):
			thisTfidf = wordTfidf[ind].A[0]
			sixBestI = thisTfidf.argsort()[-6:][::-1]
			keywordsSorted = [vocab[i].encode('ascii', 'ignore') for i in sixBestI]
			keywordList.append(keywordsSorted)
			ind += 1

		print len(idList)
		cases = ['WHEN '+str(i)+' THEN "'+",".join(keyL)+'" ' for i, keyL in zip(idList, keywordList)]
		cur.execute("UPDATE "+prefix+"page SET keywords = CASE id "+ "".join(cases) +"END WHERE id IN ("+','.join(idList)+")")

		cur.execute("SELECT COUNT(*) FROM "+prefix+"page WHERE keywords=''");
		continueRunning = (cur.fetchall()[0][0] > 0)

if __name__ == '__main__' and len(sys.argv) > 1:
	computeKeywords(sys.argv[1]+'_')