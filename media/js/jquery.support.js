jQuery.extend(jQuery.support, {
	canvas: (function() {
		var test_canvas = document.createElement("canvas");
		return (test_canvas.getContext) ? true : false;
	})(),
	input_slide: (function() {
                var test_input = document.createElement("input");
                test_input.setAttribute("type", "range");
                return !(test_input.type === "text");
	})()
});
