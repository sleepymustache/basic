<?php
namespace Mobile;

class Retina {
	public function processDownsize($folders) {
		$processable = [];

		foreach ($folders as $folder) {
			$images = glob($folder . '/*@2x.*');

			if (!is_array($images)) {
				$images = [];
			}

			foreach($images as $image) {
				$smallImage = str_replace('@2x', '', $image);
				if (!file_exists($smallImage)) {
					$this->scaleDown($image);
				}
			}
		}
	}

	public function scaleDown($filename, $percent=0.5) {
		$isJPEG = (substr($filename, strlen($filename) - 3, 3) === 'jpg') ? true : false;

		// Get new sizes
		list($width, $height) = getimagesize($filename);

		$newwidth = round($width * $percent);
		$newheight = round($height * $percent);
		$newwidth =  ($newwidth & 1) ? $newwidth + 1 : $newwidth;
		$newheight = ($newheight & 1) ? $newheight + 1 : $newheight;

		// Load
		$thumb = imagecreatetruecolor($newwidth, $newheight);

		if ($isJPEG) {
			$source = imagecreatefromjpeg($filename);
		} else {
			$source = imagecreatefrompng($filename);
			imagealphablending($thumb, false);
			imagesavealpha($thumb, true);
		}

		// Resize
		imagecopyresampled($thumb, $source, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);

		// Output and free memory
		if ($isJPEG) {
			imagejpeg($thumb, str_replace('@2x', '', $filename), 100);
		} else {
			imagepng($thumb, str_replace('@2x', '', $filename), 9);
		}

		imagedestroy($thumb);
	}
}