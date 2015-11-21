from nlpFunc import *
from dbco import *
import sys, re

def whatIs():
	global doc
	cur.execute("SELECT id, name FROM ma_page WHERE `type`!='person' ORDER BY id")
	for p in cur.fetchall():
		pageName = p[1].replace('_', ' ')
		f = open("../../crawler/summary/ma_"+str(p[0])+".txt"); myStr = unicode(f.read()); f.close();
		myStr = removeParens(re.sub(' +', ' ', myStr).replace('\t', ''))

		doc = nlp(myStr)
		sentences = doc.sents
		for s in list(sentences)[:10]:
			if len(stringify(s)) > 0:
				subj, verb, compl = svc(s)
if __name__ == '__main__':
	whatIs()