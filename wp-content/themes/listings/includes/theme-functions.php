<?php 

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Function to run on activation of this theme
-- Create a page for the "Custom psot types landing page", if none exists.
-- Create a custom role which enables users to upload images with their listings.
- Listings-specific Media Library-related Functions
- Custom CSS for the Login Screen
- Page / Post navigation
- WooTabs - Popular Posts
- WooTabs - Latest Posts
- WooTabs - Latest Comments
- Post Meta
- Misc
- WordPress 3.0 New Features Support
- Dynamic Search Functions
- Custom Array Functions
- Listings Install Content Function
- Listings Write Panel Custom Columns
- Listings Custom Post Type Filters
- Category to Color matrix
- WooThemes Google Maps Functionality
- Thickbox Styles
- Latest Listings Dashboard Widget
- Latest Posts Custom Query
- Subscribe & Connect
- Function to display a custom field drop down field value

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Function to run on activation of this theme */
/*-----------------------------------------------------------------------------------*/

// Run the code.

global $pagenow, $current_user;

get_currentuserinfo();

if ( is_admin() && 'admin.php' == $pagenow && isset( $_GET['page'] ) && $_GET['page'] == 'woothemes' ) {
	
	/*-----------------------------------------------------------------------------------*/
	/* Create a page for the "Custom psot types landing page", if none exists. */
	/*-----------------------------------------------------------------------------------*/
	
	$page_id = 0;

	$page_title = 'Custom post types landing page'; // The title of our page to handle the display of custom post type archives.

	$page_data = array( 'post_title' => $page_title, 'post_status' => 'publish', 'post_type' => 'page', 'post_content' => '', 'post_author' => $current_user->ID );
					
	// Check if a page already exists that we can use.
	$existing_page = get_page_by_title( $page_title );
	
	if ( $existing_page ) {
	
		$page_id = $existing_page->ID;
	
	} else {
		
		$page_id = wp_insert_post( $page_data );
		
	} // End IF Statement
	
	if ( $page_id > 0 ) {
	
		update_post_meta( $page_id, '_wp_page_template', 'template-custom-post-types-index.php' );
	
		update_option( 'woo_listings_cpt_page', $page_id );
	
	} // End IF Statement
	
	/*-----------------------------------------------------------------------------------*/
	/* Create a custom role which enables users to upload images with their listings. */
	/* This is for use with the frontend "Upload a listing" template. */
	/*-----------------------------------------------------------------------------------*/
	
	if ( function_exists( 'get_role' ) && function_exists( 'add_role' ) ) {
	
		$role_token = 'listings_contributor';
		$role_label = 'Listings Contributor';
		$role_caps = array(
							'read' => true, 
							'edit_posts' => true, 
							'edit_published_posts' => true, 
							'upload_files' => true
						  );
	
		$contributor_caps = get_role( 'contributor' );
		
		$role_caps = $contributor_caps->capabilities;
		
		$role_caps['edit_published_posts'] = true;
	
		// Check if the role exists.
		$role = null;
		$role = get_role( $role_token );
		
		// If it doesn't exist, create it
		// and assign the necessary capabilities.
		if ( $role == null ) {} else {
			remove_role( $role_token );
		} // End IF Statement
		
		add_role( $role_token, $role_label, $role_caps );
	
	} // End IF Statement
	
} // End IF Statement

/*-----------------------------------------------------------------------------------*/
/* Listings-specific Media Library-related Functions */
/*-----------------------------------------------------------------------------------*/

add_action( 'admin_head', 'woo_listings_inside_popup' );


if ( ! function_exists( 'woo_listings_inside_popup' ) ) {

	function woo_listings_inside_popup () {
	
		if ( isset( $_GET['is_woothemes_frontend'] ) && $_GET['is_woothemes_frontend'] == 'yes' ) {
	
			add_filter( 'media_upload_tabs', 'woo_listings_mlu_tabs', 1, 2 );
		
		} // End IF Statement
	
	} // End woo_listings_inside_popup()

} // End IF Statement


if ( ! function_exists( 'woo_listings_mlu_tabs' ) ) {

	function woo_listings_mlu_tabs ( $tabs ) {
	
		unset( $tabs['library'] );
		unset( $tabs['gallery'] );
	
		return $tabs;
	
	} // End woo_listings_mlu_tabs()

} // End IF Statement

/*-----------------------------------------------------------------------------------*/
/* Custom CSS and JS for the Login Screen */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_load_login_css-js' ) ) {

	function woo_load_login_css_js () {
	
		$_html = '';
	
		if ( file_exists( TEMPLATEPATH . '/includes/css/login_css.php' ) ) {
		
			$is_register_screen = '';
		
			if ( isset( $_REQUEST['action'] ) && $_REQUEST['action'] == 'register' ) {
			
				$is_register_screen = '?is_woothemes_register=yes';
		
			} // End IF Statement
		
			$_html .= '<link rel="stylesheet" type="text/css" href="' . get_bloginfo('template_directory') . '/includes/css/login_css.php' . $is_register_screen . '" media="screen" />' . "\n";
		
		} // End IF Statement
		
		echo $_html;
	
	} // End woo_load_login_css_js()

	// add_action( 'login_head', 'woo_load_login_css_js' );

} // End IF Statement

if ( ! function_exists( 'woo_register_form_fields' ) ) {

	function woo_register_form_fields () {
	
		$_html = '';
		
		$_html .= '<input type="hidden" name="is_woothemes_register" value="yes" />' . "\n";
		
		echo $_html;
	
	} // End woo_register_form_fields()
	
	add_action( 'register_form', 'woo_register_form_fields' );

} // End IF Statement

/*-----------------------------------------------------------------------------------*/
/* Page / Post navigation */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_pagenav')) {
	function woo_pagenav() { 
	
		global $woo_options;
		
		// If the user has set the option to use simple paging links, display those. By default, display the pagination.
		if ( array_key_exists( 'woo_pagination_type', $woo_options ) && $woo_options[ 'woo_pagination_type' ] == 'simple' ) {
			if ( get_next_posts_link() || get_previous_posts_link() ) {
		?>
		
    	    <div class="nav-entries">
    		    <?php next_posts_link( '<div class="nav-prev fl">'. __( '&laquo; Newer Entries ', 'woothemes' ) . '</div>' ); ?>
    	        <?php previous_posts_link( '<div class="nav-next fr">'. __( ' Older Entries &raquo;', 'woothemes' ) . '</div>' ); ?>
    	        <div class="fix"></div>
    	    </div>
		
		<?php
			} 
		} else {
			woo_pagination();
		}
	} 
}               	

if (!function_exists('woo_postnav')) {
	function woo_postnav() { 
	
		global $woo_options;
		
		// If the user has set the option to use simple paging links, display those. By default, display the pagination.
		if ( array_key_exists( 'woo_pagination_type', $woo_options ) && $woo_options[ 'woo_pagination_type' ] == 'simple' ) {
			if ( get_next_posts_link() || get_previous_posts_link() ) {
		?>
		
    	    <div class="post-entries">
    		    <?php next_posts_link( '<div class="post-prev fl">'. '%link', '%title <span class="meta-nav">&raquo;</span>' . '</div>' ); ?>
    	        <?php previous_posts_link( '<div class="post-next fr">'. '%link', '<span class="meta-nav">&laquo;</span> %title' . '</div>' ); ?>
    	        <div class="fix"></div>
    	    </div>
		
		<?php
			} 
		} else {
			woo_pagination();
		}
	 }           	
}    
            	
if (!function_exists('woo_listingsnav')) {
	function woo_listingsnav() { 
	
		global $woo_options;
		
		$get_string = '';
		
		if ( isset($_GET) ) {
			
			// Build GET string
			foreach ( $_GET as $key => $value ) {
				
				if ( ! in_array( $key, array('paged') ) ) {
				
					$value = str_replace(' ', '+', $value);
					$get_string .= '&'.$key.'='.$value;
					
				} // End If Statement
				
			} // End For Loop
		
		} // End If Statement
		 
		woo_pagination(array('add_fragment' => $get_string));
		
	}	
}

add_filter('woo_pagination', 'woo_listings_pagination_filter');

/**
 * woo_listings_pagination_filter function.
 *
 * Since WordPress 3.4
 *
 * Filter on the pagination links to remove default search variable
 * Assists in the pagination fix in the search filter
 *
 * @access public
 * @param mixed $page_links
 * @return void
 */
function woo_listings_pagination_filter($page_links) {
	
	$page_links = str_replace('?s=&', '?', $page_links);
	$page_links = str_replace('#038;', '', $page_links);
	
	return $page_links;
	
} // End woo_listings_pagination_filter()

/*-----------------------------------------------------------------------------------*/
/* WooTabs - Popular Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_tabs_popular')) {
	function woo_tabs_popular( $posts = 5, $size = 35 ) {
		global $post;
		$popular = get_posts('orderby=comment_count&posts_per_page='.$posts);
		foreach($popular as $post) :
			setup_postdata($post);
	?>
	<li>
		<?php if ($size <> 0) woo_image('height='.$size.'&width='.$size.'&class=thumbnail&single=true'); ?>
		<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php endforeach;
	}
}


/*-----------------------------------------------------------------------------------*/
/* WooTabs - Latest Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_tabs_latest')) {
	function woo_tabs_latest( $posts = 5, $size = 35 ) {
		global $post;
		$latest = get_posts('showposts='. $posts .'&orderby=post_date&order=desc');
		foreach($latest as $post) :
			setup_postdata($post);
	?>
	<li>
		<?php if ($size <> 0) woo_image('height='.$size.'&width='.$size.'&class=thumbnail&single=true'); ?>
		<a title="<?php the_title(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php endforeach; 
	}
}



/*-----------------------------------------------------------------------------------*/
/* WooTabs - Latest Comments */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_tabs_comments' ) ) {
	function woo_tabs_comments( $posts = 5, $size = 35 ) {
		global $wpdb;

		$comments = get_comments( array( 'number' => $posts, 'status' => 'approve' ) );
		if ( $comments ) {
			foreach ( (array) $comments as $comment) {
			$post = get_post( $comment->comment_post_ID );
			?>
				<li>
					<?php if ( $size > 0 ) echo get_avatar( $comment, $size ); ?>
					<a href="<?php echo get_comment_link($comment->comment_ID); ?>" title="<?php echo wp_filter_nohtml_kses($comment->comment_author); ?> <?php _e( 'on', 'woothemes' ); ?> <?php echo $post->post_title; ?>"><?php echo wp_filter_nohtml_kses($comment->comment_author); ?>: <?php echo stripslashes( substr( wp_filter_nohtml_kses( $comment->comment_content ), 0, 50 ) ); ?>...</a>
					<div class="fix"></div>
				</li>
			<?php
			}
 		}
	}
}


/*-----------------------------------------------------------------------------------*/
/* Post Meta */
/*-----------------------------------------------------------------------------------*/

if (!function_exists('woo_post_meta')) {
	function woo_post_meta( ) {
?>
<p class="post-meta">
    <span class="post-author"><span class="small"><?php _e('by', 'woothemes') ?></span> <?php the_author_posts_link(); ?></span>
    <span class="post-date"><span class="small"><?php _e('on', 'woothemes') ?></span> <?php the_time( get_option( 'date_format' ) ); ?></span>
    <span class="post-category"><span class="small"><?php _e('in', 'woothemes') ?></span> <?php the_category(', ') ?></span>
    <?php edit_post_link( __('{ Edit }', 'woothemes'), '<span class="small">', '</span>' ); ?>
</p>
<?php 
	}
}


/*-----------------------------------------------------------------------------------*/
/* MISC */
/*-----------------------------------------------------------------------------------*/


/*-----------------------------------------------------------------------------------*/
/* WordPress 3.0 New Features Support */
/*-----------------------------------------------------------------------------------*/

if ( function_exists('wp_nav_menu') ) {
	add_theme_support( 'nav-menus' );
	register_nav_menus( array( 'primary-menu' => __( 'Primary Menu', 'woothemes' ) ) );
}     

/*-----------------------------------------------------------------------------------*/
/* Dynamic Search Functions - Backend */
/*-----------------------------------------------------------------------------------*/


add_action( 'pre_get_posts', 'woo_dynamic_search_results_filter' );
/**
 * woo_dynamic_search_results_filter function.
 * 
 * Since WordPress 3.4
 *
 * Filter on the default search 
 * Replaces custom search in search.php to fix pagination issues since 3.4
 *
 * @access public
 * @param mixed $query
 * @return void
 */
function woo_dynamic_search_results_filter( $query ) {

	
	// Exit if not main query
	if ( ! $query->is_main_query() )
		return;
	
	// Apply Filter only if on frontend and when search is running	
	if ( $query->is_search && !is_admin() ) {
	
		ob_start();
		
		// Get Custom Search Results and Process the Query Args
		$woo_search = woo_dynamic_search_header();
		$meta_query_args = array();
		$tax_query_args = array();
		if ( isset( $woo_search['query_args'] ) ) {
			
			// Set count of custom search results
			if ( isset( $woo_search['array_counter'] ) ) { 
				$array_counter = $woo_search['array_counter']; 
			} // End If Statement
			
			// Handle WebRef and redirect to exact match post
			if ( $array_counter == 1 && isset($_GET['listings-search-webref-submit']) && isset($woo_search['query_args']['post__in'][0]) && ( $woo_search['query_args']['post__in'][0] > 0 ) ) { 
				wp_redirect( get_permalink( intval($woo_search['query_args']['post__in'][0]) ) ); 
				exit;
			} // End If Statement
			
			// Set Query Args
			foreach ( $woo_search['query_args'] as $key => $value ) {
				$query->set( $key, $value );
			} // End For Loop
			
			// Handle Additional Search Filters
			global $woo_options;
			
			// SEARCH RESULTS
	    	$sentinel = false;
	    	$cmb_counter = 0;
	    	$ctx_counter = 0;
	    	// Get number of search fields from theme options and validate each GET variable
	    	$number_of_search_fields = get_option('woo_number_of_search_fields');
	    	for ( $counter = 1; $counter <= $number_of_search_fields; $counter += 1) {
	    		
	    		// GET value for the field
	    		$value_to_check = $_GET['field_'.$counter];
	    		// Type of content field
	    		$content_type_test = get_option('woo_search_content_type_'.$counter);
	    		if ($content_type_test == 'cmb') {
	    			
	    			// Custom meta field - get the name
	    			$content_type_value = get_option('woo_search_content_type_cmb_'.$counter);
	    			
	    			// Validate the GET value
	    			if ( ( ($value_to_check != '') && ($value_to_check != 'All') ) || ( ($value_to_check >= '0') && ($value_to_check != '') && ($value_to_check != __('Enter autocomplete keywords', 'woothemes')) && ($value_to_check != __('Enter text keywords', 'woothemes')) && ($value_to_check != 'All') ) ) {
	    				
	    				// Build the Meta Query array
	    				array_push( $meta_query_args, array(
															'key' => $content_type_value,
															'value' => $value_to_check,
															'compare' => 'LIKE'
														)
									); 
									
						// Handle more than 1 custom field query		
						if ($cmb_counter == 1) {
							$meta_query_args['relation'] = 'OR';
						}
					
	    				$cmb_counter++;
	    				$sentinel = true;
	    			
	    			} // End If Statement
	    				    		
	    		} elseif($content_type_test = 'ctx') {
	    			
	    			// Taxonomy field - get the name
	    			$content_type_value = get_option('woo_search_content_type_ctx_'.$counter);
	    			
	    			// Validate the GET value
	    			if ( ($value_to_check != '0') && ($value_to_check != '') && ($value_to_check != __('Enter autocomplete keywords', 'woothemes')) && ($value_to_check != __('Enter text keywords', 'woothemes')) && ($value_to_check != 'All') ) {
	    				
	    				// Build the Tax Query array
	    				array_push( $tax_query_args, array(
															'taxonomy' => $content_type_value,
															'field' => 'id',
															'terms' => $value_to_check,
															'operator' => 'IN'
															)
									); 
									
						// Handle more than 1 taxonomy query			
						if ($ctx_counter == 1) {
							$tax_query_args['relation'] = 'OR';
						} // End If Statement
						
						$ctx_counter++;		
						$sentinel = true;			
	    			
	    			} // End If Statement
	    			
	    		} // End If Sattement
	    		
	    	} // End For Loop
			
			// Setup Meta and Tax Queries if available
			if ( count($meta_query_args) > 0 ) { 
				$query->set('meta_query', $meta_query_args); 
			} // End If Statement
			if ( count($tax_query_args) > 0 ) { 
				$query->set('tax_query', $tax_query_args); 
			} // End If Statement
			
			// Handle Search Query var for searches for 'All' items
			if ( ( get_query_var('s') == stripslashes( $woo_options['woo_search_panel_keyword_text'] ) ) || ( $array_counter > 0 ) ) {
				$query->set('s', '');
			} // End If Statement
			
		} // End If Statement
		
		ob_flush();
		
	} // End If Statement

} // End woo_dynamic_search_results_filter()

add_action('admin_head','woo_dynamic_search_options_javascript',1);
function woo_dynamic_search_options_javascript() {
	
	$number_of_search_fields = (int)get_option('woo_number_of_search_fields');
	for ( $counter = 1; $counter <= $number_of_search_fields; $counter += 1) {
		
		?>
		<script type="text/javascript" language="javascript">
		jQuery(document).ready(function(){
			
			//initial field setup
			var initialSetting = jQuery('#woo_search_input_content_type_<?php echo $counter; ?>').val();
			if (initialSetting == 'none') {
				jQuery('#woo_search_content_type_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
				/*jQuery('#woo-option-dynamicsearch input[name=woo_search_content_type_matching_method_<?php echo $counter; ?>]').parent().parent().parent().addClass('hidden');
				jQuery('#woo_search_content_type_boolean_logic_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');*/
				jQuery('#woo_search_content_type_label_<?php echo $counter; ?>').parent().parent().parent().addClass('hidden');
				jQuery('#woo_search_content_type_ctx_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
				jQuery('#woo_search_content_type_cmb_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
				jQuery('#woo_search_content_type_cpt_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
			} else {
				var initialContentType = jQuery('#woo_search_content_type_<?php echo $counter; ?>').val();
				switch (initialContentType) {
					case 'ctx':
					jQuery('#woo_search_content_type_ctx_<?php echo $counter; ?>').parent().parent().parent().parent().removeClass('hidden');
					jQuery('#woo_search_content_type_cmb_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
					jQuery('#woo_search_content_type_cpt_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
					break;
				case 'cmb':
					jQuery('#woo_search_content_type_ctx_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
					jQuery('#woo_search_content_type_cmb_<?php echo $counter; ?>').parent().parent().parent().parent().removeClass('hidden');
					jQuery('#woo_search_content_type_cpt_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
					break;
				case 'cpt':
					jQuery('#woo_search_content_type_ctx_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
					jQuery('#woo_search_content_type_cmb_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
					jQuery('#woo_search_content_type_cpt_<?php echo $counter; ?>').parent().parent().parent().parent().removeClass('hidden');
					break;
				}
			}
			
			jQuery('#woo_search_input_content_type_<?php echo $counter; ?>').change(function() {
				var contentType = jQuery('#woo_search_input_content_type_<?php echo $counter; ?>').val();
				if (contentType == 'none') {
					jQuery('#woo_search_content_type_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
					/*jQuery('#woo-option-dynamicsearch input[name=woo_search_content_type_matching_method_<?php echo $counter; ?>]').parent().parent().parent().addClass('hidden');
					jQuery('#woo_search_content_type_boolean_logic_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');*/
					jQuery('#woo_search_content_type_label_<?php echo $counter; ?>').parent().parent().parent().addClass('hidden');
					jQuery('#woo_search_content_type_ctx_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
					jQuery('#woo_search_content_type_cmb_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
					jQuery('#woo_search_content_type_cpt_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
				} else {
					jQuery('#woo_search_content_type_<?php echo $counter; ?>').parent().parent().parent().parent().removeClass('hidden');
					/*jQuery('#woo-option-dynamicsearch input[name=woo_search_content_type_matching_method_<?php echo $counter; ?>]').parent().parent().parent().removeClass('hidden');
					jQuery('#woo_search_content_type_boolean_logic_<?php echo $counter; ?>').parent().parent().parent().parent().removeClass('hidden');*/
					jQuery('#woo_search_content_type_label_<?php echo $counter; ?>').parent().parent().parent().removeClass('hidden');
					var contentType = jQuery('#woo_search_content_type_<?php echo $counter; ?>').val();
					switch (contentType) {
						case 'ctx':
						jQuery('#woo_search_content_type_ctx_<?php echo $counter; ?>').parent().parent().parent().parent().removeClass('hidden');
						jQuery('#woo_search_content_type_cmb_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						jQuery('#woo_search_content_type_cpt_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						break;
					case 'cmb':
						jQuery('#woo_search_content_type_ctx_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						jQuery('#woo_search_content_type_cmb_<?php echo $counter; ?>').parent().parent().parent().parent().removeClass('hidden');
						jQuery('#woo_search_content_type_cpt_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						break;
					case 'cpt':
						jQuery('#woo_search_content_type_ctx_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						jQuery('#woo_search_content_type_cmb_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						jQuery('#woo_search_content_type_cpt_<?php echo $counter; ?>').parent().parent().parent().parent().removeClass('hidden');
						break;
					}
				}
			});
			
			jQuery('#woo_search_content_type_<?php echo $counter; ?>').change(function() {
				var contentType = jQuery('#woo_search_content_type_<?php echo $counter; ?>').val();
				switch (contentType) {
					case 'ctx':
						jQuery('#woo_search_content_type_ctx_<?php echo $counter; ?>').parent().parent().parent().parent().removeClass('hidden');
						jQuery('#woo_search_content_type_cmb_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						jQuery('#woo_search_content_type_cpt_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						break;
					case 'cmb':
						jQuery('#woo_search_content_type_ctx_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						jQuery('#woo_search_content_type_cmb_<?php echo $counter; ?>').parent().parent().parent().parent().removeClass('hidden');
						jQuery('#woo_search_content_type_cpt_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						break;
					case 'cpt':
						jQuery('#woo_search_content_type_ctx_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						jQuery('#woo_search_content_type_cmb_<?php echo $counter; ?>').parent().parent().parent().parent().addClass('hidden');
						jQuery('#woo_search_content_type_cpt_<?php echo $counter; ?>').parent().parent().parent().parent().removeClass('hidden');
						break;
				}
			});
		
		});
		</script>
		<?php
	
	}
	
}

/*-----------------------------------------------------------------------------------*/
/* Dynamic Search Functions - Frontend */
/*-----------------------------------------------------------------------------------*/

function woo_get_custom_post_meta_entries($meta) {
	//db class	
	global $wpdb;
	//tables
	$table_1 = $wpdb->prefix . "postmeta";
	//initialize where clause
	$where_clause = '';
	if (sizeof($meta) > 0) {
		foreach ($meta as $key => $meta_item) {
			if ($key == 0) {
				$where_clause = "WHERE ".$table_1.".meta_key = '".$meta_item."'";
			} else {
				$where_clause .= " OR ".$table_1.".meta_key = '".$meta_item."'";
			}
		}
		$woo_result = $wpdb->get_results("SELECT ".$table_1.".meta_value,".$table_1.".meta_key FROM ".$table_1." ".$where_clause);
	} else {
		$woo_result = '';
	}
	return $woo_result;					
}


// search taxonomies for a match against a search term and returns array of success count
function woo_taxonomy_matches($term_name, $term_id, $post_id = 0, $keyword_to_search = '', $post_type_to_search = 'post') {
	
	$return_array = array();
	$return_array['success'] = false;
	$return_array['keywordcount'] = 0;
	$terms = get_the_terms( $post_id , $term_name );
	
	$success = false;
	$keyword_count = 0;
	if ($term_id == 0) {
		$success = true;
	}
	$counter = 0;
	// Loop over each item
	if ($terms) {
		
		foreach( $terms as $term ) {
			
			if ($term->term_id == $term_id) {
				$success = true;
			}
			if ( $keyword_to_search != '' ) {
				//check if keyword is contained in taxonomy term
				$keyword_count = substr_count( strtolower( $term->name ) , strtolower( $keyword_to_search ) );
				
				if ( $keyword_count > 0 ) {
					$success = true;
					$counter++;
					
				}
			} else {
				// If search term is blank
				$term_tax_names =  get_term_by( 'id', $term_id, $term_name );
 				// taxonomies
				if ($term_tax_names) {
					if (isset($term_tax_names->slug)) { $term_tax_name = $term_tax_names->slug; } else { $term_tax_name = ''; }
					if ($term_tax_name != '') {
						
						$term_myposts = get_posts( array('nopaging'=> true, 'post_type' => $post_type_to_search, $term_name => $term_tax_name) );
						
						foreach($term_myposts as $term_mypost) {
							
							$mypost_term_list = get_the_term_list( $post_id, $term_name, '' , '|' , ''  );
							$mypost_term_list = strip_tags($mypost_term_list);
							$mypost_term_list_array = explode('|', $mypost_term_list);
							
							foreach ($mypost_term_list_array as $mypost_term_list_array_item) {
							
								if ( $mypost_term_list_array_item == $term_tax_names->name ) {
	    					
	    							if ($term_mypost->ID == $post_id) {
										$success = true;
	        							$counter++;
									} 		
	    						}    		
	    					} 
							
						}
					}
				}
			}
		}
	}
	$return_array['success'] = $success;
	if ($counter == 0) {
		$return_array['keywordcount'] = $keyword_count;
	} else { 
		$return_array['keywordcount'] = $counter;
	}
	
	return $return_array;
}

// Recursive function to get child terms
function woo_get_taxonomy_child_terms($term_id, $depth) {

	global $wpdb;
	
	$querystr = "SELECT $wpdb->term_taxonomy.term_id, $wpdb->term_taxonomy.parent FROM $wpdb->term_taxonomy
					WHERE $wpdb->term_taxonomy.parent = $term_id
				";
							
	$child_terms = $wpdb->get_results($querystr, OBJECT);
	
	foreach ($child_terms as $child_term) {
		
		$child_terms = array_merge($child_terms,woo_get_taxonomy_child_terms($child_term->term_id,$depth));
		
	}	
	
	return $child_terms;

}

// Search Results Set - DO NOT MODIFY THIS FUNCTION!!
function woo_dynamic_search_results_set($data) {
	/* 
		$data contains
		- $get_data
		- $query_args
		- $keyword_to_search
	*/
	global $wpdb;
	
	if (isset($data)) {
		if ( !is_array($data) ) 
		parse_str( $data, $data );
	
		extract($data);
	}
	
	if ( isset( $search_type ) ) {
		// Do Nothing
	} else {
		$search_type = '';
	} // End If Statement
	
	// SEARCH OPTIONS
	$number_of_search_fields = get_option('woo_number_of_search_fields');
	$matching_method = get_option('woo_search_content_type_matching_method');
	
	$search_variables = array();
	$search_variables['cpt'] = array();
	$search_variables['ctx'] = array();
	$search_variables['cmb'] = array();
	
	// SEARCH RESULTS ARRAY
	$search_results = array();
	$number_of_posted_values = 0;

	// CHECK 1 - SEARCH KEYWORD
	$search_results['keyword'] = array();
	$search_results['keyword']['posts'] = array();
	$query_args['posts_per_page'] = -1;
	$the_query = new WP_Query($query_args);
	
	if ($the_query->have_posts()) : $count = 0;

		while ($the_query->have_posts()) : $the_query->the_post();
		
			global $post;
    	    $post_type = $post->post_type;
    	    
    	    if ($search_type == 'webref') {
				
				array_push($search_results['keyword']['posts'],$post->ID);
				
			} else {
				
				// Defaults
				$title_keyword_count = 0;
				$content_keyword_count = 0;
				$excerpt_keyword_count = 0;
				
				//Search against post data
		    	if ( $keyword_to_search != '' ) {
		    		//Default WordPress Content
		    		$raw_title = get_the_title();
		    		$raw_content = get_the_content();
		    		$raw_excerpt = get_the_excerpt();
		    		//Comparison
		    		$title_keyword_count = substr_count( strtolower( sanitize_title($raw_title) ) , strtolower( sanitize_title($keyword_to_search) ) );
		    		$content_keyword_count = substr_count( strtolower( $raw_content ) , strtolower( $keyword_to_search ) );
		    		$excerpt_keyword_count = substr_count( strtolower( $raw_excerpt ) , strtolower( $keyword_to_search ) );
				
				}
		    	
		    	if ( ( $title_keyword_count > 0 ) || ( $content_keyword_count > 0 ) || ( $excerpt_keyword_count > 0 ) ) {
		    		array_push($search_results['keyword']['posts'],$post->ID);
		    	} else {
		    		
		    		// check the terms, taxonomies
		    		$wp_custom_post_types_args = array();
		    		$wp_custom_post_types = get_post_types($wp_custom_post_types_args,'objects');
					$my_current_post_type = $post_type;
					$my_post_type_obj = $wp_custom_post_types[$my_current_post_type];
					//Start LOOP
					$sentinel = 0;
					
					foreach ($my_post_type_obj->taxonomies as $post_type_taxonomy) {
						$taxonomy_details = get_taxonomy( $post_type_taxonomy );
								
						$taxonomy_nice_name_plural = $taxonomy_details->labels->name;
		    		
		    			$taxonomy_matches = woo_taxonomy_matches($taxonomy_details->name, 0, $post->ID, $keyword_to_search, $post_type);
		    			if ($taxonomy_matches['success'] && $taxonomy_matches['keywordcount'] && ($sentinel == 0)) {
	        				array_push($search_results['keyword']['posts'],$post->ID);
	        				$sentinel++;
	        			}
		    		}
		    		
		    	}
		    
			}
		
			
		    
		endwhile; 
		if ($keyword_to_search != '') {
			$number_of_posted_values++;
		}
	endif;

	// CHECK 2 - ADDITIONAL FIELDS
	$big_posts_array = array();
	$big_posts_array = array_merge($big_posts_array , $search_results['keyword']['posts'] );
		
	if ($search_type == 'webref') {
		// Do no more checks
	} else {
		
		for ( $counter = 1; $counter <= $number_of_search_fields; $counter += 1) {
			// 
			$search_results['field_'.$counter] = array();	
			$search_results['field_'.$counter]['posts'] = array();
			$search_results['field_'.$counter]['matching'] = get_option('woo_search_content_type_matching_method_'.$counter);
			$search_results['field_'.$counter]['chaining'] = get_option('woo_search_content_type_boolean_logic_'.$counter);
			
			$value_to_check = $get_data['field_'.$counter];
			$type_of_input = get_option('woo_search_content_type_'.$counter);
			
			if ($type_of_input == 'cmb') {
				if ($value_to_check >= '0' ) {
					$cmb_validator = true;
				} else {
					$cmb_validator = false;
				}
				
			} else {
				if ($value_to_check != '0') {
					$cmb_validator = true;
				} else {
					$cmb_validator = false;
				}
			}
			
			
			if ( ($cmb_validator) && ($value_to_check != '') && ($value_to_check != __('Enter autocomplete keywords', 'woothemes')) && ($value_to_check != __('Enter text keywords', 'woothemes')) && ($value_to_check != 'All') ) {
				$number_of_posted_values++;
				
				switch ($type_of_input) {
					case 'cpt' :
						//echo 'cpt';
						/*array_push($search_variables['cpt'], array( 'ID' => $term_obj->term_id,
																			'name' => $term_obj->name,
																			'slug' => $term_obj->slug
																			));*/
						break;
					case 'ctx' :
					
						$taxonomy_obj = get_taxonomy(get_option('woo_search_content_type_ctx_'.$counter));
						if ($taxonomy_obj) {
							$taxonomy_name = $taxonomy_obj->name;
							
							$term_obj = get_term($value_to_check, $taxonomy_name);
							// check in case of autocomplete or text box field
							if (!$term_obj) {
								$term_obj = get_term_by( 'name', $value_to_check, $taxonomy_name );
							}
							
							if ($term_obj) {
								$term_id = $term_obj->term_id;
								array_push($search_variables['ctx'], array( 'ID' => $term_id,
																			'name' => $term_obj->name,
																			'slug' => $term_obj->slug,
																			'taxonomy' => $taxonomy_name
								
																			));
								if ($term_id > 0) {
								
									// get sub category id's
									$child_terms = woo_get_taxonomy_child_terms($term_id, 10);
			
									$child_terms_string = '';
									foreach ($child_terms as $child_term) {
										$child_terms_string .= ','.$child_term->term_id;
									}
									$querystr = "SELECT $wpdb->posts.ID, $wpdb->posts.post_title FROM $wpdb->posts
													LEFT JOIN $wpdb->postmeta ON($wpdb->posts.ID = $wpdb->postmeta.post_id)
									    			LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
									    			LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
									    			WHERE $wpdb->term_taxonomy.term_id IN ($term_id$child_terms_string)
									    				AND $wpdb->term_taxonomy.taxonomy = '$taxonomy_name'
									    				AND $wpdb->posts.post_status = 'publish'
									    			ORDER BY $wpdb->posts.ID ASC
												";
									
									
									
									$posts_array = $wpdb->get_results($querystr, OBJECT);
		
									$temp_array = array();
									foreach	($posts_array as $post_item) {
										array_push($search_results['field_'.$counter]['posts'],$post_item->ID);
									}
		
								}
							}
						}
						$search_results['field_'.$counter]['posts'] = array_unique($search_results['field_'.$counter]['posts']);
						
						
						break;
					case 'cmb' :
					
						$custom_field_name = get_option('woo_search_content_type_cmb_'.$counter);
						
						//check if value is integer
						if ($value_to_check == 'All') {
							$int_value = -1;
						} else {
							$int_value = (int)$value_to_check;
						}
						$int_test = false;
						if ($int_value >= 0) {
							$int_test = true;
						}
						
						// Check if is text field and not numeric field 
						$woo_metaboxes = get_option('woo_custom_template');
            			foreach ($woo_metaboxes as $woo_metabox) {
            				if ($woo_metabox['name'] == $custom_field_name) {
            					$cmb_meta_type = $woo_metabox['type'];
            				}
            			}
						
						// Check if it is a calendar field
						if ( $cmb_meta_type == 'calendar' ) {
							$int_test = false;
						}
						
						if ($int_test) {
							//Matching Method
							if ($matching_method == 'minimum') {
								//Minimum Value
								$symbol = "AND $wpdb->postmeta.meta_value >= $value_to_check ";
							} else {
								if ($cmb_meta_type == 'text') {
									$symbol = "AND $wpdb->postmeta.meta_value = '$value_to_check' ";
								} else {
									$symbol = "AND $wpdb->postmeta.meta_value = $value_to_check ";
								}
							}
						} else {
							$symbol = "AND $wpdb->postmeta.meta_value = '$value_to_check' ";
						}
							
						$querystr = "SELECT $wpdb->posts.ID 
   										FROM $wpdb->posts, $wpdb->postmeta
   										WHERE $wpdb->posts.ID = $wpdb->postmeta.post_id 
   										AND $wpdb->postmeta.meta_key = '$custom_field_name' 
   										$symbol
   										ORDER BY $wpdb->postmeta.meta_value ASC
   										";
								
						$posts_array = $wpdb->get_results($querystr, OBJECT);
		
						$temp_array = array();
						foreach	($posts_array as $post_item) {
							array_push($search_results['field_'.$counter]['posts'],$post_item->ID);
						}
						
						$search_results['field_'.$counter]['posts'] = array_unique($search_results['field_'.$counter]['posts']);
																					
						break;
				} // End Switch Statement
				
			} // End If Statement
			
			$big_posts_array = array_merge($big_posts_array , $search_results['field_'.$counter]['posts'] );
			
		}
	
	}
	
	// FIND UNIQUE VALUES
	$array_value_count = array_count_values($big_posts_array);
	
	foreach ($array_value_count as $key => $value) {
		
		$intval = $key;
	
		if ( (int)$value < $number_of_posted_values) {
			$big_posts_array = woo_array_remove_item_by_value($big_posts_array, $intval);
	
		}
		
	}
	
	$big_posts_array = array_unique($big_posts_array);
	sort($big_posts_array); // can also use - rsort (reverse), asort (associative sort), krsort (reverse associative sort)
	
	return $big_posts_array;
}

// Dynamic Search Heading Custom Logic - DO NOT MODIFY THIS FUNCTION!!
function woo_dynamic_search_header() {
	
	global $woo_options;
	
	$query_args = array();
	
	// Check if this is a standard blog search
	$blog_search = false;
	if ( isset($_GET['blog_search']) ) {
	    if ($_GET['blog_search'] == 'true') {
	    	$blog_search = true;
	    }
	}
	
	// Blog Search or Listings Search
	if ($blog_search) {
	    // Setup Blog Query
	    $query_args['post_type'] = array('post','page');
	    // Add Paging variables
	    $search_results_amount = $woo_options['woo_listings_search_results']; 
	    if ( $search_results_amount != '' ) { } else { $search_results_amount = 3; } 
	    $query_args['posts_per_page'] = $search_results_amount;
	    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	    $query_args['paged'] = $paged;
	    $query_args['s'] = get_search_query();
	    	
	} else {
	
	    // Check for which post types to search
	    $query_args['post_type'] = array();
	    $wp_custom_post_types_args = array();
	    $wp_custom_post_types = get_post_types($wp_custom_post_types_args,'objects');
	    foreach ($wp_custom_post_types as $post_type_item) {
	        $cpt_test = get_option('woo_search_post_types_'.$post_type_item->name);
	        if ($cpt_test == 'true') {
	        	array_push($query_args['post_type'], $post_type_item->name);
	        }
	    }
	    				
	    // Check how many search fields enabled in search
	    $number_of_search_fields = get_option('woo_number_of_search_fields');
	    $number_of_search_fields_in_search_form = 0;
	    for ( $field_counter = 1; $field_counter <= $number_of_search_fields; $field_counter += 1) {	
	    	$content_type_input = get_option('woo_search_input_content_type_'.$field_counter);
        	if ($content_type_input != 'none') {
        		$number_of_search_fields_in_search_form++;	
        	}
        } 
	
	    // Setup Search Args
	    if ( (get_search_query() == stripslashes( $woo_options['woo_search_panel_keyword_text'] )) ) { $keyword_to_search = ''; } else { $keyword_to_search = get_search_query(); }
	    $search_args = array(	'keyword_to_search' =>	$keyword_to_search,
	    						'query_args'		=>	$query_args,
	    						'get_data'			=>	$_GET
	    					);
	    																		
	    // SEARCH RESULTS
	    $sentinel = 0;
	    $field_all_count = 0;
	    for ( $counter = 1; $counter <= $number_of_search_fields; $counter += 1) {
	    		
	    		$value_to_check = $_GET['field_'.$counter];
	    		
	    		$cmb_test = get_option('woo_search_content_type_'.$counter);
	    		//check if its a custom field search field
	    		if ($cmb_test == 'cmb') {
	    
	    			if ( ( ($value_to_check != '') && ($value_to_check == 'All') ) || ( ($value_to_check >= '0') && ($value_to_check != '') && ($value_to_check != __('Enter autocomplete keywords', 'woothemes')) && ($value_to_check != __('Enter text keywords', 'woothemes')) && ($value_to_check != 'All') ) ) {
	    			
	    				// check if any posts exist with the custom field
	    				$content_type_value = get_option('woo_search_content_type_cmb_'.$counter);
	    				$cmb_args = array();
	    				$cmb_args['numberposts'] = 10000;
	    				$cmb_args['post_type'] = 'any';
	    				if ( $value_to_check == 'All' || $value_to_check == '' || $value_to_check == __('Enter autocomplete keywords', 'woothemes') || $value_to_check == __('Enter text keywords', 'woothemes') ) {
	    					// Bring all results back
	    				} else {
	    					$cmb_args['meta_key'] = $content_type_value;
	    					$cmb_args['meta_value'] = $value_to_check;
	    				}
	    				$custom_field_posts = get_posts( $cmb_args );
	    				$custom_field_array_counter = count($custom_field_posts);
	    				if ($custom_field_array_counter > 0) {
	    					$sentinel++;
	    				}
	    			}
	    		
	    		} else {
	    		
	    			if ( ($value_to_check != '0') && ($value_to_check != '') && ($value_to_check != __('Enter autocomplete keywords', 'woothemes')) && ($value_to_check != __('Enter text keywords', 'woothemes')) && ($value_to_check != 'All') ) {
	    				$sentinel++;
	    			}
	    		
	    		}
	    
	    		if ( ( ($value_to_check == '0') || ($value_to_check == 'All') ) && ( ($search_args['keyword_to_search'] == __('Enter autocomplete keywords', 'woothemes')) || ($search_args['keyword_to_search'] == __('Enter text keywords', 'woothemes')) || ( $search_args['keyword_to_search'] == '' ) ) ) {
	    				$field_all_count++;
	    		}
	    		
	    }
	    
	    // SECONDARY SEARCH RESULTS CHECK FOR ALL ITEMS
	    if ($field_all_count == $number_of_search_fields_in_search_form) {
	    	$sentinel++;
	    }
	    
	    // Check if general search or webref search
	    if (isset($_GET['listings-search-webref-submit'])) {
	    
	    	$keyword_to_search_sanitized = strtolower($search_args['keyword_to_search']);
	    	$keyword_to_search_sanitized = str_replace( strtolower(get_option('woo_listings_prefix')), '', $keyword_to_search_sanitized );
	    	$webref_array['post__in'] = array($keyword_to_search_sanitized);
	    	$webref_array['post_type'] = $query_args['post_type'];
	    	$search_args = array(	'keyword_to_search' =>	$keyword_to_search_sanitized,
	    							'query_args'		=>	$webref_array,
	    							'get_data'			=>	$_GET, 
	    							'search_type' => 'webref'
	    						);
	    	$posts_array = woo_dynamic_search_results_set($search_args);
	    	$sentinel++;
	    } else {
	    	if ($sentinel > 0) { 
	    		$posts_array = woo_dynamic_search_results_set($search_args); 
	    	} elseif ($search_args['keyword_to_search'] != '') { 
	    		$posts_array = woo_dynamic_search_results_set($search_args); 
	    	} else { 
	    		$posts_array = array();
	    	}
	    }
	    																	
	    // Add Paging and Query variables
	    $search_results_amount = $woo_options['woo_listings_search_results']; 
	    if ( $search_results_amount != '' ) { } else { $search_results_amount = 3; } 
	    $query_args['posts_per_page'] = $search_results_amount;
	    $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;
	    $query_args['paged'] = $paged;
	    $array_counter = count($posts_array);
	    if ( $array_counter > 0 ) {
	    	$query_args['post__in'] = $posts_array;
	    	$has_results = true;
	    } elseif ($sentinel > 0 ) {
	    	if ($field_all_count == $number_of_search_fields_in_search_form) {
	    		$has_results = true;
	    	} else {
	    		$has_results = false;
	    	}
	    } else {
	    	$has_results = false;
	    }
	
	}
	
	$return_value = array(	'query_args' => $query_args,
	    					'array_counter' => $array_counter,
	    					'has_results' => $has_results,
	    					'blog_search' => $blog_search);
	
	return $return_value;
		
}

function woo_taxonomy_image($post_images = array(), $term_link = '', $width = 139, $height = 81) {
	
	if (count($post_images) > 0) {
		woo_image('id='.$post_images[0].'&key=image&width='.$width.'&height='.$height.'&link=img');
	} else {
		?><img width="<?php echo $width; ?>" height="<?php echo $height; ?>" class="woo-image" alt="Image" src="<?php echo get_bloginfo('template_directory'); ?>/images/placeholder.jpg" /><?php
	}
	
}

/*-----------------------------------------------------------------------------------*/
/* Custom Array Functions */
/*-----------------------------------------------------------------------------------*/

function woo_multidimensional_array_unique($array)
{

	// Make sure the $array parameter is in fact an array.
	if ( ! is_array( $array ) ) { return; } // End IF Statement

	$result = array_map("unserialize", array_unique(array_map("serialize", $array)));

	foreach ($result as $key => $value)
	{
		if ( is_array($value) )
		{
			$result[$key] = super_unique($value);
		}
	}

	return $result;
}

function woo_array_remove_item_by_value($array, $val = '', $preserve_keys = true) {
	if (empty($array) || !is_array($array)) return false;
	if (!in_array($val, $array)) return $array;

	foreach($array as $key => $value) {
		if ($value == $val) unset($array[$key]);
	}

	return ($preserve_keys === true) ? $array : array_values($array);
}

/*-----------------------------------------------------------------------------------*/
/* Listings Install Content Function */
/*-----------------------------------------------------------------------------------*/

if (is_admin() && isset($_GET['activated'] ) && $pagenow == "themes.php" ) {

	// Call action that sets
	add_action( 'admin_head','woo_listings_content_install' );
	
}

if (!function_exists('woo_listings_content_install')) {
	function woo_listings_content_install( $reset = false ){
		
		$woo_array_cpt_all = array();
		$woo_array_ctx_all = array();
		$woo_array_cmb_all = array();
		
		// Check if each option is present in the database and is an array.
		// If it isn't there or isn't an array, add it or set it to it's original state.
		
		$_existing_fields = array();
		$_fields = array(
						'woo_content_builder_cpt' => $woo_array_cpt_all, 
						'woo_content_builder_ctx' => $woo_array_ctx_all, 
						'woo_content_builder_cmb' => $woo_array_cmb_all
						);
		
		foreach ( $_fields as $k => $v ) {
		
			$_option = get_option( $k );
		
			if ( ! $_option || ! is_array( $_option ) ) {
			
				add_option( $k, $v );
			
			} else {
			
				$_existing_fields[] = $k;
				
				// Assign the current value to the appropriate array variable.
				// ${$v} = $_option;
						
			} // End IF Statement
		
		} // End FOREACH Loop
		
		/*
		add_option('woo_content_builder_cpt',$woo_array_cpt_all);
		add_option('woo_content_builder_ctx',$woo_array_ctx_all);
		add_option('woo_content_builder_cmb',$woo_array_cmb_all);
		*/
		
		/* CUSTOM POST TYPES */
		
		$woo_array_cpt = array();
		
		$woo_array_cpt['name'] = 'listing';
		$woo_array_cpt['args']['label'] = 'Listings';
		$woo_array_cpt['args']['labels']['name'] = __('Listings');
		$woo_array_cpt['args']['labels']['singular_name'] = __('Listing');
		$woo_array_cpt['args']['labels']['add_new'] =  __('Add New');
		$woo_array_cpt['args']['labels']['add_new_item'] = __('Add New '.$woo_array_cpt['args']['label']);
		$woo_array_cpt['args']['labels']['edit_item'] = __('Edit '.$woo_array_cpt['args']['label']);
		$woo_array_cpt['args']['labels']['new_item'] = __('New '.$woo_array_cpt['args']['label']);
		$woo_array_cpt['args']['labels']['view_item'] = __('View '.$woo_array_cpt['args']['label']);
		$woo_array_cpt['args']['labels']['search_items'] = __('Search '.$woo_array_cpt['args']['label']);
		$woo_array_cpt['args']['labels']['not_found'] = __('No '.$woo_array_cpt['args']['label'].' found');
		$woo_array_cpt['args']['labels']['not_found_in_trash'] = __('No '.$woo_array_cpt['args']['label'].' found in Thrash');
		$woo_array_cpt['args']['labels']['parent_item_colon'] = __('Parent '.$woo_array_cpt['args']['label']);
		
		$woo_array_cpt['args']['description'] = 'The Default Listings Post Type';
		$woo_array_cpt['args']['public'] = 1;
		$woo_array_cpt['args']['publicly_queryable'] = 1;
		$woo_array_cpt['args']['exclude_from_search'] = 0;
		$woo_array_cpt['args']['show_ui'] = 1;
		$woo_array_cpt['args']['capability_type'] =  'post';
		$woo_array_cpt['args']['hierarchical'] = 0;
		$woo_array_cpt['args']['supports'] = array();
		    array_push($woo_array_cpt['args']['supports'],'title');
		    array_push($woo_array_cpt['args']['supports'],'editor');
		    array_push($woo_array_cpt['args']['supports'],'author');
		    array_push($woo_array_cpt['args']['supports'],'thumbnail');
		    array_push($woo_array_cpt['args']['supports'],'excerpt');
		    array_push($woo_array_cpt['args']['supports'],'trackbacks');
		    array_push($woo_array_cpt['args']['supports'],'custom-fields');
		    array_push($woo_array_cpt['args']['supports'],'comments');
		    array_push($woo_array_cpt['args']['supports'],'revisions');
		    array_push($woo_array_cpt['args']['supports'],'page-attributes');
		$woo_array_cpt['args']['register_meta_box_cb'] = '';
		
		$woo_array_cpt['args']['taxonomies'] = array();
		    array_push($woo_array_cpt['args']['taxonomies'],'location');
		    array_push($woo_array_cpt['args']['taxonomies'],'listingtype');
		    array_push($woo_array_cpt['args']['taxonomies'],'listingfeatures');
		    array_push($woo_array_cpt['args']['taxonomies'],'post_tag');
		    // add more here
		    	
		$woo_array_cpt['args']['menu_position'] = 20;
		$woo_array_cpt['args']['menu_icon'] = null;
		$woo_array_cpt['args']['permalink_epmask'] = 'EP_PERMALINK';
		
		$woo_array_cpt['args']['rewrite'] =array();
			$woo_array_cpt['args']['rewrite']['slug'] = $woo_array_cpt['name'];
			$woo_array_cpt['args']['rewrite']['with_front'] = 1;
		
		$woo_array_cpt['args']['query_var'] = 1;
		$woo_array_cpt['args']['can_export'] = 1;
		$woo_array_cpt['args']['has_archive'] = 1;
		$woo_array_cpt['args']['show_in_nav_menus'] = 1;
		
		array_push($woo_array_cpt_all,$woo_array_cpt);
		
		// If we're trying to reset the data, reset the data.
		
		if ( $reset ) {
			
			update_option( 'woo_content_builder_cpt', $woo_array_cpt_all );
		
		} else {
		
			// If there is data present, don't add the default data.
		
			if ( in_array( 'woo_content_builder_cpt', $_existing_fields ) ) {} else {
			
				update_option('woo_content_builder_cpt',$woo_array_cpt_all);
			
			} // End IF Statement
		
		} // End IF Statement
		
		/* CUSTOM TAXONOMIES */
		
		$woo_array_ctx = array();
		
		// Locations
		$woo_array_ctx['name'] = 'location';
		
		$woo_array_ctx['object_type'] = array();
			$ctx_item_name = 'listing';
		    array_push($woo_array_ctx['object_type'],$ctx_item_name);
		
		$woo_array_ctx['args']['label'] = __('Listing Locations');
		$woo_array_ctx['args']['labels']['name'] =  __('Listing Locations');
		$woo_array_ctx['args']['labels']['singular_name'] = __('Listing Location');
		
		
		$woo_array_ctx['args']['labels']['search_items'] = __('Search ').$woo_array_ctx['args']['label'];
		
		$woo_array_ctx['args']['labels']['popular_items'] = __('Popular ').$woo_array_ctx['args']['label'];
		$woo_array_ctx['args']['labels']['all_items'] = __('All ').$woo_array_ctx['args']['label'];
		$woo_array_ctx['args']['labels']['parent_item'] = __('Parent ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['parent_item_colon'] = __('Parent '.$woo_array_ctx['args']['labels']['singular_name'].':');
		$woo_array_ctx['args']['labels']['edit_item'] = __('Edit ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['update_item'] = __('Update ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['add_new_item'] = __('Add New ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['new_item_name'] = __('New '.$woo_array_ctx['args']['labels']['singular_name'].' Name');
		$woo_array_ctx['args']['labels']['separate_items_with_commas'] = __('Separate locations with commas');
		$woo_array_ctx['args']['labels']['add_or_remove_items'] = __('Add or remove locations');
		$woo_array_ctx['args']['labels']['choose_from_most_used'] = __('Choose from the most used locations');
		
		
		$woo_array_ctx['args']['public'] = 1;
		$woo_array_ctx['args']['hierarchical'] = 1;
		$woo_array_ctx['args']['show_ui'] = 1;
		$woo_array_ctx['args']['show_in_nav_menus'] = 1;
		$woo_array_ctx['args']['show_tagcloud'] = 1;
	
		$woo_array_ctx['args']['rewrite'] =array();
			$woo_array_ctx['args']['rewrite']['slug'] = $woo_array_ctx['name'];
			$woo_array_ctx['args']['rewrite']['with_front'] = 1;
		
		$woo_array_ctx['args']['query_var'] = 1;
		
		$woo_array_ctx['args']['update_count_callback'] = '';
		
		array_push($woo_array_ctx_all,$woo_array_ctx);
		
		// Listing Types
		$woo_array_ctx['name'] = 'listingtype';
		
		$woo_array_ctx['object_type'] = array();
			$ctx_item_name = 'listing';
		    array_push($woo_array_ctx['object_type'],$ctx_item_name);
		
		$woo_array_ctx['args']['label'] = __('Listing Types');
		$woo_array_ctx['args']['labels']['name'] =  __('Listing Types');
		$woo_array_ctx['args']['labels']['singular_name'] = __('Listing Type');
		
		
		$woo_array_ctx['args']['labels']['search_items'] = __('Search ').$woo_array_ctx['args']['label'];
		
		$woo_array_ctx['args']['labels']['popular_items'] = __('Popular ').$woo_array_ctx['args']['label'];
		$woo_array_ctx['args']['labels']['all_items'] = __('All ').$woo_array_ctx['args']['label'];
		$woo_array_ctx['args']['labels']['parent_item'] = __('Parent ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['parent_item_colon'] = __('Parent '.$woo_array_ctx['args']['labels']['singular_name'].':');
		$woo_array_ctx['args']['labels']['edit_item'] = __('Edit ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['update_item'] = __('Update ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['add_new_item'] = __('Add New ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['new_item_name'] = __('New '.$woo_array_ctx['args']['labels']['singular_name'].' Name');
		$woo_array_ctx['args']['labels']['separate_items_with_commas'] = __('Separate listings types with commas');
		$woo_array_ctx['args']['labels']['add_or_remove_items'] = __('Add or remove listing types');
		$woo_array_ctx['args']['labels']['choose_from_most_used'] = __('Choose from the most used listing types');
		
		
		$woo_array_ctx['args']['public'] = 1;
		$woo_array_ctx['args']['hierarchical'] = 0;
		$woo_array_ctx['args']['show_ui'] = 1;
		$woo_array_ctx['args']['show_in_nav_menus'] = 1;
		$woo_array_ctx['args']['show_tagcloud'] = 1;
	
		$woo_array_ctx['args']['rewrite'] =array();
			$woo_array_ctx['args']['rewrite']['slug'] = $woo_array_ctx['name'];
			$woo_array_ctx['args']['rewrite']['with_front'] = 1;
		
		$woo_array_ctx['args']['query_var'] = 1;
		
		$woo_array_ctx['args']['update_count_callback'] = '';
		
		array_push($woo_array_ctx_all,$woo_array_ctx);
		
		// Listing Features
		$woo_array_ctx['name'] = 'listingfeatures';
		
		$woo_array_ctx['object_type'] = array();
			$ctx_item_name = 'listing';
		    array_push($woo_array_ctx['object_type'],$ctx_item_name);
		
		$woo_array_ctx['args']['label'] = __('Listing Features');
		$woo_array_ctx['args']['labels']['name'] =  __('Listing Features');
		$woo_array_ctx['args']['labels']['singular_name'] = __('Listing Feature');
		
		
		$woo_array_ctx['args']['labels']['search_items'] = __('Search ').$woo_array_ctx['args']['label'];
		
		$woo_array_ctx['args']['labels']['popular_items'] = __('Popular ').$woo_array_ctx['args']['label'];
		$woo_array_ctx['args']['labels']['all_items'] = __('All ').$woo_array_ctx['args']['label'];
		$woo_array_ctx['args']['labels']['parent_item'] = __('Parent ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['parent_item_colon'] = __('Parent '.$woo_array_ctx['args']['labels']['singular_name'].':');
		$woo_array_ctx['args']['labels']['edit_item'] = __('Edit ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['update_item'] = __('Update ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['add_new_item'] = __('Add New ').$woo_array_ctx['args']['labels']['singular_name'];
		$woo_array_ctx['args']['labels']['new_item_name'] = __('New '.$woo_array_ctx['args']['labels']['singular_name'].' Name');
		$woo_array_ctx['args']['labels']['separate_items_with_commas'] = __('Separate listings features with commas');
		$woo_array_ctx['args']['labels']['add_or_remove_items'] = __('Add or remove listing features');
		$woo_array_ctx['args']['labels']['choose_from_most_used'] = __('Choose from the most used listing features');
		
		
		$woo_array_ctx['args']['public'] = 1;
		$woo_array_ctx['args']['hierarchical'] = 0;
		$woo_array_ctx['args']['show_ui'] = 1;
		$woo_array_ctx['args']['show_in_nav_menus'] = 1;
		$woo_array_ctx['args']['show_tagcloud'] = 1;
	
		$woo_array_ctx['args']['rewrite'] =array();
			$woo_array_ctx['args']['rewrite']['slug'] = $woo_array_ctx['name'];
			$woo_array_ctx['args']['rewrite']['with_front'] = 1;
		
		$woo_array_ctx['args']['query_var'] = 1;
		
		$woo_array_ctx['args']['update_count_callback'] = '';
		
		array_push($woo_array_ctx_all,$woo_array_ctx);
		
		// If we're trying to reset the data, reset the data.
		
		if ( $reset ) {
		
		 	update_option( 'woo_content_builder_ctx', $woo_array_ctx_all );
		
		} else {
		
			// If there is data present, don't add the default data.
			
			if ( in_array( 'woo_content_builder_ctx', $_existing_fields ) ) {} else {
			
				update_option('woo_content_builder_ctx',$woo_array_ctx_all);
			
			} // End IF Statement
		
		} // End IF Statement
		
		// update_option('woo_content_builder_ctx',$woo_array_ctx_all);
		
		/* CUSTOM FIELDS */
		
		$woo_array_cmb = array();
		
		$options_array = array();
		//image
		$woo_array_cmb 	= array (	"name" 		=> 	"image",
									"std" 		=> 	"",
									"label" 	=> 	"Upload Image",
									"type" 		=> 	"upload",
									"desc" 		=> 	"Upload your listings image here",
									"options" 	=> 	$options_array,
									"cpt"		=>	array(	"listing"	=>	"true"
															)
									);
		
		array_push($woo_array_cmb_all,$woo_array_cmb);
		//price
		$woo_array_cmb 	= array (	"name" 		=> 	"price",
									"std" 		=> 	"",
									"label" 	=> 	"Price in $",
									"type" 		=> 	"text",
									"desc" 		=> 	"Enter the price of the listing excluding the currency symbol.",
									"options" 	=> 	$options_array,
									"cpt"		=>	array(	"listing"	=>	"true"
															)
									);
		
		array_push($woo_array_cmb_all,$woo_array_cmb);
		//google map							
		$woo_array_cmb 	= array (	"name" 		=> 	"googlemap",
									"std" 		=> 	"Google Maps",
									"label" 	=> 	"Google Maps",
									"type" 		=> 	"googlemap",
									"desc" 		=> 	"Google Maps.",
									"options" 	=> 	$options_array,
									"cpt"		=>	array(	"listing"	=>	"true"
															)
									);
		array_push($woo_array_cmb_all,$woo_array_cmb);
		
		
		// If we're trying to reset the data, reset the data.
		
		if ( $reset ) {
		
			update_option( 'woo_content_builder_cmb', $woo_array_cmb_all );
			update_option( 'woo_custom_template', $woo_array_cmb_all );
		
		} else {
		
			// $woo_content_builder_cmb = get_option( 'woo_content_builder_cmb' );
			$woo_custom_template = get_option( 'woo_custom_template' );
		
			$woo_custom_template_backup = get_option( 'woo_content_builder_cmb' );
		
			// Always favour the backup, if one exists.
			if ( $woo_custom_template_backup ) {
			
				$woo_custom_template = $woo_custom_template_backup;
			
			} // End IF Statement
		
			// If there is data present, attempt to merge new data with existing data.
			
			if ( count( $woo_array_cmb_all ) && is_array( $woo_array_cmb_all ) ) {
			
				// If no custom fields have been created with the Content Builder, we can safely add all of ours to the array.
				if ( count( $woo_custom_template ) == 0 ) {
				
					$woo_custom_template = $woo_array_cmb_all;
				
				} else {
			
					$existing_fields = array();
			
					foreach ( $woo_custom_template as $i => $j ) {
					
						$existing_fields[] = $j['name'];
					
					} // End FOREACH Loop
			
					foreach ( $woo_array_cmb_all as $k => $v ) {
					
						// If the `name` exists and is equal to the name of the current item in the $woo_array_cmb_all,
						// don't do anything. Otherwise, add $j to the $woo_array_cmb_all array.
						
						/*
						if ( in_array( $v['name'], $existing_fields ) ) {} else {
						
							$woo_content_builder_cmb[] = $v;
						
						} // End IF Statement
						*/
						
						if ( in_array( $v['name'], array_keys( $woo_custom_template ) ) ) {} else {
						
							$woo_custom_template[$v['name']] = $v;
						
						} // End IF Statement
					
					} // End FOREACH Loop
					
				} // End IF Statement
				
				update_option( 'woo_custom_template', $woo_custom_template ); // Content Builder-generated custom fields.
			
			} // End IF Statement
			
		} // End IF Statement
		
	}
}

/*-----------------------------------------------------------------------------------*/
/* Listings Deactivation Hook */
/*-----------------------------------------------------------------------------------*/

// Every time a custom field is created, make a backup of the woo_custom_template, for use when re-activating the theme.

if ( ! function_exists( 'listings_content_builder_cmb_backup' ) ) {

	function listings_content_builder_cmb_backup () {
	
		$woo_custom_template = get_option( 'woo_custom_template' );
		
		update_option( 'woo_content_builder_cmb', $woo_custom_template );
	
	} // End listings_content_builder_cmb_backup()
	
} // End IF Statement

/*-----------------------------------------------------------------------------------*/
/* Listings Write Panel Custom Columns */
/*-----------------------------------------------------------------------------------*/

add_filter("manage_edit-listing_columns", "woo_listing_edit_columns");
add_action("manage_posts_custom_column", "woo_listing_custom_columns");
//Add filter to insure the text Listing, or listing, is displayed when user updates a property
add_filter('post_updated_messages', "woo_listing_updated_messages");
    
	//custom post type edit headers
	function woo_listing_edit_columns($columns)
	{	
		global $post;
		
		$wp_custom_post_types_args = array();
		
		$wp_custom_post_types = get_post_types($wp_custom_post_types_args,'objects');
		
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"woo_webref" => "WebRef",
			"title" => "Listing Title",
			"listing_description" => "Description",
			"listing_thumbnail" => "Thumbnail",
		);
		
		if ( isset( $post->post_type ) ) {
			
			$my_current_post_type = $post->post_type;
			if ( isset( $wp_custom_post_types[$my_current_post_type] ) ) {
				$my_post_type_obj = $wp_custom_post_types[$my_current_post_type];
			} // End If Statement
			
		} // End If Statement
		
		if ( ( isset( $my_post_type_obj ) ) && ( isset( $my_post_type_obj->taxonomies ) ) ) {
		
			foreach ($my_post_type_obj->taxonomies as $post_type_taxonomy) {
				$taxonomy_details = get_taxonomy( $post_type_taxonomy );
				
				$taxonomy_name = '';
				if ( isset($taxonomy_details->labels) ) { $taxonomy_name = $taxonomy_details->labels->name; } // End IF Statement
				
				$columns['listing_'.$post_type_taxonomy] = $taxonomy_name;
			} // End For Loop
		
		} // End If Statement
		
		return $columns;
		
	}
	
	// custom post type edit output
	function woo_listing_custom_columns($column)
	{
		global $post;
		
		$wp_custom_post_types_args = array();
		
		$wp_custom_post_types = get_post_types($wp_custom_post_types_args,'objects');
		
		if ( isset( $post->post_type ) ) {
			$my_current_post_type = $post->post_type;
			if ( isset( $wp_custom_post_types[$my_current_post_type] ) ) {
				$my_post_type_obj = $wp_custom_post_types[$my_current_post_type];
			} // End If Statement
		} // End If Statement
		
		switch ($column)
		{
			case "woo_webref":
				echo get_option('listing_property_prefix').$post->ID;
				break;
			case "listing_description":
				the_excerpt();
				break;
			case "listing_thumbnail":
				woo_image('width=100&height=100&class=thumbnail');
				break;
			default :
				$post_type_taxonomy = str_replace( strtolower('listing_'), '', $column );
				
				$taxonomy_details = get_taxonomy( $post_type_taxonomy );
				$items = get_the_terms( $post->ID, $post_type_taxonomy);
				$items_html = array();
				if ($items) {
					foreach ($items as $item)
						
						if ( ! array_key_exists( 'slug', $item ) ) {} else { array_push($items_html, '<a href="' . get_term_link($item->slug, $post_type_taxonomy) . '">' . $item->name . '</a>'); } // End IF Statement
						
					echo implode($items_html, ", ");
				} else {
					_e('None', 'woothemes');;
				}
				break;
		}
	}
	
	function woo_listing_updated_messages( $messages ) {
		
		global $post;
		
		if ( isset($post) && isset($post->ID) ) {
			$post_ID = $post->ID;
		} else {
			$post_ID = 0;
		} // End If Statement
		
  		$messages['listing'] = array(
    			0 => '', // Unused. Messages start at index 1.
    			1 => sprintf( __('Listing updated. <a href="%s">View listing</a>', 'woothemes'), esc_url( get_permalink($post_ID) ) ),
    			2 => __('Custom field updated.', 'woothemes'),
    			3 => __('Custom field deleted.', 'woothemes'),
    			4 => __('Listing updated.', 'woothemes'),
    			/* translators: %s: date and time of the revision */
    			5 => isset($_GET['revision']) ? sprintf( __('Listing restored to revision from %s', 'woothemes'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    			6 => sprintf( __('Listing published. <a href="%s">View Listing</a>', 'woothemes'), esc_url( get_permalink($post_ID) ) ),
    			7 => __('Listing saved.'),
    			8 => sprintf( __('Listing submitted. <a target="_blank" href="%s">Preview Listing</a>', 'woothemes'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    			9 => sprintf( __('Listing scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Listing</a>', 'woothemes'),
    	  			// translators: Publish box date format, see http://php.net/date
     				date_i18n( __( 'M j, Y @ G:i' , 'woothemes'), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    			10 => sprintf( __('Listing draft updated. <a target="_blank" href="%s">Preview Listing</a>', 'woothemes'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  			);

		return $messages;
	}

/*-----------------------------------------------------------------------------------*/
/* Listings Custom Post Type Filters */
/*-----------------------------------------------------------------------------------*/

add_action('restrict_manage_posts', 'woo_listing_restrict_manage_posts');
add_filter('posts_where', 'woo_listing_posts_where');

// The drop down with filter
function woo_listing_restrict_manage_posts() {
    $sentinel = 0;
    ?>
        
            <fieldset>
            <?php
				// Taxonomies
				global $post;
				
				$wp_custom_post_types_args = array();
				
				$wp_custom_post_types = get_post_types($wp_custom_post_types_args,'objects');
				
				if ( isset( $post->post_type ) ) {
					$my_current_post_type = $post->post_type;
					if ( isset( $wp_custom_post_types[$my_current_post_type] ) ) {
						$my_post_type_obj = $wp_custom_post_types[$my_current_post_type];
					} // End If Statement
				} // End If Statement
				
				if ( ( isset( $my_post_type_obj ) ) && ( isset( $my_post_type_obj->taxonomies ) ) ) {
				// Start LOOP
					foreach ($my_post_type_obj->taxonomies as $post_type_taxonomy) {
						$taxonomy_details = get_taxonomy( $post_type_taxonomy );
						
						// heirarchy check
						if ($taxonomy_details) { $sentinel++; }
        				if ( ($taxonomy_details) && ($taxonomy_details->hierarchical > 0) ) {
        					$hierarchical_value = 1;
        				} else {
        					$hierarchical_value = 0;
        				}
        				
        				$taxonomy_nice_name_plural = '';
        					
						if ( isset($taxonomy_details->labels) ) { $taxonomy_nice_name_plural = $taxonomy_details->labels->name; } // End IF Statement
						
						if (isset($_GET[$post_type_taxonomy.'_names'])) { $category_ID = $_GET[$post_type_taxonomy.'_names']; } else { $category_ID = 0; }
            			if ($category_ID > 0) {
            				// Do nothing
            			} else {
            				$category_ID = 0;
            			}
            			$dropdown_options = array	(	
            										'show_option_all'	=> __('View all '.$taxonomy_nice_name_plural), 
            										'hide_empty' 		=> 0, 
            										'hierarchical' 		=> $hierarchical_value,
													'show_count' 		=> 0, 
													'orderby' 			=> 'name',
													'name' 				=> $post_type_taxonomy.'_names',
													'id' 				=> $post_type_taxonomy.'_names',
													'taxonomy' 			=> $post_type_taxonomy, 
													'selected' 			=> $category_ID
													);
						if ($sentinel > 0) {
							wp_dropdown_categories($dropdown_options);
						}
						
					} // End LOOP
					
				} // End If Statement
            ?>
            <?php if ($sentinel > 0) { ?><input type="submit" name="submit" value="<?php _e('Filter') ?>" class="button" /><?php } ?>
        </fieldset>
        
    <?php
}

// Custom Query to filter edit grid
function woo_listing_posts_where($where) {
    if( is_admin() ) {
        global $wpdb;
        
        // Taxonomies
		global $post;
		$wp_custom_post_types_args = array();
		$wp_custom_post_types = get_post_types($wp_custom_post_types_args,'objects');
		$my_current_post_type = $_GET['post_type'];
		$my_post_type_obj = $wp_custom_post_types[$my_current_post_type];
		// Start LOOP
		$sentinel = 0;

		if ( count( $my_post_type_obj->taxonomies ) > 0 ) {

			if ( isset($my_post_type_obj->taxonomies) ) {
			
				foreach ($my_post_type_obj->taxonomies as $post_type_taxonomy) {
					$taxonomy_details = get_taxonomy( $post_type_taxonomy );
							
					$taxonomy_nice_name_plural = $taxonomy_details->labels->name;
	        		if (isset($_GET[$post_type_taxonomy.'_names'])) { $tax_ID = $_GET[$post_type_taxonomy.'_names'];  } else { $tax_ID = '';  }
	        		
	        		if ( $tax_ID > 0 ) {
						
						$item_tax_names =  &get_term( $tax_ID, $post_type_taxonomy );
						$string_post_ids = '';
	 					// taxonomy
						if ($tax_ID > 0) {
							$item_tax_name = $item_tax_names->slug;
							$term_id = $item_tax_names->term_id;
							
							$child_terms = woo_get_taxonomy_child_terms($term_id, 10);
					
							$child_terms_string = '';
							foreach ($child_terms as $child_term) {
								$child_terms_string .= ','.$child_term->term_id;
							}
											
							$querystr = "SELECT $wpdb->posts.ID, $wpdb->posts.post_title FROM $wpdb->posts
											LEFT JOIN $wpdb->postmeta ON($wpdb->posts.ID = $wpdb->postmeta.post_id)
							    			LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
							    			LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
							    			WHERE $wpdb->term_taxonomy.term_id IN ($term_id$child_terms_string)
							    				AND $wpdb->term_taxonomy.taxonomy = '$post_type_taxonomy'
							    				AND $wpdb->posts.post_status = 'publish'
							    			ORDER BY $wpdb->posts.ID ASC
										";
											
							$posts_array = $wpdb->get_results($querystr, OBJECT);
				
							$temp_array = array();
							foreach	($posts_array as $post_item) {
								$string_post_ids .= $post_item->ID.',';
								$sentinel++;
							}
							
							
						}
						
					}
				
	        		
	        	} // End LOOP
	        	
        	} // End If Statement
        	
        } // End IF Statement
        
        if ($sentinel > 0) {
        	$string_post_ids = chop($string_post_ids,',');
        	$where .= " AND $wpdb->posts.ID IN (" . $string_post_ids . ")";
   			
        }
        
    }
    
    return $where;
}

function woo_get_posts_in_taxonomy($term_id, $taxonomy, $post_type = 'post', $limit = 900000) {
	
	global $wpdb;
	
	/* DEPRECATED - OLD QUERY
	$querystr = "SELECT $wpdb->posts.ID, $wpdb->posts.post_title, $wpdb->posts.post_date FROM $wpdb->posts
					LEFT JOIN $wpdb->postmeta ON($wpdb->posts.ID = $wpdb->postmeta.post_id)
		   			LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
		   			LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
		   			WHERE $wpdb->term_taxonomy.term_id IN ($term_id)
		   				AND $wpdb->term_taxonomy.taxonomy = '$taxonomy'
		   				AND $wpdb->posts.post_status = 'publish'
		   				AND $wpdb->posts.post_type = '$post_type'
		   			ORDER BY $wpdb->posts.post_date DESC LIMIT $limit
				";
	
	$posts_array = $wpdb->get_results($querystr, OBJECT);*/
	
	$query_args = array(	'post_type' => $post_type,
	    		    		'tax_query' => array(
													array(
														'taxonomy' => $taxonomy,
														'field' => 'id',
														'terms' => $term_id
													)
												),
	    		    		'posts_per_page' 	=> $limit,
	    		    		'orderby' 			=> 'date',
	    		    		'order'				=> 'DESC',
	    		    		'post_status'		=> 'publish'
	    		    		);
	    		    					
	$related_query = new WP_Query($query_args);
	$posts_array = array();
	
	if ($related_query->have_posts()) { $count = 0;

		while ($related_query->have_posts()) { 
			
			$related_query->the_post();
			
			array_push($posts_array, array('ID' => get_the_ID(), 'title' => get_the_title(), 'date' => get_the_date('m\/d\/Y') ));
			
		} // End While Loop
		
	} // End If Statement
	
	
	return $posts_array;
	
}

/*-----------------------------------------------------------------------------------*/
/* Category to Color matrix */
/*-----------------------------------------------------------------------------------*/

// return the color dependant no the cat passed
function cat_to_color($cat_object){

	$custom = get_option('woo_cat_custom_marker_' . $cat_object[0]->term_id);
	if(!empty($custom)){
		$color = $custom;
	}
	else {
		$color = get_option('woo_cat_colors_' . $cat_object[0]->term_id);
	}
	 
	return $color;
	
}


function custom_markers_admin_head(){
	?>
	<style type="text/css">
		#woo-option-coloredcustommarkers .section-text{ border:none;}
		#woo-option-coloredcustommarkers .section-text h3{ display:none}
		
	</style>
	<?php
}
add_action('admin_head','custom_markers_admin_head');


/*-----------------------------------------------------------------------------------*/
/* WooThemes Google Maps Functionality */
/*-----------------------------------------------------------------------------------*/

function woo_maps_single_output($args){

	$key = get_option('woo_maps_apikey');
	
	// No More API Key needed
	
	if ( !is_array($args) ) 
		parse_str( $args, $args );
		
	extract($args);	
		
	$map_height = get_option('woo_maps_single_height');
	$featured_w = get_option('woo_home_featured_w');
	$featured_h = get_option('woo_home_featured_h');
	   
	$lang = get_option('woo_maps_directions_locale');
	$locale = '';
	if(!empty($lang)){
		$locale = ',locale :"'.$lang.'"';
	}
	$extra_params = ',{travelMode:G_TRAVEL_MODE_WALKING,avoidHighways:true '.$locale.'}';
	
	if(is_home() OR is_front_page()) { $map_height = get_option('woo_home_featured_h'); }
	if(empty($map_height)) { $map_height = 250;}
	
	if(is_home() && !empty($featured_h) && !empty($featured_w)){
	?>
    <div id="single_map_canvas" style="width:<?php echo $featured_w; ?>px; height: <?php echo $featured_h; ?>px"></div>
    <?php } else { ?> 
    <div id="single_map_canvas" style="width:100%; height: <?php echo $map_height; ?>px"></div>
    <?php } ?>
    <script src="<?php bloginfo('template_url'); ?>/includes/js/markers.js" type="text/javascript"></script>
    <script type="text/javascript">
		jQuery(document).ready(function(){
			function initialize() {
				
				
			<?php if($streetview == 'on'){ ?>

				var location = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
				
				<?php 
				// Set defaults if no value
				if ($yaw == '') { $yaw = 20; }
				if ($pitch == '') { $pitch = -20; }
				?>
				
				var panoramaOptions = {
  					position: location,
  					pov: {
    					heading: <?php echo $yaw; ?>,
    					pitch: <?php echo $pitch; ?>,
    					zoom: 1
  					}
				};
				
				var map = new google.maps.StreetViewPanorama(document.getElementById("single_map_canvas"), panoramaOptions);
				
		  		google.maps.event.addListener(map, 'error', handleNoFlash);
				
				<?php if(get_option('woo_maps_scroll') == 'true'){ ?>
			  	map.scrollwheel = false;
			  	<?php } ?>
				
			<?php } else { ?>
				
			  	<?php switch ($type) {
			  			case 'G_NORMAL_MAP':
			  				$type = 'ROADMAP';
			  				break;
			  			case 'G_SATELLITE_MAP':
			  				$type = 'SATELLITE';
			  				break;
			  			case 'G_HYBRID_MAP':
			  				$type = 'HYBRID';
			  				break;
			  			case 'G_PHYSICAL_MAP':
			  				$type = 'TERRAIN';
			  				break;
			  			default:
			  				$type = 'ROADMAP';
			  				break;
			  	} ?>
			  	
			  	var myLatlng = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
				var myOptions = {
				  zoom: <?php echo $zoom; ?>,
				  center: myLatlng,
				  mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>
				};
			  	var map = new google.maps.Map(document.getElementById("single_map_canvas"),  myOptions);
				<?php if(get_option('woo_maps_scroll') == 'true'){ ?>
			  	map.scrollwheel = false;
			  	<?php } ?>
			  	
				<?php if($mode == 'directions'){ ?>
			  	directionsPanel = document.getElementById("featured-route");
 				directions = new GDirections(map, directionsPanel);
  				directions.load("from: <?php echo $from; ?> to: <?php echo $to; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
			  	<?php
			 	} else { ?>
			 
			  		var point = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
	  				var root = "<?php bloginfo('template_url'); ?>";
	  				var the_link = '<?php echo get_permalink(get_the_id()); ?>';
	  				<?php $title = str_replace(array('&#8220;','&#8221;'),'"',get_the_title(get_the_id())); ?>
	  				<?php $title = str_replace('&#8211;','-',$title); ?>
	  				<?php $title = str_replace('&#8217;',"`",$title); ?>
	  				<?php $title = str_replace('&#038;','&',$title); ?>
	  				var the_title = '<?php echo html_entity_decode($title) ?>'; 
	  				
	  			<?php		 	
			 	if(is_page()){ 
			 		$custom = get_option('woo_cat_custom_marker_pages');
					if(!empty($custom)){
						$color = $custom;
					}
					else {
						$color = get_option('woo_cat_colors_pages');
						if (empty($color)) {
							$color = 'red';
						}
					}			 	
			 	?>
			 		var color = '<?php echo $color; ?>';
			 		createMarker(map,point,root,the_link,the_title,color);
			 	<?php } else { ?>
			 		var color = '<?php echo get_option('woo_cat_colors_pages'); ?>';
	  				createMarker(map,point,root,the_link,the_title,color);
				<?php 
				}
					if(isset($_POST['woo_maps_directions_search'])){ ?>
					
					directionsPanel = document.getElementById("featured-route");
 					directions = new GDirections(map, directionsPanel);
  					directions.load("from: <?php echo htmlspecialchars($_POST['woo_maps_directions_search']); ?> to: <?php echo $address; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
  					
  					
  					
					directionsDisplay = new google.maps.DirectionsRenderer();
					directionsDisplay.setMap(map);
    				directionsDisplay.setPanel(document.getElementById("featured-route"));
					
					<?php if($walking == 'on'){ ?>
					var travelmodesetting = google.maps.DirectionsTravelMode.WALKING;
					<?php } else { ?>
					var travelmodesetting = google.maps.DirectionsTravelMode.DRIVING;
					<?php } ?>
					var start = '<?php echo htmlspecialchars($_POST['woo_maps_directions_search']); ?>';
					var end = '<?php echo $address; ?>';
					var request = {
       					origin:start, 
        				destination:end,
        				travelMode: travelmodesetting
    				};
    				directionsService.route(request, function(response, status) {
      					if (status == google.maps.DirectionsStatus.OK) {
        					directionsDisplay.setDirections(response);
      					}
      				});	
      				
  					<?php } ?>			
				<?php } ?>
			<?php } ?>
			

			  }
			  function handleNoFlash(errorCode) {
				  if (errorCode == FLASH_UNAVAILABLE) {
					alert("Error: Flash doesn't appear to be supported by your browser");
					return;
				  }
				 }

			
		
		initialize();
			
		});
	jQuery(window).load(function(){
			
		var newHeight = jQuery('#featured-content').height();
		newHeight = newHeight - 5;
		if(newHeight > 300){
			jQuery('#single_map_canvas').height(newHeight);
		}
		
	});

	</script>

<?php
}

function woothemes_metabox_maps_create() {
    global $post;
	$enable = get_post_meta($post->ID,'woo_maps_enable',true);
	$streetview = get_post_meta($post->ID,'woo_maps_streetview',true);
	$address = get_post_meta($post->ID,'woo_maps_address',true);
	$long = get_post_meta($post->ID,'woo_maps_long',true);
	$lat = get_post_meta($post->ID,'woo_maps_lat',true);
	$zoom = get_post_meta($post->ID,'woo_maps_zoom',true);
	$type = get_post_meta($post->ID,'woo_maps_type',true);
	$walking = get_post_meta($post->ID,'woo_maps_walking',true);
	
	$yaw = get_post_meta($post->ID,'woo_maps_pov_yaw',true);
	$pitch = get_post_meta($post->ID,'woo_maps_pov_pitch',true);
	
	$from = get_post_meta($post->ID,'woo_maps_from',true);
	$to = get_post_meta($post->ID,'woo_maps_to',true);
	
	if(empty($zoom)) $zoom = get_option('woo_maps_default_mapzoom');
	if(empty($type)) $type = get_option('woo_maps_default_maptype');
	if(empty($pov)) $pov = 'yaw:0,pitch:0';


	
	$key = get_option('woo_maps_apikey');
	
	// No More API Key needed
	
	?>

    
    
    <?php
    $mode = get_post_meta($post->ID,'woo_maps_mode',true); 
    if($mode == 'plot'){ $directions = 'not-active'; $plot = 'active'; }
    elseif($mode == 'directions'){ $directions = 'active'; $plot = 'not-active'; }
    else {$directions = 'not-active'; $plot = 'active';}

    ?>


	<table><tr><td><strong>Enable map on this post: </strong></td>
    <td><input class="address_checkbox" type="checkbox" name="woo_maps_enable" id="woo_maps_enable" <?php if($enable == 'on'){ echo 'checked=""';} ?> /></td></tr>
    <tr><td><strong>This map will be in Streetview: </strong></td>
    <td><input class="address_checkbox" type="checkbox" name="woo_maps_streetview" id="woo_maps_streetview" <?php if($streetview == 'on'){ echo 'checked=""';} ?> /></td></tr>
    <tr class="hidden"><td><strong>Outputs directions for walking: </strong></td>
    <td><input class="address_checkbox" type="checkbox" name="woo_maps_walking" id="woo_maps_walking" <?php if($walking == 'on'){ echo 'checked=""';} ?> /></td></tr>
    
    </table>
    
    <div id="map_mode">
    	<ul>
    		<li><a class="<?php echo $plot; ?>" href="#" id="woo_plot_point">Plot Point</a></li>
    		<li class="hidden"><a class="<?php echo $directions; ?>" href="#" id="woo_directions_map">Directions Map</a></li>
    	</ul>
    </div>
   	<div class="woo_maps_search">
    <table><tr><td width="200"><b>Search for an address:</b></td>
    <td><input class="address_input" type="text" size="40" value="" name="woo_maps_search_input" id="woo_maps_search_input"/><span class="button" id="woo_maps_search">Plot</span>
    </td></tr></table>
    </div>
	<div id="woo_maps_holder" class="woo_maps_style" >
    <ul>
    	<li class="woo_plot <?php echo $plot; ?>">
    		<label>Address Name:</label>
    		<input class="address_input" type="text" size="40" name="woo_maps_address" id="woo_maps_address" value="<?php echo $address; ?>" />
    	</li>
    	<li>
    		<label>Latitude: <small class="woo_directions">Center Point</small></label>
    		<input class="address_input" type="text" size="40" name="woo_maps_lat" id="woo_maps_lat" value="<?php echo $lat; ?>"/>
    	</li>
    	<li>
    		<label>Longitude: <small class="woo_directions">Center Point</small></label>
    		<input class="address_input" type="text" size="40" name="woo_maps_long" id="woo_maps_long" value="<?php echo $long; ?>"/>
    	</li>
        <li class="woo_plot <?php echo $plot; ?>">
    		<label>Point of View: Yaw</label>    	
    		<input class="address_input" type="text" name="woo_maps_pov_yaw" id="woo_maps_pov_yaw" size="40" value="<?php echo $yaw;  ?>" />
      		<small>Streetview</small>	
      	</li>
        <li class="woo_plot <?php echo $plot; ?>">
    		<label>Point of View: Pitch</label>    		
    		<input class="address_input" type="text" name="woo_maps_pov_pitch" id="woo_maps_pov_pitch" size="40" value="<?php echo $pitch;  ?>">
      		<small>Streetview</small>
      	</li>
    	<li class="woo_directions <?php echo $directions; ?>">
    		<label>From:</label>
			<input class="address_input current_input" type="text" size="40" name="woo_maps_from" id="woo_maps_from" value="<?php echo $from; ?>"/>
    	</li>
    	<li class="woo_directions <?php echo $directions; ?>">
    		<label>To:</label>
    		<input class="address_input" type="text" size="40" name="woo_maps_to" id="woo_maps_to" value="<?php echo $to; ?>"/>
    	</li>
    	 <li>
    		<label>Zoom Level:</label>
    		<select class="address_select" style="width:120px" name="woo_maps_zoom" id="woo_maps_zoom">
    			<?php 
				for($i = 0; $i < 20; $i++) {
					if($i == $zoom){ $selected = 'selected="selected"';} else { $selected = '';} ?>
		 			<option value="<?php echo $i; ?>" <?php echo $selected; ?>><?php echo $i; ?></option>
    				<?php } ?>
    		</select>
    	</li>
    	<li>
	  		<label>Map Type:</label>
    		<select class="address_select" style="width:120px" name="woo_maps_type" id="woo_maps_type">
   			<?php
			$map_types = array('Normal' => 'G_NORMAL_MAP','Satellite' => 'G_SATELLITE_MAP','Hybrid' => 'G_HYBRID_MAP','Terrain' => 'G_PHYSICAL_MAP',); 
			foreach($map_types as $k => $v) {
				if($type == $v){ $selected = 'selected="selected"';} else { $selected = '';} ?>
				<option value="<?php echo $v; ?>" <?php echo $selected; ?>><?php echo $k; ?></option>
    		<?php } ?>
    		</select>
 		</li>

 	</ul> 
 	<input type="hidden" value="<?php echo $mode; ?>" id="woo_maps_mode" name="woo_maps_mode" />
    </div>
    
    <div id="map_canvas" style="width: 100%; height: 250px"></div>
    <div name="pano" id="pano" style="width: 100%; height:250px"></div>

    <?php
	
}


function woothemes_metabox_maps_header(){
	global $post;  
    $pID = $post->ID; 
	$key = get_option('woo_maps_apikey');
	
	// No More API Key needed
	
	?>
    <script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    <script type="text/javascript">
	jQuery(document).ready(function(){
		var map;
		var geocoder;
		var address;
		var pano;
		var location;
		var markersArray = [];
		
		<?php 
		$mode = get_post_meta($pID,'woo_maps_mode',true);
		if($mode == 'directions'){ ?>
		var mode = 'directions';
		<?php } else { ?>
		var mode = 'plot';
		<?php } ?>
		
		jQuery('#map_mode a').click(function(){
		
			var mode_set = jQuery(this).attr('id');
			if(mode_set == 'woo_directions_map'){
				mode = 'directions';
				jQuery('.woo_plot').hide();
				jQuery('.woo_directions').show();
				jQuery('#woo_maps_mode').val('directions');

			}
			else {
				mode = 'plot';
				jQuery('.woo_plot').show();
				jQuery('.woo_directions').hide();
				jQuery('#woo_maps_mode').val('plot');
			}
			
			jQuery('#map_mode a').removeClass('active');
			jQuery(this).addClass('active');
		
			return false;
		});
		
		jQuery('#woo_maps_to').focus(function(){
			jQuery('#woo_maps_from').removeClass('current_input');
			jQuery(this).addClass('current_input');
		});
		jQuery('#woo_maps_from').focus(function(){
			jQuery('#woo_maps_to').removeClass('current_input');
			jQuery(this).addClass('current_input');
		});
	
		function initialize() {
		  
		  <?php 
		  $lat = get_post_meta($pID,'woo_maps_lat',true);
		  $long = get_post_meta($pID,'woo_maps_long',true);
		  $yaw = get_post_meta($pID,'woo_maps_pov_yaw',true);
		  $pitch = get_post_meta($pID,'woo_maps_pov_pitch',true);
		 
		  if(empty($long) && empty($lat)){
		  	//Defaults...
			$lat = '40.7142691';
			$long = '-74.0059729';
			$zoom = get_option('woo_maps_default_mapzoom');
		  } else { 
		  	$zoom = get_post_meta($pID,'woo_maps_zoom',true); 
		  }
		  if(empty($yaw) OR empty($pitch)){
		  	$pov = 'yaw:20,pitch:-20';
		  } else {
		  	$pov = 'yaw:' . $yaw . ',pitch:' . $pitch;
		  }
		  
		  ?>
		  
		  // Manage API V2 existing data
		  <?php switch ($type) {
				case 'G_NORMAL_MAP':
					$type = 'ROADMAP';
					break;
				case 'G_SATELLITE_MAP':
					$type = 'SATELLITE';
					break;
				case 'G_HYBRID_MAP':
					$type = 'HYBRID';
					break;
				case 'G_PHYSICAL_MAP':
					$type = 'TERRAIN';
					break;
				default:
					$type = 'ROADMAP';
		  			break;
		  } ?>
		  
		  // Create Standard Map
		  location = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
		  var myOptions = {
		  		zoom: <?php echo $zoom; ?>,
		  		center: location,
		  		mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>,
		  		streetViewControl: false
		  };
		  map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
		  
      	  <?php
      	  // Set defaults if no value
		  if ($yaw == '') { $yaw = 20; }
		  if ($pitch == '') { $pitch = -20; }
		  ?>
		  
		  // Create StreetView Map		
		  var panoramaOptions = {
  		  	position: location,
  			pov: {
    			heading: <?php echo $yaw; ?>,
    			pitch: <?php echo $pitch; ?>,
    			zoom: 1
  			}
		  };	
		  pano = new google.maps.StreetViewPanorama(document.getElementById("pano"), panoramaOptions);
		  
		  // Set initial Zoom Levels
		  var z = map.getZoom();        
          jQuery('#woo_maps_zoom option').removeAttr('selected');
          jQuery('#woo_maps_zoom option[value="'+z+'"]').attr('selected','selected');
      	  
      	  // Event Listener - StreetView POV Change
      	  google.maps.event.addListener(pano, 'pov_changed', function(){
      	  	var headingCell = document.getElementById('heading_cell');
      		var pitchCell = document.getElementById('pitch_cell');
      	  	jQuery("#woo_maps_pov_yaw").val(pano.getPov().heading);
     	  	jQuery("#woo_maps_pov_pitch").val(pano.getPov().pitch);
     	  	
      	  });
      	  
      	  // Event Listener - Standard Map Zoom Change
      	  google.maps.event.addListener(map, 'zoom_changed', function(){
      	  	var z = map.getZoom();        
        	jQuery('#woo_maps_zoom option').removeAttr('selected');
        	jQuery('#woo_maps_zoom option[value="'+z+'"]').attr('selected','selected');
      	  });
      	  
      	  // Event Listener - Standard Map Click Event
      	  geocoder = new google.maps.Geocoder();
      	  google.maps.event.addListener(map, "click", getAddress);
      	
		} // End initialize() function
		
		// Adds the overlays to the map, and in the array
		function addMarker(location) {
  			marker = new google.maps.Marker({
    			position: location,
    			map: map
  			});
  			markersArray.push(marker);
		} // End addMarker() function
		  
		// Removes the overlays from the map, but keeps them in the array
		function clearOverlays() {
  			if (markersArray) {
    			for (i in markersArray) {
      				markersArray[i].setMap(null);
    			}
  			}
		} // End clearOverlays() function
		
		// Deletes all markers in the array by removing references to them
		function deleteOverlays() {
		 	if (markersArray) {
		    	for (i in markersArray) {
		      		markersArray[i].setMap(null);
		    	}
		    	markersArray.length = 0;
		  	}
		} // End deleteOverlays() function

		// Shows any overlays currently in the array
		function showOverlays() {
  			if (markersArray) {
    			for (i in markersArray) {
      				markersArray[i].setMap(map);
    			}
  			}
		} // End showOverlays() function
		
		// Sets initial marker on centre point
		function setSavedAddress() {
			point = new google.maps.LatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
		 	addMarker(point);
  		} // End setSavedAddress() function
		
		// Click event for address
		function getAddress(event) {
		  	
		  	clearOverlays();
		  	point = new google.maps.LatLng(event.latLng.lat(),event.latLng.lng());
		 	addMarker(point);
		  	if(mode == 'directions'){
				jQuery('#woo_maps_lat').attr('value',event.latLng.lat());
				jQuery('#woo_maps_long').attr('value',event.latLng.lng());

			} else {
				jQuery('#woo_maps_lat').attr('value',event.latLng.lat());
				jQuery('#woo_maps_long').attr('value',event.latLng.lng());
			}
			
		  	if (event.latLng != null) {
				address = event.latLng;
				geocoder.geocode( { 'location': address}, showAddress);
		  	}
		  	if (event.latLng) {
		  		pano.setPosition(event.latLng);
		  		pano.setPov({heading:<?php echo $yaw; ?>,pitch:<?php echo $pitch; ?>,zoom:1});
		  	}
		} // End getAddress() function
		
		// Updates fields with address data
		function showAddress(results, status) {
			
			if (status == google.maps.GeocoderStatus.OK) {
        		deleteOverlays();
        		
        		map.setCenter(results[0].geometry.location);
        			
        		addMarker(results[0].geometry.location);
        				
        		place = results[0].formatted_address;
        		latlngplace = results[0].geometry.location;
        				
				if(mode == 'directions'){
					jQuery('.current_input').attr('value',place);
				} else {
					jQuery('#woo_maps_address').attr('value',place);
				}
        					
        	} else {
        		alert("Status Code:" + status);
        		
        	}
		} // End showAddress() function
		
		// addAddressToMap() is called when the geocoder returns an
		// answer.  It adds a marker to the map.
		function addAddressToMap(results, status) {
		  
		  deleteOverlays();
		  if (status != google.maps.GeocoderStatus.OK) {
			alert("Sorry, we were unable to geocode that address");
		  } else {
			place = results[0].formatted_address;
			point = results[0].geometry.location;					
			
			addMarker(point);
	
			map.setCenter(point, <?php echo $zoom; ?>);
			pano.setPosition(point);
		  	pano.setPov({heading:<?php echo $yaw; ?>,pitch:<?php echo $pitch; ?>,zoom:1});
		  					
			if(mode == 'directions'){
				
				jQuery('.current_input').attr('value',place);
				jQuery('#woo_maps_lat').attr('value',point.lat());
				jQuery('#woo_maps_long').attr('value',point.lng());
		
			} else {
				jQuery('#woo_maps_address').attr('value',place);
				jQuery('#woo_maps_lat').attr('value',point.lat());
				jQuery('#woo_maps_long').attr('value',point.lng());
			}
			
		  }
		}
	
		// >> PLOT
		// showLocation() is called when you click on the Search button
		// in the form.  It geocodes the address entered into the form
		// and adds a marker to the map at that location.
		function showLocation() {
		  var address = jQuery('#woo_maps_search_input').attr('value');
		  geocoder.geocode( { 'address': address}, addAddressToMap);
		}
		initialize();
		setSavedAddress();
		
		// >> PLOT
		//Click on the "Plot" button	
		jQuery('#woo_maps_search').click(function(){
		
			showLocation();
	
		})
		
	});
	
    </script>
	<style type="text/css">
		#map_canvas { margin:10px 0}
		.woo_maps_bubble_address { font-size:16px}
		.woo_maps_style { padding: 10px}
		.woo_maps_style ul li label { width: 150px; float:left; display: block}
		.woo_maps_search { border-bottom:1px solid #e1e1e1; padding: 10px}
		
		#woo_maps_holder .not-active{ display:none }
		
		#map_mode { height: 38px; margin: 10px 0; background: #f1f1f1; padding-top: 10px}
		#map_mode ul li { float:left;  margin-bottom: 0;}
		#map_mode ul li a {padding: 10px 15px; display: block;text-decoration: none;   margin-left: 10px }
		#map_mode a.active { color: black;background: #fff;border: solid #e1e1e1; border-width: 1px 1px 0px 1px; }
		.current_input { background: #E9F2FA!important}
		
	</style>
	
	<?php
}

function woothemes_metabox_maps_handle(){   
    
    global $globals;  
    $pID = $_POST['post_ID'];
    $woo_map_input_names = array('woo_maps_enable','woo_maps_streetview','woo_maps_address','woo_maps_from','woo_maps_to','woo_maps_long','woo_maps_lat','woo_maps_zoom','woo_maps_type','woo_maps_mode','woo_maps_pov_pitch','woo_maps_pov_yaw','woo_maps_walking');
	
    
    if ($_POST['action'] == 'editpost'){                                   
        foreach ($woo_map_input_names as $name) { // On Save.. this gets looped in the header response and saves the values submitted
  
				$var = $name;
				if (isset($_POST[$var])) {            
					if( get_post_meta( $pID, $name ) == "" )
						add_post_meta($pID, $name, $_POST[$var], true );
					elseif($_POST[$var] != get_post_meta($pID, $name, true))
						update_post_meta($pID, $name, $_POST[$var]);
					elseif($_POST[$var] == "") {
					   delete_post_meta($pID, $name, get_post_meta($pID, $name, true));
					}
				}
				elseif(!isset($_POST[$var]) && $name == 'woo_maps_enable') { 
					update_post_meta($pID, $name, 'false'); 
				}     
				else {
					  delete_post_meta($pID, $name, get_post_meta($pID, $name, true)); // Deletes check boxes OR no $_POST
				}  
                
            }
        }
}

function woothemes_metabox_maps_add() {
    if ( function_exists('add_meta_box') ) {
        
        // Other Post Types
        if (isset($_GET['post'])) {
			$post_item = get_post($_GET['post']);
			$post_type = $post_item->post_type;
		} else {
			$post_type = $_GET['post_type'];
		}
		if ( $post_type != '' ) {
			
			$plugin_page_cpt = add_meta_box('woothemes-maps',get_option('woo_themename').' Custom Maps', 'woothemes_metabox_maps_create', $post_type, 'normal');
			add_action('admin_head-'. $plugin_page_cpt, 'woothemes_metabox_maps_header' );
		
		} else {
			$custom_fields = get_option('woo_custom_template');
			foreach ($custom_fields as $custom_field) {
				if ($custom_field['type'] == 'googlemap') {
					if ($custom_field['cpt']['post'] == 'true') {
						$plugin_page = add_meta_box('woothemes-maps',get_option('woo_themename').' Custom Maps','woothemes_metabox_maps_create','post','normal');
    					add_action('admin_head-'. $plugin_page, 'woothemes_metabox_maps_header' );
					}
				}
			}
		}
		
	   //add_meta_box('woothemes-settings',get_option('woo_themename').' Custom Settings','woothemes_metabox_create','page','normal');
    }
}

add_action('init','woo_check_for_google_maps');
add_action('edit_post', 'woothemes_metabox_maps_handle');

// Check if Post Type Support Google Maps
function woo_check_for_google_maps() {

	if (isset($_GET['post'])) {
		$post_item = get_post($_GET['post']);
		$post_type = $post_item->post_type;
	} // End If Statement
	$custom_fields = get_option('woo_custom_template');
	$supports_google_maps = false;
	if (is_array($custom_fields)) {
		foreach ($custom_fields as $custom_field) {
			if ($custom_field['type'] == 'googlemap') {
				if (isset($_GET['post_type'])) {
					$post_type = $_GET['post_type'];
				} // End If Statement
				if ( isset( $post_type ) ) {
					if ( isset( $custom_field['cpt'][$post_type] ) || isset( $custom_field['cpt']['post'] ) ) {
						if ($custom_field['cpt'][$post_type] == 'true') {
							$supports_google_maps = true;
						} elseif ($custom_field['cpt']['post'] == 'true') {
							$supports_google_maps = true;
						} // End If Statement	
					} // End If Statement
				} // End If Statement
			} // End If Statement
		} // End For Loop
	} // End If Statement
	if ($supports_google_maps) {
	
		
		add_action('admin_menu', 'woothemes_metabox_maps_add'); // Triggers Woothemes_metabox_create
		add_action('admin_enqueue_scripts','woo_maps_enqueue',10,1);
	
	} // End If Statement

}

function woo_maps_enqueue($hook) {
	if ($hook == 'post.php' OR $hook == 'post-new.php' OR $hook == 'page.php' OR $hook == 'page-new.php') {
	   	add_action('admin_head', 'woothemes_metabox_maps_header');
	} // End If Statement
}


/*-----------------------------------------------------------------------------------*/
/* Thickbox Styles */
/*-----------------------------------------------------------------------------------*/

function thickbox_style() {
    ?>
    <link rel="stylesheet" href="<?php echo get_bloginfo('siteurl'); ?>/wp-includes/js/thickbox/thickbox.css" type="text/css" media="screen" />
    <script type="text/javascript">
    	var tb_pathToImage = "<?php echo get_bloginfo('siteurl'); ?>/wp-includes/js/thickbox/loadingAnimation.gif";
    	var tb_closeImage = "<?php echo get_bloginfo('siteurl'); ?>/wp-includes/js/thickbox/tb-close.png"
    </script>
    <?php
}

add_action('wp_head','thickbox_style');

/*-----------------------------------------------------------------------------------*/
/* Latest Listings Dashboard Widget */
/*-----------------------------------------------------------------------------------*/

add_action('wp_dashboard_setup', 'woo_add_dashboard_widgets' );

function woo_add_dashboard_widgets() {
	// Recent Listings
	if ( current_user_can('edit_posts') )
		wp_add_dashboard_widget( 'dashboard_recent_listings', __('Recent Listings'), 'woo_dashboard_recent_listings' );
}

function woo_dashboard_recent_listings( $drafts = false ) {
	if ( !$drafts ) {
		$drafts_query = new WP_Query( array(
			'post_type' => 'listing',
			'post_status' => 'draft',
			'author' => $GLOBALS['current_user']->ID,
			'posts_per_page' => 5,
			'orderby' => 'modified',
			'order' => 'DESC'
		) );
		$drafts =& $drafts_query->posts;
	}

	if ( $drafts && is_array( $drafts ) ) {
		$list = array();
		foreach ( $drafts as $draft ) {
			$url = get_edit_post_link( $draft->ID );
			$title = _draft_or_post_title( $draft->ID );
			$item = "<h4><a href='$url' title='" . sprintf( __( 'Edit &#8220;%s&#8221;' ), esc_attr( $title ) ) . "'>" . esc_html($title) . "</a> <abbr title='" . get_the_time(__('Y/m/d g:i:s A'), $draft) . "'>" . get_the_time( get_option( 'date_format' ), $draft ) . '</abbr></h4>';
			if ( $the_content = preg_split( '#\s#', strip_tags( $draft->post_content ), 11, PREG_SPLIT_NO_EMPTY ) )
				$item .= '<p>' . join( ' ', array_slice( $the_content, 0, 10 ) ) . ( 10 < count( $the_content ) ? '&hellip;' : '' ) . '</p>';
			$list[] = $item;
		}
?>
	<ul>
		<li><?php echo join( "</li>\n<li>", $list ); ?></li>
	</ul>
	<p class="textright"><a href="edit.php?post_type=listing&post_status=draft" class="button"><?php _e('View all'); ?></a></p>
<?php
	} else {
		_e('There are no drafts at the moment');
	}
}

/*-----------------------------------------------------------------------------------*/
/* Latest Posts Custom Query */
/*-----------------------------------------------------------------------------------*/

/*
* Takes 3 Arguments
* $custom_post_types is an array as follows: array('slug' => 'Nice Name')
* $taxonomies is an array as follows: array()
* $number_of_results is an integer
*/
function woo_latest_posts_custom_query( $custom_post_types = array(), $taxonomies = array(), $number_of_results = 0 ) {

	global $wpdb;
	
	// Build SQL for Post Types
	$post_type_sql = "";
	$post_type_counter = 0;
	foreach ($custom_post_types as $key => $value) {
		// Prefix
		if ($post_type_counter > 0) {
			$prefix = " OR";
		} else {
			$prefix = "";
		}
		$post_type_sql .= "$prefix $wpdb->posts.post_type = '$key'";
		$post_type_counter++;
	}
	
	// Build SQL for Taxonomies
	$taxonomy_sql = "";
	$term_sql = "";
	$taxonomy_counter = 0;
	foreach ($taxonomies as $taxonomy_item) {
		foreach ($taxonomy_item as $taxonomy) {
			// OBJECT FIELDS - term_id, name, slug, term_group, term_taxonomy_id, taxonomy, description, parent, count
			$term_slug = $taxonomy->slug;
			$taxonomy_slug = $taxonomy->taxonomy;
			// Prefix
			if ($taxonomy_counter > 0) {
				$prefix = " OR";
			} else {
				$prefix = "";
			}
			$taxonomy_sql .= "$prefix $wpdb->term_taxonomy.taxonomy = '$taxonomy_slug'";
			$term_sql .= "$prefix $wpdb->terms.slug = '$term_slug'";
			$taxonomy_counter++;
		}
	}
	
	// Build SQL for Number of Results
	if ($number_of_results > 0) {
		$number_of_results_sql = "LIMIT $number_of_results";
	} else {
		$number_of_results_sql = "";
	}

	// SQL QUERY
	$sql = "SELECT * FROM $wpdb->posts
				LEFT JOIN $wpdb->term_relationships ON($wpdb->posts.ID = $wpdb->term_relationships.object_id)
				LEFT JOIN $wpdb->term_taxonomy ON($wpdb->term_relationships.term_taxonomy_id = $wpdb->term_taxonomy.term_taxonomy_id)
				LEFT JOIN $wpdb->terms ON($wpdb->term_taxonomy.term_id = $wpdb->terms.term_id)
			WHERE ( $post_type_sql )
				AND ( $wpdb->posts.post_status = 'publish' )
				AND ( ( $taxonomy_sql )
				AND ( $term_sql ) )
			ORDER BY $wpdb->posts.post_date DESC $number_of_results_sql";

	$latest_posts = $wpdb->get_results($sql, OBJECT);
	
	if ($latest_posts) {
		$return_value = $latest_posts;
	} else {
		$return_value = false;
	}
	
	return $return_value;
	
}

/*-----------------------------------------------------------------------------------*/
/* Subscribe / Connect */
/*-----------------------------------------------------------------------------------*/

if (!function_exists( 'woo_subscribe_connect')) {
	function woo_subscribe_connect($widget = 'false', $title = '', $form = '', $social = '') {

		//Setup default variables, overriding them if the "Theme Options" have been saved.
		$settings = array(
						'connect' => 'false', 
						'connect_title' => __('Subscribe' , 'woothemes'), 
						'connect_related' => 'true', 
						'connect_content' => __( 'Subscribe to our e-mail newsletter to receive updates.', 'woothemes' ),
						'connect_newsletter_id' => '', 
						'connect_mailchimp_list_url' => '',
						'feed_url' => '',
						'connect_rss' => '',
						'connect_twitter' => '',
						'connect_facebook' => '',
						'connect_youtube' => '',
						'connect_flickr' => '',
						'connect_linkedin' => '',
						'connect_delicious' => '',
						'connect_rss' => '',
						'connect_googleplus' => ''
						);
		$settings = woo_get_dynamic_values( $settings );

		// Setup title
		if ( $widget != 'true' )
			$title = $settings[ 'connect_title' ];

		// Setup related post (not in widget)
		$related_posts = '';
		if ( $settings[ 'connect_related' ] == "true" AND $widget != "true" )
			$related_posts = do_shortcode( '[related_posts limit="5"]' );

?>
	<?php if ( $settings[ 'connect' ] == "true" OR $widget == 'true' ) : ?>
	<div id="connect">
		<h3><?php if ( $title ) echo apply_filters( 'widget_title', $title ); else _e('Subscribe','woothemes'); ?></h3>

		<div <?php if ( $related_posts != '' ) echo 'class="col-left"'; ?>>
			<p><?php if ($settings[ 'connect_content' ] != '') echo stripslashes($settings[ 'connect_content' ]); ?></p>

			<?php if ( $settings[ 'connect_newsletter_id' ] != "" AND $form != 'on' ) : ?>
			<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="http://feedburner.google.com/fb/a/mailverify" method="post" target="popupwindow" onsubmit="window.open( 'http://feedburner.google.com/fb/a/mailverify?uri=<?php echo $settings[ 'connect_newsletter_id' ]; ?>', 'popupwindow', 'scrollbars=yes,width=550,height=520' );return true">
				<input class="email" type="text" name="email" value="<?php esc_attr_e( 'E-mail', 'woothemes' ); ?>" onfocus="if (this.value == '<?php _e( 'E-mail', 'woothemes' ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e( 'E-mail', 'woothemes' ); ?>';}" />
				<input type="hidden" value="<?php echo $settings[ 'connect_newsletter_id' ]; ?>" name="uri"/>
				<input type="hidden" value="<?php bloginfo( 'name' ); ?>" name="title"/>
				<input type="hidden" name="loc" value="en_US"/>
				<input class="submit-connect" type="submit" name="submit" value="<?php _e( 'Submit', 'woothemes' ); ?>" />
			</form>
			<?php endif; ?>

			<?php if ( $settings['connect_mailchimp_list_url'] != "" AND $form != 'on' AND $settings['connect_newsletter_id'] == "" ) : ?>
			<!-- Begin MailChimp Signup Form -->
			<div id="mc_embed_signup">
				<form class="newsletter-form<?php if ( $related_posts == '' ) echo ' fl'; ?>" action="<?php echo $settings['connect_mailchimp_list_url']; ?>" method="post" target="popupwindow" onsubmit="window.open('<?php echo $settings['connect_mailchimp_list_url']; ?>', 'popupwindow', 'scrollbars=yes,width=650,height=520');return true">
					<input type="text" name="EMAIL" class="required email" value="<?php _e('E-mail','woothemes'); ?>"  id="mce-EMAIL" onfocus="if (this.value == '<?php _e('E-mail','woothemes'); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('E-mail','woothemes'); ?>';}">
					<input type="submit" value="<?php _e('Submit', 'woothemes'); ?>" name="subscribe" id="mc-embedded-subscribe" class="btn submit button">
				</form>
			</div>
			<!--End mc_embed_signup-->
			<?php endif; ?>

			<?php if ( $social != 'on' ) : ?>
			<div class="social<?php if ( $related_posts == '' AND $settings['connect_newsletter_id' ] != "" ) echo ' fr'; ?>">
		   		<?php if ( $settings['connect_rss' ] == "true" ) { ?>
		   		<a href="<?php if ( $settings['feed_url'] ) { echo esc_url( $settings['feed_url'] ); } else { echo get_bloginfo_rss('rss2_url'); } ?>" class="subscribe" title="RSS"></a>

		   		<?php } if ( $settings['connect_twitter' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_twitter'] ); ?>" class="twitter" title="Twitter"></a>

		   		<?php } if ( $settings['connect_facebook' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_facebook'] ); ?>" class="facebook" title="Facebook"></a>

		   		<?php } if ( $settings['connect_youtube' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_youtube'] ); ?>" class="youtube" title="YouTube"></a>

		   		<?php } if ( $settings['connect_flickr' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_flickr'] ); ?>" class="flickr" title="Flickr"></a>

		   		<?php } if ( $settings['connect_linkedin' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_linkedin'] ); ?>" class="linkedin" title="LinkedIn"></a>

		   		<?php } if ( $settings['connect_delicious' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_delicious'] ); ?>" class="delicious" title="Delicious"></a>

		   		<?php } if ( $settings['connect_googleplus' ] != "" ) { ?>
		   		<a href="<?php echo esc_url( $settings['connect_googleplus'] ); ?>" class="googleplus" title="Google+"></a>

				<?php } ?>
			</div>
			<?php endif; ?>

		</div><!-- col-left -->

		<?php if ( $settings['connect_related' ] == "true" AND $related_posts != '' ) : ?>
		<div class="related-posts col-right">
			<h4><?php _e( 'Related Posts:', 'woothemes' ); ?></h4>
			<?php echo $related_posts; ?>
		</div><!-- col-right -->
		<?php wp_reset_query(); endif; ?>
		
		<div class="fix"></div>
		
	</div>
	<?php endif; ?>
<?php
	}
}

/*-----------------------------------------------------------------------------------*/
/* Function to display a custom field drop down field value */
/*-----------------------------------------------------------------------------------*/
if ( !function_exists( 'woo_display_drop_down_custom_field_value' ) ) {
	function woo_display_drop_down_custom_field_value( $custom_field_name = '', $key_value ) {
		
		$output = '';
		
		$woo_metaboxes = get_option('woo_custom_template');
	    foreach ($woo_metaboxes as $woo_metabox) {
	    	if ($woo_metabox['name'] == $custom_field_name) {
	        	$cmb_meta_options = $woo_metabox['options'];
	        	continue;
	        }
	    }
		
		$output = $cmb_meta_options[$key_value];
		
		return $output;
		
	} // End Function woo_display_drop_down_custom_field_value()
} // End If Statement
	
?>