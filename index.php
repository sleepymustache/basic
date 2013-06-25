<?php
	require_once('include/sleepy.php');

	$page = new Template('default');
	$page->bind('title', "Sleepy Mustache");
	$page->bind('header', 'Sleepy Mustache!');
	$page->bind('changelog', array(
		array(
			"title" => "Template #each nesting",
			"date" => "06/24/2013",
			"changes" => array(
				"Added the ability to nest #each loops"
			)
		),
		array(
			"title" => "Templating Upgrades and Bugfix",
			"date" => "05/29/2013",
			"changes" => array(
				"Added the ability to include other templates inside of a template using <strong>#include</strong>.",
				"Added the ability to pass arrays to a template and iterate through data in the array using <strong>#each</strong>",
				"Fixed a typo in the URLClass Module."
			)
		)
	));

	$page->show();