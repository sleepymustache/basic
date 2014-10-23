<?php
namespace Module\Navigation;

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
 * @internal
 */
function render() {
	$topNavData = '{
		"pages": [
			{
				"title": "Documentation",
				"link": "http://www.sleepymustache.com/documentation/html/index.html",
					"pages": [
						{
							"title": "Core",
							"link": "http://www.sleepymustache.com/documentation/namespace-Sleepy.html"
						}, {
							"title": "CSV",
							"link": "http://www.sleepymustache.com/documentation/namespace-Module.CSV.html"
						}, {
							"title": "Database",
							"link": "http://www.sleepymustache.com/documentation/namespace-Module.DB.html"
						}, {
							"title": "File System Database",
							"link": "http://www.sleepymustache.com/documentation/namespace-Module.FSDB.html"
						}, {
							"title": "IP 2 Country",
							"link": "http://www.sleepymustache.com/documentation/namespace-Module.IP2Country.html"
						}, {
							"title": "Mailer",
							"link": "http://www.sleepymustache.com/documentation/namespace-Module.Mailer.html"
						}, {
							"title": "Navigation",
							"link": "http://www.sleepymustache.com/documentation/namespace-Module.Navigation.html"
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

	$topNav = new Builder($topNavData);

	return $topNav->show();
}

\Sleepy\Hook::applyFilter('render_placeholder_topnav', '\Module\Navigation\render');