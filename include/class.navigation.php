<?php
	class Navigation {
		private $current;
		private $data;

		public function __construct($json='') {
			$this->data = json_decode($json);
		}

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
				return true;
			}

			// no match...
			return false;
		}

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

		public function show() {
			return $this->renderNav($this->data->pages);
		}

		public function setCurrent($string) {
			$this->current = str_replace("/iw-mount/default/main/MarketingSites/BSP/Xofigo/USA/Scientific/WORKAREA/htdocs", "", str_replace("index.php", "", $string));
		}
	}