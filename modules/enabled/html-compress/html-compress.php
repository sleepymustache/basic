<?php

/**
 * Hooks into the preprocess and postprocess hooks to buffer and compress HTML
 * 
 * @return void
 */
function htmlcompress_sleepy_preprocess() {
	ob_start('process_data_jmr1');

	ini_set("pcre.recursion_limit", "16777");
}

function htmlcompress_sleepy_postprocess() {
	ob_end_flush();
}

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

Hook::doAction('sleepy_preprocess',  'htmlcompress_sleepy_preprocess' );
Hook::doAction('sleepy_postprocess', 'htmlcompress_sleepy_postprocess');