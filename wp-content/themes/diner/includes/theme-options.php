<?php

$seo_post_types = array('post','page');
define("SEOPOSTTYPES", serialize($seo_post_types));

//Global options setup
add_action('init','woo_global_options');
function woo_global_options(){
	// Populate WooThemes option in array for use in theme
	global $woo_options;
	$woo_options = get_option('woo_options');
}

add_action( 'admin_head','woo_options' );  
if (!function_exists('woo_options')) {
function woo_options() {
	
// VARIABLES
$themename = "Diner";
$manualurl = 'http://www.woothemes.com/support/theme-documentation/diner/';
$shortname = "woo";

$GLOBALS['template_path'] = get_template_directory();

//Access the WordPress Categories via an Array
$woo_categories = array();  
$woo_categories_obj = get_categories( 'hide_empty=0' );
foreach ($woo_categories_obj as $woo_cat) {
    $woo_categories[$woo_cat->cat_ID] = $woo_cat->cat_name;}
$categories_tmp = array_unshift($woo_categories, "Select a category:");    
       
//Access the WordPress Pages via an Array
$woo_pages = array();
$woo_pages_obj = get_pages('sort_column=post_parent,menu_order');    
foreach ($woo_pages_obj as $woo_page) {
    $woo_pages[$woo_page->ID] = $woo_page->post_name; }
$woo_pages_tmp = array_unshift($woo_pages, "Select a page:");       

// Image Alignment radio box
$options_thumb_align = array( "alignleft" => "Left","alignright" => "Right","aligncenter" => "Center" ); 

// Image Links to Options
$options_image_link_to = array("image" => "The Image","post" => "The Post"); 

//Testing 
$options_select = array( "one","two","three","four","five" ); 
$options_radio = array( "one" => "One","two" => "Two","three" => "Three","four" => "Four","five" => "Five" ); 

//URL Shorteners
if (_iscurlinstalled()) {
	$options_select = array("Off","TinyURL","Bit.ly");
	$short_url_msg = 'Select the URL shortening service you would like to use.'; 
} else {
	$options_select = array("Off");
	$short_url_msg = '<strong>cURL was not detected on your server, and is required in order to use the URL shortening services.</strong>'; 
}

//Stylesheets Reader
$alt_stylesheet_path = TEMPLATEPATH . '/styles/';
$alt_stylesheets = array();

if ( is_dir($alt_stylesheet_path) ) {
    if ($alt_stylesheet_dir = opendir($alt_stylesheet_path) ) { 
        while ( ($alt_stylesheet_file = readdir($alt_stylesheet_dir)) !== false ) {
            if(stristr($alt_stylesheet_file, ".css") !== false) {
                $alt_stylesheets[] = $alt_stylesheet_file;
            }
        }    
    }
}

//More Options


$slider_image = array("Left","Right");
$other_entries = array("Select a number:","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");
$body_repeat = array("no-repeat","repeat-x","repeat-y","repeat");
$body_pos = array("top left","top center","top right","center left","center center","center right","bottom left","bottom center","bottom right");
$map_types = array('Normal','Satellite','Hybrid','Terrain'); // 2010-11-08.

// $map_types = array('G_NORMAL_MAP' => 'Normal','G_SATELLITE_MAP' => 'Satellite','G_HYBRID_MAP' => 'Hybrid','G_PHYSICAL_MAP' => 'Terrain'); // 2010-11-08.

$zoom = array("0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");

// THIS IS THE DIFFERENT FIELDS
$options = array();   

// Testing purposes only

$options[] = array( "name" => "General Settings",
					"icon" => "general",
                    "type" => "heading");
                        
$options[] = array( "name" => "Theme Stylesheet",
					"desc" => "Select your themes alternative color scheme.",
					"id" => $shortname."_alt_stylesheet",
					"std" => "default.css",
					"type" => "select",
					"options" => $alt_stylesheets);

$options[] = array( "name" => "Custom Logo",
					"desc" => "Upload a logo for your theme, or specify an image URL directly.",
					"id" => $shortname."_logo",
					"std" => "",
					"type" => "upload");    
                                                                                     
$options[] = array( "name" => "Text Title",
					"desc" => "Enable text-based Site Title and Tagline. Setup title & tagline in Settings->General.",
					"id" => $shortname."_texttitle",
					"std" => "false",
					"class" => "collapsed",
					"type" => "checkbox");

$options[] = array( "name" => "Site Title",
					"desc" => "Change the site title (must have 'Text Title' option enabled).",
					"id" => $shortname."_font_site_title",
					"std" => array('size' => '42','unit' => 'px','face' => 'Arial','style' => 'bold','color' => '#2c2525'),
					"class" => "hidden",
					"type" => "typography");  

$options[] = array( "name" => "Site Description",
					"desc" => "Change the site description (must have 'Text Title' option enabled).",
					"id" => $shortname."_font_tagline",
					"std" => array('size' => '15','unit' => 'px','face' => 'Georgia','style' => 'italic','color' => '#2c2525'),
					"class" => "hidden last",
					"type" => "typography");  
					          
$options[] = array( "name" => "Custom Favicon",
					"desc" => "Upload a 16px x 16px <a href='http://www.faviconr.com/'>ico image</a> that will represent your website's favicon.",
					"id" => $shortname."_custom_favicon",
					"std" => "",
					"type" => "upload"); 
                                               
$options[] = array( "name" => "Tracking Code",
					"desc" => "Paste your Google Analytics (or other) tracking code here. This will be added into the footer template of your theme.",
					"id" => $shortname."_google_analytics",
					"std" => "",
					"type" => "textarea");        

$options[] = array( "name" => "RSS URL",
					"desc" => "Enter your preferred RSS URL. (Feedburner or other)",
					"id" => $shortname."_feed_url",
					"std" => "",
					"type" => "text");
                    
$options[] = array( "name" => "E-Mail URL",
					"desc" => "Enter your preferred E-mail subscription URL. (Feedburner or other)",
					"id" => $shortname."_subscribe_email",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Contact Form E-Mail",
					"desc" => "Enter your E-mail address to use on the Contact Form Page Template. Add the contact form by adding a new page and selecting 'Contact Form' as page template.",
					"id" => $shortname."_contactform_email",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Custom CSS",
                    "desc" => "Quickly add some CSS to your theme by adding it to this block.",
                    "id" => $shortname."_custom_css",
                    "std" => "",
                    "type" => "textarea");

$options[] = array( "name" => "Post/Page Comments",
					"desc" => "Select if you want to enable/disable comments on posts and/or pages. ",
					"id" => $shortname."_comments",
					"type" => "select2",
					"options" => array("post" => "Posts Only", "page" => "Pages Only", "both" => "Pages / Posts", "none" => "None") );                                                          
    
$options[] = array( "name" => "Post Content",
					"desc" => "Select if you want to show the full content or the excerpt on posts. ",
					"id" => $shortname."_post_content",
					"type" => "select2",
					"options" => array("excerpt" => "The Excerpt", "content" => "Full Content" ) );                                                          

$options[] = array( "name" => "Styling Options",
					"icon" => "styling",
					"type" => "heading");   
					
$options[] = array( "name" =>  "Body Background Color",
					"desc" => "Pick a custom color for background color of the theme e.g. #697e09",
					"id" => "woo_body_color",
					"std" => "",
					"type" => "color");
					
$options[] = array( "name" => "Body background image",
					"desc" => "Upload an image for the theme's background",
					"id" => $shortname."_body_img",
					"std" => "",
					"type" => "upload");
					
$options[] = array( "name" => "Background image repeat",
                    "desc" => "Select how you would like to repeat the background-image",
                    "id" => $shortname."_body_repeat",
                    "std" => "no-repeat",
                    "type" => "select",
                    "options" => $body_repeat);

$options[] = array( "name" => "Background image position",
                    "desc" => "Select how you would like to position the background",
                    "id" => $shortname."_body_pos",
                    "std" => "top",
                    "type" => "select",
                    "options" => $body_pos);

$options[] = array( "name" =>  "Link Color",
					"desc" => "Pick a custom color for links or add a hex color code e.g. #697e09",
					"id" => "woo_link_color",
					"std" => "",
					"type" => "color");   

$options[] = array( "name" =>  "Link Hover Color",
					"desc" => "Pick a custom color for links hover or add a hex color code e.g. #697e09",
					"id" => "woo_link_hover_color",
					"std" => "",
					"type" => "color");                    

$options[] = array( "name" =>  "Button Color",
					"desc" => "Pick a custom color for buttons or add a hex color code e.g. #697e09",
					"id" => "woo_button_color",
					"std" => "",
					"type" => "color");          
					
$options[] = array( "name" => "Typography",
					"icon" => "typography",
					"type" => "heading"); 
					
$options[] = array( "name" => "Disable Cufon",
					"desc" => "Check this box if you want to disable the Cufon font replacement (used in navigation and footer).",
					"id" => $shortname."_cufon_disabled",
					"std" => "false",
					"type" => "checkbox");  

$options[] = array( "name" => "Enable Custom Typography",
					"desc" => "Enable the use of custom typography for your site. Custom styling will be output in your sites HEAD.",
					"id" => $shortname."_typography",
					"std" => "false",
					"type" => "checkbox"); 									   

$options[] = array( "name" => "General Typography",
					"icon" => "typography",
					"desc" => "Change the general font.",
					"id" => $shortname."_font_body",
					"std" => array('size' => '14','unit' => 'px','face' => 'Georgia','style' => '','color' => '#2c2525'),
					"type" => "typography");  

$options[] = array( "name" => "Navigation",
					"desc" => "Change the navigation font.",
					"id" => $shortname."_font_nav",
					"std" => array('size' => '14','unit' => 'px','face' => 'Georgia','style' => '','color' => '#9b8b8b'),
					"type" => "typography");  

$options[] = array( "name" => "Post Title",
					"desc" => "Change the post title.",
					"id" => $shortname."_font_post_title",
					"std" => array('size' => '32','unit' => 'px','face' => 'Arial','style' => 'bold','color' => '#222222'),
					"type" => "typography");  

$options[] = array( "name" => "Post Meta",
					"desc" => "Change the post meta.",
					"id" => $shortname."_font_post_meta",
					"std" => array('size' => '11','unit' => 'px','face' => 'Arial','style' => '','color' => '#868686'),
					"type" => "typography");  
					          
$options[] = array( "name" => "Post Entry",
					"desc" => "Change the post entry.",
					"id" => $shortname."_font_post_entry",
					"std" => array('size' => '14','unit' => 'px','face' => 'Georgia','style' => '','color' => '#2c2525'),
					"type" => "typography");  

$options[] = array( "name" => "Widget Titles",
					"desc" => "Change the widget titles.",
					"id" => $shortname."_font_widget_titles",
					"std" => array('size' => '20','unit' => 'px','face' => 'Arial','style' => 'bold','color' => '#2c2525'),
					"type" => "typography");  
					
$options[] = array( "name" => "Diner Settings",
					"icon" => "misc",
					"type" => "heading"); 

$options[] = array( "name" => "Currency Symbol",
                    "desc" => "Specify the currency that your meal prices will be shown in.",
                    "id" => $shortname."_diner_currency",
                    "std" => "$",
                    "type" => "text");

$options[] = array( "name" => "Enable Reservation in header",
					"desc" => "Check this box to enable the Reservation area in the header",
					"id" => $shortname."_reservations",
					"std" => "true",
					"type" => "checkbox");

$options[] = array( "name" => "Reservations Text",
					"desc" => "Enter the reservations area heading.",
					"id" => $shortname."_reservations_heading",
					"std" => "Reservations",
					"type" => "text"); 

$options[] = array( "name" => "Reservations Text",
					"desc" => "Enter the reservations area second line e.g. phone number.",
					"id" => $shortname."_reservations_text",
					"std" => "+23 456 789",
					"type" => "text"); 

$options[] = array( "name" => "Reservations Button Text",
					"desc" => "Enter the reservations area button text ",
					"id" => $shortname."_reservations_button",
					"std" => "Book Online",
					"type" => "text"); 

$options[] = array( "name" => "Enable Homepage Intro Area",
					"desc" => "Enable the homepage intro area",
					"id" => $shortname."_info_home",
					"std" => "true",
					"type" => "checkbox");

$options[] = array( "name" => "Homepage Intro - Heading",
					"desc" => "Enter the heading text for the information message about your Diner.",
					"id" => $shortname."_diner_intro_text_header",
					"std" => "Some more information",
					"type" => "textarea"); 
					
$options[] = array( "name" => "Homepage Intro - Message",
					"desc" => "Enter some information text about your Diner.",
					"id" => $shortname."_diner_intro_text",
					"std" => "An introduction message for visitors to your Diners website.",
					"type" => "textarea"); 

$options[] = array( "name" => "Homepage Intro Message Image",
					"desc" => "Upload an image that will be displayed next to the information text. Image dimensions: <strong>275x245px</strong>",
					"id" => $shortname."_diner_intro_text_image",
					"std" => get_bloginfo('template_directory')."/images/temp-moreinfo.jpg",
					"type" => "upload");
					
$options[] = array( "name" => "Booking Page Header Description",
					"desc" => "Enter the booking page description.",
					"id" => $shortname."_page_booking_desc",
					"std" => "Use the form below to reserve a table at our restaurant",
					"type" => "text"); 										

// Send to a Friend Theme Options

$options[] = array( "name" => "Send to a Friend",
					"icon" => "misc",
					"type" => "heading"); 

$options[] = array( "name" => "'Send to a Friend' From Name",
					"desc" => "The name sent with the e-mails as the `From` name. Use the codes <code>%yourname%</code> and <code>%theirname%</code> for dynamic data.",
					"id" => $shortname."_sendtofriend_from_name",
					"std" => "%yourname% via " . get_bloginfo('name'),
					"type" => "text");

$myurl = get_option('siteurl'); 
preg_match("/^(http:\/\/)?([^\/]+)/i", $myurl, $domain_only );
					
$options[] = array( "name" => "'Send to a Friend' From E-mail",
					"desc" => "The e-mail address sent with the e-mails as the `From` e-mail address.",
					"id" => $shortname."_sendtofriend_from_email",
					"std" => 'no-reply@' . $domain_only[2],
					"type" => "text");

$options[] = array( "name" => "'Send a Location' E-mail Subject",
					"desc" => "The subject line used when sending a location to a friend.",
					"id" => $shortname."_sendtofriend_location_subject",
					"std" => "Check out this diner.",
					"type" => "text"); 

$options[] = array( "name" => "'Send a Location' E-mail Message",
					"desc" => "The text used when sending a location to a friend. Use the codes <code>%yourname%</code>, <code>%theirname%</code> and <code>%url%</code> for dynamic data.",
					"id" => $shortname."_sendtofriend_location_message",
					"std" => "Hi %theirname%,\n\nI thought you would enjoy this Diner. Find Directions to the Diner here: %url%.\n\nKind Regards,\n%yourname%",
					"type" => "textarea");
					
$options[] = array( "name" => "'Send a Menu' E-mail Subject",
					"desc" => "The subject line used when sending a menu to a friend.",
					"id" => $shortname."_sendtofriend_menu_subject",
					"std" => "Check out this menu.",
					"type" => "text"); 

$options[] = array( "name" => "'Send a Menu' E-mail Message",
					"desc" => "The text used when sending a menu to a friend. Use the codes <code>%yourname%</code>, <code>%theirname%</code> and <code>%url%</code> for dynamic data.",
					"id" => $shortname."_sendtofriend_menu_message",
					"std" => "Hi %theirname%,\n\nI thought you would enjoy this menu. Check it out here: %url%.\n\nKind Regards,\n%yourname%",
					"type" => "textarea");

// Map Settings Theme Options
           
$options[] = array( "name" => "Map Settings",
					"icon" => "maps",
				    "type" => "heading");    

$options[] = array( "name" => "Google Maps API Key",
					"desc" => "Enter your Google Maps API key before using any of Postcard's mapping functionality. <a href='http://code.google.com/apis/maps/signup.html'>Signup for an API key here</a>.",
					"id" => $shortname."_maps_apikey",
					"std" => "",
					"type" => "text"); 
					
$options[] = array( "name" => "Disable Mousescroll",
					"desc" => "Turn off the mouse scroll action for all the Google Maps on the site. This could improve usability on your site.",
					"id" => $shortname."_maps_scroll",
					"std" => "",
					"type" => "checkbox");

$options[] = array( "name" => "Single Page Map Height",
					"desc" => "Height in pixels for the maps displayed on Single.php pages.",
					"id" => $shortname."_maps_single_height",
					"std" => "250",
					"type" => "text");

$options[] = array( "name" => "Enable Latitude & Longitude Coordinates:",
					"desc" => "Enable or disable coordinates in the head of single posts pages.",
					"id" => $shortname."_coords",
					"std" => "true",
					"type" => "checkbox");
					
$options[] = array( "name" => "Default Map Zoom Level",
					"desc" => "Set this to adjust the default in the post & page edit backend.",
					"id" => $shortname."_maps_default_mapzoom",
					"std" => "9",
					"type" => "select2",
					"options" => $zoom);

$options[] = array( "name" => "Default Map Type",
					"desc" => "Set this to the default rendered in the post backend.",
					"id" => $shortname."_maps_default_maptype",
					"std" => "Normal",
					"type" => "select2",
					"options" => array('G_NORMAL_MAP' => 'Normal','G_SATELLITE_MAP' => 'Satellite','G_HYBRID_MAP' => 'Hybrid','G_PHYSICAL_MAP' => 'Terrain'));

$options[] = array( "name" => "Directions Locale",
					"desc" => "Set the locale (translation language) for the directions created by Google maps.",
					"id" => $shortname."_maps_directions_locale",
					"std" => "",
					"type" => "text");

$options[] = array( "name" => "Location Map",
					"icon" => "maps",
					"type" => "heading"); 

$options[] = array( "name" => "Address",
					"desc" => "Enter the address of your Diner.",
					"id" => $shortname."_diner_address",
					"std" => "",
					"type" => "textarea"); 
										
$options[] = array( "name" => "Latitude",
                    "desc" => "Specify the latitude of your Diner.",
                    "id" => $shortname."_diner_map_latitude",
                    "std" => "",
                    "type" => "text");

$options[] = array( "name" => "Longitude",
                    "desc" => "Specify the longitude of your Diner.",
                    "id" => $shortname."_diner_map_longitude",
                    "std" => "",
                    "type" => "text");

$options[] = array( "name" => "Point of View: Yaw",
                    "desc" => "Specify the Yaw for the Google Map.",
                    "id" => $shortname."_diner_map_pov_yaw",
                    "std" => "",
                    "type" => "text");

$options[] = array( "name" => "Point of View: Pitch",
                    "desc" => "Specify the Pitch for the Google Map.",
                    "id" => $shortname."_diner_map_pov_pitch",
                    "std" => "",
                    "type" => "text");

$options[] = array( "name" => "Zoom Level",
                    "desc" => "Select the zoom level for the Google map.",
                    "id" => $shortname."_diner_map_zoom_level",
                    "std" => "6",
                    "type" => "select",
                    "options" => $other_entries);

$options[] = array( "name" => "Map Type",
                    "desc" => "Select the type of map for the Google map.",
                    "id" => $shortname."_diner_map_type",
                    "std" => "Normal",
                    "type" => "select",
                    "options" => $map_types);
                                                                                                                                            					
$options[] = array( "name" => "Featured Panel",
					"icon" => "featured",
					"type" => "heading");    

$options[] = array( "name" => "Enable Featured",
					"desc" => "Show the featured panel on the front page.",
					"id" => $shortname."_featured",
					"std" => "false",
					"type" => "checkbox");    
                    
$options[] = array( "name" => "Featured image position",
                    "desc" => "Select on which side of the slider you want to have the image",
                    "id" => $shortname."_featured_img_pos",
                    "std" => "Right",
                    "type" => "select",
                    "options" => $slider_image);

$options[] = array(    "name" => "Auto Start",
                    "desc" => "Set the slider to start sliding automatically. Adjust the speed of sliding underneath.",
                    "id" => $shortname."_slider_auto",
                    "std" => "false",
                    "type" => "checkbox");   

$options[] = array(    "name" => "Animation Speed",
                    "desc" => "The time in <b>seconds</b> the animation between frames will take e.g. 0.6",
                    "id" => $shortname."_slider_speed",
                    "std" => 0.6,
					"type" => "select",
					"options" => array( '0.0', '0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7', '0.8', '0.9', '1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '2.0' ) );
                    
$options[] = array(    "name" => "Auto Slide Interval",
                    "desc" => "The time in <b>seconds</b> each slide pauses for, before sliding to the next. Only when using Auto Start option above.",
                    "id" => $shortname."_slider_interval",
					"std" => "4",
					"type" => "select",
					"options" => array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' ) );
					 					                   
$options[] = array( "name" => "Dynamic Images",
					"icon" => "image",
				    "type" => "heading");  
				    				   
$options[] = array( "name" => 'Dynamic Image Resizing',
					"desc" => "",
					"id" => $shortname."_wpthumb_notice",
					"std" => 'There are two alternative methods of dynamically resizing the thumbnails in the theme, <strong>WP Post Thumbnail</strong> or <strong>TimThumb - Custom Settings panel</strong>. We recommend using WP Post Thumbnail option.',
					"type" => "info");					

$options[] = array( "name" => "WP Post Thumbnail",
					"desc" => "Use WordPress post thumbnail to assign a post thumbnail. Will enable the <strong>Featured Image panel</strong> in your post sidebar where you can assign a post thumbnail.",
					"id" => $shortname."_post_image_support",
					"std" => "true",
					"class" => "collapsed",
					"type" => "checkbox" );

$options[] = array( "name" => "WP Post Thumbnail - Dynamic Image Resizing",
					"desc" => "The post thumbnail will be dynamically resized using native WP resize functionality. <em>(Requires PHP 5.2+)</em>",
					"id" => $shortname."_pis_resize",
					"std" => "true",
					"class" => "hidden",
					"type" => "checkbox" );

$options[] = array( "name" => "WP Post Thumbnail - Hard Crop",
					"desc" => "The post thumbnail will be cropped to match the target aspect ratio (only used if 'Dynamic Image Resizing' is enabled).",
					"id" => $shortname."_pis_hard_crop",
					"std" => "true",
					"class" => "hidden last",
					"type" => "checkbox" );

$options[] = array( "name" => "TimThumb - Custom Settings Panel",
					"desc" => "This will enable the <a href='http://code.google.com/p/timthumb/'>TimThumb</a> (thumb.php) script which dynamically resizes images added through the <strong>custom settings panel below the post</strong>. Make sure your themes <em>cache</em> folder is writable. <a href='http://www.woothemes.com/2008/10/troubleshooting-image-resizer-thumbphp/'>Need help?</a>",
					"id" => $shortname."_resize",
					"std" => "true",
					"type" => "checkbox" );

$options[] = array( "name" => "Automatic Image Thumbnail",
					"desc" => "If no thumbnail is specifified then the first uploaded image in the post is used.",
					"id" => $shortname."_auto_img",
					"std" => "false",
					"type" => "checkbox" );
					
$options[] = array( "name" => "Thumbnail Image Dimensions",
					"desc" => "Enter an integer value i.e. 250 for the desired size which will be used when dynamically creating the images.",
					"id" => $shortname."_image_dimensions",
					"std" => "",
					"type" => array( 
									array(  'id' => $shortname. '_thumb_w',
											'type' => 'text',
											'std' => 100,
											'meta' => 'Width'),
									array(  'id' => $shortname. '_thumb_h',
											'type' => 'text',
											'std' => 100,
											'meta' => 'Height')
								  ));
                                                                                                
$options[] = array( "name" => "Thumbnail Image alignment",
					"desc" => "Select how to align your thumbnails with posts.",
					"id" => $shortname."_thumb_align",
					"std" => "alignleft",
					"type" => "radio",
					"options" => $options_thumb_align); 

$options[] = array( "name" => "Show thumbnail in Single Posts",
					"desc" => "Show the attached image in the single post page.",
					"id" => $shortname."_thumb_single",
					"class" => "collapsed",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => "Single Image Dimensions",
					"desc" => "Enter an integer value i.e. 250 for the image size. Max width is 576.",
					"id" => $shortname."_image_dimensions",
					"std" => "",
					"class" => "hidden last",
					"type" => array( 
									array(  'id' => $shortname. '_single_w',
											'type' => 'text',
											'std' => 200,
											'meta' => 'Width'),
									array(  'id' => $shortname. '_single_h',
											'type' => 'text',
											'std' => 200,
											'meta' => 'Height')
								  ));

$options[] = array( "name" => "Single Post Image alignment",
					"desc" => "Select how to align your thumbnail with single posts.",
					"id" => $shortname."_thumb_single_align",
					"std" => "alignright",
					"type" => "radio",
					"class" => "hidden",
					"options" => $options_thumb_align); 

$options[] = array( "name" => "Add thumbnail to RSS feed",
					"desc" => "Add the the image uploaded via your Custom Settings to your RSS feed",
					"id" => $shortname."_rss_thumb",
					"std" => "false",
					"type" => "checkbox");  
					
//Footer
$options[] = array( "name" => "Footer Customization",
					"icon" => "footer",
                    "type" => "heading");
                    
$url =  get_template_directory_uri() . '/functions/images/';
$options[] = array( "name" => "Footer Widget Areas",
					"desc" => "Select how many footer widget areas you want to display.",
					"id" => $shortname."_footer_sidebars",
					"std" => "4",
					"type" => "images",
					"options" => array(
						'0' => $url . 'layout-off.png',
						'1' => $url . 'footer-widgets-1.png',
						'2' => $url . 'footer-widgets-2.png',
						'3' => $url . 'footer-widgets-3.png',
						'4' => $url . 'footer-widgets-4.png')
					);
					
$options[] = array( "name" => "Custom Affiliate Link",
					"desc" => "Add an affiliate link to the WooThemes logo in the footer of the theme.",
					"id" => $shortname."_footer_aff_link",
					"std" => "",
					"type" => "text");	
									
$options[] = array( "name" => "Enable Custom Footer (Left)",
					"desc" => "Activate to add the custom text below to the theme footer.",
					"id" => $shortname."_footer_left",
					"class" => "collapsed",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => "Custom Text (Left)",
					"desc" => "Custom HTML and Text that will appear in the footer of your theme.",
					"id" => $shortname."_footer_left_text",
					"class" => "hidden last",
					"std" => "<p></p>",
					"type" => "textarea");
						
$options[] = array( "name" => "Enable Custom Footer (Right)",
					"desc" => "Activate to add the custom text below to the theme footer.",
					"id" => $shortname."_footer_right",
					"class" => "collapsed",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => "Custom Text (Right)",
					"desc" => "Custom HTML and Text that will appear in the footer of your theme.",
					"id" => $shortname."_footer_right_text",
					"class" => "hidden last",
					"std" => "<p></p>",
					"type" => "textarea");
							
//Advertising
$options[] = array( "name" => "Homepage Banner (728x90px)",
					"icon" => "ads",
                    "type" => "heading");

$options[] = array( "name" => "Enable Ad",
					"desc" => "Enable the ad space",
					"id" => $shortname."_ad_home",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => "Adsense code",
					"desc" => "Enter your adsense code (or other ad network code) here.",
					"id" => $shortname."_ad_home_adsense",
					"std" => "",
					"type" => "textarea");

$options[] = array( "name" => "Image Location",
					"desc" => "Enter the URL to the banner ad image location.",
					"id" => $shortname."_ad_home_image",
					"std" => "http://www.woothemes.com/ads/468x60b.jpg",
					"type" => "upload");
					
$options[] = array( "name" => "Destination URL",
					"desc" => "Enter the URL where this banner ad points to.",
					"id" => $shortname."_ad_home_url",
					"std" => "http://www.woothemes.com",
					"type" => "text"); 

$options[] = array( "name" => "Top Ad (468x60px)",
					"icon" => "ads",
                    "type" => "heading");

$options[] = array( "name" => "Enable Ad",
					"desc" => "Enable the ad space (this will replace 'Reservations' in the header)",
					"id" => $shortname."_ad_top",
					"std" => "false",
					"type" => "checkbox");    

$options[] = array( "name" => "Adsense code",
					"desc" => "Enter your adsense code (or other ad network code) here.",
					"id" => $shortname."_ad_top_adsense",
					"std" => "",
					"type" => "textarea");

$options[] = array( "name" => "Image Location",
					"desc" => "Enter the URL to the banner ad image location.",
					"id" => $shortname."_ad_top_image",
					"std" => "http://www.woothemes.com/ads/468x60b.jpg",
					"type" => "upload");
					
$options[] = array( "name" => "Destination URL",
					"desc" => "Enter the URL where this banner ad points to.",
					"id" => $shortname."_ad_top_url",
					"std" => "http://www.woothemes.com",
					"type" => "text");                        
                                              

// Add extra options through function
if ( function_exists("woo_options_add") )
	$options = woo_options_add($options);

if ( get_option('woo_template') != $options) update_option('woo_template',$options);      
if ( get_option('woo_themename') != $themename) update_option('woo_themename',$themename);   
if ( get_option('woo_shortname') != $shortname) update_option('woo_shortname',$shortname);
if ( get_option('woo_manual') != $manualurl) update_option('woo_manual',$manualurl);



                                     
// Woo Metabox Options
// Start name with underscore to hide custom key from the user
$woo_metaboxes = array();

if( get_post_type() == 'post' || !get_post_type()){

	$woo_metaboxes[] = array (	"name" => "image",
								"label" => "Thumbnail Image",
								"type" => "upload",
								"desc" => "Upload file here...");
								

	$woo_metaboxes[] = array (	"name" => "embed",
								"label" => __( 'Embed', 'woothemes' ),
								"type" => "textarea",
								"desc" => __( 'Enter embed code for use on single posts and with the Video widget.', 'woothemes' ) );

}

	if ( get_option('woo_resize') == "true" ) {						
		$woo_metaboxes[] = array (	"name" => "_image_alignment",
									"std" => "Center",
									"label" => "Image Crop Alignment",
									"type" => "select2",
									"desc" => "Select crop alignment for resized image",
									"options" => array(	"c" => "Center",
														"t" => "Top",
														"b" => "Bottom",
														"l" => "Left",
														"r" => "Right"));
	}


if( get_post_type() == 'slide' || !get_post_type()){

	$woo_metaboxes[] = array (	"name" => "image",
								"label" => "Slider Image (515x245px)",
								"type" => "upload",
								"desc" => "Upload image to be used in slider (515x245px)");
								
}


if( get_post_type() == 'page' || !get_post_type()){
	
	$woo_metaboxes[] = array (	"name" => "menu_additional_info",
								"std" => "Something informative about the menus.",
								"label" => "Menu Additional Info",
								"type" => "text",
								"desc" => "This is the text that will be displayed on the Menu Templates in the Additional Info area.");
	
	$woo_metaboxes[] = array (	"name" => "menu_pdf",
								"label" => "Menu PDF",
								"type" => "upload",
								"desc" => "Upload file here...");
															
}

if( get_post_type() == 'woo_menu' || !get_post_type()){

	$woo_metaboxes[] = array (	"name" => "thumbnail",
								"label" => "Thumbnail Image",
								"type" => "upload",
								"desc" => "Upload file here...");
								
	if ( get_option('woo_resize') == "true" ) {						
		$woo_metaboxes[] = array (	"name" => "_image_alignment",
									"std" => "Center",
									"label" => "Image Crop Alignment",
									"type" => "select2",
									"desc" => "Select crop alignment for resized image",
									"options" => array(	"c" => "Center",
														"t" => "Top",
														"b" => "Bottom",
														"l" => "Left",
														"r" => "Right"));
	}

								
	$woo_metaboxes[] = array (	"name" => "price",
								"std" => "0",
								"label" => "Price in ".get_option('woo_diner_currency'),
								"type" => "text",
								"desc" => "Enter the price of the meal excluding the currency symbol.");
								
}

if( get_post_type() == 'woo_location' || !get_post_type()){
	
	/*$woo_metaboxes[] = array (	"name" => "currency",
								"std" => get_option('woo_diner_currency'),
								"label" => "Currency",
								"type" => "text",
								"desc" => "This locations currency. This will override the default currency of the theme for this location.");*/
	
	$woo_metaboxes[] = array (	"name" => "head_office",
								"std" => "false",
								"label" => "Is Head Office",
								"type" => "checkbox",
								"desc" => "Choose if this location is your head office");
															
}

    
// Add extra metaboxes through function
if ( function_exists("woo_metaboxes_add") )
	$woo_metaboxes = woo_metaboxes_add($woo_metaboxes);
    
if ( get_option('woo_custom_template') != $woo_metaboxes) update_option('woo_custom_template',$woo_metaboxes);    

//Only show SEO on these registered post types


  

}
}



?>