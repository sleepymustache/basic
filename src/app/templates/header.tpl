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
	<?php if (ENV === 'LIVE'): ?>
		<link rel="stylesheet" type="text/css" href="/css/main.min.css">
	<?php else: ?>
		<link rel="stylesheet" type="text/css" href="/css/main.css">
	<?php endif ?>

	<!-- JAVASCRIPT -->
	<script async data-main="<?= URLBASE ?>js/main" src="<?= URLBASE ?>js/require.js" ></script>

	<!-- FAVICON -->
	<link rel="apple-touch-icon" href="<?= URLBASE ?>favicon.png">
	<link rel="icon" href="<?= URLBASE ?>favicon.png">
	<!--[if IE]><link rel="shortcut icon" href="<?= URLBASE ?>favicon.ico"><![endif]-->
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="<?= URLBASE ?>favicon.png">

	<!-- SHIV -->
	<!--[if lt IE 9]>
		<script src="<?= URLBASE ?>js/html5shiv.js"></script>
	<![endif]-->
</head>
<body>
	<div class="wrapper">
		<header>
			<h1>{{ header }}</h1>

			<nav class="top">
				<ul><li><a href="#">Link 1</a></li><li><a href="#">Link 2</a></li><li><a href="#">Link 3</a></li></ul>
			</nav>
		</header>
		<section class="content clearfix">