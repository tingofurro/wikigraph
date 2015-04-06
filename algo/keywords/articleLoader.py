import os
from extractKeywords import getKeywords

filenames = os.listdir('txt')
texts = [];
for filename in filenames:
	f = open(os.path.join('txt', filename), "r")
	texts.append(f.read())
	f.close()
	
keywords = getKeywords(texts)
for filename, keywordList in zip(filenames, keywords):
	f = open(os.path.join('results', filename), "w")
	f.write('\n'.join(keywordList))
	f.close()