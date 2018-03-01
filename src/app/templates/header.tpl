<!DOCTYPE html>
<html class="{{ urlClass }}" lang="en">
<head>
	<!-- META DATA -->
	<meta charset="utf-8">
	<meta name="keywords" content="{{ keywords }}">
	<meta name="description" content="{{ description }}">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>{{ title }}</title>

	<!-- FRAME BUSTING -->
	<style>html {display:none}</style>
	<script>(self == top) ?	document.documentElement.style.display = 'block' : top.location = self.location;</script>

	<!-- CSS -->
	<link rel="stylesheet" type="text/css" href="/css/main.css">

	<!-- JAVASCRIPT -->
	<script async src="<?= URLBASE ?>js/main.bundle.js"></script>

	<!-- FAVICON -->
	<link rel="apple-touch-icon" href="<?= URLBASE ?>favicon.png">
	<link rel="icon" href="<?= URLBASE ?>favicon.png">
	<!--[if IE]><link rel="shortcut icon" href="<?= URLBASE ?>favicon.ico"><![endif]-->
	<meta name="msapplication-TileColor" content="#ffffff">
	<meta name="msapplication-TileImage" content="<?= URLBASE ?>favicon.png">
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