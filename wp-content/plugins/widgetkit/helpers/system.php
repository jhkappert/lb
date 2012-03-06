<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/


/*
	Class: SystemWidgetkitHelper
		System helper class
*/
class SystemWidgetkitHelper extends WidgetkitHelper {

	/* system path */
	public $path;

	/* system url */
	public $url;
	
	/* options */
	public $options;

	/* output */
	public $output;

	/* active */
	public $active;

	public $use_old_editor;

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct($widgetkit) {
		parent::__construct($widgetkit);

		// init vars
		$this->path    = rtrim(str_replace(DIRECTORY_SEPARATOR, '/', ABSPATH), '/');
		$this->url     = rtrim(get_option('siteurl'), '/');
		$this->options = $this['data']->create(get_option('widgetkit_options'));
		$this->active  = $this['request']->get('page', 'string') == 'widgetkit' || $this['request']->get('action', 'string') == 'widgetkit';
		$this->use_old_editor = version_compare(get_bloginfo( 'version' ), '3.2.1', '<=');
	}

	/*
		Function: init
			Initialize system
		
		Returns:
			Void
	*/
	public function init() {
		
		// set translations
		load_plugin_textdomain('widgetkit', false, plugin_basename($this["path"]->path('widgetkit:languages')));
		
		// get upload directory
		$upload = wp_upload_dir();

		// set paths
		$this['path']->register($this->path, 'site');
		$this['path']->register($this['path']->path('widgetkit:widgets'), 'widgets');
		$this['path']->register($this['path']->path('widgetkit:cache'), 'cache');
        $this['path']->register($upload['basedir'], 'media');

		// load widgets
		foreach ($this['path']->dirs('widgets:') as $name) {
			if ($file = $this['path']->path("widgets:{$name}/{$name}.php")) {
				require_once($file);
			}
		}

		add_action('wp_ajax_nopriv_widgetkit_render', array($this, 'ajaxRender'));
		add_action('wp_ajax_widgetkit_render', array($this, 'ajaxRender'));

		// is admin or site
		if (is_admin()) {
			
			add_post_type_support('WIDGETKIT', 'editor');
			
			// add widgets event
            $this['event']->bind('task:editor', array($this, '_editor'));

			// trigger event
			$this['event']->trigger('admin');

			// add actions
            add_action('admin_init', array($this,'_adminInit'));
			add_action('admin_head', array($this, '_adminHead'));  
	        add_action('admin_menu', array($this, '_adminMenu'));
			add_action('wp_ajax_widgetkit', array($this,'_adminView'));

			// add notices
			if ($this->active) {
				add_action('admin_notices', array($this, '_adminNotices'));
			}
			
			// add editor filters
			add_filter('tiny_mce_before_init', array($this, '_tinymce'));
			add_filter('teeny_mce_before_init', array($this, '_tinymce'));

		} else {
			
			$this['asset']->addString("js", 'window["WIDGETKIT_URL"]="'.$this['path']->url("widgetkit:").'";');
			$this['asset']->addString("js", 'function wk_ajax_render_url(widgetid){ return "'.site_url('wp-admin').'/admin-ajax.php?action=widgetkit_render&id="+widgetid}');


			// add jquery
			wp_enqueue_script('jquery');

            // add stylesheets/javascripts
            $this['asset']->addFile('css', 'widgetkit:css/widgetkit.css');
			$this['asset']->addFile('js', 'widgetkit:js/jquery.plugins.js');
			
			// trigger event
			$this['event']->trigger('site');

			// add actions/shortcodes/filters
			add_action('wp_head', array($this, '_siteHead'));
			add_shortcode('widgetkit', array($this, '_shortcode'));
			add_filter('widget_text', 'do_shortcode');

			

			$this['event']->bind('widgetoutput', create_function('&$content', '$content=do_shortcode($content);'));
		}

	}

	/*
		Function: link
			Get link to system related resources.

		Parameters:
			$query - HTTP query options
		
		Returns:
			String
	*/
	public function link($query = array()) {
		return $this->url.'/wp-admin/'.(isset($query['ajax']) ? 'admin-ajax.php?action=widgetkit&' : 'admin.php?page=widgetkit&').http_build_query($query, '', '&');
	}

	/*
		Function: saveOptions
			Save plugin options

		Returns:
			Void
	*/
	public function saveOptions() {
		update_option('widgetkit_options', (string) $this->options);
	}

    /*
		Function: _adminInit
			Admin init actions

		Returns:
			Void
	*/
    public function _adminInit() {

		if ($this->active) {
		
			wp_enqueue_style('thickbox');
       		wp_enqueue_style('editor-buttons');
			wp_enqueue_script('thickbox');
	        wp_enqueue_script('editor');
	        wp_enqueue_script('media-upload');
	        wp_enqueue_script('quicktags');

			// execute task
			$task = $this['request']->get('task', 'string');
			$this["version"] = ($path = $this['path']->path('widgetkit:widgetkit.xml')) && ($xml = simplexml_load_file($path)) ? (string) $xml->version[0] : '';
			$this->output = $this['template']->render($task ? 'task' : 'dashboard', compact('task', 'version'));

		
		} else {
		
			add_filter("mce_external_plugins", create_function('$plugin_array', '
				$widgetkit = Widgetkit::getInstance();
				$plugin_array["widgetkit"] = $widgetkit["path"]->url("widgetkit:js/editor.plugin.js");
				return $plugin_array;'
			));
			 
			add_filter('mce_buttons', create_function('$buttons','
				array_push($buttons, "separator", "widgetkit");
				return $buttons;'
			));

		}
	}

	/*
		Function: _adminNotices
			Admin notices action callback

		Returns:
			Void
	*/
	public function _adminNotices() {

		// get widgetkit xml
		if ($xmlpath = $this['path']->path('widgetkit:widgetkit.xml')){
		
			$xml = $this['dom']->create($xmlpath, 'xml');

			// update check
			if ($url = $xml->first('updateUrl')->text()) {

				// create check url
				$url = sprintf('%s?application=%s&version=%s&format=raw', $url, 'widgetkit_wp', urlencode($xml->first('version')->text()));

				// only check once a day
				$hash = md5($url.date('Y-m-d'));
				if ($this['option']->get("update_check") != $hash) {
					if ($request = $this['http']->get($url)) {
						$this['option']->set("update_check", $hash);
						$this['option']->set("update_data", $request['body']);
					}
				}

				// decode response and set message
				if (($data = json_decode($this['option']->get("update_data"))) && $data->status == 'update-available') {
					$update = $data->message;
				}

			}
			
		}

		// show notice
		if (!empty($update)) {
			echo '<div class="update-nag">'.$update.'</div>';
		}
		
		return false;
	}

	/*
		Function: _adminHead
			Admin head actions

		Returns:
			Void
	*/
    public function _adminHead() {
        
        if ($this->active) {

			// cache writable ?
			if (!is_writable($this["path"]->path("cache:"))) {
				add_action('admin_notices', create_function('', "
				   echo '<div class=\"update-nag\"><strong>Widgetkit cache folder is not writable! Please check directory permissions.</strong><br />".$this["path"]->path("cache:")."</div>';
				    return false;
				"));
			}
	
            // add stylesheets/javascripts
			$this['asset']->addFile('css', 'widgetkit:css/admin.css');
			$this['asset']->addFile('js', 'widgetkit:js/jquery.ui.js');
			$this['asset']->addFile('js', 'widgetkit:js/jquery.plugins.js');
			$this['asset']->addFile('js', 'widgetkit:js/admin.js');

            wp_tiny_mce( false , array(
              "height" => 150
            ));
        }

        // add stylesheets/javascripts
		$this['asset']->addFile('css', 'widgetkit:css/system.css');
		$this['asset']->addString('js', 'var widgetkitajax = "'.$this['system']->link(array('ajax' => true)).'";');

		// render assets stylesheets/javascripts
		echo $this['template']->render('assets');
    }

	/*
		Function: _adminMenu
			Admin menu actions

		Returns:
			Void
	*/
    public function _adminMenu() {
	    add_menu_page('', 'Widgetkit', 8, 'widgetkit', array($this, '_adminView'), $this['path']->url('widgetkit:images/widgetkit_16.png'));
    }
	
	/*
		Function: _adminView
			Render admin view

		Returns:
			Void
	*/
    public function _adminView() {
        
		echo $this->output;
		
		if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
			exit;
		}
    }
	
	/*
		Function: _siteHead
			Site head actions

		Returns:
			Void
	*/
	public function _siteHead() {

		// render assets stylesheets/javascripts
		echo $this['template']->render('assets');

	}

	/*
		Function: _tinymce
			Tiny mce editor init callback

		Returns:
			Void
	*/
    public function _tinymce($init) {
		
		if (version_compare($GLOBALS['wp_version'], 3.2, '<')) {
			$init['extended_valid_elements'] = (isset($init['extended_valid_elements']) ? $init['extended_valid_elements'].',' : '').'@[data-lightbox],@[data-spotlight]';
		}
		
		if ($this->active) {
			$init['forced_root_block'] = "";
			$init['verify_html'] = false;

			if(!$his->use_old_editor){
				
				$init['editor_selector'] = 'slide-content';
		        $init['mode'] = 'specific_textareas';
		        $init['theme_advanced_buttons1'] = str_replace( ',wp_more', '', $init['theme_advanced_buttons1'] );
		        $init['theme_advanced_disable'] = 'fullscreen';
			}
		}


		
		return $init;
    }

	/*
		Function: _editor
			Editor plugin callback

		Returns:
			Void
	*/
    public function _editor() {
        printf('<p><strong>%s</strong></p>%s', _e('Widget:', 'widgetkit'), $this['field']->render('widget', 'widget_id', null, null, array('id' => 'widgetkit_select_box', 'class' => 'widefat')));
    }
	
	/*
		Function: _shortcode
			Shortcode callback

		Returns:
			String
	*/	
    public function _shortcode($atts, $content = null, $code = '') {

		extract(shortcode_atts(array('id' => null), $atts));

		return is_numeric($id) ? $this['widget']->render($id) : '';
    }
	
	/*
		Function: __
			Retrieve translated strings

		Returns:
			String
	*/	
    public function __($string) {

		return __($string, "widgetkit");
    }

    public function ajaxRender() {
    	
    	$output = $this->widgetkit['widget']->render(intval($_GET["id"]));

    	die($output);
    }

}

/*
	Class: WP_Widget_Widgetkit
		Widgetkit Widget for Wordpress.
*/
class WP_Widget_Widgetkit extends WP_Widget {

	public $widgetkit;

	public $options;

	public $defaults = array(
		'title' => '',
		'widget_id' => null
    );

	public function __construct() {
		parent::__construct(false, 'Widgetkit', array('description' => 'Display your widgets'));      

		// get widgetkit
		$this->widgetkit = Widgetkit::getInstance();
	}

	public function widget($args, $instance) {  
		global $wp_query;

		// init vars
		extract($args);
		extract($this->_setOptions($instance));

		echo $before_widget;

		if ($instance['title']) {
			echo $before_title.$instance['title'].$after_title;
		}
		
		$output = '';
		
        if (is_numeric($widget_id)) {

			$output = $this->widgetkit['widget']->render($widget_id);
			
			if ($output === false) {
				$output = "Could not load widget with the id $widget_id.";
			}
        }
		
		echo $output;
		
		echo $after_widget;
	}

	public function update($new_instance, $old_instance) {                
		return $new_instance;
	}

	public function form($instance) {

		extract($this->_setOptions($instance));

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'widgetkit'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
		</p>
        <p>
			<label for="<?php echo $this->get_field_id('widget_id'); ?>"><?php _e('Widget:', 'widgetkit'); ?></label>
			<?php echo $this->widgetkit['field']->render('widget', $this->get_field_name('widget_id'), $widget_id, null, array('id' => $this->get_field_id('widget_id'), 'class' => 'widefat')); ?>
		</p>        
        <?php
	}

	protected function _setOptions($instance) {
		$this->options = wp_parse_args((array) $instance, $this->defaults);
		return $this->options;
	}

}

add_action("widgets_init", create_function('', 'register_widget("WP_Widget_Widgetkit");'));

/*
	Class: WP_Widget_Widgetkit_Twitter
		Widgetkit Twitter Widget for Wordpress.
*/
class WP_Widget_Widgetkit_Twitter extends WP_Widget {

	public $widgetkit;

	public $options;

	public $defaults = array(
		'title' => '',
		'style' => '',
		'from_user' => '',
		'to_user' => '',
		'ref_user' => '',
		'hashtag' => '',
		'word' => '',
		'nots' => '',
		'limit' => 5,
		'image_size' => 48,
		'show_image' => 0,
		'show_author' => 0,
		'show_date' => 0);

	public function __construct() {
		parent::__construct(false, 'Widgetkit - Twitter', array('description' => 'Lets you display your tweets'));      

		// get widgetkit
		$this->widgetkit = Widgetkit::getInstance();
	}

	public function widget($args, $instance) {  
		global $wp_query;

		// init vars
		extract($args);

		echo $before_widget;

		if ($instance['title']) {
			echo $before_title.$instance['title'].$after_title;
		}

		echo $this->widgetkit['twitter']->render($this->_getOptions($instance));

		echo $after_widget;
	}

	public function update($new_instance, $old_instance) {                
		return $new_instance;
	}

	public function form($instance) {

		extract($this->_getOptions($instance));

		$show_image  = $show_image ? 'checked="checked"' : '';
		$show_author = $show_author ? 'checked="checked"' : '';
		$show_date   = $show_date ? 'checked="checked"' : '';

		?>
		<p>
			<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', 'warp'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo esc_attr($title); ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('style'); ?>"><?php _e('Style:', 'warp'); ?></label>
			<select name="<?php echo $this->get_field_name('style'); ?>" class="widefat" id="<?php echo $this->get_field_id('style'); ?>">
				<?php
					foreach ($this->widgetkit['path']->dirs('widgets:twitter/styles') as $dir) {
						$selected = $style == $dir ? ' selected="selected"' : '';
						printf('<option value="%s"%s>%s</option>', $dir, $selected, ucfirst($dir));
					}
				?>
			</select>
		</p>		
		<p>
			<label for="<?php echo $this->get_field_id('from_user'); ?>"><?php _e('From User(s):', 'warp'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('from_user'); ?>" value="<?php echo esc_attr($from_user); ?>" class="widefat" id="<?php echo $this->get_field_id('from_user'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('to_user'); ?>"><?php _e('To User(s):', 'warp'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('to_user'); ?>" value="<?php echo esc_attr($to_user); ?>" class="widefat" id="<?php echo $this->get_field_id('to_user'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('ref_user'); ?>"><?php _e('Referencing User(s):', 'warp'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('ref_user'); ?>" value="<?php echo esc_attr($ref_user); ?>" class="widefat" id="<?php echo $this->get_field_id('ref_user'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('hashtag'); ?>"><?php _e('With Hashtag:', 'warp'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('hashtag'); ?>" value="<?php echo esc_attr($hashtag); ?>" class="widefat" id="<?php echo $this->get_field_id('hashtag'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('word'); ?>"><?php _e('With Word:', 'warp'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('word'); ?>" value="<?php echo esc_attr($word); ?>" class="widefat" id="<?php echo $this->get_field_id('word'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('nots'); ?>"><?php _e('Not with Word:', 'warp'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('nots'); ?>" value="<?php echo esc_attr($nots); ?>" class="widefat" id="<?php echo $this->get_field_id('nots'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('limit'); ?>"><?php _e('Limit:', 'warp'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('limit'); ?>" value="<?php echo esc_attr($limit); ?>" class="widefat" id="<?php echo $this->get_field_id('limit'); ?>" />
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('image_size'); ?>"><?php _e('Image Size (px):', 'warp'); ?></label>
			<input type="text" name="<?php echo $this->get_field_name('image_size'); ?>" value="<?php echo esc_attr($image_size); ?>" class="widefat" id="<?php echo $this->get_field_id('image_size'); ?>" />
		</p>
		<p>
			<input class="checkbox" type="checkbox" name="<?php echo $this->get_field_name('show_image'); ?>" id="<?php echo $this->get_field_id('show_image'); ?>" <?php echo $show_image; ?> /> <label for="<?php echo $this->get_field_id('show_image'); ?>"><?php _e('Show image', 'warp'); ?></label>
			<br />
			<input class="checkbox" type="checkbox" name="<?php echo $this->get_field_name('show_author'); ?>" id="<?php echo $this->get_field_id('show_author'); ?>" <?php echo $show_author; ?> /> <label for="<?php echo $this->get_field_id('show_author'); ?>"><?php _e('Show name', 'warp'); ?></label>
			<br />
			<input class="checkbox" type="checkbox" name="<?php echo $this->get_field_name('show_date'); ?>" id="<?php echo $this->get_field_id('show_date'); ?>" <?php echo $show_date; ?> /> <label for="<?php echo $this->get_field_id('show_date'); ?>"><?php _e('Show publish date', 'warp'); ?></label>
		</p>				
<?php
	}

	protected function _getOptions($instance) {
		return wp_parse_args((array) $instance, $this->defaults);
	}

} 

add_action("widgets_init", create_function('', 'register_widget("WP_Widget_Widgetkit_Twitter");'));
