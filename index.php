<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/app/include/sleepy.php');

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
				Congratulations on successfully installing sleepyMUSTACHE! You can visit the <a href=\"http://www.sleepymustache.com/documentation/index.html\">documentation page</a> to learn more or hit the ground running by viewing the <a href=\"http://www.sleepymustache.com/#getting-started\">getting started</a> section.",
			"tags" => array(
				array(
					'name' => "Configuration",
					'link' => "http://www.sleepymustache.com/#getting-started"
				)
			)
		), array(
			"title" => "Sample Modules",
			"link" => "#",
			"author" => "Jaime A. Rodriguez",
			"date" => date('m/d/Y', time() - 30 * 24 * 60 * 60),
			"description" => "
				By default there are 2 sample modules included with the
				framework. These modules demonstrate how to create your own
				modules, and implement existing functionality. You may safely
				delete them.",
			"tags" => array(
				array(
					'name' => "modules",
					'link' => "http://www.sleepymustache.com/#default-modules"
				),
				array(
					'name' => "fixes",
					'link' => "https://github.com/jaimerod/sleepy-mustache/commits/master"
				)
			)
		)
	));

	$page->show();