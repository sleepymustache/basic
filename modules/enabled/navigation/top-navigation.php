<?php
function hook_render_placeholder_topNav() {
	$topNavData = '{
		"pages": [
			{
				"title": "Documentation",
				"link": "http://www.sleepymustache.com/documentation/html/index.html",
					"pages": [
						{
							"title": "CSV Class",
							"link": "http://www.sleepymustache.com/documentation/html/csv1.html"
						}, {
							"title": "DB Class",
							"link": "http://www.sleepymustache.com/documentation/html/db1.html"
						}, {
							"title": "DbGrid Class",
							"link": "http://www.sleepymustache.com/documentation/html/dbgrid1.html"
						}, {
							"title": "File System Database Class",
							"link": "http://www.sleepymustache.com/documentation/html/fsdb.html"
						}, {
							"title": "Hook Class",
							"link": "http://www.sleepymustache.com/documentation/html/hooks1.html"
						}, {
							"title": "IP 2 Country Class",
							"link": "http://www.sleepymustache.com/documentation/html/ip2country.html"
						}, {
							"title": "Mailer Class",
							"link": "http://www.sleepymustache.com/documentation/html/mailer1.html"
						}, {
							"title": "Navigation Class",
							"link": "http://www.sleepymustache.com/documentation/html/nav1.html"
						}, {
							"title": "Record Class",
							"link": "http://www.sleepymustache.com/documentation/html/record1.html"
						}
					]
			}, {
				"title": "Download",
				"link": "https://github.com/jaimerod/sleepy-mustache/zipball/master"
			}, {
				"title": "Github",
				"link": "https://github.com/jaimerod/sleepy-mustache"
			}
		]
	}';

	$topNav = new Navigation($topNavData);
	$topNav->setCurrent($_SERVER['SCRIPT_NAME']);

	return $topNav->show();
}

Hook::applyFilter('render_placeholder_topNav', 'hook_render_placeholder_topNav');