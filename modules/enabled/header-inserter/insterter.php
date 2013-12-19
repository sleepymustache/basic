<?php

function insertHeadStuffJF ($html) {
	$toInsert = Hook::addFilter('head_inserter', '');

	$position = strrpos($html, '</head>');
	$html = substr_replace($html, $toInsert, $position, 0);

	return $html;
}

Hook::applyFilter('render_template', 'insertHeadStuffJF');