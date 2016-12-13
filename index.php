<html>
	<head>
		<title>Open Civilization Online</title>
		<link href="styles.css" rel="stylesheet" />
	</head>
	<body>
		<pre>
<?php
	require 'credis/Client.php';
	$redis = new Credis_Client('localhost');

	$raw_map = $redis->get('world:map');
	$raw_bases = json_decode ($redis->get('world:bases'), yes);
	$raw_resources = json_decode ($redis->get('world:resources'));

	// render map to array (maybe should be json)
	$raw_map_exploded = explode ("\n", $raw_map);
	$max_x = $raw_map_exploded[0];
	$max_y = $raw_map_exploded[1];

	$map = array ();

	$user = $_REQUEST['user'];
	$offset = 2;

	for ($y = $offset; $y < $max_y; $y++) {
		$cur_y = $y - $offset;
		$map[$cur_y] = array ();
		for ($x = 0; $x < $max_x; $x++) {
			$map[$cur_y][$x] = $raw_map_exploded[$y]{$x};
		}
	}

	$own_base_exists = false;

	// look for users base
	for ($x = 0; $x < $max_x - 2; $x++) {
		for ($y = 0; $y < $max_y; $y++) {
			$base_name = $raw_bases[$x][$y];

			if ($user === $base_name) $own_base_exists = true;
		}
	}

	if (isset ($user)) {
		echo "User: $user";
	} else {
		echo "<script>location.href='./signup.php'</script>";
	}
	echo "</br>";

	// draw out map
	for ($x = 0; $x < $max_x - 2; $x++) {
		for ($y = 0; $y < $max_y; $y++) {
			$tile_code = $map[$x][$y];
			$resource_id = $raw_resources[$x][$y];
			$is_a_resource = $resource_id != "";
			switch ($resource_id) {
				case 'I':
					$resource_name = "Iron";
					break;
				case 'W':
					$resource_name = "Wood";
					break;
			}

			$base_name = $raw_bases[$x][$y];
			$is_a_base = $base_name != "";
			$is_own_base = false;
			if ($user === $base_name) {
				$is_own_base = true;
				$own_base_exists = true;
			}

			if ($is_a_base) {
				echo "<span class='tile $tile_code base " . ($is_own_base ? "ownbase" : "") . "' title='$base_name " . ($is_own_base ? "(ownbase)" : "") . "'><a href='base.php?user=$user&amp;base=$base_name'>B</a></span>";
			} else if ($is_a_resource) {
				echo "<span class='tile $tile_code resource $resource_id' title='$resource_name'>$resource_id</span>";
			} else {
				echo "<span class='tile $tile_code'>&nbsp;</span>";
			}
		}

		echo "<br />";
	}
?>
		</pre>
	</body>
</html>