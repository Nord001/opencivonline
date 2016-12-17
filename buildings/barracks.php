<?php
	$base_data = json_decode ($redis->get("world:bases:$user"), true);

	$units = $redis->keys("unit:*");
?>
<h1>Barracks</h1>
<h2>Train<h2>
<ul>
<?php
	foreach ($units as $key => $raw_unit) {
		$unit = explode (":", $raw_unit)[1];
		$unit_data = json_decode($redis->get("unit:$unit"), true);
		$required_technologies = $unit_data["required_technology"];
		$my_technologies = $base_data["technology"];

		// make sure all required tech is in my technolody
		$all_technology_requirments_met = true;
		foreach ($required_technologies as $required_technology) {
			if (!in_array ($required_technology, $my_technologies)) {
				$all_technology_requirments_met = false;
				break;
			}
		}

		if ($all_technology_requirments_met) {
			echo "<li>$unit</li>";
		}
	}
?>
</ul>
