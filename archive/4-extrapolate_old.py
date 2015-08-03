# -*- coding: utf-8 -*-

reload(sys)
sys.setdefaultencoding('utf-8')

count_vect = CountVectorizer(stop_words='english') #initialize the vectorizer

root = sys.argv[1]

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

trainingCounts = count_vect.fit_transform(trainingTexts)

testNodes = []
testText = []

f = open(root+'/igraph/data/fullNodeList.txt')
txt = f.read()
f.close()
nodeList = txt.split('\n')
for node in nodeList:
	if int(node) not in trainingNodes:
		testNodes.append(int(node))

for node in testNodes:
	f = open(root+'/igraph/txt/'+str(node)+'.txt', "r")
	testText.append(f.read())
	f.close()

testCounts = count_vect.transform(testText)
testTfidf = tfidf_trans.transform(testCounts)
outputLabels = clf.predict(testTfidf)

f = open(root+'/igraph/data/extrapolate.txt','w')
for node, member in izip(testNodes, outputLabels):
	f.write(str(node)+' '+str(member)+'\n')
f.close()