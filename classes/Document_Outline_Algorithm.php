<?php

/**
 * WP HTML5 Outliner: Document_Outline_Algorithm class
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
 * Implements the Document Outline Algorithm specified by the W3C.
 * 
 * The first three steps are implemented by declaring the three class 
 * properties. The remaining steps are implemented during calls to the methods 
 * walk(), enter_node(), and exit_node().
 * 
 * @since 1.0.0
 * @link  https://www.w3.org/TR/html5/sections.html#creating-an-outline
 */
class Document_Outline_Algorithm {

	/**
	 * Represents the element for which the current outline is being created.
	 * 
	 * This property is an instance of Owner. The represented element is a
	 * property of the Owner object.
	 * 
	 * @since 1.0.0
	 * @var   object
	 */
	private $current_outline_owner = null;

	/**
	 * Represents the outline section the algorithm is currently creating.
	 * 
	 * @since 1.0.0
	 * @var   object
	 */
	private $current_section = null;

	/**
	 * Holds elements and Owner instances (i.e. outline owners).
	 * 
	 * Allows nesting of sections.
	 * 
	 * @since 1.0.0
	 * @var   array
	 */
	private $stack = array();

	/**
	 * Initializes a new Document_Outline_Algorithm.
	 * 
	 * Calls the method walk(). Executing walk() leads to the other private 
	 * methods being called and all properties being set.
	 * 
	 * @since 1.0.0
	 * @param object $root the root element of the document
	 */
	public function __construct( $root ) {

		$this->walk( $root );

	}	

	/**
	 * Returns the document outline.
	 * 
	 * @since 1.0.0
	 * @return object Returns the outline for the root element that was passed
	 * to the class constructor.
	 */
	public function the_outline() {

		return $this->current_outline_owner->get_outline();

	}

	/**
	 * Traverses the nodes of a DOM tree.
	 * 
	 * @since 1.0.0
	 * @param object $node a node
	 */
	private function walk( $node ) {

		$this->enter_node( $node );

		$child = $node->firstChild;

		while ( $child ) {

			$this->walk( $child );
			$child = $child->nextSibling;

		}

		$this->exit_node( $node );

	}

	/**
	 * Triggers, conditionally, the creation of a new outline and/or section.
	 * 
	 * Entering a sectioning element triggers a new outline with the element
	 * as the (new) initial section. Entering a heading or hgroup element 
	 * triggers a new section with the element as the section's heading.
	 * 
	 * A node that is not a sectioning element nor a heading or hgroup element 
	 * will trigger no action or else be pushed on the stack.
	 * 
	 * @since 1.0.0
	 * @param object $node a node
	 */
	private function enter_node( $node ) {
		
		$section = &$this->current_section;
		$stack 	 = &$this->stack;

		$top = $this->get_stack_top();

		$top_is = Node_Analyzer::analyze( $top );

		if ( 'heading' === $top_is || 'hidden' === $top_is ) {

			// Keeps walking if a heading or hidden element is atop the stack.
			return;

		}

		$node_is = Node_Analyzer::analyze( $node );

		if ( 'hidden' === $node_is ) {
			
			/* Sets aside the element, which has a hidden attribute. The 
			 * element and its descendants are excluded from the outline.
			 */
			$stack[] = $node;

			return;

		}

		if ( false !== strpos( $node_is, 'sectioning')  ) {

			$this->open_sectioning_outline( $node, $node_is );

			return;

		}

		if ( 'heading' === $node_is ) {

			/*
			 * Handles hgroup elements that contain no headings. 
			 * 
			 * The W3C outline algorithm does not include this step. However, 
			 * an hgroup element with no heading elements has no effect on the
			 * HTML 5 ('Structural') Outline produced by the W3C Markup 
			 * Validation Service.
			 *
			 * See the get_ranking_heading() DocBlock (found in this class) for 
			 * more comments plus a link to the W3C specification for heading 
			 * content.
			 */			
			if ( ! $this->get_rank( $node ) ) {

				// Treats the hgroup element as non-heading content.			
				return;

			}
	
			if ( is_null( $section->get_heading() ) ) {
	
				// Sets the current section's heading to $node.
				$section->set_heading( $node );
				$stack[] = $node;

				return;
	
			}

			// Adds a section with the heading as first element.
			$this->add_heading_section( $node );
			$stack[] = $node;

		}

	}

	/**
	 * Triggers, conditionally, the closing of an outline/section.
	 * 
	 * Exiting a sectioning element triggers the closing of the section and 
	 * outline with which it is associated. 
	 * 
	 * A node that is not a sectioning element nor a heading or hgroup element 
	 * will be associated with the current section.
	 * 
	 * @since 1.0.0
	 * @param object $node a node
	 */
	private function exit_node( $node ) {

		$section = &$this->current_section;
		$stack 	 = &$this->stack;

		$top = $this->get_stack_top();
				
		if ( $top === $node ) {

			array_pop( $stack );

		}

		$top_is = Node_Analyzer::analyze( $top );

		if ( 'heading' === $top_is ) {

			return;

		}

		if ( 'hidden' === $top_is ) {
			
			// See the Section::associate_node() DocBlock for detailed comments.
			$section->associate_node( $node );

			return;
		
		}

		$node_is = Node_Analyzer::analyze( $node );

		if ( false !== strpos( $node_is, 'sectioning')  ) {

			$this->close_sectioning_outline( $node_is );

			return;

		}

		$section->associate_node( $node );

	}

	/**
	 * Gets the top node on $stack.
	 * 
	 * @since  1.0.0
	 * @return object Returns the top node on `$stack`.
	 */
	private function get_stack_top() { 

		$top = end( $this->stack );

		if ( is_a( $top, __NAMESPACE__ . '\\Owner' ) ) {

			// Gets the outline owner element.
			$top = $top->get_the_owner();

		}

		return $top;

	}

	/**
	 * Starts a new outline and section for the supplied sectioning element.
	 * 
	 * @since 1.0.0
	 * @param object $element a sectioning element 
	 * @param string $element_is the category of `$element`, either 
	 * 'sectioning_root' or 'sectioning_content'
	 */
	private function open_sectioning_outline( $element, $element_is ) {

		$owner 	 = &$this->current_outline_owner;
		$section = &$this->current_section;
		$stack 	 = &$this->stack;

		// Gets the category of sectioning element, either 'root' or 'content'.
		$category = substr( stristr( $element_is, '_'), 1 );

		if ( $owner ) {

			// Sets aside the current outline owner. It will be revisited.
			$stack[] = $owner;

			if ( 'content' === $category ) {

				if ( is_null( $section->get_heading() ) ) {

					// Gives the current section an implied heading.
					$section->set_heading( false );

				}

			}

		}

		// Updates the current outline owner to the element.
		$owner = new Owner( $element );

		if ( 'root' === $category ) {

			$owner->set_parent_section( $section );

		}

		// Starts a new section with the element.		
		$section = new Section( $element );

		// Makes the element the owner and initial section of a new outline.
		$owner->set_outline( new Outline( $element, $section ) );

	}

	/**
	 * Finalizes an existing outline of a sectioning element.
	 * 
	 * @since 1.0.0
	 * @param string $element_is the category of `$element`, either 
	 * 'sectioning_root' or 'sectioning_content' 
	 */
	private function close_sectioning_outline( $element_is ) {

		$owner 	 = &$this->current_outline_owner;
		$section = &$this->current_section;
		$stack 	 = &$this->stack;

		// Gets the category of sectioning element, either 'root' or 'content'.
		$category = substr( stristr( $element_is, '_'), 1 );

		if ( is_null( $section->get_heading() ) ) {

			// Gives the current section an implied heading.
			$section->set_heading( false );

		}

		if ( ! $stack ) {

			return;

		}

		if ( 'root' === $category ) {

			// Moves a level up/out in the overall outline.
			$section = $owner->get_parent_section();
			$owner   = array_pop( $stack );

			return;	

		}

		/* 
		 * Moves a level up/out in the overall outline and makes all 
		 * sections of the current outline subsections of the last section
		 * of the previous outline.
		 */

		$owner_exited = $owner;
		$owner 	      = array_pop( $stack );
		$sections     = $owner->get_outline()->get_sections();
		$section      = end( $sections );
		$subsections  = $owner_exited->get_outline()->get_sections();

		foreach ( $subsections as $sub ) {

			$section->append_subsection( $sub );				

		}

	}

	/**
	 * Starts a new section with the supplied heading. 
	 * 
	 * @since 1.0.0
	 * @param object $heading a heading or hgroup element
	 */
	private function add_heading_section( $heading ) {

		$owner 	 = &$this->current_outline_owner;
		$section = &$this->current_section;

		$rank 	      = $this->get_rank( $heading );
		$outline      = $owner->get_outline();
		$sections     = $outline->get_sections();
		$last_section = end( $sections );
		$last_h       = $last_section->get_heading();

		/*
		 * Checks whether the last heading in the current outline is
		 * implied or else outranks `$heading`.
		 */
		if ( false === $last_h || $rank >= $this->get_rank( $last_h ) ) {

			/*
			 * Starts a new section with `$heading` and sets the section's
			 * heading property to $heading.
			 */
			$section = new Section( $heading, $heading );

			// Adds the section to the outline's top/outermost level.				
			$outline->append_section( $section );

		} else {

			/* 
			 * Finds the parent section for the section that will be
			 * created for `$heading`.
			 */

			$abort_substeps	= false;
			$candidate      = $section;

			do {

				$candidate_heading = $candidate->get_heading();

				if ( $rank < $this->get_rank( $candidate_heading ) ) {

					/*
					 * Starts a new section with `$heading` and sets the 
					 * section's heading property to `$heading`.
					 */
					$section = new Section( $heading, $heading ); 

					// Adds the section as a subsection of `$candidate`.
					$candidate->append_subsection( $section );				
					$abort_substeps = true;

				}

				// Gets the parent section of the candidate section.
				$candidate = $candidate->get_container();

			} while ( ! $abort_substeps );	

		}

	}

	/**
	 * Gets a heading element's rank.
	 * 
	 * The heading elements are h1, h2, h3, h4, h5, and h6. An h1 element has
	 * the highest rank, and an h6 element has the lowest rank. The rank of an
	 * hgroup element is the same as the rank of the highest ranked heading 
	 * element it contains. An hgroup element that contains no heading elements 
	 * has no rank. For more information, see the DocBlock for 
	 * Document_Outline_Algorithm::get_ranking_heading().
	 * 
	 * @since  1.0.0
	 * @see    Document_Outline_Algorithm::get_ranking_heading()
	 * @param  object $heading a heading or hgroup element
	 * @return int Returns the heading element's rank.
	 */
	private function get_rank( $heading ) {

		if ( 'hgroup' === $heading->tagName ) {

			$ranking_heading = $this->get_ranking_heading( $heading );

			if ( $ranking_heading ) {

				$heading = $ranking_heading;

			}

		}

		// Multiplies by -1 to make lower levels outrank higher ones.
		return -1 * substr( $heading->tagName, 1 );

	}

	/**
	 * Gets the highest ranked heading element in an hgroup element.
	 * 
	 * The W3C HTML 5.2 specification does not include the hgroup element. The
	 * element is deprecated. Yet it affects the HTML 5 ('Structural') Outline
	 * generated by the W3C Markup Validation Service. In an hgroup element,
	 * heading elements appear to have no hierarchy: they are listed on the same 
	 * line, in document order, separated by a colon. That said, the highest 
	 * ranked heading element sets the rank of the containing hgroup element. Call 
	 * this heading element the 'ranking heading'. 
	 * 
	 * Making the highest ranked heading in an hgroup element the ranking 
	 * heading follows the WHATWG HTML Living Standard. According to the WHATWG, 
	 * the hgroup element is not deprecated. Further, an hgroup element that 
	 * contains no heading elements has the same rank as an h1 element. However, 
	 * the W3C Validator excludes from the HTML 5 outline any hgroup element 
	 * without heading elements, as does this plugin.
	 * 
	 * @since  1.0.0
	 * @link   https://www.w3.org/TR/html5/dom.html#heading-content-2
	 * @param  object $heading a heading or hgroup element
	 * @return object Returns either (a) the element itself if it is a  
	 * heading element, (b) the element's highest ranked heading element 
	 * descendant if the element is an hgroup element, or (c) nothing if the 
	 * element is an hgroup element that contains no headings.
	 */
	private function get_ranking_heading( $heading ) {

		// Find highest ranking heading inside hgroup element
		for ( $i = 1; $i <= 6; $i++ ) {
			
			$h = $heading->getElementsByTagName( 'h' . $i );
			
			if ( $h->length ) {

				return $h->item(0);

			}
		
		}
		
	}

} // END class Document_Outline_Algorithm