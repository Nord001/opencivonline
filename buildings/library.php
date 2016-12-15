<?php
	$base_data = json_decode ($redis->get("world:bases:$user"), true);

	$technologies = $redis->keys("technology:*");
?>
<h1>Library</h1>
<h2>Research<h2>
<ul>
		<?php
			foreach ($technologies as $technology_key) {
				$technology = explode (":", $technology_key)[1];

				if (!in_array ($technology, $base_data["technology"])) {
					if (you_are_researching ($technology, $base_data)) {
						$build_time = json_decode($redis->get("technology:$technology"), true)["time"];
						$start_building_time = get_build_start_time ($technology, $base_data);
?>
<li><?php echo $technology; ?> (<span class="building" data-build-length="<?php echo $build_time; ?>" data-start-time="<?php echo $start_building_time; ?>"></span>)</li>
<?php
					} else {
						echo "<li><a href='buildings/library/research.php?user=$user&amp;research=$technology'>$technology$mark</a></li>";	
					}
				}
			}
		?>
</ul>
<?php
	if (count ($base_data["technology"]) > 0) {
?>
<h2>Your Technology</h2>
<ul>
<?php
	foreach ($base_data["technology"] as $technology) {
		echo "<li>$technology</li>";
	}
?>
</ul>
<?php
	}

	function you_are_researching ($technology, $base_data) {
		foreach ($base_data["research"]["queue"] as $current_technology) {
			if ($technology == $current_technology["name"]) {
				return true;
			}
		}
	}

	function get_build_start_time ($technology, $base_data) {
		foreach ($base_data["research"]["queue"] as $current_technology) {
			if ($technology == $current_technology["name"]) {
				return $current_technology["start_time"];
			}
		}
	}
?>
<script>
			// taken from http://stackoverflow.com/questions/8141718/javascript-need-to-do-a-right-trim on 20161211 @ 11:55est
			function rtrim(str, length) {
				return str.substr(0, str.length - length);
			}
			function updateBuildTimer () {
				var build_queue_timer_dom = document.getElementsByClassName ("building")[0];
				var start_build_time =parseInt (build_queue_timer_dom.getAttribute ("data-start-time"));
				var build_length = parseInt (build_queue_timer_dom.getAttribute ("data-build-length"));
				var current_time = parseInt (rtrim ((new Date).getTime().toString(), 3));
				var time_left_seconds = start_build_time - current_time + build_length;

				var minutes = Math.floor (time_left_seconds / 60);
				var seconds = time_left_seconds % 60;

				var time_left = (minutes < 10 ? "0" : "") + minutes + ":" + (seconds < 10 ? "0" : "") + seconds

				document.getElementsByClassName ("building")[0].innerHTML = time_left;

				if (time_left_seconds <= 0) {
					location.reload();
				} else {
					setTimeout( function() {
						updateBuildTimer();
					}, 1000);
				}
			}
			updateBuildTimer();
</script>