<?php
namespace Mobile;

/**
 * Because sessions use cookies, we can reset the session ID to check if it was
 * stored in a cookie or not. If cookies are not enabled, the session ID would
 * not carry over. The first page load will always return false :(
 * @return bool true if cookies are enabled
 */
function cookiesAreEnabled() {
	session_start();
	$originalSessionID = session_id();
	$sessionCopy = (isset($_SESSION)) ? $_SESSION : array();
	session_destroy();
	session_start();
	$_SESSION = $sessionCopy;
	return $originalSessionID === session_id();
}

function detect() {
?>
	<script>
		document.cookie = (window.devicePixelRatio > 1) ? "density=2" : "density=1";
		document.location.reload();
	</script>
	<noscript>
		<p>Please enable cookies</p>
	</noscript>
<?php
	die();
}

if (!isset($_COOKIE['density'])) {
	\Sleepy\Hook::doAction('sleepy_preprocess', '\Mobile\detect');
}