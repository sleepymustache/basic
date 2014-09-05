<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/sleepy.php');

	$page = new \Sleepy\Template('login');

	$u = new Authentication\User();

	if ($u->isLoggedIn()) {
		header('location: /user/profile');
	}

	if ($_SERVER['REQUEST_METHOD'] === 'POST') {

		try {
			$uid = $u->authenticate($_POST['txtEmail'], $_POST['pwdPassword']);
			$_SESSION['uid'] = $uid;
			header('location: /user/profile');
		} catch (Exception $e) {
			$error = $e->getMessage();
		}
	}

	// SEO
	$page->bind('title', "Sleepy Mustache");
	$page->bind('description', 'This is the description');
	$page->bind('keywords', 'user login, blog, sleepy mustache, framework');
	$page->bind('header', 'Sleepy Mustache!');
	@$page->bind('error', $error);
	$page->show();