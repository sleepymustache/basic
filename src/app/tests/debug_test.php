<?php
  require_once('../core/class.sm.php');

  if (@!include_once($_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . 'settings.php')) {
    include_once('../core/settings.php');
  }

  require_once('../core/class.debug.php');

  use \Sleepy\Debug;
  class TestOfDebugging extends UnitTestCase {
    /**
     * Reset the Debug class in case someone else wants to use it
     */
    function tearDown() {
      Debug::disable();
      Debug::$enable_show = true;
    }

    /* there is only a static instance of Debug */
    function testDebugHighlander() {
      if (is_callable(array('Debug', '__construct'))) {
        $this->fail();
      }
      if (is_callable(array('Debug', '__clone'))) {
        $this->fail();
      }
      $this->pass();
    }

    /**
     * Debug should have show enabled by default
     * Make sure debug output is wrapped in pre
     */
    function testDebugShow() {
      ob_start();
      Debug::out('Testing, testing, 123.');
      $output = ob_get_clean();
      $this->assertPattern('/<pre>Testing, testing, 123.<\/pre>/is', $output);
    }

    /* Make sure an email is sent */
    function testDebugEmail() {
      Debug::$enable_show = false;
      Debug::$enable_send = true;
      Debug::out('Testing, testing, 123');
      $this->assertTrue(Debug::sendEmail());
      Debug::$enable_send = false;
    }

    /* no output when $enabled_show is false*/
    function testDebugDoNotShow() {
      Debug::$enable_show = false;
      ob_start();
      Debug::out('Testing, testing, 123');
      $output = ob_get_clean();
      $this->assertEqual('', $output);
    }

    /* no email is sent with $enabled_send is false */
    function testDebugDoNotEmail() {
      Debug::$enable_send = false;
      $this->assertFalse(Debug::sendEmail());
    }

    /* no database logging occurs when $enabled_log is false */

    /* database logging is working */
    function testDBLogging() {
      if (strlen(DBPASS) > 0) {
        Debug::$enable_show = false;
        Debug::$enable_log = true;
        $this->assertTrue(Debug::out('Testing, testing, 123'));
      }
    }
  }