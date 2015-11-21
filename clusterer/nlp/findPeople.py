from nlpFunc import *
from dbco import *
import sys, re

def cleanForName(s):
	toks = removeParens(s).replace(",","").replace("-", " ").lower().split(' ')
	return [t for t in toks if not (len(t) <= 3 and '.' in t)] # T. Schwartz => Schwarts, David Bla Jr. => David Bla

def findPeople():
	global doc
	#Edge cases: Charles B. Morrey, Jr., Heinz-Dieter Ebbinghaus, Roger Temam
	cur.execute("SELECT id FROM ma_page WHERE `type`='person' ORDER BY id DESC LIMIT 1")
	d = cur.fetchall()
	start = 1
	if len(d) > 0:
		start = d[0][0]+1
	cur.execute("SELECT id, name FROM ma_page WHERE id>="+str(start)+" ORDER BY id")
	for p in cur.fetchall():
		pageName = p[1].replace('_', ' ')
		personName = cleanForName(pageName) # we try to fit it
		if len(personName) > 0:
			firstName = personName[0]; familyName = personName[-1];
			f = open("../../crawler/summary/ma_"+str(p[0])+".txt"); myStr = unicode(f.read()); f.close();
			myStr = removeParens(re.sub(' +', ' ', myStr).replace('\t', ''))

			doc = nlp(myStr)
			sentences = doc.sents
			isPerson = False
			for s in list(sentences)[:10]:
				if len(stringify(s)) > 0:
					subj, verb, compl = svc(s)
					if s.root.lemma_ in ['be', 'bear', 'work', 'live'] and familyName in stringify(subj):				
						isPerson |= (s.root.lemma_ == 'be' and containsAny(compl, ["mathematician", "professor"])) or (s.root.lemma_ != 'be')
		if isPerson:
			print p[0], pageName, "is a person"
			cur.execute("UPDATE ma_page SET `type`='person' WHERE id="+str(p[0]))
			print "------------------------"
		else:
			print p[0], pageName, "is not"
findPeople()