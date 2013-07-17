<?php
	require_once('include/sleepy.php');
	$page = new Template('default');
	$page->bind('title', "Sleepy Mustache");
	$page->bind('seo_description', "This is a description");
	$page->bind('header', 'Sleepy Mustache!');
	$page->bind('changelog', array(
		array(
			"title" => "Memcache Module",
			"date" => "07/16/2013",
			"changes" => array(
				"Added a memcache module that will cache rendered pages.
				Currently it is cached for 10 seconds but can be modified
				very easily."
			)
		),
		array(
			"title" => "Module Management",
			"date" => "07/12/2013",
			"changes" => array(
				"Module can be disabled by moving them from the enabled folder into the disabled folder"
			)
		),
		array(
			"title" => "User Management",
			"date" => "07/12/2013",
			"changes" => array(
				"Added user authentications, roles, and permissions class"
			)
		),
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
