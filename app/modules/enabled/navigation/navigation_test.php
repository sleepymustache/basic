<?php
require_once(dirname(__FILE__) . '/class.navigation.php');

/**
 * Tests the \Module\Navigation\Builder class
 *
 * @internal
 * @date August 13, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.8
 * @license  http://opensource.org/licenses/MIT
 */
class TestOfNavigation extends UnitTestCase {
	function setUp() {
		$this->nav = new \Module\Navigation\Builder('{
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
					"id": "second",
					"title": "2",
					"link": "2.html",
					"class": "second"
				}
			]
		}');
	}

	function testNav() {
		$nav = $this->nav->show();
		$this->assertEqual($nav,'<ul><li><a target="_blank" href="1.html">1</a><ul><li><a href="1.1.html">1.1</a></li><li><a href="1.2.html">1.2</a></li></ul></li><li class="second"><a id="second" href="2.html">2</a></li></ul>');
	}

	function testTarget() {
		$nav = $this->nav->show();
		$this->assertEqual($nav,'<ul><li><a target="_blank" href="1.html">1</a><ul><li><a href="1.1.html">1.1</a></li><li><a href="1.2.html">1.2</a></li></ul></li><li class="second"><a id="second" href="2.html">2</a></li></ul>');
	}

	function testActive() {
		$this->nav->setCurrent('1.html');
		$nav = $this->nav->show();
		$this->assertEqual($nav,'<ul><li class="active"><a target="_blank" href="1.html">1</a><ul><li><a href="1.1.html">1.1</a></li><li><a href="1.2.html">1.2</a></li></ul></li><li class="second"><a id="second" href="2.html">2</a></li></ul>');
	}

	function testSubActive() {
		$this->nav->setCurrent('1.1.html');
		$nav = $this->nav->show();
		$this->assertEqual($nav,'<ul><li class="active"><a target="_blank" href="1.html">1</a><ul><li class="active"><a href="1.1.html">1.1</a></li><li><a href="1.2.html">1.2</a></li></ul></li><li class="second"><a id="second" href="2.html">2</a></li></ul>');
	}
}