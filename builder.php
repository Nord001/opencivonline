<?php
	require 'credis/Client.php';
	$redis = new Credis_Client('localhost');


	for (;;) {
		$raw_bases = $redis->keys('world:bases:*');
		
		foreach ($raw_bases as $base) {
			$base_name = str_replace ("world:bases:", "", $base);
			$base_data = json_decode($redis->get ("world:bases:$base_name"), 'yes');
			$now = time();

			if ($base_data["build-queue"] != "") {
				$exploded_build_queue = explode ('|', $base_data["build-queue"]);
				$resource = $exploded_build_queue[0];
				$start_time = $exploded_build_queue[1];
				$build_diff = $now - $start_time;
				$build_time = json_decode ($redis->get("buildings:$resource"))->time;

				if ($build_diff > $build_time) {
					echo "[$now] $resource finished for $base_name\n";
					array_push ($base_data["buildings"], $resource);

					$base_data["build-queue"] = "";
					$redis->set("world:bases:$base_name", json_encode($base_data));
				}
			}

			if (count ($base_data["research"]["queue"]) > 0) {
				foreach ($base_data["research"]["queue"] as $key => $queue_item) {
					$name = $queue_item["name"];
					$start_time = $queue_item["start_time"];

					$duration = json_decode ($redis->get("technology:$name"), 'yes')["time"];

					$time_left = ($start_time + $duration) - $now;

					if ($time_left <= 0) {
						echo "[$now] $name finished for $base_name\n";
						unset ($base_data["research"]["queue"]["$key"]);
						array_push ($base_data["technology"], "$name");
						$redis->set("world:bases:$base_name", json_encode($base_data));
					}
				}
			}
		}
		sleep (1);
	}
