/**

 * Make an element draggable

 * @param string id Id of the div

 * @param fn callback Callback called each time element is dragged with 4 params:

 *		{x: <current mouse x>, y: <current mouse y>, dx: <x delta since last call>, dy: <y delta since last call>}

 */

(function() {
	var draggedCallback = null;
	var mousePos = null;

	jQuery(document).bind('mouseup', function() {
		jQuery(document.body).removeClass('unselectable');
		draggedCallback = null;
		mousePos = null;
	});

	jQuery(document).bind('mousemove', function(event) {
		if(!draggedCallback)
			return;

		// Chrome utilise event.x/event.y
		// Firefox utilise event.clientX/event.clientY
		var newPos = {
			x : event.x || event.clientX,
			y : event.y || event.clientY
		};

		if(mousePos) {(draggedCallback || noop)({
				x : newPos.x,
				y : newPos.y,
				dx : newPos.x - mousePos.x,
				dy : newPos.y - mousePos.y
			});
		}
		mousePos = newPos;
	});

	jQuery.fn.extend({
		drag : function(callback) {
			return this.bind('mousedown.drag', function() {
				jQuery(document.body).addClass('unselectable');
				draggedCallback = callback;
			});
		},
		undrag : function() {
			return this.unbind('.drag');
		}
	});
})();
