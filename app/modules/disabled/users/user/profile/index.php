<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/sleepy.php');

	$page = new \Sleepy\Template('profile');

	$u = new \Authentication\User();

	if ($u->isLoggedIn()) {
		$u->load($_SESSION['uid']);
	} else {
		header('location: /user/login');
	}

	// SEO
	$page->bind('title', "Sleepy Mustache");
	$page->bind('description', 'This is the description');
	$page->bind('keywords', 'user login, blog, sleepy mustache, framework');
	$page->bind('header', 'Sleepy Mustache!');
	$page->bind('email', $u->columns['email']);
	$page->bind('role', $u->getRole());
	$page->show();