<?php
namespace Module\Download;

/**
 * Forces a download of a file
 *
 * ### Usage
 *
 * <code>
 *   $d= new \Module\Download\Forces()
 *
 *   $d->getAnchor('ISI', 'isi.pdf');
 * </code>
 *
 * ### Changelog
 *
 * ## Version 1.2
 * * Added namespacing
 *
 * @date August 13, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.2
 * @license  http://opensource.org/licenses/MIT
 * @internal
 */

class Forces {
	public function getAnchor($text, $link, $target="_blank") {
		return "<a target=\"{$target}\" href=\"" . URLBASE . "modules/enabled/download/?url={$link}\">{$text}</a>";
	}

	public function download($file) {
		header('Content-Type: application/octet-stream');
		header("Content-Transfer-Encoding: Binary");
		header("Content-disposition: attachment; filename=\"" . basename($file) . "\"");
		readfile($file);
	}
}