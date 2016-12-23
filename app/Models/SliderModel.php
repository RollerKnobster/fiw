<?php

namespace FIW\Models;

use Slim\Slim;
use Imagick;
use Exception;

class SliderModel
{
	protected $app;
	protected $slider_dir;

	public function __construct()
	{
		$this->app = Slim::getInstance();
		$this->slider_dir = implode(DIRECTORY_SEPARATOR, [ROOT_DIR, 'image', 'header', 'slider']);
	}

	/**
	 *
	 * Метод сканує папку зі слайдами і повертає масив картинок
	 *
	 * @return array
	 *
	 */
	public function listSlides()
	{
		$main_slider = [];

		foreach (scandir($this->slider_dir) as $img) {
			try{
				getimagesize(implode(DIRECTORY_SEPARATOR, [$this->slider_dir, $img]));
				$main_slider[preg_replace('/[^0-9]/', '', $img)] = $img;
			} catch (Exception $e){
				continue;
			}
		}

		ksort($main_slider);

		return $main_slider;
	}

	/**
	 *
	 * Метод зберігає слайд в папку
	 *
	 * @param string $tmp_file Шлях до тимчасового файлу
	 *
	 */
	public function saveSlide($tmp_file)
	{
		$num = max(array_map(function($i){ return preg_replace('/[^0-9]/', '', $i);}, scandir($this->slider_dir, SCANDIR_SORT_DESCENDING)));
		$num++;

		$file = implode(DIRECTORY_SEPARATOR, [$this->slider_dir, 'img'.$num.'.jpg']);

		try{
			$image = new Imagick($tmp_file);
		}catch(Exception $e){
			return false;
		}
		$image->setImageFormat('jpg');
		$image->setImageBackgroundColor('white');
		$image->setImageAlphaChannel(Imagick::ALPHACHANNEL_REMOVE);
		$image->mergeImageLayers(Imagick::LAYERMETHOD_FLATTEN);
		$image->setCompression(Imagick::COMPRESSION_JPEG);
		$image->setImageCompressionQuality(90);
		if (!$image->writeImage($file)) {
			return false;
		}
		@chmod($file, 0644);

		return 'img'.$num.'.jpg';
	}

}
