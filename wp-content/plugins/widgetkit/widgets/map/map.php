<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: MapWidgetkitHelper
		Map helper class
*/
class MapWidgetkitHelper extends WidgetkitHelper {

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

		// register path
        $this['path']->register(dirname(__FILE__), $this->type);
		
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
		foreach (array('id' => 0, 'type' => $this->type, 'name' => null, 'settings' => array(), 'items' => array()) as $var => $val) {
			if (!isset($widget->$var)) {
				$widget->$var = $val;
			}
		}
		
		return $widget;
	}
	
	/*
		Function: dashboard
			Render dashboard layout

		Returns:
			Void
	*/
	public function dashboard() {		

		// add js
        $this['asset']->addFile('js', 'map:js/dashboard.js');

		echo $this['template']->render('map:layouts/dashboard', array('maps' => $this['widget']->all('map')));
	}

	/*
		Function: site
			Site init actions

		Returns:
			Void
	*/
	public function site() {
		
		// add translations
		foreach (array('FROM_ADDRESS', 'GET_DIRECTIONS', 'FILL_IN_ADDRESS', 'ADDRESS_NOT_FOUND', 'LOCATION_NOT_FOUND') as $key) {
			$translations[$key] = $this['system']->__($key);
		}
        
        // add javascripts
		$this['asset']->addFile('js', 'map:js/lazyloader.js');
		$this['asset']->addString('js', sprintf('$widgetkit.trans.addDic(%s);', json_encode($translations)));

        // add style stylesheets
		foreach ($this['path']->dirs('map:styles') as $style) {

			// style
			if ($this['path']->path("map:styles/$style/style.css")) {
				$this['asset']->addFile('css', "map:styles/$style/style.css");
			}

			// rtl
			if ($this['system']->options->get('direction') == 'rtl' && $this['path']->path("map:styles/$style/rtl.css")) {
				$this['asset']->addFile('css', "map:styles/$style/rtl.css");
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
		
		// get style
		$style = isset($widget->settings['style']) ? $widget->settings['style'] : 'default';
		
        return $this['template']->render("map:styles/$style/template", compact('widget'));
	}

	/*
		Function: edit
			Edit action

		Returns:
			Void
	*/
	public function edit($id = null){

		// add js
        $this['asset']->addFile('js', 'map:js/edit.js');
		$this['asset']->addFile('js', 'map:js/map.js');
		$this['asset']->addFile('js', 'http://maps.google.com/maps/api/js?sensor=false&language=en');

		// get widget and xml
		$widget = $this->get($id ? $id : $this['request']->get('id', 'int'));
		$xml = simplexml_load_file(dirname(__FILE__).'/map.xml');

		// get style and xml
		$style = isset($widget->settings['style']) ? $widget->settings['style'] : 'default';
		$style_xml = simplexml_load_file($this['path']->path("map:styles/$style/config.xml"));

		echo $this['template']->render('map:layouts/edit', compact('widget', 'xml', 'style_xml'));
	}

	/*
		Function: item
			Add item action

		Returns:
			Void
	*/
	public function item() {

		// get widget
		$widget = $this->get($this['request']->get('id', 'int'));

		// get style and xml
		$style = isset($widget->settings['style']) ? $widget->settings['style'] : 'default';
		$style_xml = simplexml_load_file($this['path']->path("map:styles/$style/config.xml"));

	    echo $this['template']->render('map:layouts/item', compact('widget', 'style_xml'));
	}

	/*
		Function: locate
			Query google to get location

		Returns:
			Void
	*/
	public function locate(){
		$output = '{}';

		if ($address = $this['request']->get('address', 'string')) {
			if ($response = $this['http']->get('http://maps.google.com/maps/api/geocode/json?address='.urlencode($address).'&sensor=false')) {
				$output = $response['body'];
			}
		}

		echo $output;
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
		$data['items']    = $this['request']->get('items', 'array', array());

		// get widget
		$widget = $this->get($data['id']);

		// merge item data
		foreach ($data['items'] as $id => $item) {
			if (isset($widget->items[$id])) {
				$data['items'][$id] = array_merge($widget->items[$id], $data['items'][$id]);
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
$widgetkit['event']->bind('site', array($widgetkit['map'], 'site'));
$widgetkit['event']->bind('dashboard', array($widgetkit['map'], 'dashboard'));
$widgetkit['event']->bind('task:edit_map', array($widgetkit['map'], 'edit'));
$widgetkit['event']->bind('task:item_map', array($widgetkit['map'], 'item'));
$widgetkit['event']->bind('task:locate_map', array($widgetkit['map'], 'locate'));
$widgetkit['event']->bind('task:save_map', array($widgetkit['map'], 'save'));
$widgetkit['event']->bind('task:delete_map', array($widgetkit['map'], 'delete'));
$widgetkit['event']->bind('task:copy_map', array($widgetkit['map'], 'docopy'));