<?php
namespace Download;

/**
 * @page download Download Class
 *
 * Used to force a download of a file
 *
 * @section usage Usage
 * @code
 *   $d= new Download/Forces()
 *
 *   $d->getAnchor('ISI', 'isi.pdf');
 * @endcode
 *
 * @section changelog Changelog
 *   ## Version 1.2
 *   * Added namespacing
 *
 * @date August 13, 2014
 * @author Jaime A. Rodriguez <hi.i.am.jaime@gmail.com>
 * @version 1.2
 * @copyright  GPL 3 http://rodriguez-jr.com
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