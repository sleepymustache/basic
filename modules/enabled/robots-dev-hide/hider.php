<?php

/*
	adds do not follow tag to head of document
	this module depends on the head_insert module
*/
function robotsDevHide () {
	$unfollow = '<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">';

	return (ENV !== "LIVE") ? $unfollow : '';
}

Hook::applyFilter('head_inserter', 'robotsDevHide');