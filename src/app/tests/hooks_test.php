<?php
  require_once('../core/class.hooks.php');

  use \Sleepy\Hook;

  class TestOfHooks extends UnitTestCase {
    function testHooksAction() {
      // function to run if action works
      function action() {
        echo "YES!";
      }

      Hook::doAction('TestHook', 'action');

      // lets capture what the action does
      ob_start();
      Hook::addAction('TestHook');
      $passed = ob_get_clean();

      // Did the action do anything?
      if (strlen($passed) > 0) {
        $this->pass();
      } else {
        $this->fail();
      }
    }

    function testHooksFilter() {
      // function to run if filter works
      function filter($arg) {
        return $arg . " Smith";
      }

      Hook::applyFilter('TestFilter', 'filter');

      // Did the filter do anything?
      if (Hook::addFilter('TestFilter', "John") === "John Smith") {
        $this->pass();
      } else {
        $this->fail();
      }
    }
  }