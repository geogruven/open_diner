<?php
/*-----------------------------------------------------------------------------------*/
/* Start DinerManager Functions - Please refrain from editing this section           */
/* Unlike style.css, the functions.php of a child theme does not override its        */
/* counterpart from the parent. Instead, it is loaded in addition to the parent’s    */
/* functions.php. (Specifically, it is loaded right before the parent’s file.)
 * 
 * The fact that a child theme’s functions.php is loaded first means that if a theme 
 * developer made the user functions of his/her theme pluggable —that is, replaceable 
 * by a child theme simply by declaring them conditionally, then they can be overriden
 *  here in the child theme
 */
/*-----------------------------------------------------------------------------------*/


// Set path to GlassMenusFramework and theme specific functions
// Changed TEMPLATEPATH to STYLESHEETPATH because TEMPLATEPATH points
// to the parent theme path because the parent theme is 
// considered the "template" in the child theme style.css file.
$functions_path = STYLESHEETPATH . '/functions/';
$includes_path = STYLESHEETPATH . '/includes/';

// theme-options adapted from http://themeshaper.com/2010/06/03/sample-theme-options/
require_once ( STYLESHEETPATH . '/theme-options.php');

// GlassMenusFramework
require_once ($functions_path . 'admin-init.php');			// Framework Init

?>
