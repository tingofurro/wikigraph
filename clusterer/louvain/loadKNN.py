def loadKNN(arts, limit=50):
	nei = {}
	dic = {}
	for i in range(0,len(arts)):
		dic[str(arts[i])] = i
	f = open('../knn.txt'); lines = f.read().split('\n')
	for line in lines:
		toks = line.split('|')
		if toks[0] in dic: # this is an interesting vertex
			toks[1] = toks[1].split(',')
			nei[dic[toks[0]]] = [dic[t] for t in toks[1] if t in dic][:limit]
	return nei

if __name__ == "__main__":
    print loadKNN(range(1,100))