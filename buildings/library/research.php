<?php
	$user = $_REQUEST['user'];
	$research = $_REQUEST['research'];
	$user = $_REQUEST['user'];
	$base = $_REQUEST['base'];

	require '../../credis/Client.php';
	$redis = new Credis_Client('localhost');

	$base_data = json_decode ($redis->get("world:bases:$user"), true);

	$research_limit = $base_data["research"]["limit"];

	if (count ($base_data["research"]["queue"]) != $research_limit) {
		if (!in_array ($research, $base_data["technology"])) {
			$output_buffer = "Adding $research to the queue";
			$now = time();
			$new_build_item = json_decode ("{\"name\":\"$research\",\"start_time\":\"$now\"}", 'yes');

			array_push ($base_data["research"]["queue"], $new_build_item);

			$redis->set("world:bases:$user", json_encode($base_data));
		} else {
			$output_buffer .= "$research already researched!";
		}
	} else {
		$output_buffer = "No spots free in the queue<br />";

		foreach ($base_data["research"]["queue"] as $key => $queue_item) {
			$name = $queue_item["name"];
			$start_time = $queue_item["start_time"];

			$output_buffer .= "$key $name $start_time<br />";
		}
	}
?>
<html>
	<head>
		<title>Open Civilization Online - Library - Research</title>
		<meta http-equiv="refresh" content="1; url=../../building.php?user=<?php echo $user; ?>&amp;building=library">
	</head>
	<body>
		<?php echo $output_buffer; ?>
		<p>Please wait to be redirected back to your base . . .</p>
	</body>
</html>