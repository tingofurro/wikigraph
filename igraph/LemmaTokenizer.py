from nltk.tokenize import RegexpTokenizer
from nltk.stem import WordNetLemmatizer
from nltk.corpus import stopwords

tokenizer = RegexpTokenizer(r'\w+')
stopW = stopwords.words('english')

class LemmaTokenizer(object):
	def __init__(self):
		self.wnl = WordNetLemmatizer()
	def __call__(self, doc):
		tokenArray = [i for i in tokenizer.tokenize(doc) if i not in stopW]
		return [self.wnl.lemmatize(t) for t in tokenArray]