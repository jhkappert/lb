<?php

/*
Copyright (c) 2011 Eric Peterson (ePeterso2)

Permission is hereby granted, free of charge, to any person obtaining a copy
of this software and associated documentation files (the "Software"), to deal
in the Software without restriction, including without limitation the rights
to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
copies of the Software, and to permit persons to whom the Software is
furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in
all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
THE SOFTWARE.
*/

/*
Plugin Name: Joomla 1.5 Importer
Plugin URI: http://wordpress.org/extend/plugins/joomla-15-importer/
Description: Migrate posts from a Joomla 1.5 database into Wordpress
Author: Eric Peterson (ePeterso2)
Author URI: http://www.epeterso2.com/
Version: 1.0.0
License: MIT - http://www.opensource.org/licenses/mit-license.php
*/

if ( !defined('WP_LOAD_IMPORTERS') )
	return;

// Load Importer API
require_once ABSPATH . 'wp-admin/includes/import.php';

if ( !class_exists( 'WP_Importer' ) ) {
	$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
	if ( file_exists( $class_wp_importer ) )
		require_once $class_wp_importer;
}

/**
 * Joomla Importer
 *
 * @package WordPress
 * @subpackage Importer
 */

/**
 * Joomla Importer
 *
 * Will transfer posts from a single category in a Joomla database
 * to a single category of a Wordpress database.
 */
if ( class_exists( 'WP_Importer' ) ) {
class Joomla_Import extends WP_Importer {

	var $option_prefix = 'joomla-importer.';

	var $posts = array ();
	var $file;

	function header()
	{
		$plugin_name = basename( dirname( __FILE__ ) );

		echo '<div style="background-color: #ffffff; float: right; width: 160px; margin: 5px; padding: 5px; border: 1px solid black;">';
		echo '<p align="center"><a href="http://www.girlchoir.org" target="_blank"><img src="' . WP_PLUGIN_URL . '/' . $plugin_name . '/gcsf-logo.gif" border="0"/></a></p>';
		echo <<<EOF
<p align="center"><form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick">
<input type="hidden" name="hosted_button_id" value="P7U8C4L5QHT4Y">
<input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
<img style="margin: 0 auto;" alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1">
</form></p>

<p style="font-size: 10px;">
I wrote this plugin to migrate the website of <b><a href="http://www.girlchoir.org">The Girl Choir of South Florida</a></b>.
You can see and hear the choir in action at
their <b><a href="http://www.youtube.com/girlchoir" target="_blank">YouTube channel</a></b>.
They're amazing!
</p><p style="font-size: 10px;">
The best way to say "thank you" is to make a donation to the choir by clicking the <u>Donate</u> button above.
You and the choir will both be glad you did.
All donations received from this plugin
will go towards scholarships for needy families.
</p><p style="font-size: 10px;">
If you're not sure what to contribute, ask yourself what you would have paid someone to convert or retype <u>all</u> of your migrated articles for you,
and donate that amount. Or $10 (US) ... whichever is greater.
</p><p style="font-size: 10px;">
The Girl Choir of South Florida is a nonprofit 501(c)(3) organization, and your contribution may be tax-deductible.
Details of the choir's charitable status are available at <b><a href="http://www.girlchoir.org" target="_blank">http://www.girlchoir.org</a></b>.
</p>
EOF;
		echo '</div>';

		echo '<div class="wrap" style="float: left;">';

		screen_icon();
		echo '<h2>'.__('Import Joomla 1.5', 'joomla-importer').'</h2>';
	}

	function footer() {
		echo '</div>';
	}

	function dispatch()
	{
		if (empty ($_POST['step']))
			$step = 0;
		else
			$step = (int) $_POST['step'];

		$this->header();

		switch ($step) {
			case 0 :
				$this->controller_process_db_info();
				break;
			case 1 :
				$this->controller_process_category_info();
				break;
		}

		$this->footer();
	}

	function Joomla_Import() {
		// Nothing.
	}

	function controller_process_db_info()
	{
		$db_info_option = $this->option_prefix . 'db_info';

		add_option( $db_info_option, array(), null, 'no' );

		if ( empty( $_POST[ 'action' ] ) )
		{
			$this->view_db_page( get_option( $db_info_option ), null );
			return;
		}

		$step   = $_POST[ 'step' ];
		$action = $_POST[ 'action' ];

		if ( ! check_admin_referer( 'connect', 'step0' ) )
		{
			$this->view_db_page( get_option( $db_info_option ), 'Authorization failure' );
			return;
		}

		$result = $this->get_joomla_categories( $this->get_db_info() );

		if ( isset( $result[ 'error' ] ) )
		{
			$this->view_db_page( $this->get_db_info(), 'Database connection failure: ' . $result[ 'error' ] );
			return;
		}

		update_option( $this->option_prefix . 'db_info', $this->get_db_info() );

		$this->view_category_page( $result[ 'categories' ], $this->get_wordpress_categories(), get_users_of_blog() );
	}

	function get_joomla_categories( $db_info )
	{
		$result = array(
			'categories' => array(),
		);

		$db = new mysqli( $db_info[ 'hostname' ], $db_info[ 'username' ], $db_info[ 'password' ], $db_info[ 'database' ], $db_info[ 'port' ] );

		if ( mysqli_connect_errno() )
		{
			$result[ 'error' ] = mysqli_connect_error();
			return $result;
		}

		$prefix = $db_info[ 'prefix' ];
		$query = 'SELECT cat.id, cat.title, sec.title FROM `' . $prefix . 'content` con INNER JOIN `'
			. $prefix . 'categories` cat ON con.catid = cat.id INNER JOIN `' . $prefix . 'sections` sec ON cat.section = sec.id WHERE 1';

		if ( ! ( $stmt = $db->prepare( $query ) ) )
		{
			$result[ 'error' ] = $db->error;
			return $result;
		}

		$stmt->execute();

		if ( $stmt->errno )
		{
			$result[ 'error' ] = $stmt->error;
			return $result;
		}

		$stmt->bind_result( $cat_id, $cat_title, $sec_title );
		$categories = array();

		while ( $stmt->fetch() )
		{
			$categories[ $cat_id ] = $sec_title . ' / ' . $cat_title;
		}

		$stmt->close();

		$result[ 'categories' ] = $categories;
		return $result;
	}

	function get_db_info()
	{
		$info = array();

		$info[ 'hostname' ] = empty( $_POST[ 'hostname' ] ) ? 'localhost' : $_POST[ 'hostname' ];
		$info[ 'port' ]     = empty( $_POST[ 'port' ] )     ? 3306        : (int) $_POST[ 'port' ];
		$info[ 'database' ] = empty( $_POST[ 'database' ] ) ? null        : $_POST[ 'database' ];
		$info[ 'username' ] = empty( $_POST[ 'username' ] ) ? null        : $_POST[ 'username' ];
		$info[ 'password' ] = empty( $_POST[ 'password' ] ) ? null        : $_POST[ 'password' ];
		$info[ 'prefix'   ] = empty( $_POST[ 'prefix' ] )   ? 'jos_'      : $_POST[ 'prefix' ];
		
		return $info;
	}

	function controller_process_category_info()
	{
		if ( ! check_admin_referer( 'import', 'step1' ) )
		{
			$this->view_error_message( 'Authorization failure' );
			return;
		}

		$joomla_category_id = $_POST[ 'joomla_category' ];
		$wordpress_category_ids = $this->get_wordpress_category_ids();
		$user_id = $_POST[ 'username' ];
		$published = $_POST[ 'published' ] == 'false' ? 0 : 1;
		$areyousure = $_POST[ 'areyousure' ] == 'yes' ? 1 : 0;

		$db_info = get_option( $this->option_prefix . 'db_info' );

		$joomla_result = $this->get_joomla_categories( $db_info );
		$wordpress_categories = $this->get_wordpress_categories();
		$users = get_users_of_blog();

		$result = array();
		$error = array();

		if ( ! $areyousure )
		{
			$error[] = 'You must check the confirmation box to begin the import.';
		}

		elseif ( sizeof( $wordpress_category_ids ) == 0 )
		{
			$error[] = 'No Wordpress categories were selected for import.';
		}
		
		else
		{
			$wp_cats = array();

			foreach ( $wordpress_category_ids as $wp_cat_id )
			{
				$wp_cats[] = get_category( $wp_cat_id )->name;
			}

			$result[] = 'Exporting from Joomla section/category: ' . $joomla_result[ 'categories' ][ $joomla_category_id ] . '</p><p>Importing into Wordpress categories: ' . implode( ', ', $wp_cats ) . '</p><p>Importing ' . ( $published ? 'only published articles' : 'all articles' ) . '</p><p>Setting author to Wordpress user: ' . get_userdata( $user_id )->display_name;

			$export_result = $this->export_content( $joomla_category_id, $published );

			foreach ( $export_result[ 'result' ] as $result_msg )
			{
				$result[] = $result_msg;
			}

			foreach ( $export_result[ 'error' ] as $error_msg )
			{
				$error[] = $error_msg;
			}

			if ( sizeof( $export_result[ 'error' ] ) == 0 )
			{
				$import_result = $this->import_content( $export_result[ 'articles' ], $wordpress_category_ids, $user_id );

				foreach ( $import_result[ 'result' ] as $result_msg )
				{
					$result[] = $result_msg;
				}

				foreach ( $import_result[ 'error' ] as $error_msg )
				{
					$error[] = $error_msg;
				}
			}
		}
		
		$this->view_category_page( $joomla_result[ 'categories' ], $wordpress_categories, $users, $result, $error );
	}

	function get_wordpress_category_ids()
	{
		$ids = array();

		$categories = (int) $_POST[ 'wp_categories' ];

		for ( $c = 0; $c < $categories; ++$c )
		{
			if ( isset( $_POST[ 'wp_category_' . $c ] ) )
			{
				$ids[] = (int) $_POST[ 'wp_category_' . $c ];
			}
		}

		return $ids;
	}

	function export_content( $joomla_category_id, $published )
	{
		$result = array( 'result' => array(), 'error' => array(), 'articles' => array() );

		$db_info = get_option( $this->option_prefix . 'db_info' );
		$prefix = $db_info[ 'prefix' ];
		$db = new mysqli( $db_info[ 'hostname' ], $db_info[ 'username' ], $db_info[ 'password' ], $db_info[ 'database' ], $db_info[ 'port' ] );

		if ( mysqli_connect_errno() )
		{
			$result[ 'error' ][] = 'Unable to connect to database: ' . mysqli_connect_error();
			return result;
		}

		if ( ! $stmt = $db->prepare( "SET NAMES utf8" ) )
		{
			$result[ 'error' ][] = 'Cannot set system to use UTF8: ' . $db->error;
			return $result;
		}

		$stmt->execute();

		$query = "SELECT con.id, con.title, con.created, con.introtext, con.fulltext FROM `" . $prefix . 'content` con INNER JOIN `'
			. $prefix . 'categories` cat ON con.catid = cat.id INNER JOIN `' . $prefix . 'sections` sec ON cat.section = sec.id '
			. 'WHERE con.catid = ?';
		
		if ( $published )
		{
			$query .= ' AND con.state > 0 AND cat.published > 0 AND sec.published > 0';
		}

		$query .= ' ORDER BY con.created ASC';

		if ( ! $stmt = $db->prepare( $query ) )
		{
			$result[ 'error' ][] = 'Error in query: ' . $db->error;
			return $result;
		}
		
		$stmt->bind_param( 'i', $joomla_category_id );
		$stmt->execute();
		$stmt->bind_result( $id, $title, $created, $introtext, $fulltext );

		while ( $stmt->fetch() )
		{
			$article = array(
				'id' => $id,
				'title' => $title,
				'created' => $created,
				'introtext' => $introtext,
				'fulltext' => $fulltext,
			);

			$result[ 'articles' ][ $id ] = $article;
		}

		$stmt->close();
		$db->close();

		$result[ 'result' ][] = 'Exported ' . sizeof( $result[ 'articles' ] ) . ' articles from Joomla';

		return $result;
	}

	function import_content( $articles, $wordpress_category_ids, $user_id )
	{
		$result = array( 'result' => array(), 'error' => array() );

		$count = 0;
		foreach ( $articles as $article )
		{	
			$post = array(
				'post_status' => 'publish',
				'post_type' => 'post',
				'post_author' => $user_id,
				'post_title' => $article[ 'title' ],
				'post_content' => $article[ 'introtext' ] . $article[ 'fulltext' ],
				'post_category' => $wordpress_category_ids,
				'post_date' => $article[ 'created' ],
			);

			wp_insert_post( $post );
			$count++;
		}

		$result[ 'result' ][] = 'Inserted ' . $count . ' new posts';

		return $result;
	}

	function get_wordpress_categories()
	{
		return get_categories( array (
			'orderby' => 'name',
			'order' => 'asc',
			'hide_empty' => 0,
		));
	}

	function view_db_page( $db_info = array(), $error = null )
	{
		if ( ! is_null( $error ) )
		{
			$this->view_error_message( $error );
		}

		echo '<h3>Joomla Database Connection Info</h3>';

		echo '<form action="" method="post">';

		echo '<table class="form-table">';
		echo '<tr><th scope="row">Hostname</th><td><input name="hostname" type="text" size="50" value="' . $db_info[ 'hostname' ] . '" /></td></tr>';
		echo '<tr><th scope="row">Port</th><td><input name="port" type="text" size="50" value="' . $db_info[ 'port' ] . '" /></td></tr>';
		echo '<tr><th scope="row">Database</th><td><input name="database" type="text" size="50" value="' . $db_info[ 'database' ] . '" /></td></tr>';
		echo '<tr><th scope="row">Username</th><td><input name="username" type="text" size="50" value="' . $db_info[ 'username' ] . '" /></td></tr>';
		echo '<tr><th scope="row">Password</th><td><input name="password" type="password" size="50" value="' . $db_info[ 'password' ] . '" /></td></tr>';
		echo '<tr><th scope="row">Joomla Table Prefix</th><td><input name="prefix" type="text" size="50" value="' . $db_info[ 'prefix' ] . '" /></td></tr>';
		echo '<tr><th scope="row">&nbsp;</th><td><input class="button-primary" name="submit" value="Connect to Joomla Database" type="submit" /></td></tr>';
		echo '</table>';

		echo '<input name="step" type="hidden" value="0" />';
		echo '<input name="action" type="hidden" value="connect" />';
		wp_nonce_field( 'connect', 'step0' );

		echo '</form>';
	}

	function view_category_page( $joomla_categories = array(), $wordpress_categories = array(), $users = array(), $result = array(), $error = array() )
	{
		foreach ( $result as $result_msg )
		{
			$this->view_warning_message( $result_msg );
		}

		foreach ( $error as $error_msg )
		{
			$this->view_error_message( $error_msg );
		}

		echo '<h3>Select Import and Export Categories</h3>';

		echo '<form action="" method="post">';

		echo '<table class="form-table">';
		echo '<tr><th scope="row">Export from Joomla Category</th><td><select name="joomla_category">';

		foreach ( $joomla_categories as $cat_id => $cat_title )
		{
			echo '<option value="' . $cat_id . '">' . $cat_title . '</option>';
		}

		echo '</select></td></tr>';

		echo '<tr><th scope="row">Import into Wordpress Categories</th><td>';

		$cat_num = 0;
		foreach ( $wordpress_categories as $cat )
		{
			echo '<input name="wp_category_' . $cat_num++ . '" type="checkbox" value="' . $cat->cat_ID . '"> ' . $cat->name . '<br />';
		}

		echo '</td></tr>';

		echo '<tr><th scope="row">Import as Wordpress User</th><td><select name="username">';

		foreach ( $users as $user )
		{
			echo '<option value="' . $user->user_id . '">' . $user->display_name . ' (' . $user->user_login . ')</option>';
		}

		echo '<tr><th scope="row">Import only Published Joomla articles?</th><td>';
		echo '<input name="published" type="radio" value="true" checked /> Import only Published articles &nbsp; ';
		echo '<input name="published" type="radio" value="false" /> Import all articles';
		echo '</td></tr>';

		echo '<tr><th scope="row">Confirm Your Selections</th><td><input name="areyousure" value="yes" type="checkbox" /> My selections are correct</td></tr>';
		echo '<tr><th scope="row">&nbsp;</th><td><input class="button-primary" name="submit" value="Import Content" type="submit" /></td></tr>';
		echo '</table>';

		echo '<input name="wp_categories" type="hidden" value="' . $cat_num . '" />';
		echo '<input name="step" type="hidden" value="1" />';
		echo '<input name="action" type="hidden" value="import" />';
		wp_nonce_field( 'import', 'step1' );

		echo '</form>';

		echo '<form action="" method="post">';
		echo '<table class="form-table">';
		echo '<tr><th scope="row">&nbsp;</th><td><input name="submit" value="Edit Joomla Database Info" type="submit" /></td></tr>';
		echo '</table>';
		echo '</form>';
	}

	function view_error_message( $message )
	{
		echo '<div id="message" class="error"><p>' . $message . '</p></div>';
	}

	function view_warning_message( $message )
	{
		echo '<div id="message" class="updated"><p>' . $message . '</p></div>';
	}
}

$joomla_import = new Joomla_Import();

register_importer('joomla', __('Joomla 1.5', 'joomla-importer'), __('Import articles from a Joomla 1.5 database into Wordpress categories.', 'joomla-importer'), array ($joomla_import, 'dispatch'));

} // class_exists( 'WP_Importer' )

function joomla_importer_init() {
    load_plugin_textdomain( 'joomla-importer', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
}

add_action( 'init', 'joomla_importer_init' );

