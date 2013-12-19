<?php

function robotsDevHide () {
	$unfollow = '<META NAME="ROBOTS" CONTENT="NOINDEX, NOFOLLOW">';

	return (ENV !== "LIVE") ? $unfollow : '';
}

Hook::applyFilter('head_inserter', 'robotsDevHide');