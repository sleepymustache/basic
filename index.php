<?php
	require_once('include/sleepy.php');

	$page = new Template('templates/default.tpl');
	$page->bind('title', 'Sleepy Mustache');
	$page->bind('header', 'Sleepy Mustache');
	$page->bind('subhead', date('l, F j, Y', time()));
	$page->show();