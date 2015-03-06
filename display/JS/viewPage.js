var lastTime = 0;
var tim; var i = 0;
function changeTextFilter() {
	timeSpace = 350;
	var n = (new Date()).getTime();
	if(n-lastTime < 350) {
		clearTimeout(tim);
	}
	tim = setTimeout(function() {
		searchNames();
	}, 350);
	lastTime = n;
}
function searchNames() {
	var searchTags = encodeURIComponent($('#searchBox').val());
	if(searchTags.length > 2) {
		$.ajax({url: webroot+'explore.php?lookUp='+searchTags, success: function(dat){
			var splits = dat.split('[]');
			var html = '<div id="closeSearchLabel" onclick="closeSearch();">&#10060;</div>';
			for (var i = 0; i < splits.length; i++) {
				me = splits[i].split('||')
				html += '<a href="'+linkroot+'explore/'+me[0]+'"><div id="pageClick">'+me[1]+'</div></a>';
			}
			$('#responseContent').html(html);
		}
		});		
	}
	else {$('#responseContent').html('');}
}
function closeSearch() {
	$('#responseContent').html('');
	$('#searchBox').val('');
}
$('#articleContent a').click(function(evt) {
        evt.preventDefault();
        var nextPage = $(this).attr('href');
        nextPage = nextPage.replace('/wiki/', '');
        window.location=linkroot+'explore/'+nextPage;
});