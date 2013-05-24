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
	<title>{{ title }}</title>

</head>
<body>
	<div class="wrapper">
		<header>
			<h1>Header</h1>

			<!-- A sample menu -->
			<nav class="top">
				{{ topNav }}
			</nav>
		</header>

		<section class="content clearfix">