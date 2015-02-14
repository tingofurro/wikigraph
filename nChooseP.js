// (n, p) = (n-1,p-1) + (n-1, p)

function nChooseP() {
	n = parseInt(document.getElementById('n').value);
	p = parseInt(document.getElementById('p').value);
	if (p > n) {
		result = 0;
	}
	if (n == 0 && p == 0) {
		result = 1;
	}
	else {
		var comb = [];
		for(var r = 0; r <= n; r++) {
			comb[r] = [];
			comb[r][0] = 1;
		}
		for(var r = 1; r <= n; r ++) { // for each row
			for(var c = 1; c <= p; c ++) {
				comb[r][c] = comb[(r-1)][(c-1)];
				if(c < r) {
					comb[r][c] += comb[(r-1)][c];
				}
			}
		}
		result = comb[n][p];
	}
	document.getElementById('result').innerHTML=result;
}