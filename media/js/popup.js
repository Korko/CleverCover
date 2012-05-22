// Popup management
var popup = (function($) {
	var force_close = false;

	return {
		close : function() {
			$('#popup_content').removeClass();
			if($('#popup').is(':visible')) {
				$('#overlay').animate({
					opacity : 0
				}, function() {
					$(this).hide();
				});
				$('#popup').animate({
					opacity : 0
				}, function() {
					$(this).hide().css('opacity', 1);
				});
			} else {
				force_close = true;
			}
		},
		load : function(id, keep) {
			if(keep) {
				var old = $('#popup_content').attr('id', 'popup_content_' + rd(999)).addClass('hidden');
				$('#popup').append('<div id="popup_content"></div>');
			}
			this.content($('#' + id).html());
			return old ? old[0] : undefined;
		},
		content : function(html, closable, className) {
			var popup = $('#popup'), content = $('#popup_content');

			force_close = false;

			if(closable === false) {
				popup.removeClass('closable');
			} else {
				popup.addClass('closable');
			}

			content.removeClass().addClass(className);
			if(!popup.is(':visible')) {
				$('#overlay').css({
					height : $(document).height(),
					width : $(document).width(),
					opacity : 0
				}).show().animate({
					opacity : 0.5
				});
			}

			content.animate({
				opacity : 0,
			}, function() {
				// compute size
				content.html(html).css({
					position : 'absolute',
					top : '-10000px',
					width : 'auto',
					height : 'auto'
				}).show();

				// resize
				popup.show().animate({
					height : content.height(),
					width : content.width()
				}, function() {
					// fullfill
					content.hide().css({
						position : 'relative',
						top : 0,
						width : '100%'
					}).show().animate({
						opacity : 1
					}, function() {
						force_close && this.close();
					}.bind(this));
				}.bind(this));
			}.bind(this));
		}
	};
})(jQuery);