<?php
	session_start();

	define('DIRBASE', $_SERVER['DOCUMENT_ROOT'] . '/setup/');
	require_once($_SERVER['DOCUMENT_ROOT'] . '/include/class.template.php');

	// We're in the last step, render the global.php file
	$c = new \Sleepy\Template('configuration');
	$c->bind('stageURL', @addslashes($_SESSION['steps'][2]['txtDomain']));
	$c->bind('stageURLBASE', @addslashes($_SESSION['steps'][2]['txtURL']));
	$c->bind('stageDIRBASE', @addslashes($_SESSION['steps'][2]['txtPath']));
	$c->bind('stageDBHOST', @addslashes($_SESSION['steps'][2]['txtHost']));
	$c->bind('stageDBUSER', @addslashes($_SESSION['steps'][2]['txtUser']));
	$c->bind('stageDBPASS', @addslashes($_SESSION['steps'][2]['txtPass']));
	$c->bind('stageDBNAME', @addslashes($_SESSION['steps'][2]['txtName']));
	$c->bind('stageFROM', @addslashes($_SESSION['steps'][2]['txtEmailFrom']));
	$c->bind('stageTO', @addslashes($_SESSION['steps'][2]['txtEmailTo']));
	$c->bind('stageCC', @addslashes($_SESSION['steps'][2]['txtEmailCC']));
	$c->bind('stageBCC', @addslashes($_SESSION['steps'][2]['txtEmailBCC']));
	$c->bind('stageANALYTICS', @addslashes($_SESSION['steps'][2]['txtAnalytics']));
	$c->bind('liveURL', @addslashes($_SESSION['steps'][3]['txtDomain']));
	$c->bind('liveURLBASE', @addslashes($_SESSION['steps'][3]['txtURL']));
	$c->bind('liveDIRBASE', @addslashes($_SESSION['steps'][3]['txtPath']));
	$c->bind('liveDBHOST', @addslashes($_SESSION['steps'][3]['txtHost']));
	$c->bind('liveDBUSER', @addslashes($_SESSION['steps'][3]['txtUser']));
	$c->bind('liveDBPASS', @addslashes($_SESSION['steps'][3]['txtPass']));
	$c->bind('liveDBNAME', @addslashes($_SESSION['steps'][3]['txtName']));
	$c->bind('liveFROM', @addslashes($_SESSION['steps'][3]['txtEmailFrom']));
	$c->bind('liveTO', @addslashes($_SESSION['steps'][3]['txtEmailTo']));
	$c->bind('liveCC', @addslashes($_SESSION['steps'][3]['txtEmailCC']));
	$c->bind('liveBCC', @addslashes($_SESSION['steps'][3]['txtEmailBCC']));
	$c->bind('liveANALYTICS', @addslashes($_SESSION['steps'][3]['txtAnalytics']));
	$c->bind('devURLBASE', @addslashes($_SESSION['steps'][1]['txtURL']));
	$c->bind('devDIRBASE', @addslashes($_SESSION['steps'][1]['txtPath']));
	$c->bind('devDBHOST', @addslashes($_SESSION['steps'][1]['txtHost']));
	$c->bind('devDBUSER', @addslashes($_SESSION['steps'][1]['txtUser']));
	$c->bind('devDBPASS', @addslashes($_SESSION['steps'][1]['txtPass']));
	$c->bind('devDBNAME', @addslashes($_SESSION['steps'][1]['txtName']));
	$c->bind('devFROM', @addslashes($_SESSION['steps'][1]['txtEmailFrom']));
	$c->bind('devTO', @addslashes($_SESSION['steps'][1]['txtEmailTo']));
	$c->bind('devCC', @addslashes($_SESSION['steps'][1]['txtEmailCC']));
	$c->bind('devBCC', @addslashes($_SESSION['steps'][1]['txtEmailBCC']));
	$c->bind('stageANALYTICS', @addslashes($_SESSION['steps'][1]['txtAnalytics']));

	$handle = fopen('../../include/global.php', "a+");
	if ($handle) {
		fseek($handle, 0);
		ftruncate($handle, 0);
		if (!fwrite($handle, "<?php\n" . $c->retrieve())) {
			throw new \Exception("Cannot write data to configuration file, please check permissions");
		}
	} else {
		throw new \Exception('Could create configuration file, check permissions');
	}

	$page = new \Sleepy\Template('setup');

	// SEO
	$page->bind('title', 'Sleepy Mustache');
	$page->bind('description', 'This is the description');
	$page->bind('keywords', 'blog, sleepy mustache, framework');

	// Content
	$page->bind('header', 'sleepy<span>MUSTACHE</span>');
	$page->bind('heading', 'Setup Complete');
	$page->bindStart();
?>
	<p>
		Setup is now complete. For security reasons, you should delete the
		<em>/setup/</em> folder from your server as it is no longer needed. To
		modify settings in the future, please modify the
		<em>/include/global.php</em> file.
	</p>
	<p>
		<a href="\">Continue to site</a>
	</p>
<?php
	$page->bindStop('help');
	$page->show();
/*
	class removeMyself {
		public function deleteDirectory($dirPath) {
			if (is_dir($dirPath)) {
				$objects = scandir($dirPath);
				foreach ($objects as $object) {
					if ($object != "." && $object !="..") {
						if (filetype($dirPath . DIRECTORY_SEPARATOR . $object) == "dir") {
							$this->deleteDirectory($dirPath . DIRECTORY_SEPARATOR . $object);
						} else {
							unlink($dirPath . DIRECTORY_SEPARATOR . $object);
						}
					}
				}
				reset($objects);
				@rmdir($dirPath);
			}
		}

		public function __destruct() {
			$this->deleteDirectory($_SERVER['DOCUMENT_ROOT'] . '/setup');
		}
	}

	$delete = new removeMyself();*/