<?php

/**
 * WP HTML5 Outliner: Outline_Formatter class
 * 
 * @package Wordpress
 * @since   1.0.0
 */

namespace wph5o;

/**
 * Formats an HTML 5 Outline as HTML.
 *
 * @since 1.0.0
 */
class Outline_Formatter {
	
	/**
	 * Outputs the outline as an HTML ordered list.
	 * 
	 * @since  1.0.0
	 * @param object $outline an Outline instance to format 
	 * @return string Returns an HTML representation of the HTML 5 outline or a
	 * notice, in HTML, that no outline was created. 
	 */
	public static function format( $outline ) {
		
		return self::list_headings( $outline->get_sections() );

	}

	/**
	 * Creates an HTML ordered list of section headings.
	 * 
	 * @since  1.0.0
	 * @param  array  $sections instances of Section
	 * @return string Returns an HTML ordered list.
	 */
	private static function list_headings( $sections ) {

		$list = '';

		foreach ( $sections as $section ) {	

			$subs = $section->get_subsections();

			if ( ! $subs ) {

				$list .= '<li>' . self::wrap_section_heading( $section ) . '</li>';

			} else {

				$list .= sprintf( '<li>
								     <details class="%3$s-subsections" open> 
									   <summary class="%3$s-parent-heading-container"> 
									     %1$s
									   </summary>
									   %2$s
									 </details>
								   </li>',
								   self::wrap_section_heading( $section ),
								   self::list_headings( $subs ),
								   __NAMESPACE__
						 );

			}

		}

		return '<ol>' . $list . '</ol>';

	}

	/**
	 * Wraps a heading from a Section object in HTML.
	 * 
	 * @since  1.0.0
	 * @param  object $section an instance of Section
	 * @return string Returns the heading or heading notice in a p element. 		  
	 */
	private static function wrap_section_heading( $section ) {

		$heading = $section->get_heading();
		// Gets the name of the element that initiates the section.
		$section_name = $section->get_nodes()[0]->tagName;

		/*
		 * Wraps a user notice regarding an implied heading. Note: When a 
		 * sectioning element has no heading element to represent it, the 
		 * outline section created for that sectioning element is assigned an
		 * implied heading.
		 */
		if ( false === $heading ) {
			
			return sprintf( '<p class="%2$s-heading-notice">%1$s</p>',
					  		 self::issue_heading_notice( $section_name, 'implied' ),
					  		 __NAMESPACE__
				   );

		}
		
		// Wraps headings found in an hgroup element.
		if ( 'hgroup' == $heading->tagName ) {

			$elements = $heading->getElementsByTagName( '*' );

			for ( $i = 0; $i < $elements->length; $i++ ) {

				$element = $elements->item( $i );
	
				/*
				 * Skips non-heading elements. Grabs at least one heading 
				 * element, since Document_Outline_Algorithm weeds out empty
				 * hgroup elements.
				 */
				if ( preg_match( '/^h[1-6]$/i', $element->tagName ) ) {
					
					$headings[] = sprintf( '<p class="%2$s-heading">%1$s</p>',
					  	 					self::wrap_heading_name_and_text( 
					  	 						$element, $section_name, $i
					  	 					),
					  	 					__NAMESPACE__
			   				  	  );
				
				}

			}

			return sprintf( '<div class="%2$s-hgroup">%1$s</p>',
					  	 	 implode( ' <b>:</b> ', $headings ),
					  	 	 __NAMESPACE__
			   	   );

		}

		// Wraps a standard heading (explicit, not in an hgroup element).
		return sprintf( '<p class="%2$s-heading">%1$s</p>',
					  	 self::wrap_heading_name_and_text( $heading, $section_name ),
					  	 __NAMESPACE__
			   );

	}

	/**
	 * Wraps a heading element's name and text in HTML.
	 * 
	 * @since  1.0.0
	 * @param  object   $heading a heading element
	 * @param  string   $section_name the name of the element $heading represents
	 * @param  int|null $hgroup_index the index of $heading in an hgroup element
	 * @return string   Returns the name in a b element and either the text or an 
	 * 'empty heading' notice in a span element.
	 */
	private static function wrap_heading_name_and_text( $heading, $section_name, 
		$hgroup_index = null ) {

		$h_name = $heading->tagName;
		$text   = $heading->nodeValue ?: self::get_alt_text( $heading );
		$class  = __NAMESPACE__ . '-h-text';
	
		if ( ! $text ) {

			/*
			 * Gets a notice that cites the heading element name if either 
			 * (a) $heading initiates the section to which it belongs or (b) 
			 * $heading is in an hgroup element but is not the first descendant 
			 * thereof. Otherwise, the notice cites the name of the sectioning 
			 * element to which $heading belongs. This 'otherwise' clause holds 
			 * even if $heading is in an hgroup element but is not the ranking 
			 * heading. All that matters is that $heading is the first heading 
			 * element in the hgroup element, based on the HTML 5 ('Structural') 
			 * Outlines generated by the W3C Markup Validation Service. 
			 */
			$name = $h_name == $section_name || $hgroup_index > 0  ? $h_name : $section_name;
			$text   = self::issue_heading_notice( $name, 'empty' );
			$class .= ' ' . __NAMESPACE__ . '-heading-notice';
		}

		$tag = '&lt;' . strtoupper( $h_name ) . '&gt;';

		return sprintf( '<b class="%4$s-h-tag">%1$s</b>
						 <span class="%2$s">%3$s</span>',
						 $tag,
						 $class,
						 $text,
						 __NAMESPACE__
			   );

	}

	/**
	 * Gets the alt text of the first image element in a heading element.
	 * 
	 * Called if a heading element has no text content. The alternative text of 
	 * a child img element, if present, is returned as the heading's text.
	 * 
	 * @since  1.0.0
	 * @param  object $heading a heading element
	 * @return string Returns the alt text or else an empty string.
	 */
	private static function get_alt_text( $heading ) {

		$img = $heading->getElementsByTagName( 'img' );

		if ( $img->length && $img->item(0)->getAttribute( 'alt' ) ) {

			return $img->item(0)->getAttribute( 'alt' );
		
		} 

	}

	/**
	 * Gives notice of an empty heading or a sectioning element with no heading.
	 * 
	 * Returns one of two notices:
	 * 
	 * 1. [tag name] with no heading
	 * 2. [tag name] with empty heading
	 * 
	 * In notice (1), the tag name belongs to a sectioning element. In notice 
	 * (2), the tag name belongs to either a heading or a sectioning element.
	 * These notices mimic the ones given in the HTML 5 ('Structural') Outline
	 * generated by the W3C Markup Validation Service.
	 * 
	 * @since  1.0.0
	 * @link   https://validator.w3.org/
	 * @param  string $element_name an HTML element name
	 * @param  string $about the heading's state—'empty' or 'implied'
	 * @return string Returns a heading notice.
	 */
	private static function issue_heading_notice( $element_name, $about ) {

		if ( 'implied' == $about ) {

			/* translators: %s represents an HTML tag name, e.g., 'body'. */
			return sprintf( __( '[%s element with no heading]', 'wph5o' ), 
				   			$element_name
			 	   );
		
		}

		/* translators: %s represents an HTML tag name, e.g., 'body'. */
		return sprintf( __( '[%s element with empty heading]', 'wph5o' ), 
						$element_name 
			   );

	}
	
} // END class Outline_Formatter