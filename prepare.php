<!DOCTYPE html>
<!--
This script can generate from a global picture two parts in order to be used as cover and profile picture in Facebook.
----------------------------------------------------------------------------
"THE BEER-WARE LICENSE" (Revision 42):
<jeremy.lemesle@korko.fr> wrote this file. As long as you retain this notice you
can do whatever you want with this stuff. If we meet some day, and you think
this stuff is worth it, you can buy me a beer in return. Jeremy Lemesle
----------------------------------------------------------------------------
-->
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<link rel="stylesheet" href="media/css/style.css" />
		<link rel="stylesheet" href="media/css/prepare_style.css" />
		<script src="media/js/jquery-1.7.1.min.js"></script>
		<script src="media/js/jquery.support.js"></script>
		<script src="media/js/toolbox.js"></script>
		<script src="media/js/prepare_script.js"></script>
		<title>CleverCover - Prepare</title>
	</head>
	<body>
		<div id="header"><h1>Clever Cover</h1></div>
		<div id="timeline">
			<div id="step_site" class="step active">
				<p class="description">CleverCover helps you to generate easily and fastly a funny and original cover. In order to start, choose which platform you want a cover for.</p>
				<div class="content">
					<a href="#" onclick="choose('site', 'google', this); return false;"><img src="media/image/Google.png" alt="Google" title="Google" /></a>
					<a href="#" onclick="choose('site', 'facebook', this); return false;"><img src="media/image/Facebook.png" alt="Facebook" title="Facebook" /></a>
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
						<a class="button" href="#" onclick="choose('splited', 0, this); return false;">
							Single picture
						</a>
					</span>
					<span>
						<a class="button disabled" href="#" onclick="choose('splited', 1, this); return false;">
							Two pictures
						</a>
					</span>
				</div>
			</script>
			<script id="template_special" type="template/html">
				<p class="description">What kind of profile do you have?</p>
				<div class="content">
					<span>
						<a class="button" href="#" onclick="choose('special', 0, this); return false;">
							Classical (User)
						</a>
					</span>
					<span>
						<a class="button" href="#" onclick="choose('special', 1, this); return false;">
							Page
						</a>
					</span>
				</div>
			</script>
			<script id="template_linked_image" type="template/html">
				<p class="description">Select the image you want in your cover and in your avatar</p>
				<div class="content">
					<form action="http://imageshack.us/redirect_api.php" class="upload_picture" enctype="multipart/form-data" method="post" onsubmit="return callback('linked_image', event);">
						<div class="select_images">
							<span>
								<label for="picture_url">
									<a class="button" onclick="choose('linked_image_origin', 'url'); return false;">
										<img src="media/image/Planet.png" /> Online picture
									</a>
								</label>
							</span>
							<span>
								<label class="button">
									<img src="media/image/Folder.png" /> Local picture
									<input type="file" class="picture_file" name="media" onchange="choose('linked_image_origin', 'upload'); return false;" accept="image/*"/>
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
