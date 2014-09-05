<?php

// Enable sessions
session_start();

// If we are not setup yet, forward the user to the setup page
if (!@include_once('global.php')) {
	header('Location: /app/setup/');
	die();
}

require_once('class.hooks.php');
require_once('class.template.php');

class Sleepy {
	public function __construct() {
		\Sleepy\Hook::addAction('sleepy_preprocess');

		// Send the encoding ahead of time to speed up rendering
		header('Content-Type: text/html; charset=utf-8');
	}

	public function __destruct() {
		\Sleepy\Hook::addAction('sleepy_postprocess');
	}
}

$_sleepy = new Sleepy();