<?php
/**
 * Adds do not follow tag to head of document if not in the LIVE environment.
 * This module depends on the head_inserter module.
 * @param  string $html Whats currently in the head_inserter
 * @return string       Our alterations to head_inserter
 */
function robotsDevHide ($html) {
	return $html . "\t<meta name=\"robots\" content=\"noindex, nofollow\">\n";
}

if (ENV !== "LIVE") {
	Hook::applyFilter('head_inserter', 'robotsDevHide');
}