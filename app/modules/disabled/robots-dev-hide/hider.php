<?php
namespace Module\HeadInserter\RobotsDevHide;

/**
 * Adds do not follow tag to head of document if not in the LIVE environment.
 * This module depends on the head_inserter module.
 * @param  string $html Whats currently in the head_inserter
 * @return string       Our alterations to head_inserter
 * @internal
 */
function insert($html) {
	return $html . "\t<meta name=\"robots\" content=\"noindex, nofollow\">\n";
}

if (ENV !== "LIVE") {
	\Sleepy\Hook::applyFilter('head_inserter', '\Module\HeadInserter\RobotsDevHide\insert');
}