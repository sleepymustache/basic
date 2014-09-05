<?php
namespace Mobile;

/**
 * Generates a class based on the URL.
 *
 * This way we can target this page with css. Specifically it takes the folder
 * and filename and replaces the directory separator with a hyphen, e.g.
 * *\user\login\* will translate into *user-login-index*. The index added to the
 * end if we are using the default page. *\user\login.php* will translate into
 * *user-login*.
 *
 * @return string The class name
 */
function render() {
	// Get the current URL
	$url = $_SERVER['REQUEST_URI'];

	// Remove the parameters
	if ($parameters = strlen($url) - (strlen($url) - strpos($url, "?"))) {
		$url = substr($url, 0, $parameters);
	}

	// If it doesn't end in php, then add default page
	if (!strpos($url, ".php")) {
		$url = $url . "index";
	} else {
		$url = substr($url, 0, strlen($url) - 4);
	}

	// Remove trailing slash
	if (strpos($url, "/") == 0) {
		$url = substr($url, 1, strlen($url) - 1);
	}

	// Remove the prefix from the class by setting the variable below
	$prefixes = array(
		'****DEV_PREFIX****',
		'****STAGE_PREFIX****',
		'****LIVE_PREFIX****'
	);

	foreach ($prefixes as $prefix) {
		$url = str_replace($prefix, "", str_replace("/", "-", $url));
	}

	if (empty($url)) {
		$url = 'index';
	}

	return $url;
}

\Sleepy\Hook::applyFilter('render_placeholder_urlclass', '\URLclass\render');