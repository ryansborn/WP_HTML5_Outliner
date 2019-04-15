<?php

/**
 * WP HTML5 Outliner: Section class
 * 
 * @package WP_HTML5_Outliner
 * @since   1.0.0
 */

namespace wph5o;

/**
 * Represents a section of an outline.
 * 
 * An instance of Section is not the same as a section element. A section can
 * correspond to any sectioning element or none at all. In the latter case, a 
 * section corresponds to a heading or hgroup element.
 * 
 * @since 1.0.0
 */
class Section {

	/**
	 * Represents the nodes associated with the section.
	 * 
	 * Includes text, comments, and elements. Elements include:
	 * 
	 * - the element that opens the section, either:
	 *    - a heading or hgroup element 
	 *    - a sectioning element
	 * - all other elements the section contains
	 * 
	 * @since 1.0.0
	 * @var   array
	 */
	private $nodes;

	/**
	 * Represents the heading or hgroup element for the section.
	 * 
	 * A value of boolean false indicates that the section does not have an
	 * explicit heading. Instead, the section has an implied heading.
	 *  
	 * @since 1.0.0
	 * @var   object
	 */
	private $heading;

	/**
	 * Represents subsections of the section.
	 *  
	 * @since 1.0.0
	 * @var   array
	 */
	private $subs;
	/**
	 * Represents the parent section of the section.
	 *  
	 * @since 1.0.0
	 * @var   object
	 */
	private $container;

	/**
	 * Initializes a new section.
	 * 
	 * @since 1.0.0
	 * @param object $node a heading, hgroup, or sectioning element
	 * @param object|null $heading a heading or hgroup element
	 * @param array|array $subs an array of Section instances
	 * @param object|null $container a Section instance
	 */

	public function __construct( $node, $heading = null, $subs = [], 
		$container = null ) {

		$this->nodes     = array( $node );
		$this->heading 	 = $heading;
		$this->subs      = $subs;
		$this->container = $container;

	}

	/**
	 * Gets the nodes associated with the section.
	 * 
	 * Includes the element assigned to $heading only if that element initiates 
	 * the section. Does not include subsections, which are Section instances, 
	 * not nodes.
	 * 
	 * @since  1.0.0
	 * @return array Returns the nodes associated with the section.
	 */
	public function get_nodes() {

		return $this->nodes;

	}

	/**
	 * Adds a node to the array of nodes that are associated with the section.
	 * 
	 * According to the W3C HTML 5.2 specification, the outline algorithm 
	 * "associates each node in the DOM tree with a particular section." The 
	 * function associate_node() is called only if a node is in the section and
	 * the node is either:
	 * 
	 * - a hidden node
	 * - a text node
	 * - a comment node
	 * - an element that is NOT a heading, hgroup, or sectioning element
	 * 
	 * A heading, hgroup, or sectioning element is associated with (i.e., 
	 * is contained in or initiates) a section by one of three other routes. It 
	 * is either:
	 * 
	 * - added to the $nodes property when the constructor is called
	 * - assigned to the $heading property
	 * - added to the $subs property
	 * 
	 * The W3C documentation also states that the outline algorithm associates
	 * each node in the DOM with a section's heading, if any. In the class
	 * Section, a node's association with a heading consists of being associated
	 * with the section with which the heading is associated.
	 * 
	 * @since 1.0.0
	 * @link  https://www.w3.org/TR/html5/sections.html#creating-an-outline
	 * @param object $node a node
	 */
	public function associate_node( $node ) {

		$this->nodes[] = $node;

	}

	/**
	 * Gets the section's heading.
	 * 
	 * @since  1.0.0
	 * @return object Returns the heading or hgroup element that represents the 
	 * section.
	 */
	public function get_heading() {

		return $this->heading;

	}

	/**
	 * Sets the section's heading.
	 * 
	 * @since 1.0.0
	 * @param object $heading a heading or hgroup element
	 */
	public function set_heading( $heading ) {

		$this->heading = $heading;

	}

	/**
	 * Gets the section's subsections.
	 * 
	 * @since  1.0.0
	 * @return array Returns the section's subsections.
	 */
	public function get_subsections() {

		return $this->subs;
	
	}

	/**
	 * Adds a subsection.
	 * 
	 * @since 1.0.0
	 * @param object $section a Section instance
	 */
	public function append_subsection( $section ) {

		$this->subs[] = $section;

		// Sets this section as the parent of the appended subsection.
		$section->set_container( $this );
	
	}

	/**
	 * Gets the section's parent section.
	 * 
	 * @since  1.0.0
	 * @return array Returns the section's parent section.
	 */
	public function get_container() {

		return $this->container;

	}

	/**
	 * Sets the section's parent section.
	 * 
	 * @since 1.0.0
	 * @param object $section a Section instance
	 */
	public function set_container( $section ) {

		$this->container = $section;

	}

} // END class Section