from bs4 import BeautifulSoup
from dbco import *
import urllib, os.path
from buildSummary import *
import sys

reload(sys)
sys.setdefaultencoding('utf-8')

def isSkipSection(elem, skipping):
	badSections = ['Notes', 'References', 'Historical references', 'Additional references', 'External links', 'Further reading', 'Notes and references', 'Other resources', 'Bibliography', 'Books', 'Notes 2', 'Sources', 'References and further reading', 'Recommended reading', 'Additional reading', 'Literature', 'Footnotes', 'Citations', 'Publications', 'References and external links', 'References and notes', 'Textbooks', 'Sources and external links', 'General references', 'In-line notes and references', 'Selected bibliography', 'Selected works', 'Some publications', 'Biographical references']
	if elem.name =='h2':
		if elem.find('span').get_text() in badSections:
			return True
		else:
			return False
	return skipping

def isValid(child):
	badClasses = ['hatnote', 'stub', 'ambox', 'vertical-navbox', 'thumb', 'infobox', 'navbox']
	if child.get('class') is not None and any((True for x in badClasses if x in child.get('class'))):
		return False
	return True
	
def crawlPage(idPage, name):
	fileName = 'html/'+str(idPage)+'.html'
	if not os.path.isfile(fileName):
		f = urllib.urlopen("https://en.wikipedia.org/wiki/"+name); html_doc = f.read(); f.close();
		soup = BeautifulSoup(html_doc, 'lxml')
		innerSoup = soup.find(id='mw-content-text')
		elemList = innerSoup.contents
		listBadIds = ['siteNotice']
		listBadClass = ['']
		text = ''
		skipping = False
		for elem in elemList:
			if elem.name != None:
				skipping = isSkipSection(elem, skipping)
				if not skipping and isValid(elem):
					text += elem.prettify()
		f = open(fileName, 'w'); f.write(text); f.close();
		buildSummary(innerSoup, 'summary/'+str(idPage)+'.txt')

cur.execute("SELECT id, name FROM page ORDER BY id")
for row in cur.fetchall():
	crawlPage(row[0], row[1])
	print row[0]