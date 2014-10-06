<?php
namespace Module\Mailer;

/**
 * Simplifies sending emails.
 *
 * Simplifies sending emails by automatically verifying email addresses and
 * fetching HTML.
 *
 * ### Usage
 *
 * <code>
 *   $m = new \Module\Mailer\Message();
 *   $m->addTo("test@test.com");
 *   $m->addFrom("from.me@test.com");
 *   $m->addSubject("This is a test, don't panic.");
 *   $m->fetchHTML("http://test.com/template.php");
 *   $m->send();
 * </code>
 *
 * ### Changelog
 *
 * ## Version 1.8
 * * Added namespacing
 *
 * ## Version 1.6
 * * Fixed bug with BCC and CC
 *
 * @date	August 13, 2014
 * @author	Jaime Rodriguez, hi.i.am.jaime@gmail.com
 * @version	1.8
 * @license  http://opensource.org/licenses/MIT
 */
class Message {
	private $to;
	private $cc;
	private $bcc;
	private $from;
	private $subject;
	private $body;
	private $html;

	public function __construct() {
		$this->to;
		$this->cc;
		$this->bcc;

		$this->addSubject();
		$this->html = false;
	}

	/**
	 * Sends the email.
	 *
	 * @return bool Was the email successfully sent?
	 */
	public function send() {
		$headers = "";

		if (count($this->from) < 1) {
			throw new \Exception('You forgot to addFrom();');
		}

		if (count($this->to) < 1) {
			throw new \Exception("You forgot to addTo();");
		}

		if (strlen($this->body) < 1) {
			throw new \Exception("You forgot to add content.");
		}

		if (!isset($this->subject)) {
			$this->addSubject();
		}

		// To send HTML mail, the Content-type header must be set
		if ($this->html) {
			$headers  .= 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
		}

		// Add From
		$headers .= 'From: ' . $this->from . "\r\n";;

		// Add CC's if there are any
		if (isset($this->cc)) {
			$headers .= 'Cc: ' . implode(',', $this->cc) . "\r\n";
		}

		// Add BCC's if there are any
		if (isset($this->bcc)) {
			$headers .= 'Bcc: ' . implode(',', $this->bcc) . "\r\n";
		}

		// Mail it
		if (!mail(implode(",", $this->to), $this->subject, $this->body, $headers)) {
			throw new \Exception("Mail was not sent.");
		} else {
			return true;
		}
	}

	/**
	 * Adds a primary recipient.
	 *
	 * @param string $email A valid email address.
	 */
	public function addTo($email) {
		$emails = explode(',', $email);

		foreach ($emails as $e) {
			$e = trim($e);
			if ($this->rfcCheck($e)) {
				$this->to[] = $e;
			} else {
				throw new \Exception("The \$email parameter has a non RFC 2822 compliant addresses: {$e}");
			}
		}

		return true;
	}

	/**
	 * Adds a Carbon Copy recipient.
	 * @param string $email A valid email address.
	 */
	public function addCc($email) {
		$emails = explode(',', $email);

		foreach ($emails as $e) {
			$e = trim($e);
			if ($this->rfcCheck($e)) {
				$this->cc[] = $e;
			} else {
				throw new \Exception("The \$email parameter has a non RFC 2822 compliant addresses: {$e}");
			}
		}

		return true;
	}

	/**
	 * Adds a Blind Carbon Copy recipient.
	 * @param string $email A valid email address
	 */
	public function addBcc($email) {
		$emails = explode(',', $email);

		foreach ($emails as $e) {
			$e = trim($e);
			if ($this->rfcCheck($e)) {
				$this->bcc[] = $e;
			} else {
				throw new \Exception("The \$email parameter has a non RFC 2822 compliant addresses: {$e}");
			}
		}

		return true;
	}

	/**
	 * Sets a senders email address.
	 * @param string $email
	 *   A valid email address.
	 */
	public function addFrom($email) {
		if ($this->rfcCheck($email)) {
			$this->from = $email;
			return true;
		} else {
			throw new \Exception("The \$email parameter has no RFC 2822 compliant addresses.");
		}
	}

	/**
	 * Gets HTML for a $url and repalces anything in the body of the email with
	 * the HTML that it fetched.
	 * @param string $url the url of an html file for the email body.
	 */
	public function fetchHtml($url) {
		$tempHtml = file_get_contents($url);

		if (isset($tempHtml)) {
			$this->html = true;
			$this->body = $tempHtml;
		} else {
			throw new \Exception("Failed to capture data from " . $url);
		}
	}

	/**
	 * Sets the body of the email to text.
	 * @param string $msg a string for the body
	 * @param boolean $switch an optional parameter for overloading $this->html
	 */
	public function msgText($msg, $switch = false) {
		$this->html = $switch;
		$this->body = $msg;
	}

	/**
	 * Adds a subject to the email. If there is no subject the time will be used.
	 * @param string $subject
	 *   The subject of the email
	 */
	public function addSubject($subject='') {
		if (strlen($subject) > 78) {
			throw new \Exception('The subject cannot be longer than 78 characters.');
		}

		if ($subject == '') {
			$this->subject = time();
		} else {
			$this->subject = $subject;
		}
	}

	/**
	 * Extracts the email address from a RFC 2822 compliant string
	 * @param  string $string
	 *   RFC 2822 compliant string
	 * @return string[]
	 *   an array of email addresses
	 */
	private function rfcCheck($string) {
		if (filter_var($string, FILTER_VALIDATE_EMAIL)) {
			return true;
		} else {
			return false;
		}
	}
}