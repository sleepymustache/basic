<?php
	require_once('../core/class.debug.php');

	class TestOfDebugging extends UnitTestCase {
		/**
		 * Reset the Debug class in case someone else wants to use it
		 */
		function tearDown() {
			\Sleepy\Debug::disable();
			\Sleepy\Debug::$enable_show = true;
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
			\Sleepy\Debug::out('Testing, testing, 123.');
			$output = ob_get_clean();
			$this->assertPattern('/<pre>Testing, testing, 123.<\/pre>/is', $output);
		}

		/* Make sure an email is sent */
		function testDebugEmail() {
			\Sleepy\Debug::$enable_show = false;
			\Sleepy\Debug::$enable_send = true;
			\Sleepy\Debug::out('Testing, testing, 123');
			$this->assertTrue(\Sleepy\Debug::sendEmail());
			\Sleepy\Debug::$enable_send = false;
		}

		/* no output when $enabled_show is false*/
		function testDebugDoNotShow() {
			\Sleepy\Debug::$enable_show = false;
			ob_start();
			\Sleepy\Debug::out('Testing, testing, 123');
			$output = ob_get_clean();
			$this->assertEqual('', $output);
		}

		/* no email is sent with $enabled_send is false */
		function testDebugDoNotEmail() {
			\Sleepy\Debug::$enable_send = false;
			$this->assertFalse(\Sleepy\Debug::sendEmail());
		}

		/* no database logging occurs when $enabled_log is false */

		/* database logging is working */
		function testDBLogging() {
			if (strlen(DBPASS) > 0) {
				\Sleepy\Debug::$enable_show = false;
				\Sleepy\Debug::$enable_log = true;
				$this->assertTrue(\Sleepy\Debug::out('Testing, testing, 123'));
			}
		}
	}