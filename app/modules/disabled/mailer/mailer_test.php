<?php
require_once(dirname(__FILE__) . '/../../../include/class.debug.php');
require_once(dirname(__FILE__) . '/class.mailer.php');

/**
 * Tests the \Module\Mailer\Message() class
 *
 * @internal
 * @date August 13, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.8
 * @license  http://opensource.org/licenses/MIT
 */
class TestOfMailer extends UnitTestCase {
	function setUp() {
		$this->mail = new \Module\Mailer\Message();
	}

	// add From field
	function testFrom() {
		// Make sure we get true when the email is correct
		$this->assertTrue($this->mail->addFrom("hi.i.am.jaime@gmail.com"));
	}
	// add To field
	function testToSingle() {
		$this->assertTrue($this->mail->addTo("hi.i.am.jaime@gmail.com"));
	}
	function testToMulti() {
		// Add two emails at once
		$this->assertTrue($this->mail->addTo("jrodriguez@envivent.com, jaime@envivent.com"));
	}
	// add cc field
	function testCCSingle() {
		$this->assertTrue($this->mail->addCc("hi.i.am.jaime@gmail.com"));
	}
	function testCCMulti() {
		$this->assertTrue($this->mail->addCc("jrodriguez@envivent.com, jaime@envivent.com"));
	}
	// add bcc field
	function testBCCSingle() {
		$this->assertTrue($this->mail->addBcc("hi.i.am.jaime@gmail.com"));
	}
	function testBCCMulti() {
		// Add two emails at once
		$this->assertTrue($this->mail->addBcc("jrodriguez@envivent.com, jaime@envivent.com"));
	}
	// add subject
	function testSubject() {
		$this->mail->addSubject('Testing email');
	}
	// add external html file
	function testExternalHTML() {
		$this->mail->fetchHTML('./test.html');
		ob_start();
		\Sleepy\Debug::out($this->mail);
		$object = ob_get_clean();
		$this->assertPattern('/Heading 1/', $object);
	}
	// add html text to body
	function testTextBody() {
		$this->mail->msgText('Testing, testing, 123.');
		ob_start();
		\Sleepy\Debug::out($this->mail);
		$object = ob_get_clean();
		$this->assertPattern('/Testing, testing, 123./', $object);
	}
	// Test sending
	function testSend() {
		$this->mail->addFrom('jaime@envivent.com');
		$this->mail->addTo('hi.i.am.jaime@gmail.com');
		$this->mail->msgText('Testing, testing, 123.');
		$this->mail->send();
	}
	// Test exception when we add a bad from email
	function testFromException() {
		$this->expectException(new Exception('The $email parameter has no RFC 2822 compliant addresses.'));
		$this->mail->addFrom("test@test");
	}
	// Test exception when we add a bad email
	function testToSingleException() {
		$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: test@test'));
		$this->mail->addTo("test@test");
	}
	function testToMultiException() {
		// Test exception when adding more than 1 email
		$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: jude@judy'));
		$this->mail->addTo("hi.i.am.jaime@gmail.com,    jude@judy");
	}
	// Test exception when we add a bad email
	function testCCSingleException() {
		$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: test@test'));
		$this->mail->addCc("test@test");
	}
	function testCCMultiException() {
		// Test exception when adding more than 1 email
		$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: jude@judy'));
		$this->mail->addCc("hi.i.am.jaime@gmail.com,    jude@judy");
	}
	// Test exception when we add a bad email
	function testBCCSingleException() {
		$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: test@test'));
		$this->mail->addBcc("test@test");

	}
	function testBCCMultiException() {
		$this->expectException(new Exception('The $email parameter has a non RFC 2822 compliant addresses: jude@judy'));
		$this->mail->addBcc("hi.i.am.jaime@gmail.com,    jude@judy");
	}
	function testSubjectException() {
		$this->expectException(new Exception('The subject cannot be longer than 78 characters.'));
		$this->mail->addSubject('12345678901234567890123456789012345678901234567890123456789012345678901234567890');
	}
	function testSendException1() {
		$this->expectException(new Exception('You forgot to addFrom();'));
		$this->mail->send();
	}
	function testSendException2() {
		$this->expectException(new Exception('You forgot to addTo();'));
		$this->mail->addFrom('anonymous@mailinator.com');
		$this->mail->send();
	}
	function testSendException3() {
		$this->expectException(new Exception('You forgot to add content.'));
		$this->mail->addFrom('anonymous@mailinator.com');
		$this->mail->addTo('jaime@mailinator.com');
		$this->mail->send();
	}
}