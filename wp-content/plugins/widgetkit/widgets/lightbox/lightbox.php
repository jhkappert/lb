<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: LightboxWidgetkitHelper
		Lightbox helper class
*/
class LightboxWidgetkitHelper extends WidgetkitHelper {

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
		Function: site
			Site init actions

		Returns:
			Void
	*/
	public function site() {
		
		$options = array();

		// get options
		foreach (array('title_position' => 'float', 'transition_in' => 'fade', 'transition_out' => 'fade', 'overlay_show' => 1, 'overlay_color' => '#777', 'overlay_opacity' => 0.7) as $option => $value) {
			$var = preg_replace_callback('/[_-]+(.)?/i', create_function('$matches', 'return strtoupper($matches[1]);'), $option);
			$val = $this->options->get('lightbox_'.$option, $value);
			$options[$var] = is_numeric($val) ? (float) $val : $val;
		}

		// is enabled ?
		if ($this->options->get('lightbox_enable', 1)) {
			
			$lightboxjs = $this['path']->url("lightbox:js/lightbox.js");
			$selector   = $this->options->get('lightbox_selector', 'a[data-lightbox]');
			$params     = count($options) ? json_encode($options) : '{}';

			// add stylesheets/javascripts
			$this['asset']->addFile('css', 'lightbox:css/lightbox.css');
			$this['asset']->addString('js', "\$widgetkit.load('{$lightboxjs}').done(function(){ 
					jQuery(function($){
						$('{$selector}').lightbox({$params});
					});
			});");

			// rtl
			if ($this['system']->options->get('direction') == 'rtl') {
		        $this['asset']->addFile('css', 'lightbox:css/rtl.css');
			}

		}

	}

	/*
		Function: dashboard
			Render dashboard layout

		Returns:
			Void
	*/
	public function dashboard() {

		// get xml
		$xml = simplexml_load_file($this['path']->path('lightbox:lightbox.xml'));

		// add js
        $this['asset']->addFile('js', 'lightbox:js/dashboard.js');
		
		// render dashboard
		echo $this['template']->render('lightbox:layouts/dashboard', compact('xml'));
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
	        if (preg_match('/^lightbox_/', $option)) {
				$this['system']->options->set($option, $value);
	        }
	    }

		$this['system']->saveOptions();
	}

}

// bind events
$widgetkit = Widgetkit::getInstance();
$widgetkit['event']->bind('site', array($widgetkit['lightbox'], 'site'));
$widgetkit['event']->bind('dashboard', array($widgetkit['lightbox'], 'dashboard'));
$widgetkit['event']->bind('task:config_lightbox', array($widgetkit['lightbox'], 'config'));