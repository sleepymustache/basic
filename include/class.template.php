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
 * * added nesting to the #each loop
 *
 * @todo add #if
 *
 * @date		June 24, 2013
 * @author		Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version		1.3
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
	 * Given a path, the function returns a piece of $arr. For example
	 * 'name.first' will return $arr['name']['first']
	 * @param  array  $arr  An array to search using the $path
	 * @param  string $path A path representing the dimensions of the array
	 * @return mixed  a sub-array or string
	 */
	private function assignArrayByPath(&$arr, $path) {
		$keys = explode('.', $path);

		while ($key = array_shift($keys)) {
			$arr = &$arr[$key];
		}

		return $arr;
	}

	/**
	 * Renders the template
	 * @param  string $template The template to render
	 * @param  array $data      The data bound to the template
	 * @return string           The rendered template
	 */
	private function render($template, $data) {

		// Process the includes
		if (preg_match('/{{\s*#include\s.*}}/', $template, $include)) {
			$index = trim(str_replace('{{', '', str_replace('}}', '', $include[0])));
			ob_start();
			include($this->directory . str_replace('#include ', '', $index) . $this->extension);
			$template = str_replace($include[0], $this->render(ob_get_contents(), $data), $template);
			ob_end_clean();

			return $this->render($template, $data);
		}

		// Process the #each blocks
		if (preg_match_all('/({{\s?(#each)(.+?)}})(?:[^{}]+|(?R))*({{\s?\/each\s?}})/ism', $template, $loops)) {
			// For every #each
			foreach ($loops[0] as $value) {
				// Reset rendered data
				$rendered = "";

				// Stores the values of <for> and <in> into $forin
				preg_match('/{{\s?#each\s(?<for>\w+) in (?<in>.*?)\s?}}/', $value, $forin);

				// Removes the each loop
				$new_template = preg_replace('/{{\s?#each.*?}}/s', '', $value, 1);
				$new_template = preg_replace('/{{\s?\/each\s?}}$/s', '', $new_template, 1);

				// get the array based on the <in>

				$in = $this->assignArrayByPath($data, $forin['in']);

				// for each changelog
				foreach ($in as $new_data) {

					// Make sure it's an array
					$new_data = (array) $new_data;
					// Make the $new_data match the <for>
					$new_data[$forin['for']] =  $new_data;

					// render the new template
					$rendered = $rendered . $this->render($new_template, array_merge($new_data, $data));
				}

				$template = str_replace($value, $rendered, $template);
			}
		}

		// Find all the single placeholders
		preg_match_all('/{{\s?(.+?)\s?}}/', $template, $matches);

		// For each replace with a value
		foreach (array_unique($matches[0]) as $key => $placeholder) {
			$key = trim(str_replace('{{', '', str_replace('}}', '', $placeholder)));

			// make sure it isn't an array. We use #each for those.
			if (is_array($data[$key])) {
				throw new Exception("Arrays can only be bound in #each loops. Placeholder: {$key}");
			}

			$template = str_replace($placeholder, Hook::addFilter('render_placeholder_' . $key, $this->assignArrayByPath($data, $key)), $template);
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
		try {
				// Check if template is ok
			$this->checkTemplate($this->_file);

			// Render template file
			ob_start();
			include($this->directory . $this->_file . $this->extension);
			$template = $this->render(ob_get_contents(), $this->_data) ;
			ob_end_clean();
			$template = Hook::addFilter('render_template_' . $this->_file, $template);
			//die();
			echo Hook::addFilter('render_template', $template);
		} catch (Exception $e) {
			ob_end_clean();
			ob_end_clean();
			echo "Error: " . $e->getMessage();
		}
	}
}