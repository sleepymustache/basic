<?php
namespace Module\Navigation;

/**
 * Creates a Navigation UL based on a JSON file
 *
 * Uses JSON to structure navigation pages and attributes. It can
 * detect what page is active and assign classes to them for special treatment.
 *
 * ### Usage
 *
 * <code>
 *  $topNavData = '{
 *    "pages": [
 *      {
 *        "title": "Nav 1",
 *        "link": "/nav1/"
 *      }, {
 *        "title": "Nav 2",
 *        "link": "/nav2/",
 *        "pages": [
 *          {
 *            "title": "Subnav 1",
 *            "link": "/downloads/fpo.pdf",
 *            "target": "_blank"
 *          }
 *        ]
 *      }
 *    ]
 *  }';
 *
 *  $topNav = new \Module\Navigation\Builder($topNavData);
 *
 *  // In body somewhere...
 *  <nav class="top">
 *    <?= $topNav->show(); ?>
 *  </nav>
 * </code>
 *
 * ### Changelog
 * # Version 1.4
 * * Now automatically sets $_SERVER['SCRIPT_NAME'] as current page
 * * Added multiple hook points for manipulating navigations
 *
 * ## Version 1.2
 * * Added a track parameter
 *
 * @date June 16, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.1
 * @license  http://opensource.org/licenses/MIT
 */
class Builder {
	/**
	 * string Use this string to determine currently active page
	 * @private
	 */
	private $current;

	/**
	 * mixed Navigation data
	 */
	private $data;

	/**
	 * Constructor
	 * @param string $json json data containing the Navigation data
	 */
	public function __construct($json='') {
		if (class_exists('\Sleepy\Hook')) {
			$json = \Sleepy\Hook::addFilter('navigation_raw_json', $json);
		}

		$json = json_decode($json);

		if (class_exists('\Sleepy\Hook')) {
			$json = \Sleepy\Hook::addFilter('navigation_rendered_json', $json);
		}

		$this->data = $json;
		$this->setCurrent($_SERVER['SCRIPT_NAME']);
	}

	/**
	 * Is this page or its children an active page?
	 * @param  object  $page An object containing page data
	 * @return boolean       true if this page, or its children are active
	 * @private
	 */
	private function hasActive($page) {
		// are there subpages? check those too...
		if (isset($page->pages)) {
			foreach ($page->pages as $subPage) {
				if ($this->hasActive($subPage)) {
					return true;
				}
			}
		}

		// can we find a match?
		if (substr($page->link, strlen($page->link) * -1) === $this->current) {
			if (class_exists('\Sleepy\Hook')) {
				\Sleepy\Hook::addAction('navigation_has_active');
			}

			return true;
		}

		// no match...
		if (class_exists('\Sleepy\Hook')) {
			\Sleepy\Hook::addAction('navigation_no_active');
		}

		return false;
	}

	/**
	 * Renders the $pages as an unordered list
	 * @param  object $pages the page data
	 * @return string        The string containing the unordered list
	 */
	private function renderNav($pages, $class="") {
		$class = trim($class);
		$buffer = array();

		if (strlen($class) > 1) {
			$buffer[] = "<ul class=\"{$class}\">";
		} else {
			$buffer[] = "<ul>";
		}

		foreach ($pages as $page) {
			if (class_exists('\Sleepy\Hook')) {
				$page = \Sleepy\Hook::addFilter('navigation_page', $page);

				if (!empty($page->id)) {
					$page = \Sleepy\Hook::addFilter('navigation_page_' . $page->id, $page);
				}
			}

			$active		= ($this->hasActive($page))	? true								: false;
			$classy		= (!empty($page->class))	? true								: false;
			$track		= (!empty($page->track))	? "data-track=\"{$page->track}\" "	: "";
			$id			= (!empty($page->id))		? "id=\"{$page->id}\" "				: "";
			$target		= (!empty($page->target))	? "target=\"{$page->target}\" "		: "";
			$href		= (!empty($page->link))		? "href=\"{$page->link}\" "			: "";
			$attributes	= trim($id . $track . $target . $href);

			$buffer[] = "<li";

			if ($active || $classy) {
				$buffer[] = " class=\"";

				if ($active) {
					$page->class = "active ";
				}

				$buffer[] = trim($page->class);

				$buffer[] = "\"";
			}

			$buffer[] = ">";

			$buffer[] = "<a {$attributes}>{$page->title}</a>";

			if (isset($page->pages)) {
				$buffer[] = $this->renderNav($page->pages);
			}

			$buffer[] = "</li>";
		}
		$buffer[] = "</ul>";

		return implode("", $buffer);
	}

	/**
	 * Renders the Navigation
	 * @return string The rendered navigation
	 */
	public function show($class="") {
		$rendered = $this->renderNav($this->data->pages, $class);

		if (class_exists('\Sleepy\Hook')) {
			$rendered = \Sleepy\Hook::addFilter('navigation_rendered', $rendered);
		}

		return $rendered;
	}

	/**
	 * Sets the current page search string
	 * @param string $string A string used to determine if a page is current
	 */
	public function setCurrent($string) {
		$this->current = str_replace(@URLBASE, "/", str_replace("index.php", "", $string));

		if (class_exists('\Sleepy\Hook')) {
			$this->current = \Sleepy\Hook::addFilter('navigation_current_page', $this->current);
		}
	}
}