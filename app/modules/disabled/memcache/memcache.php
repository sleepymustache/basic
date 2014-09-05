<?php
namespace MemcacheModule;

function manage_cached_page($page) {
	$port = 11211;
	$cacheExpires = 10; //seconds

	$memcache = new \Memcache;
	$memcache->connect('localhost', $port) or die ("Memcache: Could not connect");

	if (!$get_result = $memcache->get(__FILE__)) {
		$get_result = $memcache->set(__FILE__, $page, 0, $cacheExpires) or
		die("Failed to save data at the server");
	}

	return $page;
}

function show_cached_page() {
	$port = 11211;

	$memcache = new \Memcache;
	$memcache->connect('localhost', $port) or die ("Memcache: Could not connect");

	if ($get_result = $memcache->get(__FILE__)) {
		header('Memcached: true');
		echo $get_result;
		die();
	}
}

if (ENV === "LIVE") {
	\Sleepy\Hook::applyFilter('render_template', 'MemcacheModule\manage_cached_page');
	\Sleepy\Hook::doAction('sleepy_preprocess', 'MemcacheModule\show_cached_page');
}