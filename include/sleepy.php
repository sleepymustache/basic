<?php
	session_start();

	// sent the encoding ahead of time to speed up rendering
	header('Content-Type: text/html; charset=utf-8');

	// enable gzip if it is available
	if (extension_loaded('zlib')){
		ob_start('ob_gzhandler');
	}

	include_once('class.debug.php');
	include_once('class.hooks.php');
	include_once('global.php');

	// Compress HTML, with graceful fallback
	ob_start('process_data_jmr1');

	ini_set("pcre.recursion_limit", "16777");

	function process_data_jmr1($text) {
		$re = '%# Collapse whitespace everywhere but in blacklisted elements.
			(?>             # Match all whitespans other than single space.
			  [^\S ]\s*     # Either one [\t\r\n\f\v] and zero or more ws,
			| \s{2,}        # or two or more consecutive-any-whitespace.
			) # Note: The remaining regex consumes no text at all...
			(?=             # Ensure we are not in a blacklist tag.
			  [^<]*+        # Either zero or more non-"<" {normal*}
			  (?:           # Begin {(special normal*)*} construct
			    <           # or a < starting a non-blacklist tag.
			    (?!/?(?:textarea|pre|script)\b)
			    [^<]*+      # more non-"<" {normal*}
			  )*+           # Finish "unrolling-the-loop"
			  (?:           # Begin alternation group.
			    <           # Either a blacklist start tag.
			    (?>textarea|pre|script)\b
			  | \z          # or end of file.
			  )             # End alternation group.
			)  # If we made it here, we are not in a blacklist tag.
			%Six';
		$text = preg_replace($re, " ", $text);
		if ($text === null) exit("PCRE Error! File too big.\n");
		return $text;
	}

	include_once('class.db.php');
	include_once('class.dbgrid.php');
	include_once('class.mailer.php');
	include_once('class.navigation.php');
	include_once('class.template.php');