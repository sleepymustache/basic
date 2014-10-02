<?php
	require_once(dirname(__FILE__) . '/class.navigation.php');

	/**
	 * Class for testing the \Navigation\Builder Class
	 */
	class TestOfNavigation extends UnitTestCase {
		function setUp() {
			$this->nav = new \Navigation\Builder('{
				"pages": [
					{
						"title": "1",
						"target": "_blank",
						"link": "1.html",
							"pages": [
								{
									"title": "1.1",
									"link": "1.1.html"
								}, {
									"title": "1.2",
									"link": "1.2.html"
								}
							]
					}, {
						"title": "2",
						"link": "2.html",
						"class": "second"
					}
				]
			}');
		}

		function testNav() {
			ob_start();
			echo $this->nav->show();
			$nav = ob_get_clean();
			$this->assertEqual($nav,"<ul><li><a href='1.html' target='_blank'>1</a><ul><li><a href='1.1.html'>1.1</a></li><li><a href='1.2.html'>1.2</a></li></ul></li><li class='second'><a href='2.html'>2</a></li></ul>");
		}

		function testTarget() {
			ob_start();
			echo $this->nav->show();
			$nav = ob_get_clean();
			$this->assertEqual($nav,"<ul><li><a href='1.html' target='_blank'>1</a><ul><li><a href='1.1.html'>1.1</a></li><li><a href='1.2.html'>1.2</a></li></ul></li><li class='second'><a href='2.html'>2</a></li></ul>");
		}

		function testActive() {
			$this->nav->setCurrent('1.html');
			ob_start();
			echo $this->nav->show();
			$nav = ob_get_clean();
			$this->assertEqual($nav,"<ul><li class='active'><a href='1.html' target='_blank'>1</a><ul><li><a href='1.1.html'>1.1</a></li><li><a href='1.2.html'>1.2</a></li></ul></li><li class='second'><a href='2.html'>2</a></li></ul>");
		}

		function testSubActive() {
			$this->nav->setCurrent('1.1.html');
			ob_start();
			echo $this->nav->show();
			$nav = ob_get_clean();
			$this->assertEqual($nav,"<ul><li class='active'><a href='1.html' target='_blank'>1</a><ul><li class='active'><a href='1.1.html'>1.1</a></li><li><a href='1.2.html'>1.2</a></li></ul></li><li class='second'><a href='2.html'>2</a></li></ul>");
		}
	}