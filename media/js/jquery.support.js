jQuery.extend(jQuery.support, {
	canvas: (function() {
		var test_canvas = document.createElement("canvas");
		return (test_canvas.getContext) ? true : false;
	})()
});
