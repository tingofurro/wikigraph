from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity, euclidean_distances
import numpy as np

def build_similarities(arts):
	texts = [open('../../crawler/summary/'+a+'.txt', "r").read() for a in arts];
	totalCount = CountVectorizer(stop_words='english', binary=True).fit_transform(texts) #
	totalCount = TfidfTransformer().fit_transform(totalCount).asfptype()
	B = cosine_similarity(totalCount)
	decimals = 4; B = ((pow(10, decimals)*B).astype(int))*pow(10, -decimals)
	for i in range(1,10):
		print "p ", i, ": ", np.percentile(B, 10*i)
	return B