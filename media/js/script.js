var prepare_cover = (function($) {

	var image_type = null, cover_type, cover_image, splited = false;

	return {
		choose_upload : function() {
			image_type = 'upload';

			// Only keep the filename and not the whole path
			var path = $('#picture_file').val();
			path = path.substr(path.lastIndexOf('/') + 1);
			path = path.substr(path.lastIndexOf('\\') + 1);

			// Fill the text field but disable it (it's only info not changeable)
			$('#picture_url').attr('disabled', 'disabled').attr('pattern', null).val(path).show().change();
		},
		choose_url : function() {
			image_type = 'url';

			// Enable the text field and add the browser's check on pattern
			$('#picture_url').attr('disabled', null).attr('pattern', URL_PATTERN).val('').show().focus().change();
		},
		check : function() {
			// Protect the regex (/ is a special character in JS regex)
			var regex = $('#picture_url').attr('pattern') ? $('#picture_url').attr('pattern').replace(/\//, '\/') : null;

			// If ok, enable the button to submit else, disable it
			if($('#picture_url').val() && (!regex || $('#picture_url').val().match(new RegExp(regex)))) {
				$('#picture_send').attr('disabled', null);
			} else {
				$('#picture_send').attr('disabled', 'disabled');
			}
		},
		choose_type : function(type) {
			cover_type = type;
		},
		choose_picture : function(type, event) {
			if(!lock('submit_'+type, 10)) {
				return false;
			}

			var callback = splited ? function(url) {
				if (cover_image) {
					this.init(cover_type, cover_image, url);	
				} else {
					cover_image = url;
				}
			} : function(url) {
				this.init(cover_type, url);
			};

			var oldContent = popup.load('popup_loading', true);

			// if we choose url, we do not have to generate one
			if(image_type === 'upload') {
				fuajax('upload_picture', function(url) {
					callback.apply(this, [url]);
					oldContent.parentNode.removeChild(oldContent);
				}.bind(this));
			}
			// else upload to an iframe and wait until it's loaded
			else {
				callback.apply(this, [$('#picture_url').val()]);
				return false;
			}
		},
		init : function(type, url_cover, url_avatar) {
			cleverCover.init(type, url_cover, url_avatar, function(success) {
				if(success) {
					popup.close();
				} else {
					popup.load('popup_single');
				}
			});
		}
	};
})(jQuery);
