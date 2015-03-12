<?php
namespace Basic\Navigation;

function topNav() {
	$data = '{
		"pages": [
			{
				"title": "Nav 1",
				"link": "/nav1/"
			}, {
				"title": "Nav 2",
				"link": "/nav2/"
			}, {
				"title": "Nav 3",
				"link": "#"
			}
		]
	}';

	$nav = new \Module\Navigation\Builder($data);

	return $nav->show();
}

\Sleepy\Hook::applyFilter('render_placeholder_topnav', '\Basic\Navigation\topNav');