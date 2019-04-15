<?php

/**
 * WP HTML5 Outliner: Source_Loader class
 * 
 * @package WP_HTML5_Outliner
 * @since   1.0.0
 */

namespace wph5o;

/**
 * Loads an HTML string to a DOMDocument instance and handles LibXML errors.
 *
 * @since 1.0.0
 */
class Source_Loader {

	/**
	 * Loads the to-be-outlined HTML to the supplied DOMDocument instance.
	 * 
	 * @since 1.0.0
	 * @see   DOMDocument::loadHTML()
	 * @param object $doc an instance of DOM Document
	 * @param string $source the HTML that will be outlined
	 */
	public static function load( $doc, $source ) {
		
		$source = mb_convert_encoding( $source, 'HTML-ENTITIES', 'UTF-8' );

		/*
		 * Ensures the source has an html element as its root.
		 * 
		 * DOMDocument::loadHTML() will make the first element in `$source`
		 * the root if the 'LIBXML_HTML_NOIMPLIED' parameter is used, as
		 * it will be below. The first element in $source is not an html 
		 * element, and it doesn't have to be, but making it an html element is
		 * definitely a safe (i.e. valid) choice.
		 */
		$html = '<html>' . $source . '</html>';

		libxml_use_internal_errors( true );

		$doc->loadHTML( $html, LIBXML_HTML_NODEFDTD | LIBXML_HTML_NOIMPLIED );

		self::libxml_error_handler( libxml_get_errors() );

	}

	/**
	 * Handles errors thrown by DOMDocument::loadHTML().
	 * 
	 * Suppresses invalid HTML errors—in particular, errors for invalid tags or 
	 * unexpected end tags. Prints all other errors.
	 * 
	 * @since 1.0.0
	 * @param array $errors LibXMLError objects
	 */
	private static function libxml_error_handler( $errors ) {
	
		if ( ! $errors ) {

			return;

		}

		$return = '';
		$load   = 'DOMDocument::loadHTML()';

		foreach ( $errors as $error ) {

			switch ( $error->level ) {

				case LIBXML_ERR_WARNING:

					$return .= sprintf(
						'<b>%1$s</b>: %2$s: ', 
						__( 'Warning', 'wph5o' ), 
						$load
					);

					break;

				case LIBXML_ERR_ERROR:

					$messages = [
						'/^Tag \w* invalid$/i', 
						'/^Unexpected end tag/i'
					];
					
					foreach ( $messages as $message ) {
						
						if ( preg_match( $message, $error->message ) ) {

							// Silence!
							return ;

						}
					
					}

					$return .= sprintf(
						'<b>%1$s</b>: %2$s: ', 
						__( 'Error', 'wph5o' ), 
						$load
					);

					break;

				case LIBXML_ERR_FATAL:

					$return .= sprintf(
						'<b>%1$s</b>: %2$s: ', 
						__( 'Fatal Error', 'wph5o' ), 
						$load
					);

					break;

			}

			$file = $error->file ?: __( 'Entity', 'wph5o' );

			// Emulates the output of the standard PHP error handler.
			/* 
			 * translators: PHP error. The particular error message, 
			 * represented by %1$s, is not offered for translation 
			 * since it is not generated by this plugin. %2$s represents
			 * a file name and defaults to 'Entity'. %3$s represents
			 * a line number, e.g., 199. %4$s represents a file path.
			 */
			$return .= sprintf(
				__('%1$s in %2$s, line: %3$s in %4$s', 'wph5o'), 
				trim($error->message), 
				$file,
				$error->line,
				'<b>' . __FILE__ . '</b><br><br>'
			);

		}

		libxml_clear_errors();

		echo $return;

	}

} // END class Source_Loader