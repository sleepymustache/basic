<?php
	require_once('include/class.template.php');

	$page = new Template('templates/default.tpl');
	$page->bind('title', 'Sleepy Mustache');
	$page->bind('header', 'Hello world!');
	$page->show();