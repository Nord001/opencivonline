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

	// read in map and associated data
	$raw_map = $redis->get('world.map');
	$raw_bases = json_decode ($redis->get('world.bases'));
	$raw_resources = json_decode ($redis->get('world.resources'));

	// render map to array (maybe should be json)
	$raw_map_exploded = explode ("\n", $raw_map);
	$max_x = $raw_map_exploded[0];
	$max_y = $raw_map_exploded[1];

	$map = array ();

	$user = $_REQUEST['user'];
	$base = $_REQUEST['base'];
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
			if ($raw_bases[$x][$y] === $base) {
				$base_x = $x;
				$base_y = $y;
			}
		}
	}

	echo "<a href='./?user=$user'>&lt; back</a><br />";
	echo "User: $user<br />";
	echo "Base: $base<br />";
	echo "location: x: $base_x; y: $base_y<br />"; 

	if ($base === $user) {
		?>
		<h2>Your Base</h2>
		<h3>Buildings</h3>
		<h4>Build</h4>
		<ul>
			<li>Library</li>
		</ul>
		<?php
	}
?>
		</pre>
	</body>
</html>