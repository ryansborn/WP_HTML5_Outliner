<?php

/**
 * WP HTML5 Outliner: WP_HTML5_Outliner class
 * 
 * @package WP_HTML5_Outliner
 * @since   1.0.0
 */

namespace wph5o;

/**
 * Loads the autoloader.
 */
require_once dirname( __FILE__ ) . '/Autoloader.php';

/**
 * Initializes the plugin and adds the output to the Toolbar.
 *
 * @since 1.0.0
 */
class WP_HTML5_Outliner {

	/**
	 * Represents the webpage that will be outlined.
	 * 
	 * @since 1.0.0
	 * @var   string
	 */
	private static $page;

	/**
	 * Starts buffering the webpage.
	 * 
	 * Hooks to `wp_head` with a low priority so buffering starts as close
	 * as possible to the opening of the body element.
	 * 
	 * @since 1.0.0
	 * @see   ob_start()
	 * @link  http://php.net/manual/en/function.ob-start.php
	 */
	public static function start_buffer() {

		ob_start();

	}

	/**
	 * Gets the webpage buffer.
	 * 
	 * Hooks to `wp_footer` with a high priority so buffering stops as close as
	 * possible to the end of the page content, before scripts are appended to 
	 * the body element.
	 * 
	 * @since  1.0.0
	 * @see    ob_get_clean()
	 * @link   http://php.net/manual/en/function.ob-get-clean.php
	 */
	public static function get_buffer() {

		self::$page = ob_get_clean();
		
		if ( false === self::$page ) {

			/*
			 * Prints an error message. The buffer either never started or else 
			 * already stopped. In the latter case, the browser window might be 
			 * empty save for this message.
			 */
			trigger_error( __( 'Page buffer failed', 'wp-html5-outliner' ), E_USER_WARNING );

			return;

		}

		// Prints the buffer.
		echo self::$page;

	}

	/**
	 * Adds the HTML5 outline, formatted as HTML, to the Toolbar.
	 * 
	 * @since 1.0.0
	 * @param object $wp_admin_bar an instance of WP_Admin_Bar
	 */
	public static function add_to_toolbar( $wp_admin_bar ) {

		// Adds an 'Outline' text node to the Toolbar. 
		$wp_admin_bar->add_node( [
			'id'    => __NAMESPACE__ . '-outliner-toolbar-node',
			'title' => __( 'Outline', 'wph50'),
			'meta'  => [ 'class' => __NAMESPACE__ . '-outliner-toolbar-node' ],
		] );

		// Adds a dropdown box that shows the outline. 
		$wp_admin_bar->add_node( [
			'id'     => __NAMESPACE__ . '-outliner-output',
			'parent' => __NAMESPACE__ . '-outliner-toolbar-node',
			'meta'   => [ 'html' => self::make_toolbar_html() ],
		] );

	}

	/**
	 * Outputs the outline as HTML.
	 * 
	 * Key elements include:
	 * 
	 * - HTML 5 outline as an ordered list
	 * - container for the HTML 5 outline
	 * - container for a heading-level outline (JavaScript makes that outline)
	 * - popup trigger for opening the outlines in a new window
	 * - anchors for switching between the two outline types 
	 * 
	 * @since  1.0.0
	 * @return string Returns an HTML representation of the HTML5 outline or a
	 * notice, in HTML, that no outline was created.. 
	 */
	private static function make_toolbar_html() {

		/* 
		 * Tells user if there is no outline. Appears in place of the 
		 * heading-level outline by default.
		 */
		$notice = sprintf(
			'<div class="%2$s-notice">%1$s</div>',
			__( 'No outline was created.', 'wp-html5-outliner' ),
			__NAMESPACE__ 
		);

		if ( self::$page ) {

			// Gets the outline as an ordered list
			$outline = ( new Document_Outline( self::$page ) )->render_outline();

		}

		if ( empty( $outline ) ) {

			return sprintf(
				'<div class="%2$s-wrapper">%1$s</div>',
				$notice,
				__NAMESPACE__ 
			);

		}
		
		return sprintf( 
			'<div class="%9$s-wrapper">
				<div id="%9$s-popper" title="%1$s" aria-hidden="true">
				</div><!-- #%9$s-popper -->
				<div id="%9$s-outline-toggle" aria-hidden="true">
					<a href="#%9$s-structural" id="%9$s-show-s" class="%9$s-active" title="%2$s">%3$s</a>
					&nbsp; | &nbsp;
					<a href="#%9$s-h-level" id="%9$s-show-hl" title="%2$s">%4$s</a>
				</div><!-- #%9$s-outline-toggle -->
				<div id="%9$s-outline">
					<div id="%9$s-structural">
						<p class="%9$s-sr-only %9$s-outline-title">%5$s</p>
						%6$s
					</div><!-- #%9$s-structural -->
					<div id="%9$s-h-level" class="%9$s-sr-only">
						<p class="%9$s-sr-only %9$s-outline-title">%7$s</p>
						%8$s
					</div><!-- #%9$s-h-level -->
				</div><!-- #%9$s-outline -->
			</div><!-- .%9$s-wrapper -->',
			esc_attr__( 'Open in new window', 'wp-html5-outliner' ),
			esc_attr__( 'Select Outline Type', 'wp-html5-outliner' ),
			__( 'Structural (HTML5)', 'wp-html5-outliner' ),
			__( 'Heading-level', 'wp-html5-outliner' ),
			__( 'Structural (HTML5) Outline', 'wp-html5-outliner' ),
			$outline,
			__( 'Heading-level Outline', 'wp-html5-outliner' ),
			$notice,
			__NAMESPACE__
		);

	}

} // END class WP_HTML5_Outliner