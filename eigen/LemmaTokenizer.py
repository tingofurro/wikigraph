from nltk.tokenize import RegexpTokenizer
from nltk.stem import WordNetLemmatizer
from nltk.corpus import stopwords

tokenizer = RegexpTokenizer(r'\w+')
stopW = stopwords.words('english')
stopW.extend(['content', 'contents', 'reference', 'references', 'example', 'link', 'external', 'type'])
def num_there(s):
    return any(i.isdigit() for i in s)

class LemmaTokenizer(object):
	def __init__(self):
		self.wnl = WordNetLemmatizer()
	def __call__(self, doc):

		tokenArray = [i for i in tokenizer.tokenize(doc) if i not in stopW]
		tokenArray = [i for i in tokenArray if (len(i) > 1 and not num_there(i))]
		i = 0
		for t in tokenArray:
			if t[-1] == 's':
				tokenArray[i] = t[:-1]
			i += 1

		return [self.wnl.lemmatize(t) for t in tokenArray]