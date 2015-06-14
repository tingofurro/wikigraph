var state = 0;
$(document).ready(function () {
	$('#graphIframe').width($(document).width());
	$('#graphIframe').height(($(document).height())-40);
});
function menuOver() {
	if(state==0) {
		state = 2;
		$('#leftMenu').animate({'left': 0}, 300,function() {
			state = 1;
		});
	}
}
function menuOut() {
	if(state == 1) {
		state = 2;
		$('#leftMenu').animate({'left': -300}, 300, function() {
			state = 0;
		});
		
	}
}