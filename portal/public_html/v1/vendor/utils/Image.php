<?php

namespace api\v1\vendor\utils;

use \api\v1\vendor\error\Error;

Class Image {

	private $image;
	private $name;

	public function __construct($image, $name = '') 
	{
		$this->image = $image;
		if ($name === '') {
			$this->name = str_replace("." . pathinfo($image, PATHINFO_EXTENSION), '', $image);
		} else {
			$this->name = $name;
		}	
	}

	public function treatImage($params = []) 
	{	
		$info = getimagesize($this->image);
		list($width, $height) = getimagesize($this->image);
		$newWidth = array_key_exists('width', $params) ? $params['width'] : 200;
		$newHeight = array_key_exists('height', $params) ? $params['height'] : 200;
	
		switch ($info['mime']) {
			case 'image/jpeg':
				$imageCreateFunc = 'imagecreatefromjpeg';
				$newImageExt = 'jpg';
				break;
			case 'image/png':
				$imageCreateFunc = 'imagecreatefrompng';
				$newImageExt = 'png';
				break;
			default: 
				Error::generateErrorCustomNow("Media type $mediaType not acceptable", 422);
		}       

		$img = $imageCreateFunc($this->image);			
		$tmp = imagecreatetruecolor($newWidth, $newHeight);

		if ($newImageExt == 'png') {
			imagealphablending($tmp, false);
			imagesavealpha($tmp, true);
			imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
			imagepng($tmp, "{$this->name}.png", 9);
		} else {
			imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
			imagejpeg($tmp, "{$this->name}.jpg", 75);
		}

		imagedestroy($tmp);
	}
}