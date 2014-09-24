<?php
namespace sampleNavigation;

/**
 * Sample Navigation
 *
 * The sample module uses the Navigation class to generate a unordered list with
 * using JSON. The *applyFilter* Method of the *Hook* Class takes two
 * parameters. The first is the hook point.
 *
 * All placeholders have hook points. They all follow the following pattern:
 *
 * * render_placeholder_[placeholder name]
 *
 * The second parameter is the name of the function to execute. Don't forget to
 * add the correct namespace.
 *
 * @return string The rendered navigation
 */

function render() {
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

	$topNav = new \Navigation\Builder($topNavData);
	$topNav->setCurrent($_SERVER['SCRIPT_NAME']);

	return $topNav->show();
}

\Sleepy\Hook::applyFilter('render_placeholder_topnav', '\sampleNavigation\render');