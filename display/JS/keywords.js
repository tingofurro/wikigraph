function placeKeywords() {
	var keywordGroups = [];
	for(i in graphData.nodes) {
		thisK = graphData.nodes[i].keywords;
		for(k in thisK) {
			keyw = thisK[k];
			if(!keywordGroups[keyw]) {keywordGroups[keyw] = [];}
			if(Array.isArray(keywordGroups[keyw])) {
				keywordGroups[keyw].push(graphData.nodes[i].group);
			}
		}
	}
	sortedKeyword = Object.keys(keywordGroups).sort(function(a,b){return mode(keywordGroups[b]).count-mode(keywordGroups[a]).count})
	nbKeywords = Math.floor(graphData.nodes.length/6);
	alreadyIn = [];
	for(u = 0; u < nbKeywords; u ++) {
		key = sortedKeyword[u];
		nodeGroups = []; nodeXs = []; nodeYs = [];
		for(i in graphData.nodes) {
			if(graphData.nodes[i].keywords && graphData.nodes[i].keywords.indexOf(key) != -1) {
				nodeGroups.push(graphData.nodes[i].group);
				nodeXs.push(graphData.nodes[i].x);
				nodeYs.push(graphData.nodes[i].y);
			}
		}
		var textX = 0, textY = 0;
		modeGroup = mode(nodeGroups).el; size = 0;
		for(i in nodeGroups) {
			if(nodeGroups[i] == modeGroup) {
				textX += nodeXs[i]; textY += nodeYs[i];	size ++;
			}
		}
		textX /= size; textY /= size;
		bestTextY = textY;
		draw = false;
		var minDist = 0;
		for(posY = -1; posY <= 1; posY ++) {
			thisTextY = textY + posY*25;
			thisMinDist = 100000; validPos = true;
			for(o in alreadyIn) {
				thisMinDist = Math.min((Math.pow((textX-alreadyIn[o][0]),2)+Math.pow((thisTextY-alreadyIn[o][1]),2)), thisMinDist);
				if(thisMinDist < 500) {validPos = false; break;}
			}
			if(validPos && thisMinDist > minDist) {
				bestTextY = thisTextY; minDist = thisMinDist;
				draw = true;
			}
		}
		if(draw) {
			var keyword = svg.append('text');
			keyword.attr("x", (textX+transX)).attr("y", (bestTextY+transY))
			.text(key).attr("font-size", (15+2*size)+"px").attr("fill", color(modeGroup)).attr('class', 'keyword').attr('opacity', 0).attr('id', key);
			alreadyIn.push([textX, bestTextY]);
		}
	}
	$('.keyword').animate({'opacity': 1}, 400);
	keepNodesOnTop();
}
// function openNotif(data) {
// 	deleteNotif();
// 	noty({text: '<img src="images/sources/'+data.source+'.png" class="artImg" /><b>'+data.name+'</b><div class="dblClickInfo">Double click to open article</div>', layout: 'bottomLeft', speed: 300});
// }
function deleteNotif() {
	$('.noty_bar').parent().remove();
}
function keepNodesOnTop() {
	$(".node").each(function( index ) {
		var gnode = this.parentNode;
		gnode.parentNode.appendChild(gnode);
	});
}
function mode(array) {
	if(array.length == 0)
		return null;
	var modeMap = {};
	var maxEl = array[0], maxCount = 1;
	for(var i = 0; i < array.length; i++) {
		var el = array[i];
		if(modeMap[el] == null) modeMap[el] = 1;
		else modeMap[el]++;	
		if(modeMap[el] > maxCount) {
			maxEl = el; maxCount = modeMap[el];
		}
	}
	return {'el': maxEl, 'count': maxCount};
}