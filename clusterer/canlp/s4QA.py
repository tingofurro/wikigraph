def QA():
	f = open('data/clusters.txt','r')
	txt = f.read(); f.close();
	clus = txt.split('\n')

	f = open('data/clusters.txt','w')
	for c in clus:
		tok = c.split('[]');
		if len(tok) > 1:
			good = 1
			if int(tok[0]) == 0:
				good = 0
			f.write('[]'.join(tok)+'[]'+str(good)+'\n')