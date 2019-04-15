<?php

/**
 * WP HTML5 Outliner: Document_Outline class
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
 * Supplies an HTML 5 outline of an HTML Document in HTML format.
 *
 * @since 1.0.0
 */
class Document_Outline {

	/**
	 * Represents the HTML 5 outline.
	 * 
	 * @since 1.0.0
	 * @var   object
	 */
	private $html5_outline;

	/**
	 * Initializes a new Document_Outline.
	 * 
	 * @since 1.0.0
	 * @param string $source the HTML to outline
	 */
	public function __construct( $source ) {

		$this->html5_outline = $this->outline( $source );

	}

	/**
	 * Returns the outline as HTML.
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
	 * Calls the outline algorithm on the body element of the HTML source document.
	 * 
	 * @since  1.0.0
	 * @param  string $source the HTML source
	 * @return object Returns an Outline instance.
	 */
	private function outline( $source ) {

		$doc = new \DOMDocument();

		Source_Loader::load( $doc, $source );

		$body = $doc->getElementsByTagName( 'body' );

		if ( $body->length ) {

			return ( new Document_Outline_Algorithm( $body->item(0) ) )->the_outline();
		
		}

	}

} // END class Document_Outline