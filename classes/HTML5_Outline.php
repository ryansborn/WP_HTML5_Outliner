<?php

/**
 * WP HTML5 Outliner: HTML5_Outline class
 * 
 * @package Wordpress
 * @since   1.0.0
 */

namespace wph5o;

/**
 * Loads the autoloader.
 */
require_once dirname( __FILE__ ) . '/Autoloader.php';

/**
 * Supplies an HTML 5 outline in HTML format.
 *
 * @since 1.0.0
 */
class HTML5_Outline {

	/**
	 * Represents the HTML 5 outline.
	 * 
	 * @since 1.0.0
	 * @var   object
	 */
	private $html5_outline;

	/**
	 * Initializes a new HTML5_Outline.
	 * 
	 * @since 1.0.0
	 * @param string $source the HTML to outline
	 */
	public function __construct( $source ) {

		$this->html5_outline = $this->outline( $source );

	}

	/**
	 * Outputs the outline as HTML.
	 * 
	 * @since  1.0.0
	 * @return string Returns an HTML representation of the HTML 5 outline.
	 */
	public function render_outline() {

		if ( $this->html5_outline ) {

			return Outline_Formatter::format( $this->html5_outline );

		}

	}

	/**
	 * Calls the outline algorithm on the body element of the HTML source.
	 * 
	 * @since  1.0.0
	 * @return object|string Returns an Outline instance or, if there is no body
	 * element, an empty string.
	 */
	private function outline( $source ) {

		$doc = new \DOMDocument();

		Source_Loader::load( $doc, $source );

		$body = $doc->getElementsByTagName( 'body' );

		if ( $body->length ) {

			return ( new Document_Outline_Algorithm( $body->item(0) ) )->the_outline();
		
		}

	}

} // END class HTML5_Outline