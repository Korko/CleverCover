<?php

// Sanitize params
function filter_enum($var, $enum, $default = null) {
	return in_array($var, $enum) ? $var : $default;
}

$site = filter_enum($_GET['site'], array('facebook', 'google'), 'facebook');
$cover = filter_var($_GET['cover'], FILTER_VALIDATE_URL) or die('Invalid cover url');
$splited = (isset($_GET['splited']) && $_GET['splited']);

?>
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
		<link rel="stylesheet" href="media/css/cover_style.css" />
		<script src="media/js/jquery-1.7.1.min.js"></script>
		<script src="media/js/jquery.support.js"></script>
		<script src="media/js/jquery.drag.js"></script>
		<script src="media/js/toolbox.js"></script>
		<script src="media/js/popup.js"></script>
		<script src="media/js/cover_script.js"></script>
		<title>CleverCover</title>
	</head>
	<body>
		<div id="bluebar">
			<div id="save" class="button">Save</div>
		</div>
		<div id="content">
			<div id="content_inner">
				<canvas id="canvas_cover"></canvas>
				<canvas id="canvas_picture"></canvas>
			</div>
			<div>
				<div id="cover_slider_choice">
					<label><input type="radio" name="cover_slider_choice" value="cover" checked="checked" />Cover</label>
					<label><input type="radio" name="cover_slider_choice" value="avatar" />Avatar</label>
				</div>
				<div id="cover_slider"></div>
				<div id="cover_flip"><label>Reverse? <input type="checkbox" name="flip" /></div>
			</div>
		</div>
		<div id="popup">
			<div id="popup_header"><a href="#" onclick="popup.close(); return false;">X</a></div>
			<h1>CleverCover</h1><div id="popup_content"></div>
		</div>
		<div id="overlay"></div>

		<script type="text/javascript">
			jQuery(document).ready(function() {
				popup.content('<p>Preparing your cover... It may take a while.', false);
				cleverCover.init('<?= $site ?>', '<?= $cover ?>', null, function(success) {
					popup.close();
				});
			});
		</script>
	</body>
</html>
