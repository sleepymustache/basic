<?php
	function wizard_render_placeholder_urlClass() {
		$url = $_SERVER['REQUEST_URI'];

		if ($parameters = strlen($url) - (strlen($url) - strpos($url, "?"))) {
			$url = substr($url, 0, $parameters);
		}

		if (!strpos($url, ".php")) {
			$url = $url . "index";
		} else {
			$url = strstr($url, 0, strlen($url) - 4);
		}

		if (strpos($url, "/") == 0) {
			$url = substr($url, 1, strlen($url) - 1);
		}

		// Remove the prefix from the class by setting the variable below
		$prefix = "";
		$url = str_replace($prefix, "", str_replace("/", "-", $url));

		if (empty($url)) {
			$url = 'index';
		}

		return $url;
	}

Hook::applyFilter('render_placeholder_urlClass', 'wizard_render_placeholder_urlClass');