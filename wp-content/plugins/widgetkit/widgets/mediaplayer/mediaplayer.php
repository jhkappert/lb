<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: MediaplayerWidgetkitHelper
		mediaplayer helper class
*/
class MediaplayerWidgetkitHelper extends WidgetkitHelper {

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

		// is enabled ?
		if ($this->options->get('mediaplayer_enable', 1)) {
			
			$pluginPath = $this["path"]->url('mediaplayer:mediaelement')."/";
			$options    = array("pluginPath" => $pluginPath);
		
			// add stylesheets/javascripts
			$this['asset']->addFile('css', 'mediaplayer:mediaelement/mediaelementplayer.css');


			//call

			$call = sprintf("jQuery(function($){mejs.MediaElementDefaults.pluginPath='{$pluginPath}'; $('%s').mediaelementplayer(%s);});", $this->options->get('mediaplayer_selector', 'video,audio'), count($options) ? json_encode($options) : '{}');

			$this['asset']->addString('js', "if(!window['mejs']){ \$widgetkit.load('{$pluginPath}mediaelement-and-player.js').done(function(){ $call });}else{ $call; }");
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
		$xml = simplexml_load_file($this['path']->path('mediaplayer:mediaplayer.xml'));

		// add js
        $this['asset']->addFile('js', 'mediaplayer:js/dashboard.js');
		
		// render dashboard
		echo $this['template']->render('mediaplayer:layouts/dashboard', compact('xml'));
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
	        if (preg_match('/^mediaplayer_/', $option)) {
				$this['system']->options->set($option, $value);
	        }
	    }

		$this['system']->saveOptions();
	}

}

// bind events
$widgetkit = Widgetkit::getInstance();
$widgetkit['event']->bind('site', array($widgetkit['mediaplayer'], 'site'));
$widgetkit['event']->bind('dashboard', array($widgetkit['mediaplayer'], 'dashboard'));
$widgetkit['event']->bind('task:config_mediaplayer', array($widgetkit['mediaplayer'], 'config'));