<?php
/**
* @package   Widgetkit
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   YOOtheme Proprietary Use License (http://www.yootheme.com/license)
*/

/*
	Class: TwitterWidgetkitHelper
		Twitter helper class
*/
class TwitterWidgetkitHelper extends WidgetkitHelper {

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
		if ($cache && !file_exists($cache.'/twitter')) {
			mkdir($cache.'/twitter', 0777, true);
		}

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

		// add translations
		foreach (array('LESS_THAN_A_MINUTE_AGO', 'ABOUT_A_MINUTE_AGO', 'X_MINUTES_AGO', 'ABOUT_AN_HOUR_AGO', 'X_HOURS_AGO', 'ONE_DAY_AGO', 'X_DAYS_AGO') as $key) {
			$translations[$key] = $this['system']->__($key);
		}

        // add stylesheets/javascripts
        $this['asset']->addFile('css', 'twitter:styles/style.css');
        $this['asset']->addFile('js', 'twitter:twitter.js');
		$this['asset']->addString('js', sprintf('$widgetkit.trans.addDic(%s);', json_encode($translations)));
        
		// rtl
		if ($this['system']->options->get('direction') == 'rtl') {
	        $this['asset']->addFile('css', 'twitter:styles/rtl.css');
		}

	}
    
	/*
		Function: render
			Render widget on site

		Returns:
			String
	*/
	public function render($options) {

		if ($tweets = $this->_getTweets($options)) {

			// get options
			extract($options);

			return $this['template']->render("twitter:styles/$style/template", compact('tweets', 'show_image', 'show_author', 'show_date', 'image_size'));
		}

		return 'No tweets found.';
	}

	/*
		Function: _getURL
			Create Twitter Query URL

		Returns:
			String
	*/
	protected function _getURL($options) {
		
		// get options
		extract($options);

		// clean options
		foreach (array('from_user', 'to_user', 'ref_user', 'word', 'nots', 'hashtag') as $var) {
			$$var = preg_replace('/[@#]/', '', preg_replace('/\s+/', ' ', trim($$var)));
		}
		
		// build query
		$query = array();
		
		if ($from_user) {
			$query[] = 'from:'.str_replace(' ', ' OR from:', $from_user);
		}

		if ($to_user) {
			$query[] = 'to:'.str_replace(' ', ' OR to:', $to_user);
		}

		if ($ref_user) {
			$query[] = '@'.str_replace(' ', ' @', $ref_user);
		}

		if ($word) {
			$query[] = $word;
		}

		if ($nots) {
			$query[] = '-'.str_replace(' ', ' -', $nots);
		}

		if ($hashtag) {
			$query[] = '#'.str_replace(' ', ' #', $hashtag);
		}

		$limit = min($limit ? intval($limit) : 5, 100);

		// build timeline url
		if ($from_user && !strpos($from_user, ' ') && count($query) == 1) {

			$url = 'http://twitter.com/statuses/user_timeline/'.strtolower($from_user).'.json';

			if ($limit > 15) {
				$url .= '?count='.$limit;
			}

			return $url;
		}

		// build search url
		if (count($query)) {

			$url = 'http://search.twitter.com/search.json?q='.urlencode(implode(' ', $query));

			if ($limit > 15) {
				$url .= '&rpp='.$limit;
			}

			return $url;
		}

		return null;	
	}

	/*
		Function: _getTweets
			Get Tweet Object Array

		Returns:
			Array
	*/
	protected function _getTweets($options) {

		// init vars
		$tweets = array();

		// query twitter
		if ($url = $this->_getURL($options)) {
			if ($path = $this['path']->path('cache:twitter')) {
				$file = rtrim($path, '/').sprintf('/twitter-%s.php', md5($url));

				// is cached ?
				if (file_exists($file)) {
					$response = file_get_contents($file);
				}

				// refresh cache ?
				if (!file_exists($file) || (time() - filemtime($file)) > 300) {
					
					// send query
					$request = $this['http']->get($url);
					
					if (isset($request['status']['code']) && $request['status']['code'] == 200) {
						$response = $request['body'];
						file_put_contents($file, $response);
					}
				}
			}
		}

		// create tweets
		if (isset($response)) {

			$response = json_decode($response, true);
			
			if (is_array($response)) {
				
				if (isset($response['results'])) {
					foreach ($response['results'] as $res) {

						$tweet = new WidgetkitTweet();
						$tweet->user = $res['from_user'];
						$tweet->name = $res['from_user'];
						$tweet->image = $res['profile_image_url'];
						$tweet->text = $res['text'];
						$tweet->created_at = $res['created_at'];
						
						$tweets[] = $tweet;
					}
				} else {
					foreach ($response as $res) {

						$tweet = new WidgetkitTweet();
						$tweet->user = $res['user']['screen_name'];
						$tweet->name = $res['user']['name'];
						$tweet->image = $res['user']['profile_image_url'];
						$tweet->text = $res['text'];
						$tweet->created_at = $res['created_at'];

						$tweets[] = $tweet;
					}
				}
				
			}
		}
		
		return array_slice($tweets, 0, $options['limit'] ? intval($options['limit']) : 5);
	}

}

/*
	Class: WidgetkitTweet
		Widgetkit Twitter Tweet.
*/
class WidgetkitTweet {

	public $user;
	public $name;
	public $image;
	public $text;
	public $created_at;

	public function getLink() {
		return 'http://twitter.com/'.$this->user;			
	}

	public function getText() {

		// format text
		$text = preg_replace('@(https?://([-\w\.]+)+(/([\w/_\.]*(\?\S+)?(#\S+)?)?)?)@', '<a href="$1">$1</a>', $this->text);
		$text = preg_replace('/@(\w+)/', '<a href="http://twitter.com/$1">@$1</a>', $text);
		$text = preg_replace('/\s+#(\w+)/', ' <a href="http://search.twitter.com/search?q=%23$1">#$1</a>', $text);

		return $text;			
	}

}

// bind events
$widgetkit = Widgetkit::getInstance();
$widgetkit['event']->bind('site', array($widgetkit['twitter'], 'site'));