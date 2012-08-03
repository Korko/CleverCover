jQuery.fn.extend({
	scrollToBottom: function() {
		this.animate({ scrollTop: this.height() }, 'fast');
	}
});

(function($) {
	var params = {};

	function finalize() {
		if($('#timeline .step').length > 1) {
			$('#previous').show().css('top', $('#timeline .step.active').offset().top);
		} else {
			$('#previous').hide();
		}

		$('#overlay').css({
			width: $('html,body').width(),
			height: $('html,body').height()
		});
		$("html,body").scrollToBottom();
	}

	window.step = function(nextStep) {
		if(!$('#template_'+nextStep).length) {
			return;
		}

		$('#timeline .step.active').nextAll().remove();
		$('#timeline .step.active').removeClass('active').addClass('disabled');

		var step = $('<div class="step active" id="step_'+nextStep+'"></div>').hide().html($('#template_'+nextStep).html());
		$('#timeline').append(step);
		step.slideDown('slow');

		finalize();
	};
	window.previousStep = function() {
		$('#timeline .step.active').slideUp(function(){
			$(this).remove();
			$('#timeline .step:last-child').removeClass('disabled').addClass('active');

			finalize();
		});
	};

	var steps = {
		site: function(params) {
			return params['site'] === 'facebook' ? 'special' : 'linked_image_origin';
		},
		special: 'linked_image_origin', 
		//splited: function(params) {
		//	return params['splited'] ? 'splited_cover' : 'linked_image_origin';
		//},
		linked_image_origin: function(params) {
			if(params['linked_image_origin'] === 'url') {
				var input = $('#timeline .step.active .picture_url').prop('disabled', null).prop('pattern', URL_PATTERN).val('').change();
				input.parent().show();
				input.focus();
			} else {
				// Only keep the filename and not the whole path
				var path = $('#timeline .step.active .picture_file').val();
				path = path.substr(path.lastIndexOf('/') + 1);
				path = path.substr(path.lastIndexOf('\\') + 1);

				// Fill the text field but disable it (it's only info not changeable)
				$('#timeline .step.active .picture_url').prop('disabled', true).prop('pattern', null).val(path).change().parent().show();
			}
			$("html,body").scrollToBottom();
		}
	};

	window.choose = function(field, value) {
		var $button = $('#step_'+field+' a[data-value="'+value+'"]');
		if($button.length === 0) {
			return;
		}

		params[field] = value;
		if(steps[field]) {
			step($.isFunction(steps[field]) ? steps[field](params) : steps[field]);
		}

		$button.parents('.step').find('.highlight').removeClass('highlight');
		$button.addClass('highlight');
	};

	window.callback = function(field, event) {
		if(!lock('submit_'+field, 10)) {
			return false;
		}

		var callback = function(url) {
			if (!params['cover'] || !params['splited']) {
				params['cover'] = url;
			} else {
				params['avatar'] = url;
			}

			if (!params['splited'] || (params['splited'] && params['avatar'])) {
				var form = c('form', {
					attributes: {
						method: "post",
						action: "cover.php"
					}
				});
				$.each(params, function(key, value) {
					form.appendChild(c('input', {
						attributes: {
							type: 'hidden',
							name: key,
							value: value
						}
					}));
				});
				form.submit();
			}
		};

		// if we choose upload, send to an iframe and wait until it's loaded
		if(params[field+'_origin'] === 'upload') {
			fuajax(jQuery('#timeline .step.active form.upload_picture')[0], function(url) {
				callback.apply(this, [url]);
			}.bind(this));
		}
		// else we do not have to generate one
		else {
			$.ajax('img.php?url='+$('#timeline .step.active input.picture_url').val(), {
				success: function(data) {
					callback.apply(this, [window.location.protocol+'//'+window.location.host+data]);
				}
			});
			return false;
		}
	};

	// Workaround against FF bug with click on a input[type=file] label
	$(function() {
		if($.browser.mozilla) {
			$('label').live('click', function(event) {
				if(event.target === this) {
					$('#'+ $(this).prop('for')).extend($('input', this)).filter('[type="file"]').first().click();
				}
			})
		}
	});
})(jQuery);

if(!jQuery.support.canvas) {
	alert("I'm sorry but your browser seems not to be able to support CleverCover. Please use Chrome or Firefox instead. Thanks.");
	throw "exit";
}
