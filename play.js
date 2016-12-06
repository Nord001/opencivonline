var fs = require('fs')
var http = require('http');
const PORT=8080; 

function handleRequest(request, response){
	http_buffer = "<html><body><style>.w { background-color: blue; } .l { background-color: green; } .m { background-color: grey; }</style><pre>"
    for (x = 0; x < max_x; x++) {
    	for (y = 0; y < max_y; y++) {
    		http_buffer += "<span class='" + map[x][y] + "'>&nbsp;</span>"
    	}
    	http_buffer += "<br/>"
    }
    http_buffer += "</pre></body></html>"

    response.end(http_buffer);
}
var server = http.createServer(handleRequest);

server.listen(PORT, function(){
    console.log("Server listening on: http://localhost:%s", PORT);
});

fs.readFile('test-world.map', function(err, data) {
    if(err) throw err;

    raw_map = data.toString().split("\n");
    max_x = raw_map[0]
    max_y = raw_map[1]

    map = new Array (max_x)

    for (x = 0; x < max_x; x++) {
    	map[x] = new Array (max_y)

    	for (y = 0; y < max_y; y++) {
    		map[x][y] = raw_map[x + 2].charAt(y)
    	}
    } 
})
