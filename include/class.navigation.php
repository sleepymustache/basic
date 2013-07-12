<?php
/**
 * @page nav1 Navigation Class
 *
 * A Navigation class that simplifies creating multi-lever navigations.
 *
 * This class uses JSON to structure navigation pages and attributes. It can
 * detect what page is active and assign classes to them for special treatment.
 *
 * @section usage Usage:
 * @code
 * $topNavData = '{
 *		"pages": [
 *			{
 *				"title": "Nav 1",
 *				"link": "/nav1/"
 *			}, {
 *				"title": "Nav 2",
 *				"link": "/nav2/",
 *				"pages": [
 *					{
 *						"title": "Subnav 1",
 *						"link": "/downloads/fpo.pdf",
 *						"target": "_blank"
 *					}
 *				]
 *			}
 *		]
 *	}';
 *
 *	$topNav = new Navigation($topNavData);
 *	$topNav->setCurrent($_SERVER['SCRIPT_NAME']);
 *
 * // In body somewhere...
 * <nav class="top">
 *		<?php echo $topNav->show(); ?>
 *	</nav>
 * @endcode
 *
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.0
 * @copyright  GPL 3 http://cuttingedgecode.com
 */

class Navigation {
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
		$json = Hook::addFilter('navigation_raw_json', $json);
		$this->data = Hook::addFilter('navigation_rendered_json', json_decode($json));
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
			Hook::addAction('navigation_has_active');
			return true;
		}

		// no match...
		return false;
	}

	/**
	 * Renders the $pages as an unordered list
	 * @param  object $pages the page data
	 * @return string        The string containing the unordered list
	 */
	private function renderNav($pages) {
		$buffer = array();
		$buffer[] = "<ul>";
		foreach ($pages as $page) {
			$buffer[] = "<li class='";
			if ($this->hasActive($page)) {
				$buffer[] = "active ";
			}
			if (!empty($page->class)){
				$buffer[] = $page->class . " ";
			}
			$buffer[] = "'>";
			if (isset($page->target)) {
				$buffer[] = "<a href='{$page->link}' target='{$page->target}'>{$page->title}</a>";
			} else {
				$buffer[] = "<a href='{$page->link}'>{$page->title}</a>";
			}
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
	public function show() {
		return $this->renderNav($this->data->pages);
	}

	/**
	 * Sets the current page search string
	 * @param string $string A string used to determine if a page is current
	 */
	public function setCurrent($string) {
		$this->current = str_replace("/iw-mount/default/main/MarketingSites/BSP/Xofigo/USA/Scientific/WORKAREA/htdocs", "", str_replace("index.php", "", $string));
	}
}