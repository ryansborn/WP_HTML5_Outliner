/**
 * Writes a Heading-level outline and adds toggle and popout functionality.
 *
 * Writes a Heading-level outline to complement the HTML 5 ('Structural') 
 * Outline generated server-side. Also, enables the user to toggle which outline 
 * is shown, as well as open a new window that displays both outlines.
 *
 * @since 1.0.0
 */

/* global jQuery, wp */
/* jshint { globals jQuery, wp } */

jQuery( document ).ready( function( $ ) {

	/**
	 * Retrieves translated text. 
	 *
	 * @since 1.3.0
	 * @see {@link https://developer.wordpress.org/block-editor/packages/packages-i18n/#__}
	 */
	var __ = wp.i18n.__;

	    // Contains the outlines.
	var container = document.getElementById( 'wph5o-outline' ),

	    // Lists the headings.
	    headings  = container.querySelectorAll( '.wph5o-heading' ),

	    // Presents the popout icon (as a pseudo-element).
	    popper    = document.getElementById( 'wph5o-popper' ),
	    outlines  = {

	    	// Contains the Structural Outline.
	    	s : document.getElementById( 'wph5o-structural' ),

	    	// Contains the Heading-level Outline.
	    	hl: document.getElementById( 'wph5o-h-level' )

	    },
	    anchors   = {

	    	// Links (same page) to the Structural Outline.
	    	showS : document.getElementById( 'wph5o-show-s' ),

	    	// Links (same page) to the Heading-level Outline.
	    	showHl: document.getElementById( 'wph5o-show-hl' )
	    
	    };

	for ( var i in anchors ) {

		if ( anchors.hasOwnProperty( i ) ) {

			$( anchors[i] ).click( toggleOutlines );

		}	

	}
	
	/**
	 * Shows/hides outlines when anchor text (an outline's name) is clicked. 
	 *
	 * @since 1.0.0
	 * @this {Element}
	 */
	function toggleOutlines() {

		// Does nothing if anchor is for the outline currently shown.
		if ( $( this ).hasClass( 'wph5o-active' ) ) {

			return;

		}

		// Adds/removes screenreader-only class, which shows/hides the outline.
		for ( var i in outlines ) {

			if ( outlines.hasOwnProperty( i ) ) {

				$( outlines[i] ).toggleClass( 'wph5o-sr-only' );

			}

		}

		// Adds/removes active styles from links.
		for ( var j in anchors ) {

			if ( anchors.hasOwnProperty( j ) ) {

				$( anchors[j] ).toggleClass( 'wph5o-active' );

			}

		}

	}

	$( popper ).click( popout );
	
	/**
	 * Opens a new window and clones elements to that window. 
	 * 
	 * Clones the outlines, anchors, and plugin stylesheet link. No scripts are 		
	 * cloned. The new window shows both outlines. Anchors are ordinary 'go to' 
	 * links, and classes toggled in toggleOutlines have no effect.
	 *
	 * @since 1.0.0
	 */ 
	function popout() {
			
		var popped = window.open( '', '', 'scrollbars=1,resizable=1'),

		    // Contains the anchors.
		    toggle = document.getElementById( 'wph5o-outline-toggle' ).cloneNode( true ),
		    contnr = container.cloneNode( true ),

		    // Links to the plugin stylesheet.
		    css    = document.getElementById( 'wph5o-styles-css' ).cloneNode();

		// Marks body as the wrapper for the anchor and outline containers.
		$( popped.document.body ).addClass( 'wph5o-wrapper' );

		popped.document.head.appendChild( css );
		popped.document.body.appendChild( toggle );
		popped.document.body.appendChild( contnr );

	}

	if ( headings.length ) {

		writeHeadingLevelOutline();

	}
	
	/**
	 * Writes Heading-level Outline.
	 *
	 * Output is intended to match the Heading-level Outline generated by the 
	 * W3C Markup Validation Service.
	 *
	 * @since 1.0.0
	 * @link  https://validator.w3.org/
	 */
	function writeHeadingLevelOutline() {
	
		// Holds the last heading level when looping through the headings.
		var prevLevel = 1;

		// Removes the default notice for the outline: 'No outline was created'.
		$( outlines.hl ).children( '.wph5o-notice' ).remove();

		for ( var i = 0, l = headings.length; i < l; i++ ) {
				
			    // Contains the heading tag name and text.
			var heading = headings.item(i).cloneNode( true ),

			    // Gets the element that holds the name, e.g., '<H1>'.
			    tag	    = heading.querySelector( '.wph5o-h-tag' ),

			    // Gets the element that holds the text.
			    text    = heading.querySelector( '.wph5o-h-text' ),

			    // Gets the level, .e.g., 1 from '<H1>'
			    level   = Number( tag.textContent.substring( 2, 3 ) ),

			    // Finds the difference between the current and last levels.
			    diff    = level - prevLevel;

			// Checks for skipped heading level(s), e.g., H3 after H1.
			if ( diff > 1 ) {

				// Finds the first level that was skipped.
				var j = prevLevel + 1; 
				
				while ( j < level ) {
					
					// Adds fill-in heading to represent a skipped level.
					
					var p = document.createElement( 'p' ),
					    h = tag.cloneNode();

					$( p ).addClass( 'wph5o-missing-heading wph5o-level-' + j );
					$( p ).text( __( ' [missing]', 'wp-html5-outliner' ) ).appendTo( outlines.hl );
					$( h ).text( '<H' + j + '>' ).prependTo( p );
					j++;

				}

			}

			// Checks for empty headings.
			if ( $( text ).hasClass( 'wph5o-heading-notice' ) ) {

				// Replaces empty-heading notice from Structural Outline.
				$( text ).text( __( '[empty]', 'wp-html5-outliner' ) );

			}

			// Adds classes to indent headings. 
			$( heading ).addClass( 'wph5o-level-' + level ).appendTo( outlines.hl );

			prevLevel = level;

		}

	}

} );