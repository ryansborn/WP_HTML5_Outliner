<?php

/**
 * WP HTML5 Outliner: Owner class
 * 
 * @package WP_HTML5_Outliner
 * @since   1.0.0
 */

namespace wph5o;

/**
 * Represents an element for which an outline is created.  
 * 
 * @since 1.0.0
 */
class Owner {

	/**
	 * Represents the outline owner element.
	 * 
	 * @since 1.0.0
	 * @var   object
	 */
	private $the_owner;

	/**
	 * Represents the section to which the owner belongs.
	 * 
	 * @since 1.0.0
	 * @var   object
	 */
	private $parent_section;

	/**
	 * Represents the outline that belongs to the outline owner.
	 * 
	 * @since 1.0.0
	 * @var   object
	 */
	private $outline;

	/**
	 * Initializes a new Owner.
	 * 
	 * @since 1.0.0
	 * @param object $element the outline owner element
	 */
	public function __construct( $element ) {

		$this->the_owner = $element;

	}

	/**
	 * Gets the outline owner element.
	 * 
	 * @since  1.0.0
	 * @return object Returns the outline owner element.
	 */
	public function get_the_owner() {

		return $this->the_owner;

	}

	/**
	 * Gets the outline owner's parent section.
	 * 
	 * @since  1.0.0
	 * @return object Returns the outline owner's parent section.
	 */
	public function get_parent_section() {

		return $this->parent_section;

	}

	/**
	 * Sets the outline owner's parent section.
	 * 
	 * @since 1.0.0
	 * @param object $section a Section instance
	 */	
	public function set_parent_section( $section ) {

		$this->parent_section = $section;

	}

	/**
	 * Gets the outline that belongs to the outline owner.
	 * 
	 * @since  1.0.0
	 * @return object Returns the outline that belongs to the outline owner.
	 */
	public function get_outline() {

		return $this->outline;

	}

	/**
	 * Sets the outline that belongs to the outline owner.
	 * 
	 * @since 1.0.0
	 * @param object an instance of Outline 
	 */	
	public function set_outline( $outline ) {

		$this->outline = $outline;

	}

} // END class Owner