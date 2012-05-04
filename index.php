<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<style type="text/css">
			html, body {
				min-height: 100%;
				min-width: 100%;
				margin: 0;
				padding: 0;
				overflow: hidden;
			}

			#template {
				display: none;
			}

			#header {
				position: relative;
				top: 0;
				width: 100%;
				text-align: center;
				height: 10%;
				min-height: 50px;
				background-image: url(separator.jpg);
				background-position: center bottom;
				background-repeat: no-repeat;
				padding-bottom: 140px;
				z-index: 3;
			}
			#header h1 {
				position: relative;
				display: inline-block;
				vertical-align: middle;
				top: 50%;
				margin: -0.5em 0 0;
			}

			.step {
				position: relative;
				min-height: 150px;
				text-align: center;
				border-top: 2px solid #ccc;
				z-index: 2;
			}
			.step.disabled {
				z-index: 0;
			}
			.step:first-child {
				border: 0;
			}

			.button {
				font-family: 'Yanone Kaffeesatz', arial, serif;
				font-size: 30px;
				color: #000;
				text-decoration: none;
				display: inline-block;
				width: 478px;
				height: 36px;
				padding: 10px;
				border: 2px solid #DDD;
				text-align: center;
				box-shadow: 1px 1px 0 #ddd;
				-moz-border-radius: 5px;
				-webkit-border-radius: 5px;
				-o-border-radius: 5px;
				border-radius: 5px;
				background: #FFF;
				background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#FFF), to(#EEE));
				background: -moz-linear-gradient(0% 90% 90deg, #EEE, #FFF);
				-webkit-transition: all .4s ease-in-out;
				-moz-transition: all .4s ease-in-out;
				-o-transition: all .4s ease-in-out;
				transition: all .4s ease-in-out;
				cursor: pointer;
			}
			.button img {
				vertical-align: middle;
				height: 35px;
			}
			.button.disabled {
				cursor: default;
				color: #000 !important;
				border-color: #AAA !important;
				background: #CCC !important;
				background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#CCC), to(#EEE)) !important;
				background: -moz-linear-gradient(0% 90% 90deg, #EEE, #CCC) !important;
			}
			.button:hover {
				color: #fff;
				border-color: #3278BE;
				background: #4195DD;
				background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#4195DD), to(#003C82));
				background: -moz-linear-gradient(0% 90% 90deg, #003C82, #4195DD);
			}
			.button:active {
				background: #4195DD;
				background: -webkit-gradient(linear, 0% 0%, 0% 100%, from(#003C82), to(#4195DD));
				background: -moz-linear-gradient(0% 90% 90deg, #4195DD, #003C82);
			}

			label {
				position: relative;
			}
			label input[type="file"] {
				opacity: 0;
				width: 100%;
				height: 100%;
				position: absolute;
				top: 0;
				left: 0;
			}

			#step_choice a:first-child {
				margin-right: 50px;
			}

			#step_linked_image .picture_url {
				width: 300px;
			}

			.upload_fields {
				display: none;
				margin-top: 10px;
			}

			#overlay {
				position: absolute;
				top: 0;
				left: 0;
				width: 100%;
				height: 100%;
				z-index: 1;
				opacity: 0.7;
				background-color: #FFF;
			}

			#previous {
				position: absolute;
				top: 0;
				display: none;
				z-index: 3;
				right: 10px;
				margin-top: 10px;
			}
		</style>
		<script src="jquery-1.7.1.min.js"></script>
		<script src="toolbox.js"></script>
		<script type="text/javascript">
			jQuery.fn.extend({
				scrollToBottom: function() {
					this.animate({ scrollTop: this.height() }, 3000);
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
					site: 'linked_image',
					//site: 'splited',
					//splited: function(params) {
					//	return params['splited'] ? 'splited_cover' : 'linked_image';
					//},
					linked_image_origin: function(params) {
						if(params['linked_image_origin'] === 'url') {
							var input = $('#timeline .step.active .picture_url').attr('disabled', null).attr('pattern', URL_PATTERN).val('').change();
							input.parent().show();
							input.focus();
						} else {
							// Only keep the filename and not the whole path
							var path = $('#timeline .step.active .picture_file').val();
							path = path.substr(path.lastIndexOf('/') + 1);
							path = path.substr(path.lastIndexOf('\\') + 1);
				
							// Fill the text field but disable it (it's only info not changeable)
							$('#timeline .step.active .picture_url').attr('disabled', 'disabled').attr('pattern', null).val(path).change().parent().show();
						}
						$("html,body").scrollToBottom();
					}
				};

				window.choose = function(field, value) {
					params[field] = value;
					if(steps[field]) {
						step($.isFunction(steps[field]) ? steps[field](params) : steps[field]);
					}
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
							var url = 'cover.php?';
							$.each(params, function(key, value) {
								url += encodeURIComponent(key)+'='+encodeURIComponent(value)+'&';
							});
							location.href = url;
						}
					};
		
					// if we choose url, we do not have to generate one
					if(params['origin'] === 'upload') {
						fuajax('#timeline .step.active form.upload_picture', function(url) {
							callback.apply(this, [url]);
						}.bind(this));
					}
					// else upload to an iframe and wait until it's loaded
					else {
						callback.apply(this, [$('#timeline .step.active input.picture_url').val()]);
						return false;
					}
				};

				// Workaround against FF bug with click on a input[type=file] label
				$(function() {
					if($.browser.mozilla) {
						$('label').live('click', function(event) {
							if(event.target === this) {
								$('#'+ $(this).attr('for')).extend($('input', this)).filter('[type="file"]').first().click();
							}
						})
					}
				});
			})(jQuery);
		</script>
	</head>
	<body>
		<div id="header"><h1>Clever Cover</h1></div>
		<div id="timeline">
			<div id="step_site" class="step active">
				<p class="description">CleverCover helps you to generate easily and fastly a funny and original cover. In order to start, choose which platform you want a cover for.</p>
				<div class="content">
					<a href="#" onclick="choose('site', 'google'); return false;"><img src="Google.png" alt="Google" title="Google" /></a>
					<a href="#" onclick="choose('site', 'facebook'); return false;"><img src="Facebook.png" alt="Facebook" title="Facebook" /></a>
				</div>
			</div>
		</div>
		<div id="previous"><a href="#" onclick="previousStep(); return false;">Previous</a></div>
		<div id="overlay"></div>
		<div id="templates">
			<script id="template_splited" type="template/html">
				<p class="description">What kind of cover do you want to do?</p>
				<div class="content">
					<span>
						<a class="button" href="#" onclick="choose('splited', false); return false;">
							Single picture
						</a>
					</span>
					<span>
						<a class="button disabled" href="#" onclick="choose('splited', true); return false;">
							Two pictures
						</a>
					</span>
				</div>
			</script>
			<script id="template_linked_image" type="template/html">
				<p class="description">Select the image you want in your cover and in your avatar</p>
				<div class="content">
					<form action="http://imageshack.us/redirect_api.php" class="upload_picture" enctype="multipart/form-data" method="post" onsubmit="callback('linked_image', event); return false;">
						<div class="select_images">
							<span>
								<label for="picture_url">
									<a class="button" onclick="choose('linked_image_origin', 'url'); return false;">
										<img src="Planet.png" /> Online picture
									</a>
								</label>
							</span>
							<span>
								<label class="button">
									<img src="Folder.png" /> Local picture
									<input type="file" class="picture_file" name="media" onchange="choose('linked_image_origin', 'upload'); return false;" />
								</label>
							</span>
						</div>
						<div class="upload_fields">
							<input type="text" class="picture_url" name="picture_url" placeholder="URL of the picture" required="required" />
							<input type="hidden" name="key" value="245BDFGU01bf81c0d51e350622385c569d4acc75" />
							<input type="hidden" name="success_url" value="http://<?php echo $_SERVER['SERVER_NAME'].dirname($_SERVER['SCRIPT_NAME']); ?>/imageshack.php?s=%s&b=%b&i=%i" />
							<p><input type="submit" class="picture_send" value="Generate your Cover"/></p>
						</div>
					</form>
				</div>
			</script>

		</div>
	</body>
</html>
