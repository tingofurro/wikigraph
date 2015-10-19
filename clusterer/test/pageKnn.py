from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity
from LemmaTokenizer import *
from dbco import *
import numpy as np

k = 100

cur.execute("SELECT id, name, keywords FROM page ORDER BY id")
art = cur.fetchall()
pageNames = np.array([a[1] for a in art])
pageId = [a[0] for a in art]
texts = []
for i in range(0,len(pageId)):
	a = art[i];
	f = open('../../crawler/summary/'+str(a[0])+'.txt'); texts.append(f.read()); f.close()

count_vect = CountVectorizer(tokenizer=LemmaTokenizer(), stop_words='english', min_df=1)

count = count_vect.fit_transform(texts).asfptype()

# vocab = {v: k for k,v in count_vect.vocabulary_.items()}

f = open('../knn.txt', 'w')
for i in range(0,len(pageId)):
	simi = cosine_similarity(count[i], count)
	neighInd = simi[0].argsort()[-(k+1):-1]
	neighId = [str(pageId[j]) for j in neighInd]
	f.write(str(pageId[i])+'|'+','.join(neighId)+'\n')
	if i%100 == 0:
		print i

f.close()
