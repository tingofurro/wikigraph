from bs4 import BeautifulSoup
import urllib, os.path, sys
from soup2obj import *
from dbco import *

if __name__ == '__main__' and len(sys.argv) > 1:
	prefix = sys.argv[1]+'_'
	cur.execute("SELECT id, name FROM "+prefix+"page ORDER BY id")
	for p in cur.fetchall():
		print p
		buildSummary(p[1], 'summary/'+prefix+str(p[0])+'.txt')