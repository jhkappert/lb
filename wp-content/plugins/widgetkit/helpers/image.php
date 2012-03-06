<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: ImageWidgetkitHelper
		Image helper class.
*/
class ImageWidgetkitHelper extends WidgetkitHelper {
	
	/*
		Function: __construct
			Class Constructor.
	*/
	public function __construct($widgetkit) {
		parent::__construct($widgetkit);
	}

	/*
		Function: create
			Create a image object

		Parameters:
			$file - Image file path

		Returns:
			Object
	*/
	public function create($file) {
		return new WidgetkitImage($file);
	}
	
	/*
		Function: check
			Check GD library support

		Returns:
			Boolean
	*/
	public function check() {

		$gd_functions = array(
			'getimagesize',
			'imagecreatefromgif',
			'imagecreatefromjpeg',
			'imagecreatefrompng',
			'imagecreatetruecolor',
			'imagecopyresized',
			'imagecopy',
			'imagegif',
			'imagejpeg',
			'imagepng'
			);
		
		foreach ($gd_functions as $name) {
			if (!function_exists($name)) return false;
		}
		
		return true;
    }

	/*
		Function: prepareLazyload
			Prepare Html images for lazy loading

		Returns:
			String
	*/
	public function prepareLazyload($content) {
		
		if (stripos($content, '<img') !== false) {
			
			$imgs_regex = '#<img[^>]+>#im';
            preg_match_all($imgs_regex, $content, $images);

            foreach($images as $img){ $img = $img[0];

            	preg_match('/src=["|\']([^"]*)["|\']/i',$img, $src);

            	$tmp_img = str_replace($src[0], 'src="data:image/gif;base64,R0lGODlhAQABAJEAAAAAAP///////wAAACH5BAEHAAIALAAAAAABAAEAAAICVAEAOw==" data-'.$src[0], $img);
            	$content = str_replace($img, $tmp_img, $content);
            }
        }

		return $content;
	}

}

/*
	Class: WidgetkitImage
		Image class for info, resizing.
*/
class WidgetkitImage {

	protected $_file;
	protected $_resource;

    public function __construct($file) {
		$this->_file = $file;
	}
	
	public function __destruct() {
		
		if (is_resource($this->_resource)) {
			imagedestroy($this->_resource);
		}
		
    }

	/*
		Function: options
			Retrieve image options

		Returns:
			Array
	*/
	public function options() {

		$data = @getimagesize($this->_file);

		foreach (array(0 => 'width', 1 => 'height', 'bits' => 'bits', 'mime' => 'mime') as $key => $name) {
			$options[$name] = $data[$key];

			if ($key == 'mime') {
				$options['format'] = str_replace('image/', '', $data[$key]);
			}
		}

		return $options;
	}

	/*
		Function: resource
			Retrieve image resource

		Returns:
			Array
	*/
	public function resource() {

		if (empty($this->_resource)) {

			extract($this->options());

			if ($format == 'jpeg') {
	        	$this->_resource = imagecreatefromjpeg($this->_file);
			} else if ($format == 'png') {
	        	$this->_resource = imagecreatefrompng($this->_file);
			} else if ($format == 'gif') {
	        	$this->_resource = imagecreatefromgif($this->_file);
			}
			
		}

		return $this->_resource;
	}

	/*
		Function: output
			Get image output
			
		Returns:
			Void
	*/
    public function output($options = array()) {
		
		$options = $this->_options($options);
		$image   = $this->_image($options);

		if ($options['format'] == 'jpeg') {
	      	imagejpeg($image, $options['file'], 90);
		} else if ($options['format'] == 'png') {
	       	imagepng($image, $options['file']);
		} else if ($options['format'] == 'gif') {
	       	imagegif($image, $options['file']);
		}

    }

	/*
		Function: _options
			Parse image options using defaults

		Returns:
			Array
	*/
    protected function _options($options) {

		// get options
		$source  = $this->options();
		$options = array_merge(array('format' => 'auto', 'width' => 'auto', 'height' => 'auto', 'resize' => true, 'file' => null), $options);

		// set format
		if ($options['format'] == 'auto') {
			$options['format'] = $source['format'];
		}
		
		// set size
		if ($options['width'] == 'auto' && $options['height'] == 'auto') {
			$options['width'] = $source['width'];
			$options['height'] = $source['height'];
		} else if ($options['width'] == 'auto' && is_int($options['height'])) {
			$options['width'] = @($options['height'] / $source['height']) * $source['width'];
		} else if ($options['height'] == 'auto' && is_int($options['width'])) {
			$options['height'] = @($options['width'] / $source['width']) * $source['height'];
		}
	
		return $options;
	}

	/*
		Function: _image
			Create image resource with current options
			
		Returns:
			Mixed
	*/
    protected function _image($options) {

		// init vars
		$source = $this->options();
		$source['image'] = $this->resource();
		$source['x'] = 0;
		$source['y'] = 0;

        // resize image
		if ($options['resize']) {

			$resized['width']  = @($options['height'] / $source['height']) * $source['width'];
			$resized['height'] = @($options['width'] / $source['width']) * $source['height'];
			
			if ($options['width'] <= $resized['width']) {
				$width  = $resized['width'];
				$height = $options['height'];
				$source['x'] = intval(($resized['width'] - $options['width']) / 2);
			} else {
				$width  = $options['width'];
				$height = $resized['height'];
				$source['y'] = intval(($resized['height'] - $options['height']) / 2);
			}

			$image = imagecreatetruecolor($width, $height);

			// save transparent colors
			if ($options['format'] == 'png') {
				imagecolortransparent($image, imagecolorallocate($image, 0, 0, 0));
				imagealphablending($image, false);
				imagesavealpha($image, true);
			}
			
			// get and reallocate transparency-color for gif
			if ($options['format'] == 'gif') {
				imagealphablending($image, false);
				$transindex = imagecolortransparent($source['image']) <= imagecolorstotal($image) ? imagecolortransparent($source['image']) : imagecolorstotal($image);
				if ($transindex >= 0) {
					$transcol = imagecolorsforindex($source['image'], $transindex);
					$transindex = imagecolorallocatealpha($image, $transcol['red'], $transcol['green'], $transcol['blue'], 127);
					imagefill($image, 0, 0, $transindex);
				}
			}
			
			if (function_exists('imagecopyresampled')) {
				@imagecopyresampled($image, $source['image'], 0, 0, 0, 0, $width, $height, $source['width'], $source['height']);
			} else {
				@imagecopyresized($image, $source['image'], 0, 0, 0, 0, $width, $height, $source['width'], $source['height']);
			}
			
			// restore transparency for gif
			if ($options['format'] == 'gif') {
				if ($transindex >= 0) {
					imagecolortransparent($image, $transindex);
					for ($y=0; $y < imagesy($image); ++$y) {
						for ($x=0; $x < imagesx($image); ++$x) {
							if(((imagecolorat($image, $x, $y)>>24) & 0x7F) >= 100) {
								imagesetpixel($image, $x, $y, $transindex);				
							}
						}
					}
				}
			}	
			
			$source['image'] = $image;
		}
							
        // create image
		$image = imagecreatetruecolor($options['width'], $options['height']);
		
		// save transparent colors for png
		if ($options['format'] == 'png') {
			imagecolortransparent($image, imagecolorallocate($source['image'], 0, 0, 0));
			imagealphablending($image, false);
			imagesavealpha($image, true);
		}
		
		// get and reallocate transparency-color for gif
		if ($options['format'] == 'gif') {
			imagealphablending($image, false);
			$transindex = imagecolortransparent($source['image']);
			if ($transindex >= 0) {
				$transcol = imagecolorsforindex($source['image'], $transindex);
				$transindex = imagecolorallocatealpha($image, $transcol['red'], $transcol['green'], $transcol['blue'], 127);
				imagefill($image, 0, 0, $transindex);
			}
		}
		
		@imagecopy($image, $source['image'], 0, 0, $source['x'], $source['y'], $options['width'], $options['height']);

		// restore transparency for gif
		if ($options['format'] == 'gif') {
			if ($transindex >= 0) {
				imagecolortransparent($image, $transindex);
				for ($y=0; $y < imagesy($image); ++$y) {
					for ($x=0; $x < imagesx($image); ++$x) {
						if(((imagecolorat($image, $x, $y)>>24) & 0x7F) >= 100) {
							imagesetpixel($image, $x, $y, $transindex);				
						}
					}
				}
			}
		}	

		return $image;
    }

}