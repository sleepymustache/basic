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
 * * fixed #each
 *
 * @todo  add #if
 *
 * @date		May 30, 2013
 * @author		Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version		1.2
 * @copyright	GPL 3 http://cuttingedgecode.com
 */

class Template {
	/**
	 * string The extension for template files
	 */
	public $extension = ".tpl";

	/**
	 * string The template directory
	 */
	public $directory;

	/**
	 * string The template file
	 * @protected
	 */
	protected $_file;

	/**
	 * array The data bound to the template
	 * @protected
	 */
	protected $_data = array();

	/**
	 * Does the template exist?
	 * @param  string $file Name of template
	 * @return Bool         True if template exists
	 * @private
	 */
	private function checkTemplate($file) {
		if (!file_exists($this->directory . $file . $this->extension)) {
			throw new Exception("Template " . $this->directory . $this->_file . $this->extension . " doesn't exist.");
		}

		return true;
	}

	/**
	 * Renders the template
	 * @param  string $template The template to render
	 * @param  array $data      The data bound to the template
	 * @return string           The rendered template
	 */
	private function render($template, $data) {
		// Process #includes
		preg_match_all('/{{\s*#include\s.*}}/', $template, $includes);
		foreach (array_unique($includes[0]) as $index => $file) {
			$index = trim(str_replace('{{', '', str_replace('}}', '', $file)));

			ob_start();
			include($this->directory . str_replace('#include ', '', $index) . $this->extension);
			$template = str_replace($file, $this->render(ob_get_contents(), $data), $template);
			ob_end_clean();
		}

		// Process #each
		preg_match_all('/{{\s*#each\s.*\s}}.*\/each\s*}}/s', $template, $loops);
		foreach (array_unique($loops[0]) as $key => $value) {
			preg_match('/(?<for>\w+) in (?<in>\w+)/', $value, $forin);
			$new_template = preg_replace('/{{\s*#each\s.*\s*}}/', '', $value);
			$new_template = preg_replace('/{{\s*\/each\s*}}/', '', $new_template);

			// Iterate through each
			foreach ($data[$forin['in']] as $new_data) {
				$new_data = (array) $new_data;

				// add the 'for' to the variable key
				foreach ($new_data as $k => $v) {
					$newKey = $forin['for'] . "." . $k;
					$new_data[$newKey] = $new_data[$k];
					unset($new_data[$k]);
				}

				$rendered = $rendered . $this->render($new_template, $new_data);
			}

			$template = str_replace($value, $rendered, $template);
		}

		// Do the rest
		preg_match_all('/{{.*}}/', $template, $matches);
		foreach (array_unique($matches[0]) as $key => $placeholder) {
			$key = trim(str_replace('{{', '', str_replace('}}', '', $placeholder)));
			$template = str_replace($placeholder, Hook::addFilter('render_placeholder_' . $key, $data[$key]), $template);
		}

		return $template;
	}

	/**
	 * The constructor
	 * @param string $template The name of the template
	 */
	public function __construct($template) {
		$this->directory = $_SERVER['DOCUMENT_ROOT'] . "/templates/";
		$this->_file = $template;
	}

	/**
	 * Binds data to the template placeholders
	 * @param  string $placeholder   The template placeholder
	 * @param  mixed  $value         The value that replaced the placeholder
	 */
	public function bind($placeholder, $value) {
		$this->_data[$placeholder] = $value;
	}

	/**
	 * Gets the data for a placeholder
	 * @param  string $placeholder The placeholder
	 * @return mixed               The data stored in the placeholder
	 */
	public function get($key) {
		return $this->_data[$key];
	}

	/**
	 * Shows the rendered template
	 */
	public function show() {
		// Check if template is ok
		$this->checkTemplate($this->_file);

		// Render template file
		ob_start();
		include($this->directory . $this->_file . $this->extension);
		$template = $this->render(ob_get_contents(), $this->_data) ;
		ob_end_clean();

		$template = Hook::addFilter('render_template_' . $this->_file, $template);
		echo Hook::addFilter('render_template', $template);
	}
}