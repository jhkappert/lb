<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: WidgetWidgetkitHelper
		Widget helper class. Create and manage Widgets.
*/
class WidgetWidgetkitHelper extends WidgetkitHelper {

	/* post type */
	const POST_TYPE = 'WIDGETKIT';

	/* database */
	public $db;

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct($widgetkit) {
		parent::__construct($widgetkit);

		// init vars
		$this->db = $GLOBALS['wpdb'];
	}
	
	/*
		Function: get
			Retrieve a widget instance by id.

		Returns:
			Array
	*/	
	public function get($id) {
		
		// get post
		$post = $this->db->get_row('SELECT * FROM '.$this->db->prefix.'posts WHERE post_type="'.self::POST_TYPE.'" AND ID='.$id, ARRAY_A);	
		
		return is_array($post) ? new WidgetkitWidget($id, $post['post_mime_type'], $post['post_excerpt'], $post['post_title'], $post['post_content'], $post['post_date'], $post['post_modified']) : null;
	}

	/*
		Function: all
			Retrieve all widget instances.

		Returns:
			Array
	*/	
	public function all($type = null) {

		// init vars
		$widgets = array();
		$query   = 'SELECT * FROM '.$this->db->prefix.'posts WHERE post_type="'.self::POST_TYPE.'"'.($type ? ' AND post_mime_type="'.$type.'"' : null).' ORDER BY post_title ASC';
		
		foreach ((array) $this->db->get_results($query) as $post) {
			$widgets[] = new WidgetkitWidget($post->ID, $post->post_mime_type, $post->post_excerpt, $post->post_title, $post->post_content, $post->post_date, $post->post_modified);
		}

		return $widgets;
	}

	/*
		Function: save
			Save a widget instance, returns widget instance id.

		Parameters:
			$data - Widget data

		Returns:
			Int
	*/
	public function save($data) {
		
		// convert numeric strings to real integers
		if (isset($data['settings']) && is_array($data['settings'])) {
			$data['settings'] = array_map(create_function('$item', 'return is_numeric($item) ? (float)$item : $item;'),$data['settings']); 
		}
		
		// init vars		
		$time = time();
		$post = array(
			'post_title'        => mysql_real_escape_string($data['name']),
			'post_mime_type'    => mysql_real_escape_string($data['type']),
			'post_excerpt'      => mysql_real_escape_string($data['style']),
			'post_content'      => mysql_real_escape_string(json_encode($data)),
			'post_modified'     => date('Y-m-d H:i:s', $time),
			'post_modified_gmt' => gmdate('Y-m-d H:i:s', $time)
		);

		// is update ?
		if (isset($data['id']) && $data['id']) {
			return wp_update_post(array_merge($post, array('ID' => $data['id'])));
		}

		// do insert
		return wp_insert_post(array_merge($post, array(
			'post_status'    => 'publish',
			'comment_status' => 'closed',
			'ping_status'    => 'closed',
			'post_date'      => $post['post_modified'],
			'post_date_gmt'  => $post['post_modified_gmt'],
			'post_type'      => self::POST_TYPE
		)));
	}

	/*
		Function: delete
			Delete a widget instance.

		Parameters:
			$id - Widget id

		Returns:
			Void
	*/
	public function delete($id) {
		return $this->db->query('DELETE FROM '.$this->db->prefix.'posts WHERE post_type="'.self::POST_TYPE.'" AND ID='.(int) $id);
	}

	/*
		Function: copy
			Copy a widget instance.

		Parameters:
			$id - Widget id

		Returns:
			Void
	*/
	public function copy($id) {

		// get widget
		$widget = $this->get($id);

		// set data
		$data = json_decode((string) $widget->content, true);
		$data['id'] = null;
		$data['name'] .= ' (Copy)';

		return $this->save($data);
	}
    
	/*
		Function: render
			Render a widget instance.

		Parameters:
			$id - Widget id

		Returns:
			String
	*/
	public function render($id) {

		if ($widget = $this->get((int) $id)) {

			// on render event
			$this['event']->trigger('render', array($widget));

			$output = $this[$widget->type]->render($widget);

			$this['event']->trigger('widgetoutput', array(&$output));

			return $output;
		}

		return false;
	}	

}

/*
	Class: WidgetkitWidget
		The Widget class.
*/
class WidgetkitWidget {
	
	/* identifier */	
	public $id;
	
	/* type */	
	public $type;

	/* style */	
	public $style;

	/* name */	
	public $name;
	
	/* content */	
	public $content;

	/* created at */
	public $created;

	/* modified at */
	public $modified;

	/*
		Function: Constructor
			Class Constructor.
	*/
	public function __construct($id, $type, $style, $name, $content, $created, $modified) {
		
		$widgetkit = Widgetkit::getInstance();
		
		// init vars
		$this->id       = $id;
		$this->type     = $type;
		$this->name     = $name;
		$this->content  = $widgetkit['data']->create($content);
		$this->created  = $created;
		$this->modified = $modified;

	}

	/*
		Function: __isset
			Has a key ? (via magic method)

		Parameters:
			$name - String

		Returns:
			Boolean
	*/
	public function __isset($name) {
		return $this->content->has($name);
	}

	/*
		Function: __get
			Get a value (via magic method)

		Parameters:
			$name - String

		Returns:
			Mixed
	*/
	public function __get($name) {
		return $this->content->get($name);
	}

 	/*
		Function: __set
			Set a value (via magic method)

		Parameters:
			$name - String
			$value - Mixed
			
		Returns:
			Void
	*/
	public function __set($name, $value) {
		$this->content->set($name, $value);
	}

 	/*
		Function: __unset
			Unset a value (via magic method)

		Parameters:
			$name - String
			
		Returns:
			Void
	*/
	public function __unset($name) {
		$this->content->remove($name);
	}

}