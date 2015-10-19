from sklearn.feature_extraction.text import CountVectorizer, TfidfTransformer
from sklearn.metrics.pairwise import cosine_similarity
from LemmaTokenizer import *
from dbco import *
import numpy as np

cur.execute("SELECT id, name, keywords FROM page ORDER BY id")
art = cur.fetchall()
mathematicians = []
pageNames = np.array([a[1] for a in art])
pageId = [a[0] for a in art]
texts = []
for i in range(0,len(pageId)):
	a = art[i];
	if 'mathematician' in a[2]:
		mathematicians.append(i)
	f = open('../../crawler/summary/'+str(a[0])+'.txt'); texts.append(f.read()); f.close()

count_vect = CountVectorizer(stop_words='english', min_df=1)

count = count_vect.fit_transform(texts).asfptype()
allFreq = count.mean(axis=0).A[0]



vocab = {v: k for k,v in count_vect.vocabulary_.items()}

roun = 1
while roun < 100:
	print "----------------------"
	mathematicians = np.array(mathematicians)
	mathematiciansCount = (count[mathematicians, :]).mean(axis=0).A[0]
	bestWordsI = (mathematiciansCount-allFreq).argsort()[-100:][::-1]
	bestWords = [vocab[i].encode('ascii', 'ignore') for i in bestWordsI]
	print bestWords[:10]

	badWords = np.array(list(set(range(0,count.shape[1])) - set(bestWordsI)))
	mathematiciansCount[badWords] = 0
	mathematicians = []
	for r in range(0,len(texts)):
		thisC = count[r].A[0]; thisC[badWords] = 0;
		sim = cosine_similarity(thisC, mathematiciansCount)[0][0]
		if r == 1160 or r == 5648:
			print pageNames[r], "=>", sim
		if sim > 0.5:
			mathematicians.append(r)
	roun += 1
	print len(mathematicians)

mathematicianIds = [str(pageId[i]) for i in mathematicians]
# cur.execute("UPDATE page SET badPage=0 WHERE badPage=1")
# cur.execute("UPDATE page SET badPage=1 WHERE id IN ("+",".join(mathematicianIds)+")")