<?php

/**
 * This is where the <link> tag points to. It should be in the format of:
 *
 * @code
 * <link rel="stylesheet" href="/modules/enabled/css-compress/?css=reset&main" >
 * @endcode
 */

// Do nothing if the get string is empty
if (!empty($_GET['css'])) {
	require_once('class.css.php');

	$c = new CSS();
	$files = explode("&", $_GET['css']);
	foreach ($files as $file) {
		$c->add("/css/" . $file . ".css");
	}

	$c->show();
}