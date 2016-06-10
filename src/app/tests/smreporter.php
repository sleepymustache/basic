<?php

require_once('simpletest/reporter.php');

class SMReporter extends HtmlReporter {
	public function paintHeader($test_name) {
		$this->sendNoCacheHeaders();
		echo "<!DOCTYPE html>";
		echo "<html>\n<head>\n<title>SleepyMustache Tests</title>\n";
		echo "<meta charset=\"UTF-8\">\n";
		echo "<style>\n";
		echo $this->getCss() . "\n";
		echo "</style>\n";
		echo "</head>\n<body>\n";
		echo "<h1>SleepyMustache Tests</h1>\n";
		echo "<h2>Test Results</h2>";
		echo "<div style='margin-bottom:24px;height:350px;overflow-y:scroll;'>";
	}

	protected function getCss() {
	    return  '.fail { background-color: inherit; color: red; }' .
	            '.pass { background-color: inherit; color: green; }' .
	            'div.fail { background-color: red; color: white; padding: 12px;}' .
	            'div.pass { background-color: green; color: white; padding: 12px;}' .
	            ' pre { background-color: lightgray; color: black; }';
	}

	public function paintFooter($test_name)
	{
		echo "</div>";
	    $colour = ($this->getFailCount() + $this->getExceptionCount() > 0 ? 'fail' : 'pass');
	    echo '<div class="' . $colour . '">';
	    echo $this->getTestCaseProgress() . '/' . $this->getTestCaseCount();
	    echo " test suites completed:\n";
	    echo '<strong>' . $this->getPassCount() . '</strong> passes, ';
	    echo '<strong>' . $this->getFailCount() . '</strong> fails and ';
	    echo '<strong>' . $this->getExceptionCount() . '</strong> exceptions.';
	    echo "</div>\n";
	    echo "</body>\n</html>\n";
	}

	public function paintPass($message) {
		parent::paintPass($message);
		echo "<div>";
		echo "<span class=\"pass\">Pass</span>: ";
		$breadcrumb = $this->getTestList();
		array_shift($breadcrumb);
		array_shift($breadcrumb);
		echo implode("->", $breadcrumb);
		echo "</div>";
	}

	public function paintFail($message) {
		SimpleReporter::paintFail($message);

		echo "<div>";
		echo "<span class=\"fail\">Fail</span>: ";
		$breadcrumb = $this->getTestList();
		array_shift($breadcrumb);
		array_shift($breadcrumb);
		echo implode("->", $breadcrumb);
		echo "<pre style='padding-left:24px;white-space: pre-wrap;'>" . htmlspecialchars($message) . "</pre>";
		echo "</div>";
		flush();
	}
}