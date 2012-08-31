<?php 

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

1. Add specific IE styling/hacks to HEAD
2. Add custom styling to HEAD
3. Add custom typograhpy to HEAD

-----------------------------------------------------------------------------------*/


add_action('wp_head','woo_IE_head');					// Add specific IE styling/hacks to HEAD
add_action('woo_head','woo_custom_styling');			// Add custom styling to HEAD
add_action('woo_head','woo_custom_typography');			// Add custom typography to HEAD


/*-----------------------------------------------------------------------------------*/
/* 1. Add specific IE styling/hacks to HEAD */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_IE_head')) {
	function woo_IE_head() {
	?>
	
<!--[if IE 6]>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/includes/js/pngfix.js"></script>
<script type="text/javascript" src="<?php bloginfo('template_directory'); ?>/includes/js/menu.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/css/ie6.css" />
<![endif]-->	

<!--[if IE 7]>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/css/ie7.css" />
<![endif]-->

<!--[if IE 8]>
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo('template_directory'); ?>/css/ie8.css" />
<![endif]-->
	<?php
	}
}


/*-----------------------------------------------------------------------------------*/
/* 2. Add Custom Styling to HEAD */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_custom_styling')) {
	function woo_custom_styling() {
	
		global $woo_options;
		
		$output = '';
		// Get options
		$body_color = $woo_options['woo_body_color'];
		$body_img = $woo_options['woo_body_img'];
		$body_repeat = $woo_options['woo_body_repeat'];
		$body_position = $woo_options['woo_body_pos'];
		$link = $woo_options['woo_link_color'];
		$hover = $woo_options['woo_link_hover_color'];
		$button = $woo_options['woo_button_color'];
			
		// Add CSS to output
		if ($body_color)
			$output .= 'body {background:'.$body_color.'}' . "\n";
			
		if ($body_img)
			$output .= 'body {background-image:url('.$body_img.')}' . "\n";

		if ($body_img && $body_repeat && $body_position)
			$output .= 'body {background-repeat:'.$body_repeat.'}' . "\n";

		if ($body_img && $body_position)
			$output .= 'body {background-position:'.$body_position.'}' . "\n";

		if ($link)
			$output .= 'a:link, a:visited {color:'.$link.'}' . "\n";

		if ($hover)
			$output .= 'a:hover {color:'.$hover.'}' . "\n";

		if ($button) {
			$output .= 'a.button, a.comment-reply-link, #commentform #submit {background:'.$button.';border-color:'.$button.'}' . "\n";
			$output .= 'a.button:hover, a.button.hover, a.button.active, a.comment-reply-link:hover, #commentform #submit:hover {background:'.$button.';opacity:0.9;}' . "\n";
		}
		
		// Output styles
		if (isset($output)) {
			$output = strip_tags($output);
			$output = "<!-- Woo Custom Styling -->\n<style type=\"text/css\">\n" . $output . "</style>\n";
			echo $output;
		}
			
	}
} 

/*-----------------------------------------------------------------------------------*/
/* 3. Add custom typograhpy to HEAD */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_custom_typography')) {
	function woo_custom_typography() {
	
		// Get options
		global $woo_options;
				
		// Reset	
		$output = '';
		
		// Add Text title and tagline if text title option is enabled
		if ( $woo_options['woo_texttitle'] == "true" ) {		
			
			if ( $woo_options['woo_font_site_title'] )
				$output .= '#logo .site-title a {'.woo_generate_font_css($woo_options['woo_font_site_title'], '1.2').'}' . "\n";	
			if ( $woo_options['woo_font_tagline'] )
				$output .= '#logo .site-description {'.woo_generate_font_css($woo_options['woo_font_tagline']).'}' . "\n";	
		}

		if ( $woo_options[ 'woo_typography' ] == "true") {
			
			if ( $woo_options[ 'woo_font_body' ] )
				$output .= 'body, p { '.woo_generate_font_css($woo_options[ 'woo_font_body' ], '1.5').' }' . "\n";	

			if ( $woo_options[ 'woo_font_nav' ] )
				$output .= '#navigation, #navigation .nav a { '.woo_generate_font_css($woo_options[ 'woo_font_nav' ], '1.4').' }' . "\n";	

			if ( $woo_options[ 'woo_font_post_title' ] )
				$output .= '.post .title { '.woo_generate_font_css($woo_options[ 'woo_font_post_title' ]).' }' . "\n";	
		
			if ( $woo_options[ 'woo_font_post_meta' ] )
				$output .= '.post-meta { '.woo_generate_font_css($woo_options[ 'woo_font_post_meta' ]).' }' . "\n";	

			if ( $woo_options[ 'woo_font_post_entry' ] )
				$output .= '.entry, .entry p { '.woo_generate_font_css($woo_options[ 'woo_font_post_entry' ], '1.5').' } h1, h2, h3, h4, h5, h6 { font-family: \''.stripslashes($woo_options[ 'woo_font_post_entry' ]['face']).'\', arial, sans-serif; }'  . "\n";	

			if ( $woo_options[ 'woo_font_widget_titles' ] )
				$output .= '.widget h3 { '.woo_generate_font_css($woo_options[ 'woo_font_widget_titles' ]).' }'  . "\n";	
		} else {
			
			// Set default font face
			$woo_options['woo_default_face'] = array( 'face' => 'Rosario', 'variant' => '' );

			// Output default font face
			if ( !empty( $woo_options['woo_default_face'] ) )
				$output .= 'h1, h2, h3, h4, h5, h6, .widget h3, .post .title, .archive_header { '.woo_generate_font_css($woo_options['woo_default_face']).' }' . "\n";
		
		}
		
		// Output styles
		if (isset($output) && $output != '') {

			// Enable Google Fonts stylesheet in HEAD
			if (function_exists( 'woo_google_webfonts')) woo_google_webfonts();

			$output = "<!-- Woo Custom Typography -->\n<style type=\"text/css\">\n" . $output . "</style>\n\n";
			echo $output;

		}
			
	}
} 

// Returns proper font css output
if (!function_exists( 'woo_generate_font_css')) {
	function woo_generate_font_css($option, $em = '1') {

		// Test if font-face is a Google font
		global $google_fonts;
		foreach ( $google_fonts as $google_font ) {
					
			// Add single quotation marks to font name and default arial sans-serif ending
			if ( $option[ 'face' ] == $google_font[ 'name' ] )
				$option[ 'face' ] = "'" . $option[ 'face' ] . "', arial, sans-serif";		
		
		} // END foreach
		
		if ( !@$option["style"] && !@$option["size"] && !@$option["unit"] && !@$option["color"] )
			return 'font-family: '.stripslashes($option["face"]).';'; 
		else
			return 'font:'.$option["style"].' '.$option["size"].$option["unit"].'/'.$em.'em '.stripslashes($option["face"]).';color:'.$option["color"].';';
	}
}

// Output stylesheet and custom.css after custom styling
remove_action('wp_head', 'woothemes_wp_head');
add_action('woo_head', 'woothemes_wp_head');


/*-----------------------------------------------------------------------------------*/
/* Featured Slider Settings */
/*-----------------------------------------------------------------------------------*/

add_filter('woo_head', 'woo_slider_options');
function woo_slider_options() { 
	
	global $woo_options;
	
	if ($woo_options[ 'woo_featured' ] == 'true' && is_home() && !is_paged() && !is_single()) { ?>
	
		<script type="text/javascript">
			jQuery(window).load(function(){
						
				jQuery('#loopedSlider').slides({
					preload: true,
					preloadImage: '<?php echo get_template_directory_uri(); ?>/images/loading.png',
					autoHeight: true,
					effect: '<?php echo $woo_options[ 'woo_slider_effect' ]; ?>',
					container: 'slides',
					<?php if ($woo_options[ 'woo_slider_hover' ] == "true"): ?>			
					hoverPause: true,
					<?php endif; ?>
					<?php if ($woo_options[ 'woo_slider_auto' ] == "true"): ?>
					play: <?php echo $woo_options[ 'woo_slider_interval' ] * 1000; ?>,
					<?php endif; ?>			
					slideSpeed: <?php echo $woo_options[ 'woo_slider_speed' ] * 1000; ?>,
					fadeSpeed: <?php echo $woo_options[ 'woo_fade_speed' ] * 1000; ?>,
					
					generateNextPrev: false,
					next: 'next',
					prev: 'previous',
					crossfade: true,
					generatePagination: false
					
				});
								
			});
		</script><?php 
	}
}

/*-----------------------------------------------------------------------------------*/
/* Single Listings Gallery Settings */
/*-----------------------------------------------------------------------------------*/

add_filter('woo_head', 'woo_listings_options');
function woo_listings_options() { 
	
	global $woo_options;  
	if (is_single()) { ?>
	
		<script type="text/javascript">
			jQuery(window).load(function(){
			
				jQuery("#loopedSlider").slides({
					
					autoHeight: true,
					effect: '<?php echo $woo_options[ 'woo_slider_effect' ]; ?>',
					container: 'slides',
					<?php if ($woo_options[ 'woo_slider_hover' ] == "true"): ?>			
					hoverPause: true,
					<?php endif; ?>
					<?php if ($woo_options[ 'woo_slider_auto' ] == "true"): ?>
					play: <?php echo $woo_options[ 'woo_slider_interval' ] * 1000; ?>,
					<?php endif; ?>			
					slideSpeed: <?php echo $woo_options[ 'woo_slider_speed' ] * 1000; ?>,
					fadeSpeed: <?php echo $woo_options[ 'woo_fade_speed' ] * 1000; ?>,
					
					crossfade: true,
					generatePagination: false, 
					paginationClass: 'pagination'
				});
			
			});
			
			jQuery(document).ready(function(){
			
				var show_thumbs = 3;
				
    			jQuery('#loopedSlider.gallery ul.pagination').jcarousel({
    				visible: show_thumbs,
    				wrap: 'both'
    			});
    		
			});

		</script><?php 
	}	
}


/*-----------------------------------------------------------------------------------*/
/* END */
/*-----------------------------------------------------------------------------------*/
?>