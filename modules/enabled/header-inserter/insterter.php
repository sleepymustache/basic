<?php
/*
	creates a new Hook called head_inserter, when
	called this hook inserts the given string just above
	the closing head tag. If </head> is not found, 
	nothing is inserted
*/

function insertHeadStuffJF ($html) {
	$toInsert = Hook::addFilter('head_inserter', "\n");

	$pos = strrpos($html, '</head>');

	$html = ($pos !== -1) 
			? substr_replace($html, $toInsert, $pos, 0) 
			: $html;

	return $html;
}

Hook::applyFilter('render_template', 'insertHeadStuffJF');