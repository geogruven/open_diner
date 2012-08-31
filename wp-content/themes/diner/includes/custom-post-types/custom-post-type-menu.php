<?php

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- WooThemes WooMenu Custom Post Type Class
- WooThemes WooMenu Custom Post Type Filters
- WooThemes WooMenu Custom Post Type Metabox Setup

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* WooThemes WooMenu Custom Post Type Class */
/*-----------------------------------------------------------------------------------*/

class WooMenu {
	
	var $menu_order_field_name;
	var $menu_ordering;
	
	function WooMenu() {

		$this->menu_order_field_name = 'woo_diner_menutype_orders';
		
		$this->menu_ordering = get_option($this->menu_order_field_name);

		// Register custom post types
		register_post_type(	'woo_menu', 
							array(	'label' 			=> __( 'Food Menu', 'woothemes' ),
									'labels' 			=> array(	'name' 					=> __( 'Food Menu', 'woothemes' ),
																	'singular_name' 		=> __( 'Food Menu', 'woothemes' ),
																	'add_new' 				=> __( 'Add New', 'woothemes' ),
																	'add_new_item' 			=> __( 'Add New Meal', 'woothemes' ),
																	'edit' 					=> __( 'Edit', 'woothemes' ),
																	'edit_item' 			=> __( 'Edit Meal', 'woothemes' ),
																	'new_item' 				=> __( 'New Meal', 'woothemes' ),
																	'view_item'				=> __( 'View Meal', 'woothemes' ),
																	'search_items' 			=> __( 'Search Menu', 'woothemes' ),
																	'not_found' 			=> __( 'No Meals found', 'woothemes' ),
																	'not_found_in_trash' 	=> __( 'No Meals found in trash', 'woothemes' )	),
									'public' 			=> true,
									'can_export'		=> true,
									'show_ui' 			=> true, // UI in admin panel
									'_builtin' 			=> false, // It's a custom post type, not built in
									'_edit_link' 		=> 'post.php?post=%d',
									'capability_type' 	=> 'post',
									'menu_icon' 		=> get_bloginfo('template_url').'/includes/images/burger.png',
									'hierarchical' 		=> false,
									'rewrite' 			=> array( "slug" => "menu" ), // Permalinks
									'query_var' 		=> "woo_menu", // This goes to the WP_Query schema
									'supports' 			=> array(	'title',
																	'author', 
																	'excerpt',
																	'thumbnail',																
																	'editor', 
																	'custom-fields',
																	'page-attributes' ),
									'show_in_nav_menus'	=> true ,
									'taxonomies'		=> array(	'menutype',
																	'post_tag')
								)
							);
		
		//Custom columns
		add_filter("manage_edit-woo_menu_columns", array(&$this, "woo_menu_edit_columns"));
		add_action("manage_posts_custom_column", array(&$this, "woo_menu_custom_columns"));
		//Add filter to insure the text Meal, or menu, is displayed when user updates a menu
		add_filter('post_updated_messages', array(&$this, "woo_menu_updated_messages"));
		//custom styles
		add_action( 'admin_print_styles', array( &$this, 'woo_menu_admin_enqueue_style' ), null, 2 );
		
		// Register custom taxonomy
		register_taxonomy(	"menutype", 
							array(	"woo_menu"	), 
							array(	"hierarchical" 		=> true, 
									"label" 			=> __( 'Menu Types', 'woothemes' ), 
									'labels' 			=> array(	'name' 				=> __( 'Menu Types', 'woothemes' ),
																	'singular_name' 	=> __( 'Menu Type', 'woothemes' ),
																	'search_items' 		=> __( 'Search Menu Types', 'woothemes' ),
																	'popular_items' 	=> __( 'Popular Menu Types', 'woothemes' ),
																	'all_items' 		=> __( 'All Menu Types', 'woothemes' ),
																	'parent_item' 		=> __( 'Parent Menu Type', 'woothemes' ),
																	'parent_item_colon' => __( 'Parent Menu Type:', 'woothemes' ),
																	'edit_item' 		=> __( 'Edit Menu Type', 'woothemes' ),
																	'update_item'		=> __( 'Update Menu Type', 'woothemes' ),
																	'add_new_item' 		=> __( 'Add New Menu Type', 'woothemes' ),
																	'new_item_name' 	=> __( 'New Menu Type Name', 'woothemes' ) ),  
									'public' 			=> true,
									'show_ui' 			=> true,
									"rewrite" 			=> array(	"slug" => "menus"	)	)
							);
		
		// Register custom fields for "add" and "edit" screens,
		// as well as custom columns and column headings.
		
		$this->register_form_fields();
		$this->register_custom_columns_filters();
		
		add_action( 'contextual_help', array( &$this, 'add_help_text' ), 10, 3 );
		
	} // End WooMenu()
	
	//custom post type edit headers
	function woo_menu_edit_columns($columns) {
		$columns = array(
			"cb" => "<input type=\"checkbox\" />",
			"title" => __( 'Meal Title', 'woothemes' ),
			"woo_menu_description" => __( 'Description', 'woothemes' ),
			"woo_menu_thumbnail" => __( 'Thumbnail', 'woothemes' ),
			"woo_menu_price" => __( 'Price', 'woothemes' ),
			"woo_menu_rating" => __( 'Rating', 'woothemes' ),
			"woo_menu_type" => __( 'Menu Type', 'woothemes' ),
		);
		
		return $columns;
	} // End woo_menu_edit_columns()
	
	//custom post type edit output
	function woo_menu_custom_columns($column) {
		global $post;
		switch ($column)
		{
			case "woo_menu_description":
				the_excerpt();
				break;
			case "woo_menu_thumbnail":
				woo_image('key=thumbnail&width=100&height=100&class=thumbnail');
				break;
			case "woo_menu_price":
				$custom = get_post_custom();
				$price = $custom["price"][0];
				$meal_price = number_format($price , 2 , '.', ',');
				
				$output = '';

				if (has_tag('featured') ) { echo '<strong>'; _e('On Special', 'woothemes'); echo '</strong><br />'; }
				if ($price != '') { echo get_option('woo_diner_currency').''.$meal_price; }
				
				break;
			case "woo_menu_rating":
				echo woo_get_post_rating_average($post->ID);
				break;
			case "woo_menu_type":
				$menutypes = get_the_terms($post->ID, "menutype");
				$menutypes_html = array();
				if ($menutypes) {
				foreach ($menutypes as $menutype)
					array_push($menutypes_html, '<a href="' . get_term_link($menutype->slug, "menutype") . '">' . $menutype->name . '</a>');
				
				echo implode($menutypes_html, ", ");
				} else {
					_e('None', 'woothemes');;
				}
				break;
		}
	} // End woo_menu_custom_columns()
	
	function woo_menu_updated_messages( $messages ) {
		
  		$messages['woo_menu'] = array(
    			0 => '', // Unused. Messages start at index 1.
    			1 => sprintf( __( 'Meal updated. <a href="%s">View meal</a>', 'woothemes' ), esc_url( get_permalink($post_ID) ) ),
    			2 => __( 'Custom field updated.', 'woothemes' ),
    			3 => __( 'Custom field deleted.', 'woothemes' ),
    			4 => __( 'Meal updated.', 'woothemes' ),
    			/* translators: %s: date and time of the revision */
    			5 => isset($_GET['revision']) ? sprintf( __( 'Meal restored to revision from %s', 'woothemes' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    			6 => sprintf( __( 'Meal published. <a href="%s">View Meal</a>', 'woothemes' ), esc_url( get_permalink($post_ID) ) ),
    			7 => __( 'Meal saved.'),
    			8 => sprintf( __( 'Meal submitted. <a target="_blank" href="%s">Preview Meal</a>', 'woothemes' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    			9 => sprintf( __( 'Meal scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Meal</a>', 'woothemes' ),
    	  			// translators: Publish box date format, see http://php.net/date
     				date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    			10 => sprintf( __( 'Meal draft updated. <a target="_blank" href="%s">Preview Meal</a>', 'woothemes' ), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  			);

		return $messages;
	} // End woo_menu_updated_messages()
	
	//create initial taxonomy terms
	function woo_menutypes_create_initial_taxonomy_terms() {
		
		if (get_option('woo_install_complete') != 'true') {

			$menu_items = array('starters'	=> __( 'Starters', 'woothemes' ),
								'mains' 	=> __( 'Mains', 'woothemes' ),
								'desserts' 	=> __( 'Desserts', 'woothemes' )
								);
			$taxonomy = 'menutype';
			//loop and create terms
			foreach ($menu_items as $key => $value) {
				$id = term_exists($key, $taxonomy);
				if ($id > 0) {
					update_option('woo_'.$key.'_term_id',$id['term_id']);
				} else {
					$term = wp_insert_term($value, $taxonomy);
					if ( !is_wp_error($term) ) {
						update_option('woo_'.$key.'_term_id',$term['term_id']);
					}
				}
			}
			//installation complete
			update_option('woo_install_complete', 'true');
		}
		
	} // End woo_menutypes_create_initial_taxonomy_terms()
	
	function woo_menu_admin_enqueue_style () {
	 		
	 	global $post_type;
	 			
	 	if ( isset( $_GET['post_type'] ) && ( $_GET['post_type'] == 'woo_menu' ) || ( $post_type == 'woo_menu' ) ) {
	 		
			// Enqueue CSS file for custom post type screen	
			wp_enqueue_style( 'woo-menu-icon-css', get_template_directory_uri() . '/includes/custom-post-types/posttype-icon-woomenu.css', array(), '0.0.0.1', 'screen' );
				
	 	} // End IF Statement
	 	
	 	wp_enqueue_style( 'woo-menu-icon-hover', get_template_directory_uri() . '/includes/custom-post-types/posttype-icon-woomenu-hover.css', array(), '0.0.0.1', 'screen' );
	 		
	 } // End woo_menu_admin_enqueue_style() 
	
	/*----------------------------------------
 	  add_help_text()
 	  ----------------------------------------
 	  
 	  * Add contextual help text on our
 	  * custom taxonomy's admin screen.
 	----------------------------------------*/
	
	function add_help_text ( $contextual_help, $screen_id, $screen ) { 
	  
	  // $contextual_help .= var_dump($screen); // use this to help determine $screen->id
	  
	  if ( 'edit-' . 'menutype' == $screen->id ) {
	  
	    $contextual_help =
	      '<p>' . sprintf( __( '%sPlease note:%s The order in which the items are displayed below, is not the same as how they will be displayed in your theme.', 'woothemes' ), '<strong>', '</strong>' ) . '</p>' . "\n" .
	      '<p>' . __( 'The "display order" property determines the order in which these items are displayed in your theme, 1 for first, etc...', 'woothemes' ) . '</p>' . "\n" .
	      '<p>' . __( 'Each time a new level begins, start the ordering from 0 or 1 again, for example:', 'woothemes' ) . '</p>' . "\n" .
	      '<ul>' . "\n" . 
	      '<li>' . __( 'Category', 'woothemes' ) . ' 01 - ' . __( 'Display Order', 'woothemes' ) . ' <strong>0</strong></li>' . "\n" . 
	      '<li>' . __( 'Category', 'woothemes' ) . ' 02 - ' . __( 'Display Order', 'woothemes' ) . ' <strong>1</strong>' . "\n" . 
		      '<ul>' . "\n" . 
		      '<li>' . __( 'Sub-Category', 'woothemes' ) . ' 01 - ' . __( 'Display Order', 'woothemes' ) . ' <strong>0</strong></li>' . "\n" . 
		      '<li>' . __( 'Sub-Category', 'woothemes' ) . ' 02 - ' . __( 'Display Order', 'woothemes' ) . ' <strong>1</strong>' . "\n" . 
			      '<ul>' . "\n" . 
			      '<li>' . __( 'Sub-Sub-Category', 'woothemes' ) . ' 01 - ' . __( 'Display Order', 'woothemes' ) . ' <strong>0</strong></li>' . "\n" . 
			      '<li>' . __( 'Sub-Sub-Category', 'woothemes' ) . ' 02 - ' . __( 'Display Order', 'woothemes' ) . ' <strong>1</strong></li>' . "\n" . 
			      '<li>' . __( 'Sub-Sub-Category', 'woothemes' ) . ' 03 - ' . __( 'Display Order', 'woothemes' ) . ' <strong>2</strong></li>' . "\n" . 
			      '</ul>' . "\n" .
		      '</li>' . "\n" . 
		      '<li>' . __( 'Sub-Category', 'woothemes' ) . ' 03 - ' . __( 'Display Order', 'woothemes' ) . ' <strong>2</strong></li>' . "\n" . 
		      '</ul>' . "\n" .
	      '</li>' . "\n" . 
	      '<li>' . __( 'Category', 'woothemes' ) . ' 03 - ' . __( 'Display Order', 'woothemes' ) . ' <strong>2</strong></li>' . "\n" .
	      '</ul>' . "\n";
	  
	  } // End IF Statement
	  
	  return $contextual_help;
	  
	} // End add_help_text()
	
	/*----------------------------------------
 	  register_custom_columns_filters()
 	  ----------------------------------------
 	  
 	  * Register our custom taxonomy's
 	  * custom column headings and data hooks.
 	----------------------------------------*/
	
	public function register_custom_columns_filters () {
	
		add_filter( 'manage_edit-' . 'menutype' . '_columns', array( &$this, 'add_custom_column_headings' ), 10, 1 );
		add_action( 'manage_' . 'menutype' . '_custom_column', array( &$this, 'add_custom_column_data' ), 10, 3 );
		
	} // End register_custom_columns_filters()
	
	/*----------------------------------------
 	  add_custom_column_headings()
 	  ----------------------------------------
 	  
 	  * Add custom column headings on
 	  * the `posts list` page of our custom
 	  * taxonomy.
 	----------------------------------------*/
	
	public function add_custom_column_headings ( $columns ) {
		
		$new_columns = array(
		'cb' => '<input type="checkbox" />',
		'name' => __( 'Name', 'woothemes' ),
		'description' => __( 'Description', 'woothemes' ),
		'slug' => __( 'Slug', 'woothemes' ),
		'display_order' => __( 'Display Order', 'woothemes' ), 
		'posts' => __( 'Food Items', 'woothemes' )
		);
		
		return $new_columns;
	
	} // End add_custom_column_headings()
	
	/*----------------------------------------
 	  add_custom_column_data()
 	  ----------------------------------------
 	  
 	  * Add data for our custom columns on
 	  * the `posts list` page of our custom
 	  * taxonomy.
 	----------------------------------------*/
	
	public function add_custom_column_data ( $out, $column_name, $id ) {
		
		$this->menu_ordering = get_option($this->menu_order_field_name);
		
		switch ($column_name) {
		
		case 'display_order':
					
			$menu_ordering = $this->menu_ordering;
 		
 			$display_order = $menu_ordering[$id];
			
			if ( ! $display_order ) { $display_order = 0; } // End IF Statement
			
			$out .= $display_order;
		
		break;
		
		default:
		break;
		
		} // End SWITCH Statement
		
		return $out;
	
	} // End add_custom_column_data()
	
	/*----------------------------------------
 	  form_fields_add()
 	  ----------------------------------------
 	  
 	  * Add custom form fields to the `add`
 	  * screen of our custom taxonomy.
 	----------------------------------------*/
	
	function form_fields_add () {
 		
 		global $tax;
?>	 		
	<div class="form-field">  
		<label for="display_order"><?php _e( 'Display Order','woothemes' ); ?></label>  
		<input type='text' name="display_order" id="display_order" />  
		<p><?php _e( 'The order in which this menu is displayed. 1 for first, etc...', 'woothemes' ); ?></p> 
	</div>
<?php
 		
 	} // End form_fields_add()
 	
 	/*----------------------------------------
 	  form_fields_edit()
 	  ----------------------------------------
 	  
 	  * Add custom form fields to the `edit`
 	  * screen of our custom taxonomy.
 	----------------------------------------*/
 	
 	function form_fields_edit () {
 		
 		global $tax, $tag_ID, $wpdb;
 		
 		if ( !$tag_ID || !is_numeric( $tag_ID ) ) { return; } // End IF Statement
 		
 		$menu_ordering = get_option( $this->menu_order_field_name );
 		
 		$display_order = $menu_ordering[$tag_ID];
 		
 		if ( ! $display_order || ! is_numeric( $display_order ) ) { $display_order = 0; } // End IF Statement
?>
	<tr class="form-field">  
		<th scope="row" valign="top"><label for="display_order"><?php _e( 'Display Order','woothemes' ); ?></label></th>  
		<td><input type='text' name="display_order" id="display_order" value="<?php echo $display_order; ?>" />  
		<p class="description"><?php _e( 'The order in which this menu is displayed. 1 for first, etc...', 'woothemes' ); ?></p></td>
	</tr>
<?php	
 	} // End form_fields_edit()
 	
 	/*----------------------------------------
 	  meta_data_add()
 	  ----------------------------------------
 	  
 	  * The save function for our custom form
 	  * fields on the `add` screen of our
 	  * custom taxonomy.
 	  
 	  * Params:
 	  * - int $term_id
 	  * - int $tt_id
 	----------------------------------------*/
 	
 	function meta_data_add ( $term_id, $tt_id ) {
 	
 		global $wpdb;
 		
 		$menu_ordering = get_option( $this->menu_order_field_name );
 		
 		// Make sure we've got an array. If not, start fresh and make one.
 		
 		if ( ! is_array( $menu_ordering ) ) {
 				
 			$menu_ordering = array();
 			
 		} // End IF Statement
 		
 		$tag_ID = $term_id;
 		$taxonomy = $_POST['taxonomy'];
 		
 		$display_order = '';
 		
 		$options = array(
 						'display_order' => $display_order
 						);
 		
 		foreach ( $options as $k => $v ) {
 		
 			if ( ( $v == '' && $_POST[$k] != '' ) ) {
				
				$value = $_POST[$k];
				
				if ( ! $value || ! is_numeric( $value ) ) { $value = 0; } // End IF Statement
				
				$menu_ordering[$tag_ID] = intval( $value );
 			
  			} // End IF Statement	
 				 			
 		} // End FOREACH Loop
 		
 		update_option( $this->menu_order_field_name, $menu_ordering );
 		
 	} // End meta_data_add()
 	
 	/*----------------------------------------
 	  meta_data_edit()
 	  ----------------------------------------
 	  
 	  * The save function for our custom form
 	  * fields on the `edit` screen of our
 	  * custom taxonomy.
 	----------------------------------------*/
 	
 	function meta_data_edit () {
 		
 		global $wpdb;
 		
 		$tag_ID = $_POST['tag_ID'];
 		$taxonomy = $_POST['taxonomy'];
 		
 		$menu_ordering = get_option( $this->menu_order_field_name );
 		
 		// Make sure we've got an array. If not, start fresh and make one.
 		
 		if ( ! is_array( $menu_ordering ) ) {
 				
 			$menu_ordering = array();
 			
 		} // End IF Statement
 		
 		$options = array(
 						'display_order' => $display_order
 						);
 		
 		foreach ( $options as $k => $v ) {
 		
 			if ( ( $v == '' && $_POST[$k] != '' ) ) {
				
				$value = $_POST[$k];
				
				if ( ! $value || ! is_numeric( $value ) ) { $value = 0; } // End IF Statement
				
				$menu_ordering[$tag_ID] = intval( $value );
 			
  			} // End IF Statement	
 				 			
 		} // End FOREACH Loop
 		
 		update_option( $this->menu_order_field_name, $menu_ordering );
 		
 	} // End meta_data_edit()
 	
 	/*----------------------------------------
 	  register_form_fields()
 	  ----------------------------------------
 	  
 	  * Add custom form fields to the `add`
 	  * and `edit` forms of our custom
 	  * taxonomy, as well as registering
 	  * the save functions on the necessary
 	  * WordPress hooks.
 	----------------------------------------*/
 	
 	function register_form_fields () {
 			
 			// Register form fields.
 			add_action( 'menutype' . '_add_form_fields', array( &$this, 'form_fields_add' ) );
 			add_action( 'menutype' . '_edit_form_fields', array( &$this, 'form_fields_edit' ) );
 			
 			// Register add and edit functions.
 			add_action( 'created_' . 'menutype', array( &$this, 'meta_data_add' ), 10, 2 );  
			add_action( 'edit_' . 'menutype', array( &$this, 'meta_data_edit' ), 10, 2 );
 		
 	} // End register_form_fields()
	
} // End Class

// Initiate the plugin
add_action("init", "WooMenuInit");
function WooMenuInit() { 
	global $woo_menu_cpt; 
	$woo_menu_cpt = new WooMenu(); 
	$woo_menu_cpt->woo_menutypes_create_initial_taxonomy_terms(); 
} // End WooMenuInit()

/*-----------------------------------------------------------------------------------*/
/* WooThemes WooMenu Custom Post Type Filters */
/*-----------------------------------------------------------------------------------*/

// Custom Taxonomy Filters
if ( isset($_GET['post_type']) ) {
	$post_type = $_GET['post_type'];
}
else {
	$post_type = '';
}

if ( $post_type == 'woo_menu' ) {
	add_action('restrict_manage_posts', 'woo_menu_restrict_manage_posts');
	add_filter('posts_where', 'woo_menu_posts_where');
}

// The drop down with filter
function woo_menu_restrict_manage_posts() {
    ?>
        <form name="location_attachmentform" id="location_attachmentform" action="" method="get">
            <fieldset>
            <?php
				//Meal Types
            	$category_ID = $_GET['type_names'];
            	if ($category_ID > 0) {
            		//Do nothing
            	} else {
            		$category_ID = 0;
            	}
            	$dropdown_options = array	(	
            								'show_option_all'	=> __( 'View all Meals', 'woothemes' ), 
            								'hide_empty' 		=> 0, 
            								'hierarchical' 		=> 1,
											'show_count' 		=> 0, 
											'orderby' 			=> 'name',
											'name' 				=> 'type_names',
											'id' 				=> 'type_names',
											'taxonomy' 			=> 'menutype', 
											'selected' 			=> $category_ID
											);
				wp_dropdown_categories($dropdown_options);
			?>
            <input type="submit" name="submit" value="<?php _e('Filter') ?>" class="button" />
        </fieldset>
        </form>
    <?php
} // End woo_menu_restrict_manage_posts()

// Custom Query to filter edit grid
function woo_menu_posts_where($where) {
    if( is_admin() ) {
        global $wpdb;
        $type_ID = $_GET['type_names'];
		if ( ($location_ID > 0) || ($type_ID > 0) ) {

			$type_tax_names =  &get_term( $type_ID, 'menutype' );

			$string_post_ids = '';
 			//menu types
			if ($type_ID > 0) {
				$type_tax_name = $type_tax_names->slug;
				$type_myposts = get_posts('nopaging=true&post_type=woo_menu&menutype='.$type_tax_name);
				foreach($type_myposts as $post) {
					$string_post_ids .= $post->ID.',';
				}
			}
			$string_post_ids = chop($string_post_ids,',');
   			$where .= "AND ID IN (" . $string_post_ids . ")";
		}
    }
    return $where;
} // End woo_menu_posts_where()

/*-----------------------------------------------------------------------------------*/
/* WooThemes WooMenu Custom Post Type Metabox Setup */
/*-----------------------------------------------------------------------------------*/

//Add meta boxes to woo_menu post type
function woothemes_woo_menu_metabox_add() {
    if ( function_exists('add_meta_box') ) {
        add_meta_box('woothemes-settings', get_option('woo_themename') . ' Custom Settings','woothemes_metabox_create','woo_menu','normal');
    }
} // End woothemes_woo_menu_metabox_add()

add_action('admin_menu', 'woothemes_woo_menu_metabox_add',1,1);
?>