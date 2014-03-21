<?php
	class ForceDownload {
		public function getAnchor($text, $link, $target="_blank") {
			return "<a href=\"" . URLBASE . "modules/enabled/download/?url={$link}\">{$text}</a>";
		}

		public function download($file) {
			header('Content-Type: application/octet-stream');
			header("Content-Transfer-Encoding: Binary"); 
			header("Content-disposition: attachment; filename=\"" . basename($file) . "\""); 
			readfile($file);	
		}
	}