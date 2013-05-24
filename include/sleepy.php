<?php
	session_start();
	ob_start();

	include_once('class.debug.php');
	include_once('class.hooks.php');
	include_once('global.php');
	include_once('class.db.php');
	include_once('class.tables.php');
	include_once('class.dbgrid.php');
	include_once('class.mailer.php');
	include_once('class.navigation.php');
	include_once('class.template.php');