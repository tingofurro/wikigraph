import os
from extractKeywords import getKeywords

filenames = os.listdir('/var/www/wikigraph/algo/11-texts')
texts = [];
for filename in filenames:
	f = open('/var/www/wikigraph/algo/11-texts/'+ filename, "r")
	texts.append(f.read())
	f.close()
	
keywords = getKeywords(texts)
for filename, keywordList in zip(filenames, keywords):
	f = open('/var/www/wikigraph/algo/11-results/'+ filename, "w")
	f.write('\n'.join(keywordList))
	f.close()
