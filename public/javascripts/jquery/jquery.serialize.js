function serialize(mixed_value) {
	var _getType = function( inp ) {
		var type = typeof inp, match;
		var key;
		if (type == 'object' && !inp) return 'null';
		if (type == "object") {
			if (!inp.constructor) return 'object';
			var cons = inp.constructor.toString();
			match = cons.match(/(\w+)\(/);
			if (match) cons = match[1].toLowerCase();
			var types = ["boolean", "number", "string", "array"];
			for (key in types) {
				if (cons == types[key]) {
					type = types[key];
					break;
				}
			}
		}
		return type;
	};
	var type = _getType(mixed_value);
	var val, ktype = '';
	switch (type) {
		case "function":
			val = "";
			break;
		case "boolean":
			val = "b:" + (mixed_value ? "1" : "0");
			break;
		case "number":
			val = (Math.round(mixed_value) == mixed_value ? "i" : "d") + ":" + mixed_value;
			break;
		case "string":
			val = "s:" + encodeURIComponent(mixed_value).replace(/%../g, 'x').length + ":\"" + mixed_value + "\"";
			break;
		case "array":
		case "object":
			val = "a";
			var count = 0;
			var vals = "";
			var okey;
			var key;
			for (key in mixed_value) {
				ktype = _getType(mixed_value[key]);
				if (ktype == "function") continue;
				okey = (key.match(/^[0-9]+$/) ? parseInt(key, 10) : key);
				vals += serialize(okey) +
				serialize(mixed_value[key]);
				count++;
			}
			val += ":" + count + ":{" + vals + "}";
			break;
		case "undefined": 
		default:
			val = "N";
			break;
	}
	if (type != "object" && type != "array") val += ";";
	return val;
}