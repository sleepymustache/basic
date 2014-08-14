<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/sleepy.php');

	$page = new \Sleepy\Template('homepage');

	// SEO
	$page->bind('title', 'Sleepy Mustache');
	$page->bind('description', 'This is the description');
	$page->bind('keywords', 'blog, sleepy mustache, framework');

	// Content
	$page->bind('header', 'sleepy<span>MUSTACHE</span>');
	$page->bind('teasers', array(
		array(
			"title" => "Getting Started",
			"link" => 'http://www.sleepymustache.com/',
			"author" => "Jaime A. Rodriguez",
			"date" => date('m/d/Y', time()),
			"description" => "
				Congratulations! sleepyMUSTACHE is up and running. To learn more
				about what you can do with sleepyMUSTACHE visit the
				documentation at <a href=\"http://www.sleepymustache.com\">
				http://www.sleepymustache.com</a>",
			"tags" => array(
				array(
					'name' => "Configuration",
					'link' => "http://www.sleepymustache.com/documentation/html/index.html"
				)
			)
		), array(
			"title" => "Sample Modules",
			"link" => "#",
			"author" => "Jaime A. Rodriguez",
			"date" => "08/05/2013",
			"description" => "
				By default there are 2 sample modules included with the
				framework. These modules demonstrate how to create your own
				modules. You may safely delete them.",
			"tags" => array(
				array(
					'name' => "modules",
					'link' => "http://google.com"
				),
				array(
					'name' => "fixes",
					'link' => "http://google.com"
				)
			)
		)
	));

	$page->show();