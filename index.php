<?php
	/*
		This is a supposed to scratch my Instagram itch.
		I want a way to constantly saving my posts, both
		the images and the captions, to a location that
		I control.
	*/

	//enable error reporting
	error_reporting(E_ALL);
	ini_set('display_errors', '1');	

	require_once('instagram.php');
	
	$api = new instagram();

?>

<!DOCTYPE html />

<html>

<head>
	<meta charset="utf-8" />
	<title>SaveAGram</title>
	<link rel="stylesheet" href="style.css" />
</head>

<body>
	<h1>My posts</h1>
	<ul id="instagram">
		<?php
			$posts = $api->getFullFeed();

			// Now we have an array of objects
			foreach ($posts as $post) {
				echo '<li class="filter-' . str_replace(' ', '_', $post->filter) . '">';
					// Find out whether the file is already cached or not
					if (file_exists('cached_images/' . $post->created_time . '.jpg')) {
						// The file IS cached
						echo '<img src="' . 'cached_images/' . $post->created_time . '.jpg' . '" />';
					} else {
						// The file is NOT cached
						echo '<img src="' . $post->images->standard_resolution->url . '" />';
						$api->saveImage($post->images->standard_resolution->url, 'cached_images/' . $post->created_time . '.jpg');
					}
				if (isset($post->caption->text )) {
					echo '<p>' . $post->caption->text . '</p>';
				}
				$posted_time = $post->created_time;
				echo '<p class="posted_time">' . date('j/n Y', $posted_time) . '</p>';
				echo '</li>';
			}

		?>
	</ul>
</body>

</html>

