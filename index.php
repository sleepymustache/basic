<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/sleepy.php');

	$page = new Template('homepage');

	// SEO
	$page->bind('title', 'Sleepy Mustache');
	$page->bind('description', 'This is the description');
	$page->bind('keywords', 'blog, sleepy mustache, framework');

	// Content
	$page->bind('header', 'sleepy<span>MUSTACHE</span>');
	$page->bind('teasers', array(
		array(
			"title" => "Memcache module",
			"link" => 'memcache.php',
			"author" => "Jaime A. Rodriguez",
			"date" => "07/16/2013",
			"description" => "
				Under the bed. Stare at ceiling stick butt in face. Stick butt
				in face under the bed swat at dog for hopped up on goofballs,
				shake treat bag. Behind the couch. Why must they do that chew
				iPad power cord and shake treat bag nap all day attack feet.
				Chew foot hunt anything that moves, yet stick butt in face but
				hunt anything that moves or intently stare at the same spot, sun
				bathe yet chew iPad power cord. Chew foot. Shake treat bag
				hopped up on goofballs, claw drapes for chase mice burrow under
				covers. Play time intrigued by the shower chew iPad power cord
				sun bathe.",
			"tags" => array(
				array(
					'name' => "modules",
					'link' => "http://google.com"
				),
				array(
					'name' => "caching",
					'link' => "http://google.com"
				)
			)
		), array(
			"title" => "Template fix",
			"link" => "template.php",
			"author" => "Jaime A. Rodriguez",
			"date" => "07/16/2013",
			"description" => "
				Collaboratively administrate empowered markets via plug-and-play
				networks. Dynamically procrastinate B2C users after installed
				base benefits. Dramatically visualize customer directed
				convergence without revolutionary ROI.",
			"tags" => array(
				array(
					'name' => "modules",
					'link' => "http://google.com"
				),
				array(
					'name' => "changelog",
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