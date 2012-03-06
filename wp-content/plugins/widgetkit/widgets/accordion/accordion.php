<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: AccordionWidgetkitHelper
		Accordion helper class
*/
class AccordionWidgetkitHelper extends WidgetkitHelper {

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
        $this['asset']->addFile('js', 'accordion:js/dashboard.js');
				
		echo $this['template']->render('accordion:layouts/dashboard', array('accordions' => $this['widget']->all('accordion')));
	}
	
	/*
		Function: site
			Site init actions

		Returns:
			Void
	*/
	public function site() {

        // add javascripts
        $this['asset']->addFile('js', 'accordion:js/accordion.js');
 
        // add style stylesheets
		foreach ($this['path']->dirs('accordion:styles') as $style) {

			// style
			if ($this['path']->path("accordion:styles/$style/style.css")) {
				$this['asset']->addFile('css', "accordion:styles/$style/style.css");
			}

			// rtl
			if ($this['system']->options->get('direction') == 'rtl' && $this['path']->path("accordion:styles/$style/rtl.css")) {
				$this['asset']->addFile('css', "accordion:styles/$style/rtl.css");
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
		
		// random order
		if(count($widget->items) && isset($widget->settings['order']) && $widget->settings['order'] =="random") {
		   $keys = array_keys($widget->items); 
		   shuffle($keys); 
		   $widget->items = array_merge(array_flip($keys), $widget->items); 
		}
		
        return $this['template']->render("accordion:styles/$style/template", array('widget' => $widget));
	}
    
	/*
		Function: edit
			Edit action

		Returns:
			Void
	*/
	public function edit($id = null){

		// add js
        $this['asset']->addFile('js', 'accordion:js/edit.js');

		// get widget and xml
		$widget = $this->get($id ? $id : $this['request']->get('id', 'int'));
		$xml = simplexml_load_file(dirname(__FILE__).'/accordion.xml');

		// get style and xml
		$style = isset($widget->settings['style']) ? $widget->settings['style'] : 'default';

		if (!$this['path']->path("accordion:styles/$style/config.xml")) {
			$style = 'default';
		}
		
		$style_xml = simplexml_load_file($this['path']->path("accordion:styles/$style/config.xml"));

		echo $this['template']->render('accordion:layouts/edit', compact('widget', 'xml', 'style_xml'));
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
		$style_xml = simplexml_load_file($this['path']->path("accordion:styles/$style/config.xml"));

        echo $this['template']->render('accordion:layouts/item', compact('widget', 'style_xml'));
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
$widgetkit['event']->bind('site', array($widgetkit['accordion'], 'site'));
$widgetkit['event']->bind('dashboard', array($widgetkit['accordion'], 'dashboard'));
$widgetkit['event']->bind('task:edit_accordion', array($widgetkit['accordion'], 'edit'));
$widgetkit['event']->bind('task:item_accordion', array($widgetkit['accordion'], 'item'));
$widgetkit['event']->bind('task:save_accordion', array($widgetkit['accordion'], 'save'));
$widgetkit['event']->bind('task:delete_accordion', array($widgetkit['accordion'], 'delete'));
$widgetkit['event']->bind('task:copy_accordion', array($widgetkit['accordion'], 'docopy'));