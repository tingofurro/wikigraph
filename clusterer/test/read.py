f = open('data/recommunity.txt')
txt = f.read()
f.close()
toks = txt.split('\n')

classesArray = []; nodes = [];
for tok in toks:
	infos = tok.split(' ')
	if infos[1] == '2':
		print infos[0]