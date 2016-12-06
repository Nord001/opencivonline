var MAX_X = 128
var MAX_Y = 128
var TILE_TYPES = [
	'W',	// Water
	'L',	// Land
	'M',	// Mountain
]
var TILE_TYPES_PROBABILITIES = [
	20,	// Water
	75,	// Land
	5,	// Mountain
]

console.log (MAX_X)
console.log (MAX_Y)

for (y = 0; y < MAX_Y; y++) {
	for (x = 0; x < MAX_X; x++) {
		random_number = Math.floor(Math.random() * 100)

		probabilities_counter = 0
		TILE_TYPES_PROBABILITIES.forEach (function (item, index) {
			this_max_number = item + probabilities_counter

			if (random_number >= probabilities_counter && random_number < this_max_number) {
				tile_type_index = index
			}

			probabilities_counter += item
		})

		process.stdout.write(TILE_TYPES[tile_type_index])
	}
	console.log ()
}