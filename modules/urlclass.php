<?php
	function wizard_render_placeholder_urlClass() {
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
		$prefix = "";
		$url = str_replace($prefix, "", str_replace("/", "-", $url));

		if (empty($url)) {
			$url = 'index2';
		}

		return $url;
	}

Hook::applyFilter('render_placeholder_urlClass', 'wizard_render_placeholder_urlClass');