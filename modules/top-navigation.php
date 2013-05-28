<?php
	function hook_render_placeholder_topNav() {
		$topNavData = '{
			"pages": [
				{
					"title": "Nav 1",
					"link": "/nav1/"
				}, {
					"title": "Nav 2",
					"link": "/nav2/",
					"pages": [
						{
							"title": "Subnav 1",
							"link": "/downloads/fpo.pdf",
							"target": "_blank"
						}
					]
				}
			]
		}';

		$topNav = new Navigation($topNavData);
		$topNav->setCurrent($_SERVER['SCRIPT_NAME']);

		return $topNav->show();
	}

	Hook::applyFilter('render_placeholder_topNav', 'hook_render_placeholder_topNav');