def QA(root):
	f = open(root+'/igraph/data/clusters.txt','r')
	txt = f.read(); f.close();
	clus = txt.split('\n')

	f = open(root+'/igraph/data/reclusters.txt','w')
	for c in clus:
		tok = c.split('[]');
		if len(tok) > 1:
			good = 1
			if int(tok[0]) == 0:
				good = 0
			f.write('[]'.join(tok)+'[]'+str(good)+'\n')