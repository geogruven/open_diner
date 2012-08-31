<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- WooThemes WooMenu Staff Roles Setup
- WooThemes WooMenu Taxonomy Search Functions
- WooThemes WooMenu Meal Search Function 

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* WooThemes Diner Custom Post Types Setup */
/*-----------------------------------------------------------------------------------*/

$includes_path = TEMPLATEPATH . '/includes/custom-post-types/';
require_once ($includes_path . 'custom-post-type-menu.php');		// Menu CPT

/*-----------------------------------------------------------------------------------*/
/* WooThemes WooMenu Staff Roles Setup */
/*-----------------------------------------------------------------------------------*/

//add a new role if it doesnt exist
if ( get_option('woo_staff_user_role_enable') == 'true' ) {
	woo_add_staff_role();
} else {
	//remove user role if exists - woo_diner_staff
	$staff_role = 'woo_diner_staff';
	$existing_role = get_role( $staff_role );
	
	if ( isset( $existing_role ) ) {
		$removed_role = remove_role( $staff_role );
	} 
}

//adds staff role
function woo_add_staff_role() {
	
	$staff_role = 'woo_diner_staff';
	$staff_name = get_option('woo_staff_role_name');
	$theme_staff_role = get_option('woo_staff_role_default');
	$existing_role = get_role( $staff_role );
	
	if ( isset( $existing_role ) ) {
		$role = get_role( $theme_staff_role );
		$staff_capabilities = $role->capabilities;
		$existing_role->capabilities = $staff_capabilities;
		
	} else {
		if ( ($theme_staff_role != '') && ($theme_staff_role != 'Select a Role:') ) {
			//get existing role for theme setting - default is editor
			$role = get_role( $theme_staff_role );
			$staff_capabilities = $role->capabilities;
	
			/* Sanitize the new role, removing any unwanted characters. */
			$new_role = strip_tags( $staff_role );
			$new_role = str_replace( array( '-', ' ', '&nbsp;' ) , '_', $new_role );
			$new_role = preg_replace('/[^A-Za-z0-9_]/', '', $new_role );
			$new_role = strtolower( $new_role );

			/* Sanitize the new role name/label. We just want to strip any tags here. */
			$new_role_name = strip_tags( $staff_name ); // Should we use something like the WP user sanitation method?

			/* Add a new role with the data input. */
			$new_role_added = add_role( $new_role, $new_role_name, $staff_capabilities );
	
		}
	}
}


//add_action( 'show_user_profile', 'woo_staff_extra_profile_fields' );
//add_action( 'edit_user_profile', 'woo_staff_extra_profile_fields' );

//extra user fields output
function woo_staff_extra_profile_fields( $user ) { ?>

	<h3><?php _e('Additional Contact Information', 'woothemes'); ?></h3>

	<table class="form-table">

		<tr>
			<th><label for="contact-number"><?php _e('Contact Number', 'woothemes'); ?></label></th>

			<td>
				<input type="text" name="contact-number" id="contact-number" value="<?php echo esc_attr( get_the_author_meta( 'contact_number', $user->ID ) ); ?>" class="regular-text" /><br />
				<span class="description"><?php _e('Please enter your Contact Number', 'woothemes'); ?></span>
			</td>
		</tr>

	</table>
<?php }

add_action( 'personal_options_update', 'woo_staff_save_extra_profile_fields' );
add_action( 'edit_user_profile_update', 'woo_staff_save_extra_profile_fields' );

//handle save of extra user fields
function woo_staff_save_extra_profile_fields( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	/* Copy and paste this line for additional fields. Make sure to change 'twitter' to the field ID. */
	update_usermeta( $user_id, 'contact_number', $_POST['contact-number'] );
}

/*-----------------------------------------------------------------------------------*/
/* WooThemes WooMenu Taxonomy Search Functions */
/*-----------------------------------------------------------------------------------*/

//search taxonomies for a match against a search term and returns array of success count
function woo_taxonomy_matches($term_name, $term_id, $post_id = 0, $keyword_to_search = '') {
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
				$keyword_count = substr_count( strtolower( $term->name ) , strtolower( $keyword_to_search ) );
				if ( $keyword_count > 0 ) {
					$success = true;
					$counter++;
				}
			} else {
				//If search term is blank
				$location_tax_names =  get_term_by( 'id', $term_id, 'location' );
 				//locations
				if ($location_tax_names) {
					$location_tax_name = $location_tax_names->slug;
					if ($location_tax_name != '') {
						$location_myposts = get_posts('nopaging=true&post_type=woo_menu&location='.$location_tax_name);
						foreach($location_myposts as $location_mypost) {
							if ($location_mypost->ID == $post_id) {
								$success = true;
	        					$counter++;
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



/*-----------------------------------------------------------------------------------*/
/* WooThemes WooMenu Meal Search Function 
/*-----------------------------------------------------------------------------------*/

function woo_menu_search_result_set($query_args,$keyword_to_search, $location_id, $menutypes_id, $advanced_search = null, $search_type = '') {
	
	$search_results = array();
	$query_args['showposts'] = -1;
	$the_query = new WP_Query($query_args);
	
	//Prepare Garages, Beds, Baths variables
	
	if ($advanced_search['beds'] == '10+') { 
		$advanced_beds = 10;
	} else {
		$advanced_beds = $advanced_search['beds'];
	}
	if ($advanced_search['baths'] == '10+') { 
		$advanced_baths = 10;
	} else {
		$advanced_baths = $advanced_search['baths'];
	}
	if ($advanced_search['garages'] == '10+') { 
		$advanced_garages = 10;
	} else {
		$advanced_garages = $advanced_search['garages'];
	}
	
	//Get matching method
	$matching_method = get_option('woo_feature_matching_method');
	
	if ($the_query->have_posts()) : $count = 0;

	while ($the_query->have_posts()) : $the_query->the_post();

		global $post;
        $post_type = $post->post_type;
		
		if ($search_type == 'webref') {
			array_push($search_results,$post->ID);
		} 
		else {
	        //Check Locations for matches
	        $location_terms = woo_taxonomy_matches('location', $location_id, $post->ID, $keyword_to_search);
	        $success_location = $location_terms['success'];
	        $location_keyword_count = $location_terms['keywordcount'];

	        //Secondary Location Check
	        if ( (!$success_location) || ($location_keyword_count == 0) ) {
	        	$location_tax_names =  get_term_by( 'name', $keyword_to_search, 'location' );

 				//locations
				if ($location_tax_names) {
					$location_tax_name = $location_tax_names->slug;
					//echo $location_tax_name.'<br />';
					if ($location_tax_name != '') {
						$location_myposts = get_posts('nopaging=true&post_type=woo_menu&location='.$location_tax_name);
						foreach($location_myposts as $location_mypost) {
							if ($location_mypost->ID == $post->ID) {
								$success_location = true;
	        					$location_keyword_count++;
							} 
						}
					}
				} 
	        }
	        
	        //Check Meal Types for matches
	        $menutypes_terms = woo_taxonomy_matches('menutype', $menutypes_id, $post->ID, $keyword_to_search);
	        $success_menutype = $menutypes_terms['success'];
	        $menutype_keyword_count = $menutypes_terms['keywordcount'];
	        
	        //Secondary Meal Type Check
	        if ( (!$success_menutype) || ($menutype_keyword_count == 0) ) {
	        	$menutype_tax_names =  get_term_by( 'name', $keyword_to_search, 'menutype' );

 				//locations
				if ($menutype_tax_names) {
					$menutype_tax_name = $menutype_tax_names->slug;
					//echo $location_tax_name.'<br />';
					if ($menutype_tax_name != '') {
						$menutype_myposts = get_posts('nopaging=true&post_type=woo_menu&menutype='.$menutype_tax_name);
						foreach($menutype_myposts as $menutype_mypost) {
							if ($menutype_mypost->ID == $post->ID) {
								$success_menutype = true;
	        					$menutype_keyword_count++;
							} 
						}
					}
				} 
	        }
	        
	        //Check Rating for matches
	        $menufeatures_terms = woo_taxonomy_matches('menufeatures', 0, $post->ID, $keyword_to_search);
	        $success_menufeatures = $menufeatures_terms['success'];
	        $menufeatures_keyword_count = $menufeatures_terms['keywordcount'];
		    //Do custom meta boxes comparisons here
	    	$menu_address = get_post_meta($post->ID,'address',true);
	    	$menu_garages = get_post_meta($post->ID,'garages',true);
	    	if ($menu_garages == '10+' ) {
	    		$menu_garages = 10;
	    	}
			$menu_garages_success = false;
			if ($advanced_garages == 'all') {
				$menu_garages_success = true;
			} else {
				//Matching Method
				if ($matching_method == 'minimum') {
					//Minimum Value
					if ($menu_garages >= $advanced_garages) {
						$menu_garages_success = true;
					} else {
						$menu_garages_success = false;
					}
				} else {
					//Exact Matching
					if ($menu_garages == $advanced_garages) {
						$menu_garages_success = true;
					} else {
						$menu_garages_success = false;
					}
				}
			}
	    	$menu_beds = get_post_meta($post->ID,'beds',true);
	    	if ($menu_beds == '10+' ) {
	    		$menu_beds = 10;
	    	}
			$menu_beds_success = false;
			if ($advanced_beds == 'all') {
				$menu_beds_success = true;
			} else {
				//Matching Method
				if ($matching_method == 'minimum') {
					//Minimum Value
					if ($menu_beds >= $advanced_beds) {
						$menu_beds_success = true;
					} else {
						$menu_beds_success = false;
					}
				} else {
					//Exact Matching
					if ($menu_beds == $advanced_beds) {
						$menu_beds_success = true;
					} else {
						$menu_beds_success = false;
					}
				}
			}
	    	$menu_baths = get_post_meta($post->ID,'bathrooms',true);
	    	if ($menu_baths == '10+' ) {
	    		$menu_baths = 10;
	    	}
			$menu_baths_success = false;
			if ($advanced_baths == 'all') {
				$menu_baths_success = true;
			} else {
				//Matching Method
				if ($matching_method == 'minimum') {
					//Minimum Value
					if ($menu_baths >= $advanced_baths) {
						$menu_baths_success = true;
					} else {
						$menu_baths_success = false;
					}
				} else {
					//Exact Matching
					if ($menu_baths == $advanced_baths) {
						$menu_baths_success = true;
					} else {
						$menu_baths_success = false;
					}
				}
			}
			
			// SIZE COMPARISON SCENARIO(S)
	    	$menu_size = get_post_meta($post->ID,'size',true);
			$menu_size_success = false;
			//scenario 1 - only size min
			if ( ($advanced_search['size_min'] != '') && ( ($advanced_search['size_max'] == '') || ($advanced_search['size_max'] == 0) ) ) { 
				if ( ($menu_size >= $advanced_search['size_min']) ) {
					$menu_size_success = true;
				} else {
					$menu_size_success = false;
				}
			}
			//scenario 2 - only size max
			elseif ( ( ($advanced_search['size_max'] != '') || ($advanced_search['size_max'] != 0) ) && ($advanced_search['size_min'] == '') ) { 
				if ( ($menu_size <= $advanced_search['size_max']) ) {
					$menu_size_success = true;
				} else {
					$menu_size_success = false;
				}
			}
			//scenario 3 - size min and max are zero
			elseif ( ($advanced_search['size_min'] == '0') && ($advanced_search['size_max'] == 0) ) { 
				$menu_size_success = true;
			}
			//scenario 4 - both min and max
			else {
				if ( ($menu_size >= $advanced_search['size_min']) && ($menu_size <= $advanced_search['size_max']) ) {
					$menu_size_success = true;
				} else {
					$menu_size_success = false;
				}
			}
			
			// PRICE COMPARISON SCENARIO(S)
	    	$menu_price = get_post_meta($post->ID,'price',true);
			$menu_price_success = false;
			//scenario 1 - only price min
			if ( ($advanced_search['price_min'] != '') && ( ($advanced_search['price_max'] == '') || ($advanced_search['price_max'] == 0) ) ) { 
				if ( ($menu_price >= $advanced_search['price_min']) ) {
					$menu_price_success = true;
				} else {
					$menu_price_success = false;
				}
			}
			//scenario 2 - only price max
			elseif ( ( ($advanced_search['price_max'] != '') || ($advanced_search['price_max'] != 0) ) && ($advanced_search['price_min'] == '') ) { 
				if ( ($menu_price <= $advanced_search['price_max']) ) {
					$menu_price_success = true;
				} else {
					$menu_price_success = false;
				}
			}
			//scenario 3 - price min and max are zero
			elseif ( ($advanced_search['price_min'] == '0') && ($advanced_search['price_max'] == 0) ) { 
				$menu_price_success = true;
			}
			//scenario 4 - both min and max
			else {
				if ( ($menu_price >= $advanced_search['price_min']) && ($menu_price <= $advanced_search['price_max']) ) {
					$menu_price_success = true;
				} else {
					$menu_price_success = false;
				}
			}
			
			//format price
			$menu_price = number_format($menu_price , 0 , '.', ',');
			
	    	if ( $success_location && $success_menutype ) {  
	    		//Search against post data
	    		if ( $keyword_to_search != '' ) {
	    			//Default WordPress Content
	    			$raw_title = get_the_title();
	    			$raw_content = get_the_content();
	    			$raw_excerpt = get_the_excerpt();
	    			//Comparison
	    			$title_keyword_count = substr_count( strtolower( $raw_title ) , strtolower( $keyword_to_search ) );
	    			$content_keyword_count = substr_count( strtolower( $raw_content ) , strtolower( $keyword_to_search ) );
	    			$excerpt_keyword_count = substr_count( strtolower( $raw_excerpt ) , strtolower( $keyword_to_search ) );
	    			$menu_address_count = substr_count( strtolower( $menu_address ) , strtolower( $keyword_to_search ) );
	    		}
	    		//Check for matches or blank keyword
	    		
	    		if ( $keyword_to_search == '') {
	    			
	    			if ( ( $location_keyword_count > 0 ) || ( $menutype_keyword_count > 0 ) || ( $menufeatures_keyword_count > 0 ) ) { 

						if ( (count($advanced_search) > 0) && ( ($advanced_search['garages'] != 'all') || ($advanced_search['beds'] != 'all') || ($advanced_search['baths'] != 'all') || ($advanced_search['price_min'] != '0') || ($advanced_search['price_max'] != '0') || ($advanced_search['size_min'] != '0') || ($advanced_search['size_max'] != '0') ) ) {
								
								if ($menu_garages_success && $menu_beds_success && $menu_baths_success && $menu_price_success && $menu_size_success ) {
									//increment post counter
									
									$count++; 
									$has_results = true;
	    			
									//setup array data here
									array_push($search_results,$post->ID);
								}
							
						} else {
							//increment post counter
							$count++; 
							$has_results = true;
	    			
							//setup array data here
							array_push($search_results,$post->ID);
						}
						
	    			}
	    			elseif ( ( $location_keyword_count == 0 ) && ( $menutype_keyword_count == 0 ) && ( $menufeatures_keyword_count == 0 ) ) { 
						
						if ( (count($advanced_search) > 0) && ( ($advanced_search['garages'] != 'all') || ($advanced_search['beds'] != 'all') || ($advanced_search['baths'] != 'all') || ($advanced_search['price_min'] != '0') || ($advanced_search['price_max'] != '0') || ($advanced_search['size_min'] != '0') || ($advanced_search['size_max'] != '0') ) ) {
								
								if ($menu_garages_success && $menu_beds_success && $menu_baths_success && $menu_price_success && $menu_size_success ) {
									//increment post counter
									$count++; 
									$has_results = true;
	    			
									//setup array data here
									array_push($search_results,$post->ID);
								}
							
						} else {
							//increment post counter
							$count++; 
							$has_results = true;
	    			
							//setup array data here
							array_push($search_results,$post->ID);
						}
						
	    			}
	    			
	    		} else {
	    		
	    			if ( ( $title_keyword_count > 0 ) || ( $content_keyword_count > 0 ) || ( $excerpt_keyword_count > 0 ) || ( $location_keyword_count > 0 ) || ( $menu_address_count > 0 ) || ( $menutype_keyword_count > 0 ) || ( $menufeatures_keyword_count > 0 ) ) {
	    				if ( (count($advanced_search) > 0) && ( ($advanced_search['garages'] != 'all') || ($advanced_search['beds'] != 'all') || ($advanced_search['baths'] != 'all') || ($advanced_search['price_min'] != '0') || ($advanced_search['price_max'] != '0') || ($advanced_search['size_min'] != '0') || ($advanced_search['size_max'] != '0') ) ) {
								
								if ($menu_garages_success && $menu_beds_success && $menu_baths_success && $menu_price_success && $menu_size_success ) {
									//increment post counter
									$count++; 
									$has_results = true;
	    			
									//setup array data here
									array_push($search_results,$post->ID);
								}
						} else {
							//increment post counter
							$count++; 
							$has_results = true;
	    			
							//setup array data here
							array_push($search_results,$post->ID);
						}
	    			} else {
					
					}
	    		
	    		}
	    		
	    		
	    	}
		
		}
		
	endwhile; else:
    	//no posts	    	
    endif;
	
	return $search_results;
}

?>