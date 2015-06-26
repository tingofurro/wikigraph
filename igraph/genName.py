from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity
from StringIO import StringIO
from sklearn.svm import SVC

from itertools import izip
import numpy as np
import sys, os


def genName(cluster):
	f = open('data/community.txt')
	txt = f.read()
	f.close()
	toks = txt.split('\n')
	nodesInCluster = []
	nodesNotIn = []
	for tok in toks:
		infos = tok.split(' ')
		if len(infos) == 2:
			myClass = int(infos[1])
			if myClass == cluster:
				nodesInCluster.append(int(infos[0]))
			else:
				nodesNotIn.append(int(infos[0]))

	texts = []
	for node in nodesInCluster:
		f = open('txt/'+str(node)+'.txt', "r")
		texts.append(f.read())
		f.close()

	textsNotIn = []
	for node in nodesNotIn:
		f = open('txt/'+str(node)+'.txt', "r")
		textsNotIn.append(f.read())
		f.close()

	count_vect = CountVectorizer(stop_words='english', ngram_range=(1,3))

	word_count = count_vect.fit_transform(texts)
	word_count_not_in = count_vect.transform(textsNotIn)

	vocabValue = count_vect.vocabulary_.keys()
	vocabIndex = count_vect.vocabulary_.values()

	word_count = word_count.toarray()
	word_count_not_in = word_count_not_in.toarray()

	maxScore = 0
	bestWord = ''

	for wordI in range(0, len(word_count[0])):
		yes = 0
		no = 0
		for docI in range(0, len(texts)):
			if word_count[docI][wordI] > 0:
				yes += 1
			else:
				no += 1
		inFreq = (1.0*yes/(1.0*yes+no))
		if inFreq > maxScore:
			yes = 0
			no = 0
			for docI in range(0, len(textsNotIn)):
				if word_count_not_in[docI][wordI] > 0:
					yes += 1
				else:
					no += 1
			outFreq = (1.0*yes/(1.0*yes+no))+0.00001
			# score = inFreq*inFreq/outFreq
			score = inFreq-outFreq
			word = vocabValue[vocabIndex.index(wordI)]
			if score > maxScore:
				bestWord = word
				maxScore = score
				print maxScore
	return bestWord