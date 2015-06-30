import os
import sys
from extractKeywords import getKeywords

root = sys.argv[1]
filenames = os.listdir(root +'/algo/11-texts')
texts = [];
for filename in filenames:
	f = open(root +'/algo/11-texts/'+ filename, "r")
	texts.append(f.read())
	f.close()

keywords = getKeywords(texts)
for filename, keywordList in zip(filenames, keywords):
	f = open(root +'/algo/11-results/'+ filename, "w")
	f.write('\n'.join(keywordList))
	f.close()
