<html>
	<head>
		<title>Open Civilization Online</title>
		<link href="styles.css" rel="stylesheet" />
	</head>
	<body>
		<pre>
<?php
	$user = $_REQUEST['user'];
	$base = $_REQUEST['base'];

	require 'credis/Client.php';
	$redis = new Credis_Client('localhost');

	// read in map and associated data
	$raw_map = $redis->get('world.map');
	$raw_bases = json_decode ($redis->get('world.bases'), yes);
	$raw_resources = json_decode ($redis->get('world.resources'));
	$raw_buildings_available = $redis->keys('buildings:*');
	$json_base_data = json_decode ($redis->get("world.bases.$user"));
	$base_data = json_decode ($redis->get("world.bases.$user"), yes);

	// render map to array (maybe should be json)
	$raw_map_exploded = explode ("\n", $raw_map);
	$max_x = $raw_map_exploded[0];
	$max_y = $raw_map_exploded[1];

	$map = array ();

	$offset = 2;

	for ($y = $offset; $y < $max_y; $y++) {
		$cur_y = $y - $offset;
		$map[$cur_y] = array ();
		for ($x = 0; $x < $max_x; $x++) {
			$map[$cur_y][$x] = $raw_map_exploded[$y]{$x};
		}
	}

	// find base
	$base_x = -1;
	$base_y = -1;
	for ($x = 0; $x < $max_x - 2; $x++) {
		for ($y = 0; $y < $max_y; $y++) {
			if ($raw_bases[$x][$y] == $base) {
				$base_x = $x;
				$base_y = $y;
			}
		}
	}

	echo "<a href='./?user=$user'>&lt; back</a><br />";
	echo "User: $user<br />";
	echo "Base: $base<br />";
	echo "location: x: $base_x; y: $base_y<br />"; 

	if ($base == $user) {
		$number_of_buildings = sizeof ($json_base_data->buildings);
?>
		<h2>Your Base</h2>
		<h3>Buildings</h3>
<?php
		if ($number_of_buildings == 0) {
			echo "Build something!";
		} else {
?>
		<ul>
<?php
			for ($i = 0; $i < $number_of_buildings; $i++) {
				$building_name = $json_base_data->buildings[$i];
				echo "<li>$building_name</li>";
			}
?>
		</ul>
<?php
	}

	if ($base_data["build-queue"] != "") {
?>
		<h3>Building queue</h3>
		<ul>
			<?php
				$exploded_build_queue = explode ("|", $base_data["build-queue"]);
				$building = $exploded_build_queue[0];
				$start_building_time = $exploded_build_queue[1];

				$build_time = json_decode ($redis->get("buildings:$building"), yes)["time"];
			?>
			<li><?php echo $building; ?>(<span class="building" data-build-length="<?php echo $build_time; ?>" data-start-time="<?php echo $start_building_time; ?>"></span>)</li>
		</ul>
<?php
	}
?>
		<h3>Build</h3>
		<ul>
<?php
		for ($i = 0; $i < sizeof ($raw_buildings_available); $i++) {
			$building_name = explode (':', $raw_buildings_available[$i])[1];
			if (!in_array ($building_name, $json_base_data->buildings)) {
				echo "<li><a href='build.php?user=$user&amp;base=$base&amp;resource=$building_name&amp;x=$x&amp;y=$y'>$building_name</a></li>";
			}
		}
?>
		</ul><?php
	} ?>
		</pre>
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
				var time_left = start_build_time - current_time + build_length;

				document.getElementsByClassName ("building")[0].innerHTML = time_left;

				if (time_left <= 0) {
					location.reload();
				} else {
					setTimeout( function() {
						updateBuildTimer();
					}, 1000);
				}
			}
			updateBuildTimer();
		</script>
	</body>
</html>