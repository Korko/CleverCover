<?php

$css = array(
	'style',
	'prepare_style'
);
$js = array(
	'jquery-1.7.1.min',
	'jquery.support',
	'toolbox',
	'prepare_script'
);

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
		<meta property="og:url" content="http://www.korko.fr/clevercover/?utm_source=fb&utm_medium=share" />
		<meta property="og:title" content="CleverCover" />
		<meta property="og:site_name" content="CleverCover" />
		<meta property="og:image" content="http://korko.fr/clevercover/media/image/example_facebook.png" />

		<link rel="stylesheet" href="media/css/style.css" />
		<link rel="stylesheet" href="media/css/prepare_style.css" />
<?php
		foreach($css as $style) {
			$path = 'media/css/'.$style.'.css';
			$stats = stat($path);
			echo '<link rel="stylesheet" href="'.$path.'?mtime='.($stats['mtime']).'" />';
		}
		foreach($js as $script) {
			$path = 'media/js/'.$script.'.js';
			$stats = stat($path);
			echo '<script type="text/javascript" src="'.$path.'?mtime='.($stats['mtime']).'"></script>';
		}
?>
		<title>CleverCover - Prepare</title>
	</head>
	<body>
		<div id="header">
			<h1>Clever Cover</h1>
		</div>
		<div id="examples">
			<img src="media/image/example_google.png" title="Example of what CleverCover will help you realize" class="thumb" />
			<img src="media/image/example_facebook.png" title="Example of what CleverCover will help you realize" class="thumb" />
		</div>
		<div id="timeline">
			<div id="step_site" class="step active">
				<p class="description">CleverCover helps you to generate easily and fastly a funny and original cover. In order to start, choose which platform you want a cover for.</p>
				<div class="content">
					<a href="#" data-value="google"><img src="media/image/Google.png" alt="Google" title="Google" /></a>
					<a href="#" data-value="facebook"><img src="media/image/Facebook.png" alt="Facebook" title="Facebook" /></a>
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
						<a class="button" href="#" data-value="0">
							Single picture
						</a>
					</span>
					<span>
						<a class="button disabled" href="#" data-value="1">
							Two pictures
						</a>
					</span>
				</div>
			</script>
			<script id="template_special" type="template/html">
				<p class="description">What kind of profile do you have?</p>
				<div class="content">
					<span>
						<a class="button" href="#" data-value="0">
							Classical (User)
						</a>
					</span>
					<span>
						<a class="button" href="#" data-value="1">
							Page
						</a>
					</span>
				</div>
			</script>
			<script id="template_linked_image_origin" type="template/html">
				<p class="description">Select the image you want in your cover and in your avatar</p>
				<div class="content">
					<form action="img.php" class="upload_picture" enctype="multipart/form-data" method="post" onsubmit="return callback('linked_image', event);">
						<div class="select_images">
							<span>
								<label for="picture_url">
									<a class="button" data-value="url">
										<img src="media/image/Planet.png" /> Online picture
									</a>
								</label>
							</span>
							<span>
								<label class="button">
									<img src="media/image/Folder.png" /> Local picture
									<input type="file" class="picture_file" name="img" data-value="upload" accept="image/*" />
								</label>
							</span>
						</div>
						<div class="upload_fields">
							<input type="text" class="picture_url" name="picture_url" placeholder="URL of the picture" required="required" />
							<p><input type="submit" class="picture_send" value="Generate your Cover" /></p>
						</div>
					</form>
				</div>
			</script>
		</div>

		<script type="text/javascript">
			var _gaq = _gaq || [];
			_gaq.push(['_setAccount', 'UA-22420319-1']);
			_gaq.push(['_setDomainName', 'korko.fr']);
			_gaq.push(['_trackPageview']);

			asyncjs('http://www.google-analytics.com/ga.js');
		</script>
		<script type="text/javascript">
			var on = function() {
				var step = jQuery(this).parents('.step').prop('id');
				step = /step_(.+)/.exec(step)[1];
				choose(step, jQuery(this).data('value'));
			};
			jQuery(document)
				.on('click', '.step.active a[data-value]', on)
				.on('change', '.step.active input[data-value]', on);

			<?php
				function jsescape($str) { return str_replace("'", "\'", $str); };
				foreach($_GET as $choice => $value) {
					echo "choose('".jsescape($choice)."', '".jsescape($value)."');\n";
				}
			?>
		</script>
	</body>
</html>
