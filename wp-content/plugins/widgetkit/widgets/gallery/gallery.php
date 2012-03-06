<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: GalleryWidgetkitHelper
		Photo gallery helper class
*/
class GalleryWidgetkitHelper extends WidgetkitHelper {

	/* type */
	public $type;

	/* options */
	public $options;

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct($widgetkit) {
		parent::__construct($widgetkit);

		// init vars
		$this->type    = strtolower(str_replace('WidgetkitHelper', '', get_class($this)));
		$this->options = $this['system']->options;

		// create cache
		$cache = $this['path']->path('cache:');
		if ($cache && !file_exists($cache.'/gallery')) {
			mkdir($cache.'/gallery', 0777, true);
		}

		// register path
        $this['path']->register(dirname(__FILE__), $this->type);
        $this['path']->register($this['path']->path('cache:gallery'), 'gallery.cache');
	}

	/*
		Function: get
			Get widget

		Returns:
			Array
	*/
	public function get($id = 0) {

		// get widget
		if ($id) {
			$widget = $this['widget']->get($id);
		}

		// set defaults
		foreach (array('id' => 0, 'type' => $this->type, 'name' => null, 'settings' => array(), 'paths' => array(), 'captions' => array()) as $var => $val) {
			if (!isset($widget->$var)) {
				$widget->$var = $val;
			}
		}
		
		return $widget;
	}

	/*
		Function: site
			Site init actions

		Returns:
			Void
	*/
	public function site() {
	
		// add javascripts
		$this['asset']->addFile('js', 'gallery:js/lazyloader.js');
		
        // add style stylesheets
		foreach ($this['path']->dirs('gallery:styles') as $style) {

			// style
			if ($this['path']->path("gallery:styles/$style/style.css")) {
				$this['asset']->addFile('css', "gallery:styles/$style/style.css");
			}

			// rtl
			if ($this['system']->options->get('direction') == 'rtl' && $this['path']->path("gallery:styles/$style/rtl.css")) {
				$this['asset']->addFile('css', "gallery:styles/$style/rtl.css");
			}

		}

	}

	/*
		Function: render
			Render widget on site

		Returns:
			String
	*/
	public function render($widget) {
		
		// get images and style
        $style = isset($widget->settings['style']) ? $widget->settings['style'] : 'default';
		
        return $this['template']->render("gallery:styles/$style/template", array('widget' => $widget));
	}

	/*
		Function: images
			Get widget images

		Returns:
			Array
	*/
	public function images($widget, $overrides = array()) {

		$images   = array();
		$settings = $widget->settings;
		$captions = $widget->captions;
		$links    = $widget->links;
		$cache    = $this['path']->path('gallery.cache:');
		
		// merge setting overrides
		if (is_array($overrides)) {
			$settings = array_merge($settings, $overrides);
		}
		
		// create cache
		if ($cache && !file_exists($cache.'/'.$widget->id)) {
			mkdir($cache.'/'.$widget->id, 0777, true);
		}

		if (is_array($widget->paths)) {
			foreach ($widget->paths as $path) {
				foreach ($this['path']->files("media:$path") as $file) {
					if (preg_match("/(\.gif|\.jpg|\.jpeg|\.png)$/i", $file)) {

						// image data
						$image['name']     = basename($file);
						$image['ext']      = pathinfo($file, PATHINFO_EXTENSION);
						$image['filename'] = pathinfo($file, PATHINFO_FILENAME);
						$image['caption']  = isset($captions[$path.'/'.$file]) ? $captions[$path.'/'.$file] : null;
						$image['link']     = isset($links[$path.'/'.$file]) ? $links[$path.'/'.$file] : null;
						$image['file']     = sprintf("media:%s/%s", $path, $file);
						$image['cache']    = sprintf("gallery.cache:/%s/%s-%s.%s", $widget->id, $image['filename'], substr(md5($image['file'].$settings['width'].$settings['height']), 0, 10), $image['ext']);
						$image['url']      = $this['path']->url($image['file']);

						// cache image
						if ($cache && !$this['path']->path($image['cache'])) {
														
							if (isset($settings['animated']) && $settings['animated'] == 'kenburns' && is_numeric($settings['width']) && is_numeric($settings['height'])){
								
								$this['image']->create($this['path']->path($image['file']))->output(array('width' => $settings['width'] * 1.2, 'height' => $settings['height'] * 1.2, 'file' => $cache.preg_replace('/^gallery\.cache:/', '', $image['cache'], 1)));
							} else {
								
								$this['image']->create($this['path']->path($image['file']))->output(array('width' => $settings['width'], 'height' => $settings['height'], 'file' => $cache.preg_replace('/^gallery\.cache:/', '', $image['cache'], 1)));
							}

						}
						
						$image['cache_url'] = $this['path']->url($image['cache']);

						if ($p = $this['path']->path($image['cache'])) {
							if(!is_numeric($settings['width']) || !is_numeric($settings['height'])){
								list($image['width'], $image['height']) = @getimagesize($p);
							}else{
								$image['width']  = $settings['width'];
								$image['height'] = $settings['height'];
							}
						}

						array_push($images, $image);
					}
				}
			}
		}

		// random order
		if(count($images) && isset($settings['order']) && $settings['order'] =="random") {
		   shuffle($images);
		}

		return $images;
	}

	/*
		Function: dashboard
			Render dashboard layout

		Returns:
			Void
	*/
	public function dashboard() {
		
		// init vars
		$xml = simplexml_load_file($this['path']->path('gallery:gallery.xml'));
		$galleries = $this['widget']->all($this->type);

		// add js
        $this['asset']->addFile('js', 'gallery:js/dashboard.js');

		// render dashboard
		echo $this['template']->render('gallery:layouts/dashboard', compact('xml', 'galleries'));
	}

	/*
		Function: config
			Save configuration

		Returns:
			Void
	*/
	public function config() {
		
		// save configuration
	    foreach ($this['request']->get('post:', 'array') as $option => $value) {
	        if (preg_match('/^gallery_/', $option)) {
				$this['system']->options->set($option, $value);
	        }
	    }

		$this['system']->saveOptions();
	}

	/*
		Function: edit
			Edit action

		Returns:
			Void
	*/
	public function edit($id = null) {

		// add css/js
		$this['asset']->addFile('css', 'gallery:css/admin.css');
		$this['asset']->addFile('js', 'gallery:js/edit.js');
		
		// get xml/widget
		$xml = simplexml_load_file(dirname(__FILE__).'/gallery.xml');
		$widget = $this->get($id ? $id : $this['request']->get('id', 'int'));
		
		// get style and xml
		$style = isset($widget->settings['style']) ? $widget->settings['style'] : 'default';

		if (!$this['path']->path("gallery:styles/$style/config.xml")) {
			$style = 'default';
		}
		
		$style_xml = simplexml_load_file($this['path']->path("gallery:styles/$style/config.xml"));

		// render edit form
		echo $this['template']->render('gallery:layouts/edit', compact('widget', 'xml', 'style_xml'));
	}

	/*
		Function: dirs
			Get directory list JSON formatted

		Returns:
			Void
	*/
	public function dirs() {

		$dirs = array();
		$path = $this['request']->get('path', 'string');
		
		foreach ($this['path']->dirs('media:'.$path) as $dir) {
			$dirs[] = array('name' => basename($dir), 'path' => $path.'/'.$dir, 'type' => 'folder');
		}

		echo json_encode($dirs);
	}

	/*
		Function: files
			Get image file list JSON formatted

		Returns:
			Void
	*/
	public function files() {

		$files = array();
		$path  = $this['request']->get('path', 'string');
		
		foreach ($this['path']->files('media:'.$path) as $file) {
			if (preg_match("/(\.gif|\.jpg|\.jpeg|\.png)$/i", $file)) {
				$files[] = array('name' => basename($file), 'path' => $path.'/'.$file, 'type' => 'file');
			}
		}

		echo json_encode($files);
	}
	
	/*
		Function: save
			Save action

		Returns:
			Void
	*/
	public function save() {

		// save data
		$data['type']     = $this->type;
		$data['id']       = $this['request']->get('id', 'int');
		$data['name']     = $this['request']->get('name', 'string');
		$data['settings'] = $this['request']->get('settings', 'array');
		$data['style']    = $this['request']->get('settings.style', 'array');
		$data['paths']    = $this['request']->get('paths', 'array', array());
		$data['captions'] = $this['request']->get('captions', 'array', array());
		$data['links']    = $this['request']->get('links', 'array', array());


		// force numeric values if not auto
		
		if ($data['settings']['width']!="auto") {
			$data['settings']['width'] = intval($data['settings']['width']);
		}

		if ($data['settings']['height']!="auto") {
			$data['settings']['height'] = intval($data['settings']['height']);
		}

		// merge style settings defaults
		if ($path = $this['path']->path("gallery:styles/{$data['style']}/config.xml")) {

			$xml = simplexml_load_file($path);
			
			foreach ($xml->xpath('settings/setting') as $setting) {
				
				$name  = (string) $setting->attributes()->name;
				$value = (string) $setting->attributes()->default;

				if (!array_key_exists($name, $data['settings'])) {
					$data['settings'][$name] = $value;
				}
			}
		}

		$this->edit($this['widget']->save($data));
	}

	/*
		Function: delete
			Delete action

		Returns:
			Void
	*/
	public function delete(){

		$data['id'] = false;
		
		if ($id = $this['request']->get('id', 'int')) {
			if ($this['widget']->delete($id)) {
				$data['id'] = $id;
			}
		}

		echo json_encode($data);
	}
	
	/*
		Function: docopy
			Copy action

		Returns:
			Void
	*/
	public function docopy(){

		if ($id = $this['request']->get('id', 'int')) {
			$this['widget']->copy($id);
		}

		echo $this['template']->render('dashboard');
	}	

}

// bind events
$widgetkit = Widgetkit::getInstance();
$widgetkit['event']->bind('site', array($widgetkit['gallery'], 'site'));
$widgetkit['event']->bind('dashboard', array($widgetkit['gallery'], 'dashboard'));
$widgetkit['event']->bind('task:config_gallery', array($widgetkit['gallery'], 'config'));
$widgetkit['event']->bind('task:edit_gallery', array($widgetkit['gallery'], 'edit'));
$widgetkit['event']->bind('task:dirs_gallery', array($widgetkit['gallery'], 'dirs'));
$widgetkit['event']->bind('task:files_gallery', array($widgetkit['gallery'], 'files'));
$widgetkit['event']->bind('task:save_gallery', array($widgetkit['gallery'], 'save'));
$widgetkit['event']->bind('task:delete_gallery', array($widgetkit['gallery'], 'delete'));
$widgetkit['event']->bind('task:copy_gallery', array($widgetkit['gallery'], 'docopy'));