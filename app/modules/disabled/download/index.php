<?php
namespace Module\Download;

require_once(dirname(__FILE__) . '/../../../include/global.php');
require_once('class.download.php');

if (isset($_GET['url'])) {
	$fd = new Force();
	$fd->download(DIRBASE . $_GET['url']);
}