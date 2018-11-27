=== WP HTML5 Outliner ===
Contributors: rsborn
Tags: html5, outline, accessibility
Requires at least: 3.3
Tested up to: 4.9.8
Requires PHP: 5.4.5
Stable tag: 1.0.0
License: GPL-2.0 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Adds an HTML 5 outline plus a heading-level outline to the WordPress Toolbar.

== Description ==
WP HTML5 Outliner (WPH5O) adds an HTML 5 outline plus a heading-level outline to the WordPress Toolbar.

= Features =
- Mimics the outlines provided by the [W3C Markup Validation Service](https://validator.w3.org/#validate_by_uri+with_options). *Caveat*: In an HTML 5 outline, the W3C validator may hide some of the headings in an `<hgroup>` if any of them are empty. WPH5O will not. This difference is presentational, not structural.
- Adds an ‘HTML5 Outliner’ node to the Toolbar. Outlines are displayed in a dropdown box, with the option to view them in a new browser window.
- Works only on pages or posts the user can edit. Administrators can access the outliner on any page or post. Administration Screens are not outlined.

== Frequently Asked Questions ==
= What is an HTML 5 outline? =
An HTML 5 Outline represents the sections of an HTML document. Each section corresponds to an element from one of three categories:

 *Sectioning root*: `<blockquote>`, `<body>`, `<details>`, `<dialog>`, `<fieldset>`, `<figure>`, `<td>`
 *Sectioning content*: `<article>`, `<aside>`, `<nav>`, `<section>`
 *Heading content*: `<h1>`, `<h2>`, `<h3>`, `<h4>`, `<h5>`, `<h6>`

Sections may be nested to create subsections.

Source: [W3C HTML 5.2 Specification](https://www.w3.org/TR/html52/sections.html)

= What is a heading-level outline? =
A heading-level outline also represents the sections of an HTML document, but the sections correspond to heading elements only. Sections may still be nested to create subsections.

Sources: [W3C Quality Assurance](https://www.w3.org/QA/Tips/headings), [W3C HTML 5.2 Specification](https://www.w3.org/TR/html52/sections.html)

= Why do these outlines matter? =
Each outline shows how well a page meets web standards for marking up document structure. User agents, particularly screen readers, use the heading-level outline to aid navigation. However, no web browsers or assistive technologies make use of the HTML 5 outline. So, really, the question is this: Why does *that* outline matter?

Although user agents still haven’t implemented the HTML 5 outline (which is about a decade old), developers aren’t giving up on it. And they have their reasons. The MDN web docs highlight some [problems solved by HTML 5 document structure](https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/Using_HTML_sections_and_outlines#Problems_solved_by_HTML5).

Sources: [W3C Web Accessibility Initiative](https://www.w3.org/WAI/tutorials/page-structure/headings/), [W3C HTML 5.2 Specification](https://www.w3.org/TR/html52/sections.html)

= Is there developer documentation? =
Yes. The source code conforms to [WordPress Inline Documentation Standards](https://make.wordpress.org/core/handbook/best-practices/inline-documentation-standards/). For PHP classes, browsable documentation was generated using [phpDocumentor](https://www.phpdoc.org/). Get it all at the [WPH5O GitHub page](https://github.com/ryansborn/WP_HTML5_Outliner).

== Screenshots ==

1. HTML 5 outline in a Toolbar dropdown box. Theme: Twenty Seventeen. Context: Homepage. WordPress Version: 4.9.8.
2. Heading-level outline in a Toolbar dropdown box. Theme: Twenty Seventeen. Context: Homepage. WordPress Version: 4.9.8.
3. HTML 5 outline and heading-level outline in a new window. The new window is opened by clicking the icon in the top-right corner of the Toolbar dropdown box. Theme: Twenty Seventeen. Context: Homepage. WordPress Version: 4.9.8.

== Changelog ==
= 1.0.0 =
Initial Release