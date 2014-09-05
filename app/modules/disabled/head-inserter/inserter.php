<?php
namespace HeadInserter;

/*
	Creates a new Hook called head_inserter, when
	called this hook inserts the given string just above
	the closing head tag. If </head> is not found,
	nothing is inserted
*/
function render ($html) {
	$toInsert = \Sleepy\Hook::addFilter('head_inserter', "\n");
	$pos = strrpos($html, '</head>');
	$html = ($pos !== false)
			? substr_replace($html, $toInsert, $pos, 0)
			: $html;

	return $html;
}

\Sleepy\Hook::applyFilter('render_template', 'HeadInserter\render');