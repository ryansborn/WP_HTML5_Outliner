<?php

/**
 * WP HTML5 Outliner
 * 
 * @package     WP_HTML5_Outliner
 * @author      Ryan Born
 * @copyright   2018 Ryan Born
 * @license     GPL-2.0+
 * 
 * @wordpress-plugin
 * Plugin Name: WP HTML5 Outliner
 * Plugin URI:  https://github.com/ryansborn/WP_HTML5_Outliner
 * Description: Adds an HTML 5 outline plus a heading-level outline to the WordPress Toolbar.
 * Version:     1.3.0
 * Author:      Ryan Born
 * Author URI:  https://github.com/ryansborn
 * License:     GPL-2.0+
 * Text Domain: wp-html5-outliner
 * Domain Path: /languages
 */

/*
Copyright 2018  Ryan Born

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as 
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

namespace wph5o;

defined( 'ABSPATH' ) or die( 'Don\'t call plugins directly please.' );

if ( is_admin() || is_customize_preview() ) {

	return;

}

/**
 * Loads a class used for callables in add_action().
 */
require_once plugin_dir_path( __FILE__ ) . 'classes/WP_HTML5_Outliner.php';

add_action( 'init', __NAMESPACE__ . '\\is_administrator' );

/**
 * Runs the plugin if the current user is a site administrator.
 * 
 * Checks whether the user is logged in and can manage options, a capability
 * that Wordpress pre-assigns only to Administrators and Super Admins. If the 
 * user is not a site administrator, an action is added to permit limited
 * non-administrator usage.
 * 
 * @since 1.0.0
 */
function is_administrator() {

	if ( ! is_user_logged_in() ) {

		return;

	}

	if ( current_user_can( 'manage_options' ) ) { 
	
		// Adds actions to output the outline.
		add_outliner_actions();
		
	} else {

		// Adds action to permit limited non-administrator usage.
		add_action( 'wp', __NAMESPACE__ . '\\current_user_can_edit' );

	}

}

/**
 * Limits non-administrator usage to posts/pages the current user can edit.
 * 
 * Called if the current user is not an administrator.
 * 
 * @since 1.0.0
 */
function current_user_can_edit() {

	// Aborts if not a single post or page.
	if ( ! is_singular() || is_front_page() || is_home() ) {

		return;

	}

	$post_id = get_queried_object_id();

	if ( current_user_can( 'edit_post', $post_id ) || current_user_can( 'edit_page', $post_id ) ) {

		add_outliner_actions();

	}

}

/**
 * Enqueues JavaScript and CSS files.
 * 
 * As of version 1.3.0, enqueued scripts include 'wp-i18n' dependency.
 * 
 * @since 1.0.0
 */
function enqueue_scripts_and_styles() {

	wp_enqueue_script( 'wph5o-scripts', 
		plugins_url( 'js/wph5o-scripts.min.js', __FILE__ ), 
		array( 'jquery', 'wp-i18n' ) 
	);

	wp_enqueue_style( 'wph5o-styles', 
		plugins_url( 'css/wph5o-styles.min.css', __FILE__ ) 
	);

}

/**
 * Loads the plugin text domain for translation.
 *
 * @since 1.2.0
 */
function load_plugin_text_domain() {

	load_plugin_textdomain(
		'wp-html5-outliner',
		false,
		plugins_url( 'languages/', __FILE__ ) 
	);

}

/**
 * Adds actions required to run the plugin.
 * 
 * @since 1.0.0
 */
function add_outliner_actions() {

	$class = __NAMESPACE__ . '\\WP_HTML5_Outliner';

	add_action( 'init', __NAMESPACE__ . '\\load_plugin_text_domain' );
	add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\enqueue_scripts_and_styles' );
	add_action( 'wp_head', array( $class, 'start_buffer' ), '999' );
	add_action( 'wp_footer', array( $class, 'get_buffer' ), '1' );
	add_action( 'admin_bar_menu', array( $class, 'add_to_toolbar' ), '999' );

}