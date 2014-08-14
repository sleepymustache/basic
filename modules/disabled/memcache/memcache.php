<?php
namespace MemcacheModule;

function manage_cached_page($page) {
	$memcache = new \Memcache;
	$memcache->connect('localhost', 11211) or die ("Could not connect");

	if (!$get_result = $memcache->get(__FILE__)) {
		$get_result = $memcache->set(__FILE__, $page, false, 10) or
		die ("Failed to save data at the server");
	}

	return $page;
}

function show_cached_page() {
	$memcache = new \Memcache;
	$memcache->connect('localhost', 11211) or die ("Could not connect");

	if ($get_result = $memcache->get(__FILE__)) {
		echo $get_result;
		die();
	}
}

\Sleepy\Hook::applyFilter('render_template', 'MemcacheModule\manage_cached_page');
\Sleepy\Hook::doAction('sleepy_preprocess', 'MemcacheModule\show_cached_page');