<?php

$woo_cpt_all = get_option("woo_content_builder_cpt");
$array_seo_types = array();
// Check if custom post types exist
if ( is_array($woo_cpt_all) ) {
	foreach ($woo_cpt_all as $cpt_item) {
		array_push($array_seo_types, $cpt_item['name']);
	}
}
$seo_post_types = array_merge(array('post','page'), $array_seo_types);
define("SEOPOSTTYPES", serialize($seo_post_types));

//Global options setup
add_action('init','woo_global_options');
function woo_global_options(){
	// Populate WooThemes option in array for use in theme
	global $woo_options;
	$woo_options = get_option('woo_options');
}

add_action('admin_head','woo_options',2);  
if (!function_exists('woo_options')) {
function woo_options() {
	
// VARIABLES
$themename = "Listings";
$manualurl = 'http://www.woothemes.com/support/theme-documentation/listings/';
$shortname = "woo";

$GLOBALS['template_path'] = get_bloginfo('template_directory');

//Access the WordPress Categories via an Array
$woo_categories = array();  
$woo_categories_obj = get_categories('hide_empty=0');
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
$options_thumb_align = array("alignleft" => "Left","alignright" => "Right","aligncenter" => "Center"); 

// Image Links to Options
$options_image_link_to = array("image" => "The Image","post" => "The Post"); 

//Testing 
$options_select = array("one","two","three","four","five"); 
$options_radio = array("one" => "One","two" => "Two","three" => "Three","four" => "Four","five" => "Five"); 

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

//Access the WordPress Post types via an Array
$cpt_args = array( 'public' => true,'_builtin' => false);
$out = 'names';
$operator = 'and';
$woo_posttypes = array();
$woo_posttypes_obj = get_post_types($cpt_args,$out,$operator);
foreach ($woo_posttypes_obj as $woo_posttype) {
    $woo_posttypes[] = $woo_posttype; }
$woo_posttypes_tmp = array_unshift($woo_posttypes, "Select a post type:");

//GET all custom fields
$woo_wp_custom_fields = array();  
$woo_wp_custom_fields = get_option('woo_custom_template');
if ( $woo_wp_custom_fields == '' ) { $woo_wp_custom_fields = array(); } // Fix if empty
$woo_wp_custom_fields_formatted = array();  
$woo_wp_custom_fields_formatted['none'] = 'None';
foreach ($woo_wp_custom_fields as $woo_wp_custom_field) {
  	$woo_wp_custom_fields_formatted[$woo_wp_custom_field['name']] = $woo_wp_custom_field['label']; }

//GET all custom taxonomies
//$wp_custom_taxonomy_args = array(	'_builtin' => false );
$wp_custom_taxonomy_args = array();
$woo_wp_custom_taxonomies = array();  
$woo_wp_custom_taxonomies = get_taxonomies($wp_custom_taxonomy_args,'objects');   
$woo_wp_custom_taxonomies_formatted = array();  
$woo_wp_custom_taxonomies_formatted['none'] = 'None';
foreach ($woo_wp_custom_taxonomies as $woo_wp_custom_taxonomy) {
    $woo_wp_custom_taxonomies_formatted[$woo_wp_custom_taxonomy->name] = $woo_wp_custom_taxonomy->label; }
    
//GET all custom post types
//$wp_custom_post_types_args = array(	'_builtin' => true	);
$wp_custom_post_types_args = array();
$wp_custom_post_types = array();  
$wp_custom_post_types = get_post_types($wp_custom_post_types_args,'objects');   
$woo_wp_custom_post_types_formatted = array();
$woo_wp_custom_post_types_formatted['none'] = 'None';
foreach ($wp_custom_post_types as $woo_wp_content_type) {
    $woo_wp_custom_post_types_formatted[$woo_wp_content_type->name] = $woo_wp_content_type->label; }
     

$zoom = array("0","1","2","3","4","5","6","7","8","9","10","11","12","13","14","15","16","17","18","19");
$colors = array('blue'=>'Blue','red'=>'Red','green'=>'Green','yellow'=>'Yellow','pink'=>'Pink','purple'=>'Purple','teal'=>'Teal','white'=>'White','black'=>'Black');                    

function category_nav($options,$colors) {

     $options[] = array(    "name" =>  "Map Markers",
							"icon" => "maps",
							"id" => "woo_map_markers_heading",
        					"type" => "heading");  
    
    $options[] = array(    "name" =>  "Marker Pin Color",
                        	"desc" => "Choose from a preset colored pin.",
                        	"id" => "woo_cat_colors_pages",
                        	"std" => "red",
                        	"type" => "select2",
                        	"options" => $colors);
    $options[] = array(    
            				"name" =>  "",
                       		"desc" => "Add a custom image. Find more <a href='http://groups.google.com/group/google-chart-api/web/chart-types-for-map-pins?pli=1'>here</a>.",
                        	"id" => "woo_cat_custom_marker_pages",
                        	"std" => "",
                        	"class" => "hidden",
                        	"type" => "text");  

    $cats = get_categories('hide_empty=0');

    foreach ($cats as $cat) {

            $options[] = array(    "name" =>  $cat->cat_name,
                        "desc" => "Choose from a preset colored pin.",
                        "id" => "woo_cat_colors_".$cat->cat_ID,
                        "std" => "red",
                        "type" => "select2",
                        "class" => "hidden",
                        "options" => $colors);
            $options[] = array(    
            			"name" =>  "",
                        "desc" => "Add a custom image. Find more <a href='http://groups.google.com/group/google-chart-api/web/chart-types-for-map-pins?pli=1'>here</a>.",
                        "id" => "woo_cat_custom_marker_".$cat->cat_ID,
                        "std" => "",
                        "class" => "hidden",
                        "type" => "text");
                                   
    
    }

    return $options;
}
  	
// THIS IS THE DIFFERENT FIELDS
$options = array();   

$options[] = array( "name" => "General Settings",
					"icon" => "general",
					"id" => $shortname."_general_heading",
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
					"desc" => "Enable text-based Site Title and Tagline. Setup title & tagline in <a href='".home_url()."/wp-admin/options-general.php'>General Settings</a>.",
					"id" => $shortname."_texttitle",
					"std" => "false",
					"class" => "collapsed",
					"type" => "checkbox" );

$options[] = array( "name" => "Site Title",
					"desc" => "Change the site title typography.",
					"id" => $shortname."_font_site_title",
					"std" => array( 'size' => '40','unit' => 'px','face' => 'Droid Sans','style' => 'bold','color' => '#222222'),
					"class" => "hidden",
					"type" => "typography" );

$options[] = array( "name" => "Site Description",
					"desc" => "Enable the site description/tagline under site title.",
					"id" => $shortname."_tagline",
					"class" => "hidden",
					"std" => "false",
					"type" => "checkbox" );

$options[] = array( "name" => "Site Description",
					"desc" => "Change the site description typography.",
					"id" => $shortname."_font_tagline",
					"std" => array( 'size' => '13','unit' => 'px','face' => 'Droid Serif','style' => 'italic','color' => '#ffffff'),
					"class" => "hidden last",
					"type" => "typography" );
					          
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

$options[] = array( "name" => "Twitter",
					"desc" => "Enter your Twitter username",
					"id" => $shortname."_social_twitter",
					"std" => "",
					"type" => "text");
					
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

$options[] = array( "name" => "Pagination Style",
					"desc" => "Select the style of pagination you would like to use on the blog.",
					"id" => $shortname."_pagination_type",
					"type" => "select2",
					"options" => array( "paginated_links" => "Numbers", "simple" => "Next/Previous" ) );
					
$options[] = array( "name" => "Styling Options",
					"icon" => "styling",
					"id" => $shortname."_styling_options_heading",
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
					"id" => $shortname."_typography_heading",
					"type" => "heading");    

$options[] = array( "name" => "Enable Custom Typography",
					"desc" => "Enable the use of custom typography for your site. Custom styling will be output in your sites HEAD.",
					"id" => $shortname."_typography",
					"std" => "false",
					"type" => "checkbox"); 									   

$options[] = array( "name" => "General Typography",
					"desc" => "Change the general font.",
					"id" => $shortname."_font_body",
					"std" => array('size' => '12','unit' => 'px','face' => 'Arial','style' => '','color' => '#555555'),
					"type" => "typography");  

$options[] = array( "name" => "Navigation",
					"desc" => "Change the navigation font.",
					"id" => $shortname."_font_nav",
					"std" => array('size' => '14','unit' => 'px','face' => 'Arial','style' => '','color' => '#555555'),
					"type" => "typography");  

$options[] = array( "name" => "Post Title",
					"desc" => "Change the post title.",
					"id" => $shortname."_font_post_title",
					"std" => array('size' => '24','unit' => 'px','face' => 'Arial','style' => 'bold','color' => '#222222'),
					"type" => "typography");  

$options[] = array( "name" => "Post Meta",
					"desc" => "Change the post meta.",
					"id" => $shortname."_font_post_meta",
					"std" => array('size' => '11','unit' => 'px','face' => 'Arial','style' => '','color' => '#868686'),
					"type" => "typography");  
					          
$options[] = array( "name" => "Post Entry",
					"desc" => "Change the post entry.",
					"id" => $shortname."_font_post_entry",
					"std" => array('size' => '14','unit' => 'px','face' => 'Arial','style' => '','color' => '#555555'),
					"type" => "typography");  

$options[] = array( "name" => "Widget Titles",
					"desc" => "Change the widget titles.",
					"id" => $shortname."_font_widget_titles",
					"std" => array('size' => '16','unit' => 'px','face' => 'Arial','style' => 'bold','color' => '#555555'),
					"type" => "typography");  
 					                   
$options[] = array( "name" => "Dynamic Images",
					"icon" => "image",
					"id" => $shortname."_dynamic_images_heading",
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
					"id" => $shortname."_footer_customization_heading",
                    "type" => "heading");
					
					
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

/* Subscribe & Connect */
$options[] = array( "name" => "Subscribe & Connect",
					"type" => "heading",
					"icon" => "connect" );

$options[] = array( "name" => "Enable Subscribe & Connect - Single Post",
					"desc" => "Enable the subscribe & connect area on single posts. You can also add this as a <a href='".home_url()."/wp-admin/widgets.php'>widget</a> in your sidebar.",
					"id" => $shortname."_connect",
					"std" => 'false',
					"type" => "checkbox" );

$options[] = array( "name" => "Subscribe Title",
					"desc" => "Enter the title to show in your subscribe & connect area.",
					"id" => $shortname."_connect_title",
					"std" => '',
					"type" => "text" );

$options[] = array( "name" => "Text",
					"desc" => "Change the default text in this area.",
					"id" => $shortname."_connect_content",
					"std" => '',
					"type" => "textarea" );

$options[] = array( "name" => "Subscribe By E-mail ID (Feedburner)",
					"desc" => "Enter your <a href='http://www.google.com/support/feedburner/bin/answer.py?hl=en&answer=78982'>Feedburner ID</a> for the e-mail subscription form.",
					"id" => $shortname."_connect_newsletter_id",
					"std" => '',
					"type" => "text" );

$options[] = array( "name" => 'Subscribe By E-mail to MailChimp', 'woothemes',
					"desc" => 'If you have a MailChimp account you can enter the <a href="http://woochimp.heroku.com" target="_blank">MailChimp List Subscribe URL</a> to allow your users to subscribe to a MailChimp List.',
					"id" => $shortname."_connect_mailchimp_list_url",
					"std" => '',
					"type" => "text");

$options[] = array( "name" => "Enable RSS",
					"desc" => "Enable the subscribe and RSS icon.",
					"id" => $shortname."_connect_rss",
					"std" => 'true',
					"type" => "checkbox" );

$options[] = array( "name" => "Twitter URL",
					"desc" => "Enter your  <a href='http://www.twitter.com/'>Twitter</a> URL e.g. http://www.twitter.com/woothemes",
					"id" => $shortname."_connect_twitter",
					"std" => '',
					"type" => "text" );

$options[] = array( "name" => "Facebook URL",
					"desc" => "Enter your  <a href='http://www.facebook.com/'>Facebook</a> URL e.g. http://www.facebook.com/woothemes",
					"id" => $shortname."_connect_facebook",
					"std" => '',
					"type" => "text" );

$options[] = array( "name" => "YouTube URL",
					"desc" => "Enter your  <a href='http://www.youtube.com/'>YouTube</a> URL e.g. http://www.youtube.com/woothemes",
					"id" => $shortname."_connect_youtube",
					"std" => '',
					"type" => "text" );

$options[] = array( "name" => "Flickr URL",
					"desc" => "Enter your  <a href='http://www.flickr.com/'>Flickr</a> URL e.g. http://www.flickr.com/woothemes",
					"id" => $shortname."_connect_flickr",
					"std" => '',
					"type" => "text" );

$options[] = array( "name" => "LinkedIn URL",
					"desc" => "Enter your  <a href='http://www.www.linkedin.com.com/'>LinkedIn</a> URL e.g. http://www.linkedin.com/in/woothemes",
					"id" => $shortname."_connect_linkedin",
					"std" => '',
					"type" => "text" );

$options[] = array( "name" => "Delicious URL",
					"desc" => "Enter your <a href='http://www.delicious.com/'>Delicious</a> URL e.g. http://www.delicious.com/woothemes",
					"id" => $shortname."_connect_delicious",
					"std" => '',
					"type" => "text" );

$options[] = array( "name" => "Google+ URL",
					"desc" => "Enter your <a href='http://plus.google.com/'>Google+</a> URL e.g. https://plus.google.com/104560124403688998123/",
					"id" => $shortname."_connect_googleplus",
					"std" => '',
					"type" => "text" );

$options[] = array( "name" => "Enable Related Posts",
					"desc" => "Enable related posts in the subscribe area. Uses posts with the same <strong>tags</strong> to find related posts. Note: Will not show in the Subscribe widget.",
					"id" => $shortname."_connect_related",
					"std" => 'true',
					"type" => "checkbox" );
												
//Advertising
$options[] = array( "name" => "Ads - Top Ad (468x60px)",
					"icon" => "ads",
					"id" => $shortname."_ads_top_ad_heading",
                    "type" => "heading");

$options[] = array( "name" => "Enable Ad",
					"desc" => "Enable the ad space",
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

// Dynamic Search
$options[] = array( "name" => "Search Fields",
					"icon" => "searchoption",
					"id" => $shortname."_search_fields_heading",
                    "type" => "heading");

$number_of_search_fields = 3;
update_option('woo_number_of_search_fields', $number_of_search_fields);

//Unset array items that we dont want	
if ( $woo_wp_custom_taxonomies_formatted['nav_menu'] != '' ) { unset($woo_wp_custom_taxonomies_formatted['nav_menu']);	}
if ( $woo_wp_custom_taxonomies_formatted['link_category'] != '' ) { unset($woo_wp_custom_taxonomies_formatted['link_category']);	}

//Unset array items that we dont want	
if ( $woo_wp_custom_post_types_formatted['nav_menu_item'] != '' ) { unset($woo_wp_custom_post_types_formatted['nav_menu_item']);	}
if ( $woo_wp_custom_post_types_formatted['attachment'] != '' ) { unset($woo_wp_custom_post_types_formatted['attachment']);	}
if ( $woo_wp_custom_post_types_formatted['revision'] != '' ) { unset($woo_wp_custom_post_types_formatted['revision']);	}
if ( $woo_wp_custom_post_types_formatted['wooframework'] != '' ) { unset($woo_wp_custom_post_types_formatted['wooframework']);	}
	
// SEARCH FIELDS LOOP	
for ( $counter = 1; $counter <= $number_of_search_fields; $counter += 1) {

	$options[] = array( "name" => "<strong>Field ".$counter." Search Settings</strong>",
					"type" => "info",
					"std" => "<small>The below settings only apply to Field ".$counter." of the search. You can switch off this field by setting the input type to 'None'. </small>");
					
	$options[] = array( "name" => "Input Type",
					"desc" => "This is the type of input field that will be shown to the user.",
					"id" => $shortname."_search_input_content_type_".$counter,
					"std" => "none",
					"options" => array(	'none' => 'None',
										'text' => 'Text box',
										'autocomplete' => 'Autocomplete Text box',
										'select2' => 'Drop down box'/*,
										'checkbox' => 'Check box',
										'rangetext' => 'Range Text box',
										'rangedropdown' => 'Range Drop down box',
										'calendar' => 'Datepicker Calendar',
										'time' => 'Time Input'*/),
					"type" => "select2");
	
	$options[] = array( "name" => "Content Label",
					"desc" => "Enter the label description for the content field on the frontend - if this is blank the name of the Content Type will be displayed.",
					"id" => $shortname."_search_content_type_label_".$counter,
					"std" => "",
					"type" => "text");   
					
	$options[] = array( "name" => "Content Type",
						"desc" => "Choose the type of content for this search field.",
						"id" => $shortname."_search_content_type_".$counter,
						"std" => "ctx",
						"type" => "select2",
						"options" => array( 'ctx' => 'Taxonomy', 'cmb' => 'Custom Field'/*, 'cpt' => 'Post Type'*/ ));   
	
	$options[] = array( "name" => "Taxonomy",
						"desc" => "Select a taxonomy for the search field",
						"id" => $shortname."_search_content_type_ctx_".$counter,
						"std" => "none",
						"type" => "select2",
						"options" => $woo_wp_custom_taxonomies_formatted);  
	
	$options[] = array( "name" => "Custom Field",
						"desc" => "Select a custom field for the search field",
						"id" => $shortname."_search_content_type_cmb_".$counter,
						"std" => "none",
						"type" => "select2",
						"options" => $woo_wp_custom_fields_formatted);  
	
	$options[] = array( "name" => "Post Type",
						"desc" => "Select a post type for the search field",
						"id" => $shortname."_search_content_type_cpt_".$counter,
						"std" => "none",
						"type" => "select2",
						"options" => $woo_wp_custom_post_types_formatted);  
	
	$options[] = array( "name" => "Search by Features Matching Method",
						"desc" => "Choose the matching method for Search Results. <br /><strong>Exact Match</strong> means only results with the same number of this item searched for will be returned while <strong>Minimum Value</strong> means that all results with at least the amount searched for will be returned. <strong>NOTE: Only use minimum value on numeric fields otherwise the desired result will not be achieved.</strong>",
						"id" => $shortname."_search_content_type_matching_method_".$counter,
						"std" => "exact",
						"type" => "radio",
						"class" => "hidden",
						"options" => array("exact" => "Exact Match","minimum" => "Minimum Value")); 
	
	//Boolean Logic
	$options[] = array( "name" => "Chaining Logic",
						"desc" => "This alters the search logic. AND means results must have this field match as well, OR means results will contain matches for both fields.",
						"id" => $shortname."_search_content_type_boolean_logic_".$counter,
						"std" => "and",
						"type" => "select2",
						"class" => "hidden",
						"options" => array('and' => 'AND', 'or' => 'OR'));	
																									
}

$options[] = array( "name" => "Search Options",
					"icon" => "searchoption",
					"id" => $shortname."_search_options_heading",
                    "type" => "heading");

$options[] = array( "name" => "Homepage Display Search Panel",
					"desc" => "Sets the Search Panel state to open by default on the homepage. This will override the below global option.",
					"id" => $shortname."_search_panel_state",
					"std" => "true",
					"type" => "checkbox"); 

$options[] = array( "name" => "Global Display Search Panel",
					"desc" => "Sets the Search Panel state to open by default across the site.",
					"id" => $shortname."_search_panel_state_global",
					"std" => "false",
					"type" => "checkbox"); 
										 
//set post types array to just posts and listings
unset($woo_wp_custom_post_types_formatted['none']);
unset($woo_wp_custom_post_types_formatted['page']);
 					                    
$options[] = array( "name" => "Search Post Types",
					"desc" => "Select which post types you would like the search to query.",
					"id" => $shortname."_search_post_types",
					"std" => 'listing',
					"type" => "multicheck",
					"options" => $woo_wp_custom_post_types_formatted); 

$options[] = array( "name" => "Search Results",
                    "desc" => "Select the number of entries that should appear on the search results page.",
                    "id" => $shortname."_listings_search_results",
                    "std" => "3",
                    "type" => "select",
                    "options" => $other_entries);

$options[] = array( "name" => "Search by Features Matching Method",
					"desc" => "Choose the matching method for Custom Field Search Results. <br /><strong>Exact Match</strong> means only results with the same number of this item searched for will be returned while <strong>Minimum Value</strong> means that all results with at least the amount searched for will be returned. <strong>NOTE: Only use minimum value will only be applied on numeric fields otherwise the desired result will not be achieved.</strong>",
					"id" => $shortname."_search_content_type_matching_method",
					"std" => "exact",
					"type" => "radio",
					"class" => "",
					"options" => array("exact" => "Exact Match","minimum" => "Minimum Value")); 
			
// General Labels
$options[] = array( "name" => "Labels : General",
					"icon" => "listing	",
					"id" => $shortname."_labels_general_heading",
					"type" => "heading");

$options[] = array( "name" => "Listings Prefix Symbol",
                    "desc" => "Specify the prefix that will be attached to a listing to give it a unique number that can be used in the search.",
                    "id" => $shortname."_listings_prefix",
                    "std" => "LIST",
                    "type" => "text");
					
$options[] = array( "name" => "Currency Symbol",
                    "desc" => "Specify the currency that your listings price will be shown in.",
                    "id" => $shortname."_listings_currency",
                    "std" => "$",
                    "type" => "text");
					
$options[] = array( "name" => "Search Panel Title",
                    "desc" => "Include a short title for your search panel on the home page, e.g. Advanced Search Module.",
                    "id" => $shortname."_search_panel_header",
                    "std" => "Advanced Search Module",
                    "type" => "text");
					
$options[] = array( "name" => "Search Listings Keyword Input Text",
                    "desc" => "Specify the default text in the search keyword input box.",
                    "id" => $shortname."_search_panel_keyword_text",
                    "std" => "Enter search keywords",
                    "type" => "text");

$options[] = array( "name" => "Search Listings Button Text",
                    "desc" => "Specify the default text for the search listings button.",
                    "id" => $shortname."_search_panel_listings_button_text",
                    "std" => "Search Site",
                    "type" => "text");
                    
$options[] = array( "name" => "Search WebRef Input Text",
                    "desc" => "Specify the default text in the search keyword input box.",
                    "id" => $shortname."_search_panel_webref_text",
                    "std" => "Listings ID",
                    "type" => "text");
                    
$options[] = array( "name" => "Search WebRef Button Text",
                    "desc" => "Specify the default text for the search by webref button.",
                    "id" => $shortname."_search_panel_webref_button_text",
                    "std" => "Go To",
                    "type" => "text");

$options[] = array( "name" => "Search Results Title",
                    "desc" => "Specify the default text for the search results title.",
                    "id" => $shortname."_search_results_header",
                    "std" => "Search results:",
                    "type" => "text");
                    
$options[] = array( "name" => "Archive Text : Listings",
                    "desc" => "Specify the default text for the listings archive page headers.",
                    "id" => $shortname."_archive_listings_header",
                    "std" => "Listings Archive",
                    "type" => "text");

$options[] = array( "name" => "Archive Text : General",
                    "desc" => "Specify the default text for the general archive page headers.",
                    "id" => $shortname."_archive_general_header",
                    "std" => "Archive",
                    "type" => "text");
                    
$options[] = array( "name" => "'Latest Listings' header text",
                    "desc" => "Specify the default text for the header area on the 'latest listings' page.",
                    "id" => $shortname."_listings_more_header",
                    "std" => "Latest listings",
                    "type" => "text");

$options[] = array( "name" => "'View more latest listings' link text",
                    "desc" => "Specify the default text for the 'view more latest listings' link in the footer.",
                    "id" => $shortname."_listings_viewmore_label",
                    "std" => "View more latest listings",
                    "type" => "text");
                                                            					                                                            
// Featured Panel
$options[] = array( "name" => "Featured Panel",
					"icon" => "slider",
					"id" => $shortname."_featured_panel_heading",
					"type" => "heading");
					
$options[] = array( "name" => "Enable Featured Panel",
					"desc" => "Show the featured panel on the front page.",
					"id" => $shortname."_featured",
					"std" => "false",
					"type" => "checkbox");  

$options[] = array( "name" => "Featured Panel Title",
                    "desc" => "Include a short title for your featured panel on the home page, e.g. Featured Listings.",
                    "id" => $shortname."_featured_header",
                    "std" => "Featured Listings",
                    "type" => "text");
                    
$options[] = array( "name" => "Featured Tag",
                    "desc" => "Add comma separated list for the tags that you would like to have displayed in the featured section on your homepage. For example, if you add 'tag1, tag3' here, then all properties tagged with either 'tag1' or 'tag3' will be shown in the featured area.",
                    "id" => $shortname."_featured_tags",
                    "std" => "",
                    "type" => "text");

$options[] = array( "name" => "Featured Entries",
                    "desc" => "Select the number of listing entries that should appear in the Featured panel.",
                    "id" => $shortname."_featured_entries",
                    "std" => "3",
                    "type" => "select",
                    "options" => $other_entries);
                    
$options[] = array( "name" => "Slider Image Position",
                    "desc" => "Select the alignment for the featured slider image",
                    "id" => $shortname."_slider_image",
                    "std" => "Left",
                    "type" => "select",
                    "options" => $slider_image);   

$options[] = array( "name" => "Custom Field for Image Caption",
					"desc" => "Select a custom field for the image caption",
					"id" => $shortname."_slider_image_caption",
					"std" => "price",
					"type" => "select2",
					"options" => $woo_wp_custom_fields_formatted);  
			
$options[] = array( "name" => "Effect",
					"desc" => "Select the animation effect. ",
					"id" => $shortname."_slider_effect",
					"type" => "select2",
					"std" => "fade",
					"options" => array("slide" => "Slide", "fade" => "Fade") );     

$options[] = array( "name" => "Hover Pause",
                    "desc" => "Hovering over slideshow will pause it",
                    "id" => $shortname."_slider_hover",
                    "std" => "false",
                    "type" => "checkbox"); 
                    
$options[] = array( "name" => "Fade Speed",
                    "desc" => "The time in <b>seconds</b> the fade between frames will take.",
                    "id" => $shortname."_fade_speed",
                    "std" => "0.6",
					"type" => "select",
					"options" => array( '0.0', '0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7', '0.8', '0.9', '1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '2.0' ) );

$options[] = array( "name" => "Auto Start",
                    "desc" => "Set the slider to start sliding automatically. Adjust the speed of sliding underneath.",
                    "id" => $shortname."_slider_auto",
                    "std" => "false",
                    "type" => "checkbox"); 

$options[] = array( "name" => "Animation Speed",
                    "desc" => "The time in <b>seconds</b> the animation between frames will take e.g. 0.6",
                    "id" => $shortname."_slider_speed",
                    "std" => "0.6",
					"type" => "select",
					"options" => array( '0.0', '0.1', '0.2', '0.3', '0.4', '0.5', '0.6', '0.7', '0.8', '0.9', '1.0', '1.1', '1.2', '1.3', '1.4', '1.5', '1.6', '1.7', '1.8', '1.9', '2.0' ) );
                    
$options[] = array( "name" => "Auto Slide Interval",
                    "desc" => "The time in <b>seconds</b> each slide pauses for, before sliding to the next. Only when using Auto Start option above.",
                    "id" => $shortname."_slider_interval",
                    "std" => "6",
					"type" => "select",
					"options" => array( '1', '2', '3', '4', '5', '6', '7', '8', '9', '10' ) );


// Listing Categories Panel
$options[] = array( "name" => "Listing Categories Panel",
					"icon" => "listing",
					"id" => $shortname."_listings_categories_panel_heading",
					"type" => "heading");

//set post types array to just posts and listings
$temp_cpt_list = $woo_wp_custom_post_types_formatted;
unset($temp_cpt_list['none']);
unset($temp_cpt_list['page']);
if ( ( isset( $temp_cpt_list['nav_menu_item'] ) ) && ( $temp_cpt_list['nav_menu_item'] != '' ) ) { unset($temp_cpt_list['nav_menu_item']);	}
if ( ( isset( $temp_cpt_list['attachment'] ) ) && ( $temp_cpt_list['attachment'] != '' ) ) { unset($temp_cpt_list['attachment']);	}
if ( ( isset( $temp_cpt_list['revision'] ) ) && ( $temp_cpt_list['revision'] != '' ) ) { unset($temp_cpt_list['revision']);	}
if ( ( isset( $temp_cpt_list['wooframework'] ) ) && ( $temp_cpt_list['wooframework'] != '' ) ) { unset($temp_cpt_list['wooframework']);	}
 					                    
$options[] = array( "name" => "Display List for these Post Types",
					"desc" => "Select which post types you would like to generate a list of taxonomies for.",
					"id" => $shortname."_categories_panel_post_types",
					"std" => 'listing',
					"type" => "multicheck",
					"options" => $temp_cpt_list); 

//set taxonomies array default
$temp_ctx_list = $woo_wp_custom_taxonomies_formatted;
unset($temp_ctx_list['none']);
				
$options[] = array( "name" => "Taxonomies to Output",
					"desc" => "Select which taxonomy terms you would like to display in the Listings Category Panel for each of the above Post Types.",
					"id" => $shortname."_categories_panel_taxonomies",
					"std" => 'location',
					"type" => "multicheck",
					"options" => $temp_ctx_list); 

$options[] = array( "name" => "Number of Taxonomy Entries",
                    "desc" => "Select the number of taxonomy entries that should appear in each Listing Categories panel.",
                    "id" => $shortname."_categories_panel_entries",
                    "std" => "6",
                    "type" => "select",
                    "options" => $other_entries);

// More Listings Panel
$options[] = array( "name" => "More Listings Panel",
					"icon" => "listing",
					"id" => $shortname."_more_listings_panel_heading",
					"type" => "heading");

$options[] = array( "name" => "Enable More Listings Panel",
					"desc" => "Enable the More Listings panel below the Listing Categories Panel.",
					"id" => $shortname."_more_listings_area",
					"std" => "false",
					"type" => "checkbox"); 
					
$options[] = array( "name" => "Display List for these Post Types",
					"desc" => "Select which post types you would like to display More Listings for.",
					"id" => $shortname."_more_listings_area_post_types",
					"std" => 'listing',
					"type" => "multicheck",
					"options" => $temp_cpt_list); 

$options[] = array( "name" => "Number of Entries",
                    "desc" => "Select the number of entries that should appear in the More Listings panel.",
                    "id" => $shortname."_more_listings_area_entries",
                    "std" => "3",
                    "type" => "select",
                    "options" => $other_entries);					

			                    
$options[] = array( "name" => "Popular Keywords",
					"icon" => "header",
					"id" => $shortname."_popular_keywords_heading",
                    "type" => "heading");
                    
$options[] = array( "name" => "Enable Popular Keywords",
					"desc" => "Enable the popular keywords section in the search module.",
					"id" => $shortname."_keywords",
					"std" => "false",
					"type" => "checkbox");  

$options[] = array( "name" => "Popular Keywords Panel Title",
                    "desc" => "Include a short title for your popular keywords panel on the home page, e.g. Popular Keywords.",
                    "id" => $shortname."_popular_keywords_header",
                    "std" => "Popular Keywords",
                    "type" => "text");
                    	
//set taxonomies array default
unset($woo_wp_custom_taxonomies_formatted['none']);
				
$options[] = array( "name" => "Popular Keywords Taxonomies",
					"desc" => "Select which taxonomy terms you would like to display in the popular keywords panel.",
					"id" => $shortname."_search_post_types",
					"std" => 'location',
					"type" => "multicheck",
					"options" => $woo_wp_custom_taxonomies_formatted); 

$options[] = array( "name" => "Number of Popular Keywords per Taxonomy",
                    "desc" => "The number of popular keywords to include from each taxonomy.",
                    "id" => $shortname."_popular_keywords_limit",
                    "std" => "20",
                    "type" => "text");

// Single Listing Setting
$options[] = array( "name" => "Single Listing Details",
					"icon" => "main",
					"id" => $shortname."_single_listings_details_heading",
					"type" => "heading");
				
$options[] = array( "name" => "Features Taxonomy",
					"desc" => "Select a taxonomy for the listing features area",
					"id" => $shortname."_single_listing_feature_taxonomy",
					"std" => "listingfeatures",
					"type" => "select2",
					"options" => $woo_wp_custom_taxonomies_formatted);  

$options[] = array( "name" => "Similar Listings Taxonomy",
					"desc" => "Select a taxonomy to match Similar Listings by",
					"id" => $shortname."_single_listing_related_taxonomy",
					"std" => "location",
					"type" => "select2",
					"options" => $woo_wp_custom_taxonomies_formatted);  

$options[] = array( "name" => "Custom Field for Image Caption",
					"desc" => "Select a custom field for the image caption",
					"id" => $shortname."_single_listing_image_caption",
					"std" => "price",
					"type" => "select2",
					"options" => $woo_wp_custom_fields_formatted); 

$options[] = array( "name" => "Starting at Text",
                    "desc" => "Specify the default text for the Starting at area.",
                    "id" => $shortname."_single_listing_starting_at_text",
                    "std" => "Starting at ",
                    "type" => "text");

$options[] = array( "name" => "Image Gallery Title Text",
                    "desc" => "Specify the default text for the Image Gallery title.",
                    "id" => $shortname."_single_listing_image_gallery_title",
                    "std" => "Image Gallery",
                    "type" => "text");

$options[] = array( "name" => "Google Map Title Text",
                    "desc" => "Specify the default text for the Google Map title.",
                    "id" => $shortname."_single_listing_google_map_title",
                    "std" => "Location",
                    "type" => "text");

$options[] = array( "name" => "Similar Listings Title Text",
                    "desc" => "Specify the default text for the Similar Listings title.",
                    "id" => $shortname."_single_listing_similar_listings_title",
                    "std" => "Similar Listings",
                    "type" => "text");
                                        					
// Upload Listing Page Template
$options[] = array( "name" => "Upload Listing",
					"icon" => "upload",
					"id" => $shortname."_upload_listing_heading",
					"type" => "heading");

$options[] = array( "name" => "Only Register Users may post a Listing",
					"desc" => "This forces users to register before being able to add a listing.",
					"id" => $shortname."_upload_user_logged_in",
					"std" => "false",
					"type" => "checkbox"); 
					
$options[] = array( "name" => "Upload Post Types",
					"desc" => "Select which post types you would users to be able to upload.",
					"id" => $shortname."_upload_post_types",
					"std" => 'listing',
					"type" => "multicheck",
					"options" => $woo_wp_custom_post_types_formatted);

// Google Maps Settings
$options[] = array( "name" => "Maps",
					"icon" => "maps",
					"id" => $shortname."_maps_heading",
				    "type" => "heading");    

$options[] = array( "name" => "Google Maps API Key",
					"desc" => "Enter your Google Maps API key before using any of Postcard's mapping functionality. <a href='http://code.google.com/apis/maps/signup.html'>Signup for an API key here</a>.",
					"id" => $shortname."_maps_apikey",
					"std" => "",
					"class" => "hidden",
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
					"std" => "10",
					"type" => "select2",
					"options" => $zoom);

$options[] = array( "name" => "Default Map Type",
					"desc" => "Set this to the default rendered in the post backend.",
					"id" => $shortname."_maps_default_maptype",
					"std" => "Normal",
					"type" => "select2",
					"options" => array('G_NORMAL_MAP' => 'Normal','G_SATELLITE_MAP' => 'Satellite','G_HYBRID_MAP' => 'Hybrid','G_PHYSICAL_MAP' => 'Terrain'));

$options[] = array( "name" => "Archive Maps",
					"icon" => "maps",
					"id" => $shortname."_archive_maps_heading",
				    "type" => "heading"); 
				    
$options[] = array( "name" => "Enable Archive Maps",
					"desc" => "Enable a map with markers relative the listings on the current archive page.",
					"id" => $shortname."_show_archive_map",
					"std" => "false",
					"type" => "checkbox"); 				    
				    			    
$options[] = array( "name" => "Overview map type",
					"desc" => "Select the type of map you would like to produce on archive pages.",
					"id" => $shortname."_archive_type",
					"std" => "G_NORMAL_MAP",
					"options" => array('G_NORMAL_MAP' => 'Normal','G_SATELLITE_MAP' => 'Satellite','G_HYBRID_MAP' => 'Hybrid','G_PHYSICAL_MAP' => 'Physical'), 
					"type" => "select2"); 
					
$options[] = array( "name" => "Overview map zoom",
					"desc" => "Select the type of map you would like to produce on archive pages.",
					"id" => $shortname."_archive_zoom",
					"std" => "10",
					"options" => $zoom, 
					"type" => "select"); 

$options[] = array( "name" => "Disable Mousescroll",
					"desc" => "Turn off the mouse scroll action for all the Google Maps on the archive pages. This could improve usability on your site.",
					"id" => $shortname."_maps_archive_scroll",
					"std" => "",
					"type" => "checkbox");
										
$options[] = array( "name" => "Object Dimensions",
					"desc" => "Enter the size of the map or image featured on the archive featured area.",
					"id" => $shortname."_image_dimensions",
					"std" => "",
					"type" => array( 
									array(  'id' => $shortname. '_archive_featured_h',
											'type' => 'text',
											'std' => 250,
											'meta' => 'Height')
								  )); 					
$options[] = array( "name" => "Number of Pins (Posts)",
					"desc" => "Adjust the amount of pins (posts) you want to show on the archive overview map. <strong>-1</strong> is show all.",
					"id" => $shortname."_cat_show_pins",
					"std" => "20",
					"type" => "text"); 

$options = category_nav($options,$colors);
// Filter
$options = apply_filters( 'woothemes_theme_options', $options );																				
if ( get_option('woo_template') != $options) update_option('woo_template',$options);      
if ( get_option('woo_themename') != $themename) update_option('woo_themename',$themename);   
if ( get_option('woo_shortname') != $shortname) update_option('woo_shortname',$shortname);
if ( get_option('woo_manual') != $manualurl) update_option('woo_manual',$manualurl);


                                     
// Woo Metabox Options
// Start name with underscore to hide custom key from the user
$woo_metaboxes = array();

if( get_post_type() == 'post' ){
			
	$woo_metaboxes[] = array (	"name" => "image",
								"label" => "Image",
								"type" => "upload",
								"desc" => "Upload an image or enter an URL.");
	
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

} // End post

// Old post custom fields go here - controlled by content builder now

//Only show SEO on these registered post types  

//Only show SEO on these registered post types


  

}
}



?>