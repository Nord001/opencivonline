<?php
	require 'credis/Client.php';
	$redis = new Credis_Client('localhost');

	$user = $_REQUEST["user"];

	$raw_map = $redis->get('world.map');
	$raw_bases = json_decode ($redis->get('world.bases'), yes);

	// render map to array (maybe should be json)
	$raw_map_exploded = explode ("\n", $raw_map);
	$max_x = $raw_map_exploded[0];
	$max_y = $raw_map_exploded[1];
?>
<html>
	<head>
		<title>Open Civilization Online</title>
		<meta http-equiv="refresh" content="1; url=./?user=<?php echo $user; ?>">
	</head>
	<body>
<?php
	if (json_decode($redis->get("world.bases.$user")) == NULL) {
		$redis->set("world.bases.$user", "{\"buildings\":[],\"build-queue\":\"\"}");

		do {
			$proposed_x = rand(0, $max_x - 1);
			$proposed_y = rand(0, $max_y - 1);

			if ($raw_bases[$proposed_x][$proposed_y] == "") {
				$raw_bases[$proposed_x][$proposed_y] = $user;
			}
		} while ($raw_bases[$proposed_x][$proposed_y] != $user);


		// save new base to file
		$redis->set('world.bases', json_encode($raw_bases));

		echo "new user $user has been set up";
	} else {
		echo "User $user already exists";
	}
?>
		<p>Please wait to be redirected back to your base . . .</p>
	</body>
</html>