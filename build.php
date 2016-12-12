<?php
	$user = $_REQUEST['user'];
	$resource = $_REQUEST['resource'];
	$x = $_REQUEST['x'];
	$y = $_REQUEST['y'];
	$user = $_REQUEST['user'];
	$base = $_REQUEST['base'];

	require 'credis/Client.php';
	$redis = new Credis_Client('localhost');

	$raw_bases = $redis->get("world.bases");
	$base_data = json_decode ($redis->get("world.bases.$user"), true);

	$build_queue_is_empty = $base_data["build-queue"] == "";
	$already_have_building = in_array ($resource, $base_data["buildings"]);

	if ($build_queue_is_empty) {
		if (!$already_have_building) {
			$base_data["build-queue"] = "$resource|".time();
			$redis->set("world.bases.$user", json_encode ($base_data));
		}
	}
?>
<html>
	<head>
		<title>Open Civilization Online</title>
		<meta http-equiv="refresh" content="1; url=./base.php?user=<?php echo $user; ?>&amp;base=<?php echo $user; ?>">
	</head>
	<body>
		<p>Item added to build queue.</p>
		<p>Please wait to be redirected back to your base . . .</p>
	</body>
</html>