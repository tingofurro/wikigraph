from bs4 import BeautifulSoup
import urllib, os.path
from dbco import *
from soup2obj import *
from collections import Counter
import numpy as np

def loadCategory(name):
	whereToSave = 'category/'+(name.replace('/', ''))+'.html'
	if os.path.isfile(whereToSave):
		f = open(whereToSave); html = f.read(); f.close();
	else:
		f = urllib.urlopen("https://en.wikipedia.org/wiki/Category:"+name); html = f.read(); f.close();
		f2 = open(whereToSave, 'w'); f2.write(html); f2.close();
	return html

def getChildren(name):
	soup = BeautifulSoup(loadCategory(name), 'lxml');
	return {'subcat': findSubCategories(soup), 'pages': findPages(soup)}

def getLinks(name):
	return pageLinks(name)
catToAdd = []; visitedCats = set([])

# root = {'prefix': 'ee', 'rootCat': 'Electrical_engineering'};
# root = {'prefix': 'ma', 'rootCat': 'Fields_of_mathematics'};
root = {'prefix': 'cs', 'rootCat': 'Areas_of_computer_science'};
# root = {'prefix': 'bio', 'rootCat': 'Biology'};

level = 1
layerCategories = {};
layerCategories[level] = set(getChildren(root['rootCat'])['subcat'])
corePages = {}; catOfPage = {};
corePages[0] = set([])

pageEdges = {};

while level < 10:
	corePages[level] = set([]) | corePages[level-1]
	layerCategories[level+1] = set([]);
	score = {}; var  = {}
	o = 0
	for cat in layerCategories[level]:
		o += 1
		visitedCats.add(cat);
		thisCat = getChildren(cat)
		clusterPages = set(thisCat['pages'])
		clusterLinks = []
		for p in clusterPages:
			pageEdges[p] = getLinks(p)
			clusterLinks.extend(pageEdges[p])

		counter = Counter(clusterLinks)
		outgoingUnique = set(counter.keys())
		toCorePages = outgoingUnique & corePages[level-1]
		goodCount = 0.0; innerCount = 0.0;
		for c in toCorePages:
			goodCount += counter[c]
		for p in clusterPages:
			innerCount += counter.get(p, 0.0)

		totalCount = len(clusterLinks)
		if (len(clusterPages) > 0 and totalCount > 0) or level==1:
			score[cat] = (float(len(toCorePages))/len(clusterPages))*((0.00001+goodCount)/totalCount)*((0.00001+innerCount)/totalCount)
			var[cat] = {'core_p': len(toCorePages), 'size': len(clusterPages), 'core_e': goodCount, 'in-in': innerCount, 'tot_e': totalCount}
			# print var[cat]
			if level==1:
				score[cat] = 1
			if score[cat] >= ((level-1)**1.5)*0.2/85.0:
				print o, " / ", len(layerCategories[level]), ": ", cat, " score:", score[cat]
				layerCategories[level+1] |= (set(thisCat['subcat'])-visitedCats);
				corePages[level] |= set(thisCat['pages'])
				catToAdd.append({'name': cat, 'level': level})
				for p in thisCat['pages']:
					if p not in catOfPage:
						catOfPage[p] = len(catToAdd)


	sortedScores = sorted(score, key=score.get, reverse=True)
	f = open('test'+str(level)+'.txt', 'w')
	for c in sortedScores:
		f.write(c+": "+str(score[c])+' '+str(var[c])+'\n')
	f.close()
	level += 1
	print "Starting level", level, "pages: ", len(corePages[level-1]), " categories 2 explore: ", len(layerCategories[level])

#Create the category table
cur.execute("CREATE TABLE IF NOT EXISTS `"+root['prefix']+"_category` (`id` int(100) NOT NULL AUTO_INCREMENT, `name` text CHARACTER SET utf32 COLLATE utf32_bin NOT NULL, `level` int(11) NOT NULL, PRIMARY KEY (`id`)) DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;")
cur.execute("TRUNCATE TABLE `"+root['prefix']+"_category`")
categoryString = ["(NULL, '"+c['name']+"', '"+str(c['level'])+"')" for c in catToAdd]
cur.execute("INSERT INTO `"+root['prefix']+"_category` (`id`, `name`, `level`) VALUES "+','.join(categoryString)+";")

#Create the page table
cur.execute("CREATE TABLE IF NOT EXISTS `"+root['prefix']+"_page` (`id` int(11) NOT NULL AUTO_INCREMENT, `name` text CHARACTER SET utf32 COLLATE utf32_bin NOT NULL, `category` int(11) NOT NULL, PRIMARY KEY (`id`)) DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;")
cur.execute("TRUNCATE TABLE `"+root['prefix']+"_page`")

pages = catOfPage.keys()
pageSet = set(pages)
pageId = {}
i = 1
for p in pages:
	pageId[p] = i
	i += 1

edgeList = []; pageList = [];
for p in pages:
	myLinks = set(pageEdges[p]) & pageSet; 
	neighborId = [pageId[p2] for p2 in myLinks]
	pageList.append('(NULL, "'+p+'", '+str(catOfPage[p])+')')

	edgeList.extend(['(NULL, '+str(pageId[p])+', '+str(n)+')' for n in neighborId])

cur.execute("INSERT INTO `"+root['prefix']+"_page` (`id`, `name`, `category`) VALUES "+','.join(pageList)+";")

#Create the table of links
cur.execute("CREATE TABLE IF NOT EXISTS `"+root['prefix']+"_link` (`id` int(11) NOT NULL AUTO_INCREMENT, `from` int(11) NOT NULL, `to` int(11) NOT NULL, PRIMARY KEY (`id`)) DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;")
cur.execute("TRUNCATE TABLE `"+root['prefix']+"_link`")

cur.execute("INSERT INTO `"+root['prefix']+"_link` (`id`, `from`, `to`) VALUES "+','.join(edgeList)+";")
