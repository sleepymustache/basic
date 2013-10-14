<?php 
	require_once('../include/class.debug.php');

	class TestOfDebugging extends UnitTestCase {
		/* Make sure an email is sent */
		/*function testDebugEmail() {
			Debug::$enable_send = true;
			$this->assertTrue(Debug::sendEmail());
			Debug::$enable_send = false;
		}*/

		/* no output when $enabled_show is false*/
		function testDebugDoNotShow() {
			Debug::$enable_show = false;
			ob_start();
			Debug::out(array(
				'test' => 'data'
			));
			$output = ob_get_clean();
			$this->assertEqual('', $output);
		}
		/* no email is sent with $enabled_send is false */
		function testDebugDoNotEmail() {
			Debug::$enable_send = false;
			$this->assertFalse(Debug::sendEmail());
		}

		/* no database logging occures when $enabled_log is false */

		/* database logging is working */

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
		/* Make sure debug output is wrapped in pre */
		function testDebugShow() {
			Debug::$enable_show = true;
			ob_start();
			Debug::out(array(
				'test' => 'data'
			));
			$output = ob_get_clean();
			$this->assertNotNull($output);
			$this->assertPattern('/<pre>(.*)?<\/pre>/is', $output);
		}
	}