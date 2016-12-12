<?php
	require 'credis/Client.php';
	$redis = new Credis_Client('localhost');


	for (;;) {
		$raw_bases = $redis->keys('world.bases.*');
		
		foreach ($raw_bases as $base) {
			$base_name = str_replace ("world.bases.", "", $base);

			$base_data = json_decode($redis->get ("world.bases.$base_name"), 'yes');

			if ($base_data["build-queue"] != "") {
				$exploded_build_queue = explode ('|', $base_data["build-queue"]);
				$resource = $exploded_build_queue[0];
				$start_time = $exploded_build_queue[1];
				$now = time();
				$build_diff = $now - $start_time;
				$build_time = json_decode ($redis->get("buildings:$resource"))->time;

				if ($build_diff > $build_time) {
					echo "[" . time() . "] $resource finished for $base_name\n";
					array_push ($base_data["buildings"], $resource);

					$base_data["build-queue"] = "";
					$redis->set("world.bases.$base_name", json_encode($base_data));
				}
			}
		}
		sleep (1);
	}
