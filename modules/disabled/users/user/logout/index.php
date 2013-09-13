<?php
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/sleepy.php');

	unset($_SESSION['uid']);
	header('location: /user/login');