<?php
require_once('class.hooks.php');

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