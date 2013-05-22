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

	/**
	 * Creates a class based on the url
	 * @return string A class you can assign to the html tag
	 */
	function urlToClass() {
		$url = $_SERVER['REQUEST_URI'];

		if ($parameters = strlen($url) - (strlen($url) - strpos($url, "?"))) {
			$url = substr($url, 0, $parameters);
		}

		if (!strpos($url, ".php")) {
			$url = $url . "index";
		} else {
			$url = strstr($url, 0, strlen($url) - 4);
		}

		if (strpos($url, "/") == 0) {
			$url = substr($url, 1, strlen($url) - 1);
		}

		// Remove the prefix from the class by setting the variable below
		$prefix = "";
		$url = str_replace($prefix, "", str_replace("/", "-", $url));

		if (empty($url)) {
			$url = 'index';
		}

		return $url;
	}

	/***************************************************************************
	 * Top Navigation
	 **************************************************************************/
	$topNavData = '{
		"pages": [
			{
				"title": "Nav 1",
				"link": "/nav1/"
			}, {
				"title": "Nav 2",
				"link": "/nav2/",
				"pages": [
					{
						"title": "Subnav 1",
						"link": "/downloads/fpo.pdf",
						"target": "_blank"
					}
				]
			}
		]
	}';

	$topNav = new Navigation($topNavData);
	$topNav->setCurrent($_SERVER['SCRIPT_NAME']);

	/***************************************************************************
	 * Page Settings
	 **************************************************************************/
	$pageSettings = json_decode($pageSettings);

	$pageSettings->title = Hook::addFilter('header_title', $pageSettings->title);
	$pageSettings->keywords = Hook::addFilter('header_keywords', $pageSettings->keywords);
	$pageSettings->desciption = Hook::addFilter('header_description', $pageSettings->description);
	$htmlClass = Hook::addFilter('header_htmlClass', urlToClass());
?>

<!DOCTYPE html>
<!--[if lt IE 9 ]>	  <html class="ie ie8 <?php echo urlToClass();?>" lang="en"> <![endif]-->
<!--[if IE 9 ]>		  <html class="ie ie9 <?php echo urlToClass();?>" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--><html class="<?php echo $htmlClass;?>" lang="en"><!--<![endif]-->
<head>
	<!-- META DATA -->
	<meta charset="utf-8">
	<meta name="keywords" content="<?php echo $pageSettings->keywords;?>">
	<meta name="description" content="<?php echo $pageSettings->desciption;?>">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">

	<!-- CSS -->
	<link rel="stylesheet" href="/css/normalize.css">
	<link rel="stylesheet" href="/css/style.css">

	<!-- LOAD JAVASCRIPT -->
	<script data-main="/js/main" src="/js/require.js" ></script>

	<!-- FAVICON -->
	<link rel="apple-touch-icon" href="path/to/touchicon.png">
	<link rel="icon" href="path/to/favicon.png">
	<!--[if IE]><link rel="shortcut icon" href="path/to/favicon.ico"><![endif]-->
	<meta name="msapplication-TileColor" content="#D83434">
	<meta name="msapplication-TileImage" content="path/to/tileicon.png">

	<!-- SHIV -->
	<!--[if lt IE 9]>
		<script src="/js/html5shiv.js"></script>
	<![endif]-->

	<!-- TITLE -->
	<title><?php echo $pageSettings->title; ?></title>

</head>
<body>
	<div class="wrapper">
		<header>
			<h1>Header</h1>

			<!-- A sample menu -->
			<nav class="top">
				<?php echo $topNav->show(); ?>
			</nav>
		</header>

		<section class="content clearfix">