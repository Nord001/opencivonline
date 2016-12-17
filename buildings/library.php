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
<li><?php echo $technology; ?> (<span class="timer" data-duration-in-seconds="<?php echo $build_time; ?>" data-start-time="<?php echo $start_building_time; ?>"></span>)</li>
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
<script src="/js/timer.js"></script>
