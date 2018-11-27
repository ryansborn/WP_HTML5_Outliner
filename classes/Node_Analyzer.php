<?php

/**
 * WP HTML5 Outliner: Node_Analyzer class
 * 
 * @package Wordpress
 * @since   1.0.0
 */

namespace wph5o;

/**
 * Detects the hidden attribute (if any) or content category of an element.
 * 
 * @since 1.0.0
 */
class Node_Analyzer {

	/**
	 * Checks whether an element is hidden or is heading or sectioning content.
	 * 
	 * @since  1.0.0
	 * @param  object $node a node
	 * @return string|null Returns an attribute or content category name or  
	 * null if $node is not an element.
	 */
	public static function analyze( $node ) {

		if ( ! $node || 1 != $node->nodeType ) {
			return null;	
		}

		$results = [ 
			'hidden', 
			'heading', 
			'sectioning_root', 
			'sectioning_content' 
		];

		foreach ( $results as $result ) {
			
			$is_x = "is_{$result}";

			if ( self::$is_x( $node ) ) {
				return $result;
			}

		}
	
	}

	/**
	 * Checks whether an element has the hidden attribute.
	 * 
	 * @since  1.0.0
	 * @param  object $element an element
	 * @return boolean Returns true if the element has a hidden attribute and 
	 * false otherwise.
	 */
	private static function is_hidden( $element ) {

		return $element->hasAttribute( 'hidden' );
	
	}

	/**
	 * Checks whether an element is a heading or hgroup element.
	 * 
	 * @since  1.0.0
	 * @param  object $element an element
	 * @return boolean Returns true if the element is a heading or hgroup 
	 * element and false otherwise.
	 */
	private static function is_heading( $element ) { 

		return self::check_tag( $element, 'h([1-6]|group)' );

	}

	/**
	 * Checks whether an element is a sectioning root element.
	 * 
	 * @since  1.0.0
	 * @param  object $element an element
	 * @return boolean Returns true if the element is a sectioning root element 
	 * and false otherwise.
	 */
	private static function is_sectioning_root( $element ) {

		$sectioning_roots = '(blockquote|body|details|fieldset|figure|td)';
	
		return self::check_tag( $element, $sectioning_roots );

	}

	/**
	 * Checks whether an element is a sectioning content element.
	 * 
	 * @since  1.0.0
	 * @param  object $element an element
	 * @return boolean Returns true if the element is a sectioning content 
	 * element and false otherwise.
	 */
	private static function is_sectioning_content( $element ) {
	
		return self::check_tag( $element, '(article|aside|nav|section)' );

	}

	/**
	 * Checks whether an element has one of the specified names.
	 * 
	 * @since  1.0.0
	 * @param  object $element an element
	 * @param  string $names a regular expression of selected element names
	 * @return boolean Returns true if the element has the specified name and 
	 * false otherwise.
	 */
	private static function check_tag( $element, $names ) {

		return preg_match( '/^' . $names . '$/', $element->tagName );

	}	

} // END class Node_Analyzer