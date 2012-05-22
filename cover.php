<?php

// Sanitize params
function filter_enum($var, $enum, $default = null) {
	return in_array($var, $enum) ? $var : $default;
}

$site = filter_enum($_GET['site'], array('facebook', 'google'), 'facebook');
$cover = filter_var($_GET['cover'], FILTER_VALIDATE_URL) or die('Invalid cover url');
$splited = (isset($_GET['splited']) && $_GET['splited']);

$siteUrl = 'http://www.korko.fr/clevercover/';

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
		<script type="text/javascript" src="media/js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src="media/js/jquery.support.js"></script>
		<script type="text/javascript" src="media/js/jquery.drag.js"></script>
		<script type="text/javascript" src="media/js/toolbox.js"></script>
		<script type="text/javascript" src="media/js/popup.js"></script>
		<script type="text/javascript" src="media/js/cover_script.js"></script>
		<title>CleverCover</title>
	</head>
	<body>
		<div id="bluebar">
			<div class="social-network facebook">
				<div id="fb-root"></div>
				<div class="fb-like" data-href="<?= $siteUrl ?>" data-send="false" data-layout="button_count" data-width="100" data-show-faces="false"></div>
				<script type="text/javascript">asyncjs('http://connect.facebook.net/fr_FR/all.js#xfbml=1', 'facebook-jssdk');</script>
			</div>
			<div class="social-network google">
				<div class="g-plusone" data-annotation="none" data-href="<?= $siteUrl ?>"></div>
				<script type="text/javascript">asyncjs('https://apis.google.com/js/plusone.js', 'twitter-wjs');</script>
			</div>
			<div class="social-network twitter">
				<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?= $siteUrl ?>" data-text="CleverCover" data-via="korkof" data-related="korkof">Tweet</a>
				<script type="text/javascript">asyncjs('http://platform.twitter.com/widgets.js', 'twitter-wjs');</script>
			</div>
			<div class="social-network stumble">
				<su:badge layout="2" location="<?= $siteUrl ?>"></su:badge>
				<script type="text/javascript">asyncjs('https://platform.stumbleupon.com/1/widgets.js');</script>
			</div>

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

		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-22420319-1']);
			_gaq.push(['_setDomainName', 'korko.fr']);
			_gaq.push(['_trackPageview']);

			asyncjs('http://www.google-analytics.com/ga.js');
		</script>
	</body>
</html>
