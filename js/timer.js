// taken from http://stackoverflow.com/questions/8141718/javascript-need-to-do-a-right-trim on 20161211 @ 11:55est
function rtrim(str, length) {
	return str.substr(0, str.length - length);
}
function updateTimer () {
	var timer_dom = document.getElementsByClassName ("timer")[0];
	var start_time =parseInt (timer_dom.getAttribute ("data-start-time"));
	var build_length = parseInt (timer_dom.getAttribute ("data-duration-in-seconds"));
	var current_time = parseInt (rtrim ((new Date).getTime().toString(), 3));
	var time_left_seconds = start_time - current_time + build_length;

	var minutes = Math.floor (time_left_seconds / 60);
	var seconds = time_left_seconds % 60;

	var time_left = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds

	document.getElementsByClassName ("timer")[0].innerHTML = time_left;

	if (time_left_seconds <= 0) {
		location.reload();
	} else {
		setTimeout( function() {
			updateTimer();
		}, 1000);
	}
}
updateTimer();