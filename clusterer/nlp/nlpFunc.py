
def recursiveCleaning(tok, head, arr, expecting):
	# returns an array of indices that correspond to the 
	for w in tok.subtree:
		if w.dep_ in expecting and w.head == head:
			arr.append(w.i)
			for w2 in w.children:
				recursiveCleaning(w2, w, arr, ['poss', 'case', 'compound', 'adj', 'aux', 'nsubj', 'prt', 'advcl','part','dobj', 'pobj', 'conj', 'npadvmod', 'cc', 'pcomp', 'ccomp', 'mark', 'prep', 'nummod', 'prep', 'amod', 'det'])

def svc(s):
	# Returns the subject, verb, complement of the sentence, as 3 arrays of indices in the sentence.
	global doc
	verb = s.root
	subj = []; compl = [];
	for l in verb.lefts:
		su = []
		recursiveCleaning(l, verb, su, ['nsubj', 'aux'])
		subj.extend(su)
	for r in verb.rights:
		co = []
		recursiveCleaning(r, verb, co, ['dobj', 'acomp', 'attr'])
		compl.extend(co)

	subj = sorted(subj); compl = sorted(compl);
	return subj, verb.i, compl

def contains(haystack, needle):
	# Is the name of the Article the subject of this sentence?
	# Subj is the subject of a sentence, name is the name of the Wikipedia page
	haystack = set(stringify(haystack)); needle = set(stringify(needle))
	return needle.issubset(haystack)
def containsAny(haystack, needles):
	return any([contains(haystack, [needle]) for needle in needles])

def lemmatize(nlp):
	global doc
	app = []
	for w in nlp:
		if isinstance(w, int):
			app.append(doc[w].lemma_.lower())
		elif isinstance(w, unicode):
			app.append(w.lower())
		else:
			app.append(w.lemma_.lower())
	return app
def stringify(obj):
	global doc
	stringObj = []
	for w in list(obj):
		if isinstance(w, int): # we are given a list of ordered indices
			stringObj.append(doc[w].orth_.lower())
		elif isinstance(w, unicode) or isinstance(w, str): # we are given a list of strings
			stringObj.append(w.lower())
		else: # we are given a list of token objects
			stringObj.append(w.orth_.lower())
	return stringObj
def removeParens(s):
	return re.sub(r'\([^)]*\)', '', s)