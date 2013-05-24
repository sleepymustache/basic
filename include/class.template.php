<?php
require_once('class.hooks.php');
/**
 * @page template1 Template Class
 *
 * Basic templating functionality
 *
 * @section usage Usage
 *
 * index.php
 * @code
 *	require_once('include/sleepy.php');
 *
 *	$page = new Template('templates/default.tpl');
 *	$page->bind('title', 'Sleepy Mustache');
 *	$page->bind('header', 'Hello world!');
 *	$page->show();
 * @endcode
 *
 * default.tpl
 * @code
 * 	<html>
 *		<head>
 *			<title>{{ title }}</title>
 *		</head>
 *		<body>
 *			<h1>{{ header }}</h1>
 *			<p>This page has been viewed {{ hits }} times.</p>
 *		</body>
 *	</html>
 * @endcode
 *
 * @section changelog Changelog
 * * Matches all placeholders, not just the was that were bound
 *
 * @date		May 24, 2012
 * @author		Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version		1.1
 * @copyright	GPL 3 http://cuttingedgecode.com
 */

class Template {
	protected $_file;
	protected $_data = array();

	private function checkTemplate() {
		if (!file_exists($this->_file)) {
			throw new Exception("Template " . $this->_file . " doesn't exist.");
		}
	}

	private function render($template) {
		preg_match_all('/{{.*}}/', $template, $matches);
		foreach (array_unique($matches[0]) as $key => $placeholder) {
			$key = trim(str_replace('{{', '', str_replace('}}', '', $placeholder)));
			$template = str_replace($placeholder, Hook::addFilter('render_placeholder_' . $key, $this->_data[$key]), $template);
		}

		return $template;
	}

	public function __construct($file) {
		$this->_file = $file;
	}

	public function bind($key, $value) {
		$this->_data[$key] = $value;
	}

	public function get($key) {
		return $this->_data[$key];
	}

	public function show() {
		// Check if template is ok
		$this->checkTemplate();

		// Render template file
		ob_start();
		include($this->_file);
		$template = ob_get_contents();
		ob_end_clean();

		echo Hook::addFilter('render_tempate', $this->render($template));
	}
}