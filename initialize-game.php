<?php
	require 'credis/Client.php';
	$redis = new Credis_Client('localhost');

	$max_x = isset ($_REQUEST["max_x"]) ? $_REQUEST["max_x"] : 64;
	$max_y = isset ($_REQUEST["max_y"]) ? $_REQUEST["max_y"] : 64;

	$TILE_TYPES = [
		'W',	// Water
		'L',	// Land
		'M',	// Mountain
	];
	$TILE_TYPES_PROBABILITIES = [
		20,	// Water
		75,	// Land
		5,	// Mountain
	];

	// generate map
	echo "Generating map: ";
	$map_buffer  = "$max_x\n";
	$map_buffer .= "$max_y\n";
	
	for ($y = 0; $y < $max_y; $y++) {
		for ($x = 0; $x < $max_x; $x++) {
			$random_number = rand (0,99);

			$probabilities_counter = 0;
			foreach ($TILE_TYPES_PROBABILITIES as $index => $item) {
				$this_max_number = $item + $probabilities_counter;

				if ($random_number >= $probabilities_counter && $random_number < $this_max_number) {
					$tile_type_index = $index;
				}

				$probabilities_counter += $item;
			}

			$map_buffer .= $TILE_TYPES[$tile_type_index];
		}
		$map_buffer .= "\n";
	}
	echo "Done\n";

	echo "Building empty overlay: ";
	// build empty json overlay for maps
	$empty_json = "[";
	for ($y = 0; $y < $max_y; $y++) {
		$empty_json .= "[";
		for ($x = 0; $x < $max_x; $x++) {
			$empty_json .= "\\\"\\\"";
			if ($x < $max_x - 1) {
				$empty_json .= ",";
			}
		}
		$empty_json .= "]";
		if ($y < $max_y - 1) {
			$empty_json .= ",";
		}
	}
	$empty_json .= "]";
	echo "Done\n";

	echo "Saving map: ";
	$redis->set ("world:map", $map_buffer);
	echo "Done\n";

	// load empty overlays
	echo "Saving overlays: ";
	$redis->set ("world:bases", $empty_json);
	$redis->set ("world:resources", $empty_json);
	echo "Done\n";

	// generate buildings
	echo "Saving buildings: ";
	$redis->set ("buildings:library", "{\"time\":5}");
	$redis->set ("buildings:barracks", "{\"time\":10}");

	// generate technology
	$redis->set ("technology:agriculture", "{\"time\":10}");

	// generate units
	$redis->set ("unit:scout", "{\"time\":15,\"moves\":3,\"attack\": 0,\"defence\": 0,\"actions\":[],\"required_technology\":[]}");
	$redis->set ("unit:worker", "{\"time\":15,\"moves\":3,\"attack\": 0,\"defence\": 0,\"actions\":[\"farm\"],\"required_technology\":[\"agriculture\"]}");

	echo "Done\n";
