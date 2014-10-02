<!DOCTYPE html>
<!--[if lt IE 9 ]>	  <html class="ie ie8 {{ urlClass }}" lang="en"> <![endif]-->
<!--[if IE 9 ]>		  <html class="ie ie9 {{ urlClass }}" lang="en"> <![endif]-->
<!--[if gt IE 9]><!--><html class="{{ urlClass }}" lang="en"><!--<![endif]-->
<head>
	<!-- META DATA -->
	<meta charset="utf-8">
	<meta name="keywords" content="{{ keywords }}">
	<meta name="description" content="{{ description }}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ title }}</title>

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="<?= URLBASE ?>css/normalize.css">
	<link rel="stylesheet" type="text/css" href="<?= URLBASE ?>css/style.css">

	<!-- FAVICON -->
	<link rel="apple-touch-icon" href="/favicon.png">
	<link rel="icon" href="/favicon.png">
	<!--[if IE]><link rel="shortcut icon" href="/favicon.ico"><![endif]-->
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="/favicon.png">
</head>
<body>
	<div class="wrapper">
		<header>
			<h1>{{ header }}</h1>

			<!-- A sample menu -->
			<nav class="top">
				{{ topNav }}
			</nav>
		</header>
		<section class="content clearfix">