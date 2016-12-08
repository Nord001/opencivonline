<html>
	<head>
		<title>Open Civilization Online</title>
		<link href="styles.css" rel="stylesheet" />
	</head>
	<body>
		<pre>
<?php
	// read in map file
	$myfile = fopen("test-world.map", "r") or die("Unable to open file!");
	$raw_map = fread($myfile,filesize("test-world.map"));
	fclose($myfile);

	// read in bases overlay
	$myfile = fopen("test-world.bases", "r") or die("Unable to open file!");
	$raw_bases = json_decode (fread($myfile,filesize("test-world.bases")), true);
	fclose($myfile);

	// read in resources overlay
	$myfile = fopen("test-world.resources", "r") or die("Unable to open file!");
	$raw_resources = json_decode (fread($myfile,filesize("test-world.resources")), true);
	fclose($myfile);

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

	// if user is new and has no base give them one
	if (!$own_base_exists) {
		do {
			$proposed_x = rand(0, $max_x - 1);
			$proposed_y = rand(0, $max_y - 1);

			if ($raw_bases[$proposed_x][$proposed_y] == "") {
				$raw_bases[$proposed_x][$proposed_y] = $user;
			}
		} while ($raw_bases[$proposed_x][$proposed_y] != $user);

		// save new base to file
		$fp = fopen('test-world.bases', 'w');
		fwrite($fp, json_encode($raw_bases));
		fclose($fp);
	}

	if (isset ($user)) {
		echo "User: $user";
	} else {
		echo "Watching mode";
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