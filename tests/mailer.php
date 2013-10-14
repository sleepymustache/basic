<?php
	if (file_exists('../modules/disabled/mailer/class.mailer.php')) {
		require_once('../modules/disabled/mailer/class.mailer.php');
	} else {
		require_once('../modules/enabled/mailer/class.mailer.php');
	}

	class TestOfMailer extends UnitTestCase {
		function setUp() {
			$this->mail = new Mailer();
		}

		// add From field
		function testFrom() {
			// Test exception when we add a bad email
			$this->expectException(new Exception('The $email parameter has no RFC 2822 compliant addresses.'));
			$this->mail->addFrom("test@test");

			// Make sure we get true when the email is correct
			$this->assertTrue($this->mail->addFrom("hi.i.am.jaime@gmail.com"));
		}

		// add To field
		function testToSingleException() {
			// Test exception when we add a bad email
			$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: test@test'));
			$this->mail->addTo("test@test");

		}
		function testToMultiException() {
			// Test exception when adding more than 1 email
			$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: jude@judy'));
			$this->mail->addTo("hi.i.am.jaime@gmail.com,    jude@judy");
		}
		function testToSingle() {
			$this->assertTrue($this->mail->addTo("hi.i.am.jaime@gmail.com"));
		}
		function testToMulti() {
			// Add two emails at once
			$this->assertTrue($this->mail->addTo("jrodriguez@envivent.com, jaime@envivent.com"));
		}
		// add cc field
		function testCCSingleException() {
			// Test exception when we add a bad email
			$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: test@test'));
			$this->mail->addCc("test@test");

		}
		function testCCMultiException() {
			// Test exception when adding more than 1 email
			$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: jude@judy'));
			$this->mail->addCc("hi.i.am.jaime@gmail.com,    jude@judy");
		}
		function testCCSingle() {
			$this->assertTrue($this->mail->addCc("hi.i.am.jaime@gmail.com"));
		}
		function testCCMulti() {
			// Add two emails at once
			$this->assertTrue($this->mail->addCc("jrodriguez@envivent.com, jaime@envivent.com"));
		}
		// add bcc field
		function testBCCSingleException() {
			// Test exception when we add a bad email
			$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: test@test'));
			$this->mail->addBcc("test@test");

		}
		function testBCCMultiException() {
			// Test exception when adding more than 1 email
			$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: jude@judy'));
			$this->mail->addBcc("hi.i.am.jaime@gmail.com,    jude@judy");
		}
		function testBCCSingle() {
			$this->assertTrue($this->mail->addBcc("hi.i.am.jaime@gmail.com"));
		}
		function testBCCMulti() {
			// Add two emails at once
			$this->assertTrue($this->mail->addBcc("jrodriguez@envivent.com, jaime@envivent.com"));
		}
		// add to and cc
		// add to and bcc
		// add to and cc and bcc
		// add multiple to and cc and bcc
		// add external html file
		// add html text to body
		// add plain text body
		// validate email addresses
	}