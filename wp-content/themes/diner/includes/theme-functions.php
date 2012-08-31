<?php 

/*-----------------------------------------------------------------------------------

TABLE OF CONTENTS

- Add food menu posts to tag archive screens
- Order food menu screens' posts by menu_order
- Page / Post navigation
- WooTabs - Popular Posts
- WooTabs - Latest Posts
- WooTabs - Latest Comments
- Post Meta
- Misc
- WordPress 3.0 New Features Support
- Woo Google Mapping
- Thickbox Styles
- Ajax functions
- Custom Post Type: Slides

-----------------------------------------------------------------------------------*/

/*-----------------------------------------------------------------------------------*/
/* Add food menu posts to tag archive screens */
/*-----------------------------------------------------------------------------------*/

add_filter( 'pre_get_posts', 'woo_show_fooditems_in_tag_archive' );

function woo_show_fooditems_in_tag_archive ( $query ) {

	if ( $query->is_tag ) { $query->set( 'post_type', array( 'post', 'woo_menu' ) ); }

	return $query;

} // End woo_show_fooditems_in_tag_archive()

/*-----------------------------------------------------------------------------------*/
/* Order food menu screens' posts by menu_order */
/*-----------------------------------------------------------------------------------*/

// Change the order of the posts to order by menu_order.
function woo_menu_items_orderby ( $orderby ) {

	if ( is_tax( 'menutype' ) ) {

		$orderby = 'menu_order ASC';
	
	} // End IF Statement
	
	return $orderby;

} // End woo_menu_items_orderby()

add_filter( 'posts_orderby', 'woo_menu_items_orderby' );


/*-----------------------------------------------------------------------------------*/
/* Page / Post navigation */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_pagenav' ) ) {
	function woo_pagenav() { 
	
		if ( function_exists( 'wp_pagenavi' ) ) { ?>
	    
			<?php wp_pagenavi(); ?>
	    
		<?php } else { ?>    
	    
			<?php if ( get_next_posts_link() || get_previous_posts_link() ) { ?>
	        
	            <div class="nav-entries">
	                <div class="nav-prev fl"><?php previous_posts_link(__( '&laquo; Newer Entries', 'woothemes') . ' ' ); ?></div>
	                <div class="nav-next fr"><?php next_posts_link( ' ' . __( 'Older Entries &raquo;', 'woothemes')); ?></div>
	                <div class="fix"></div> 
	            </div>	
	        
			<?php } ?>
	    
		<?php }   
	} 
}               	

if ( ! function_exists( 'woo_postnav' ) ) {
	function woo_postnav() { 
		?>
	        <div class="post-entries">
	            <div class="post-prev fl"><?php previous_post_link( '%link', '<span class="meta-nav">&laquo;</span> %title' ); ?></div>
	            <div class="post-next fr"><?php next_post_link( '%link', '%title <span class="meta-nav">&raquo;</span>' ); ?></div>
	            <div class="fix"></div>
	        </div>	
	
		<?php 
	}                	
}                	


/*-----------------------------------------------------------------------------------*/
/* WooTabs - Popular Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_tabs_popular')) {
	function woo_tabs_popular( $posts = 5, $size = 35 ) {
		$popular = new WP_Query('orderby=comment_count&posts_per_page='.$posts);
		while ($popular->have_posts()) : $popular->the_post();
	?>
	<li>
		<?php if ($size <> 0) woo_get_image('image',$size,$size,'thumbnail',90,null,'src',1,0,'','',true,false,false); ?>
		<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php endwhile; 
	}
}


/*-----------------------------------------------------------------------------------*/
/* WooTabs - Latest Posts */
/*-----------------------------------------------------------------------------------*/
if (!function_exists('woo_tabs_latest')) {
	function woo_tabs_latest( $posts = 5, $size = 35 ) {
		$the_query = new WP_Query('showposts='. $posts .'&orderby=post_date&order=desc');	
		while ($the_query->have_posts()) : $the_query->the_post(); 
	?>
	<li>
		<?php if ($size <> 0) woo_get_image('image',$size,$size,'thumbnail',90,null,'src',1,0,'','',true,false,false); ?>
		<a title="<?php the_title_attribute(); ?>" href="<?php the_permalink() ?>"><?php the_title(); ?></a>
		<span class="meta"><?php the_time( get_option( 'date_format' ) ); ?></span>
		<div class="fix"></div>
	</li>
	<?php endwhile; 
	}
}



/*-----------------------------------------------------------------------------------*/
/* WooTabs - Latest Comments */
/*-----------------------------------------------------------------------------------*/
if ( ! function_exists( 'woo_tabs_comments' ) ) {
	function woo_tabs_comments( $posts = 5, $size = 35 ) {
		global $wpdb;
		$sql = "SELECT DISTINCT ID, post_title, post_password, comment_ID,
		comment_post_ID, comment_author, comment_author_email, comment_date_gmt, comment_approved,
		comment_type,comment_author_url,
		SUBSTRING(comment_content,1,50) AS com_excerpt
		FROM $wpdb->comments
		LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID =
		$wpdb->posts.ID)
		WHERE comment_approved = '1' AND comment_type = '' AND
		post_password = ''
		ORDER BY comment_date_gmt DESC LIMIT ".$posts;
		
		$comments = $wpdb->get_results($sql);
		
		foreach ($comments as $comment) {
		?>
		<li>
			<?php echo get_avatar( $comment, $size ); ?>
		
			<a href="<?php echo get_permalink( $comment->ID ); ?>#comment-<?php echo $comment->comment_ID; ?>" title="<?php echo __( 'on', 'woothemes' ) . ' '; ?> <?php echo $comment->post_title; ?>">
				<?php echo strip_tags($comment->comment_author); ?>: <?php echo strip_tags( $comment->com_excerpt ); ?>...
			</a>
			<div class="fix"></div>
		</li>
		<?php 
		}
	}
}



/*-----------------------------------------------------------------------------------*/
/* Post Meta */
/*-----------------------------------------------------------------------------------*/

if ( ! function_exists( 'woo_post_meta' ) ) {
	function woo_post_meta() {
?>
<p class="post-meta">
    <span class="post-author"><span class="small"><?php _e('by', 'woothemes'); ?></span> <?php the_author_posts_link(); ?></span>
    <span class="post-date"><span class="small"><?php _e('on', 'woothemes'); ?></span> <?php the_time( get_option( 'date_format' ) ); ?></span>
    <span class="post-category"><span class="small"><?php _e('in', 'woothemes'); ?></span> <?php the_category(', ') ?></span>
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
/* Woo Google Mapping */
/*-----------------------------------------------------------------------------------*/

function woo_maps_single_output($args){

	$key = get_option('woo_maps_apikey');
	
	if(empty($key)){ ?>
	 Please enter your <strong>API Key</strong> before using the maps.
    <?php
	} else {
	
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

      	  		var locationPOV = {<?php echo $pov; ?>};
				var location = new GLatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
		  		map = new GStreetviewPanorama(document.getElementById("single_map_canvas"));
		  		map.setLocationAndPOV(location, locationPOV);
				GEvent.addListener(map, "error", handleNoFlash);
				
			<?php } else { ?>
				
			  	var map = new GMap2(document.getElementById("single_map_canvas"));
			  	map.setUIToDefault();
			  	<?php if(get_option('woo_maps_scroll') == 'true'){ ?>
			  	map.disableScrollWheelZoom();
			  	<?php } ?>
			  	map.setMapType(<?php echo $type; ?>);
			  	map.setCenter(new GLatLng(<?php echo $lat; ?>,<?php echo $long; ?>), <?php echo $zoom; ?>);

				<?php if($mode == 'directions'){ ?>
			  	directionsPanel = document.getElementById("featured-route");
 				directions = new GDirections(map, directionsPanel);
  				directions.load("from: <?php echo $from; ?> to: <?php echo $to; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
			  	<?php
			 	} else { ?>
			 
			  		var point = new GLatLng(<?php echo $lat; ?>,<?php echo $long; ?>);
	  				var root = "<?php bloginfo('template_url'); ?>";
	  				var the_link = '<?php echo get_permalink(get_the_id()); ?>';
	  				<?php $title = str_replace(array('&#8220;','&#8221;'),'"',get_the_title(get_the_id())); ?>
	  				var the_title = '<?php echo html_entity_decode($title) ?>'; 
	  				
	  			<?php		 	
			 	if(is_page()){ 
			 		$custom = get_option('woo_cat_custom_marker_pages');
					if(!empty($custom)){
						$color = $custom;
					}
					else {
						$color = get_option('woo_cat_custom_pages');
					}			 	
			 	?>
			 		var color = '<?php echo $color; ?>';
			 		map.addOverlay(createMarker(point,root,the_link,the_title,color));

			 	<?php } else { ?>
			 		var color = '<?php echo cat_to_color(get_the_category( get_the_id() )); ?>';
	  				map.addOverlay(createMarker(point,root,the_link,the_title,color));
				<?php 
				}
				
				
					if(isset($_POST['woo_maps_directions_search'])){ ?>
					
					directionsPanel = document.getElementById("featured-route");
 					directions = new GDirections(map, directionsPanel);
  					directions.load("from: <?php echo htmlspecialchars($_POST['woo_maps_directions_search']); ?> to: <?php echo $address; ?>" <?php if($walking == 'on'){ echo $extra_params;} ?>);
  					GEvent.addListener(directions, "error", handleErrors);
  					
  					function handleErrors(){
						if (directions.getStatus().code == G_GEO_UNKNOWN_ADDRESS)
  		 					jQuery('.map_error').show().html('No corresponding geographic location could be found for one of the specified addresses. This may be due to the fact that the address is relatively new, or it may be incorrect.');
 		 				else if (directions.getStatus().code == G_GEO_SERVER_ERROR)
 		  					jQuery('.map_error').show().html('A geocoding or directions request could not be successfully processed, yet the exact reason for the failure is not known.');
 						else if (directions.getStatus().code == G_GEO_MISSING_QUERY)
   							jQuery('.map_error').show().html('The HTTP q parameter was either missing or had no value. For geocoder requests, this means that an empty address was specified as input. For directions requests, this means that no query was specified in the input.');
 						else if (directions.getStatus().code == G_GEO_BAD_KEY)
   							jQuery('.map_error').show().html('The given key is either invalid or does not match the domain for which it was given.');
 		 				else {
 		 					if (directions.getStatus().code == G_GEO_BAD_REQUEST)
   								jQuery('.map_error').show().html('A directions request could not be successfully parsed.');
 							else jQuery('.map_error').show().html('An unknown error occurred.');
							}
						}
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
    <?php } ?>

<?php
}

/*-----------------------------------------------------------------------------------*/
/*Thickbox Styles */
/*-----------------------------------------------------------------------------------*/

function thickbox_style() {
	$site_url = site_url( '/' );

    ?>
    <link rel="stylesheet" href="<?php echo $site_url; ?>/wp-includes/js/thickbox/thickbox.css" type="text/css" media="screen" />
    <script type="text/javascript">
    	var tb_pathToImage = "<?php echo $site_url; ?>/wp-includes/js/thickbox/loadingAnimation.gif";
    	var tb_closeImage = "<?php echo $site_url; ?>/wp-includes/js/thickbox/tb-close.png"
    </script>
    <?php
}

add_action( 'wp_head', 'thickbox_style' );

/*-----------------------------------------------------------------------------------*/
/* Woo Ratings */
/*-----------------------------------------------------------------------------------*/
    
add_action('init','woo_create_ratings_tables');
function woo_create_ratings_tables() {
	
	//CREATE Post Ratings tables
	$ratings_version = '0.1';
	//Check for Upgrades
	if ( get_option( 'woo_settings_woo_ratings_version' ) != '' ) {
		$ratings_version_in_db = get_option( 'woo_settings_woo_ratings_version' );
	}
	else {
		$ratings_version_in_db = '0';
	}
	
	global $wpdb;
	$table_name = $wpdb->prefix . "woo_ratings";
	$charset_collate = '';
	if ( ! empty($wpdb->charset) ) {
		$charset_collate = "DEFAULT CHARACTER SET $wpdb->charset";		
	}
	if ( ! empty($wpdb->collate) ) {
		$charset_collate .= " COLLATE $wpdb->collate";		
	}
	
	if(($wpdb->get_var("show tables like '$table_name'") != $table_name) || ($ratings_version_in_db <> $ratings_version)) 
	{
		$sql = "CREATE TABLE " . $table_name . " (".
			"rating_id INT(11) NOT NULL auto_increment,".
			"rating_postid INT(11) NOT NULL ,".
			"rating_rating INT(2) NOT NULL ,".
			"rating_ip VARCHAR(40) NOT NULL ,".
			"rating_host VARCHAR(200) NOT NULL,".
			"PRIMARY KEY (rating_id)) ".$charset_collate.";";
			
		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
		
		update_option('woo_settings_woo_ratings_version',$ratings_version);
			
	}
	
	$role = get_role('administrator');
	if(!$role->has_cap('manage_woo_ratings')) {
		$role->add_cap('manage_woo_ratings');
	}
}

function woo_get_post_rating_form($postid, $ipaddress, $debug = false) {
	
	$host_address = @gethostbyaddr($ipaddress);
	
	global $wpdb;
	$table_name = $wpdb->prefix . "woo_ratings";
	
	$woo_result = $wpdb->get_results("SELECT AVG(rating_rating) as rating_avg FROM ".$table_name." WHERE rating_postid=".$postid);
	if ($woo_result > 0 && isset($woo_result[0]->rating_avg)) {
		$result = round($woo_result[0]->rating_avg,1);
	} else {
		$result = 0;
	}
	
	$number   = $result; // Replace with the number you wish to round
	$rounding = 0.5;   // Replace this with whatever you want to round to

	$roundedUp   = ceil($number/$rounding)*$rounding;  // = 15.5
	$roundedDown = floor($number/$rounding)*$rounding; // = 15
	$rounded     = round($number/$rounding)*$rounding; // = 15
	
	//Count Votes
	$woo_result = $wpdb->get_results("SELECT COUNT(rating_rating) as rating_avg FROM ".$table_name." WHERE rating_postid=".$postid);
	if ($woo_result > 0 && isset($woo_result[0]->rating_avg)) {
		$vote_count = $woo_result[0]->rating_avg;
	} else {
		$vote_count = 0;
	}
	
	if ($vote_count == 1) {
		$vote_count_text = 'Vote';
	} else {
		$vote_count_text = 'Votes';
	}
	
	//Check for previous voting on this meal
	$woo_result = $wpdb->get_results("SELECT rating_rating as rating_avg FROM ".$table_name." WHERE rating_postid=".$postid." AND rating_ip='".$ipaddress."' AND rating_host='".$host_address."'");
	if ($woo_result > 0 && isset($woo_result[0]->rating_avg)) {
		//has already voted - do not allow voting for this ip address
		$img_class = '';
	} else {
		//has not voted - carry on and allow voting for this ip address
		$img_class = 'class="meal-vote" ';
	}	
		
	if ($result > 0) {
		$on_url = get_bloginfo('template_url').'/images/ratings/rating_on.gif';
		$off_url = get_bloginfo('template_url').'/images/ratings/rating_off.gif';
		$half_url = get_bloginfo('template_url').'/images/ratings/rating_half.gif';
		
		
		switch ($rounded)
		{
			case 0.5:
				$output = '<img '.$img_class.'src="'.$half_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 1:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 1.5:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$half_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 2:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 2.5:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$half_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 3:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 3.5:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$half_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 4:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 4.5:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$half_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 5:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
		}
		
	} else {
		$off_url = get_bloginfo('template_url').'/images/ratings/rating_off.gif';
		$output = '<img '.$img_class.'src="'.$off_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
	}
	if ($debug) {
		$output = 'debug';
	}
	return $output;
}

function woo_get_post_rating_average($postid) {
	
	global $wpdb;
	$table_name = $wpdb->prefix . "woo_ratings";
	
	$woo_result = $wpdb->get_results("SELECT AVG(rating_rating) as rating_avg FROM ".$table_name." WHERE rating_postid=".$postid);
	if ($woo_result > 0 && isset($woo_result[0]->rating_avg)) {
		$result = round($woo_result[0]->rating_avg,1);
	} else {
		$result = 0;
	}
	
	$number   = $result; // Replace with the number you wish to round
	$rounding = 0.5;   // Replace this with whatever you want to round to

	$roundedUp   = ceil($number/$rounding)*$rounding;  // = 15.5
	$roundedDown = floor($number/$rounding)*$rounding; // = 15
	$rounded     = round($number/$rounding)*$rounding; // = 15
	
	//Count Votes
	$woo_result = $wpdb->get_results("SELECT COUNT(rating_rating) as rating_avg FROM ".$table_name." WHERE rating_postid=".$postid);
	if ($woo_result > 0 && isset($woo_result[0]->rating_avg)) {
		$vote_count = $woo_result[0]->rating_avg;
	} else {
		$vote_count = 0;
	}
	
	if ($vote_count == 1) {
		$vote_count_text = 'Vote';
	} else {
		$vote_count_text = 'Votes';
	}
	
	$img_class = '';
		
	if ($result > 0) {
		
		$template_url = get_template_directory_uri();
	
		$on_url = $template_url . '/images/ratings/rating_on.gif';
		$off_url = $template_url . '/images/ratings/rating_off.gif';
		$half_url = $template_url . '/images/ratings/rating_half.gif';
		
		
		switch ( $rounded )
		{
			case 0.5:
				$output = '<img '.$img_class.'src="'.$half_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 1:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 1.5:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$half_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 2:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 2.5:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$half_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 3:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 3.5:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$half_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 4:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 4.5:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$half_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
			case 5:
				$output = '<img '.$img_class.'src="'.$on_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$on_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
				break;
		}
		
	} else {
		$off_url = get_bloginfo('template_url').'/images/ratings/rating_off.gif';
		$output = '<img '.$img_class.'src="'.$off_url.'" alt="1" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="2" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="3" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="4" title="'.$vote_count.' '.$vote_count_text.'" /><img '.$img_class.'src="'.$off_url.'" alt="5" title="'.$vote_count.' '.$vote_count_text.'" /><input class="rating-hidden" type="hidden" value="'.$rounded.'" />';
	}
	
	return $output;
	
}

add_action('wp_head', 'woo_ajax_save_post_rating_vote' );

function woo_ajax_save_post_rating_vote() {
  	// Define custom JavaScript function
	?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery.noConflict();
		
		//gets comment form post elements and serializes
		function newRatingValues(post_id, ip_address, vote_value) {
			var string = 'postid=' + post_id + '&ipaddress=' + ip_address + '&votevalue=' + vote_value;
			//var serializedValues = jQuery("#vote-" + post_id).serialize();
			return string;
		}
		
		//Ajax write a comment button click event	
		function woo_ajax_save_vote( post_id, ip_address, vote_value ) {
    		// function body defined below
			//jQuery('#ajax-loader').show();
			
			// 2010-11-02 - Changed in V1.0.5.
			
			var serializedReturn = newRatingValues(post_id, ip_address, vote_value);
			
			// var serializedReturn = 'postid=' + post_id + '&ipaddress=' + ip_address + '&votevalue=' + vote_value;
			
			var ajax_url = '<?php echo admin_url( "admin-ajax.php" ); ?>';
			
			var data = {
			    action: 'woo_ajax_save_vote',
			    data: serializedReturn
			};
			
			jQuery.post(ajax_url, data, function(response) {
			    //jQuery('#ajax-loader').hide();
			    jQuery('#vote-' + post_id).html(response);
			});
			
			return false; 
			
		} // end of JavaScript function 
		//]]>
	</script>
	<?php
} // end of PHP function 

add_action('wp_ajax_woo_ajax_save_vote', 'woo_handle_ajax_save_vote');
add_action('wp_ajax_nopriv_woo_ajax_save_vote', 'woo_handle_ajax_save_vote');

function woo_handle_ajax_save_vote() {
	//variables from the post
	$data = $_POST['data'];
	parse_str($data, $output);
	$post_id = $output['postid'];
	$ip_address = $output['ipaddress'];
	$vote_value = $output['votevalue'];
	$host_address = @gethostbyaddr($ip_address);
	//Insert vote into db
	global $wpdb;
	$table_name = $wpdb->prefix . "woo_ratings";
	$insert_sql = "INSERT INTO ".$table_name." 
					(rating_postid,rating_rating,rating_ip,rating_host) 
					VALUES (".$post_id.",".$vote_value.",'".$ip_address."','".$host_address."')";
	$results = $wpdb->query( $insert_sql );
	//Get the updated voting html
	$results = woo_get_post_rating_form($post_id, $ip_address);
	// Compose JavaScript for return
	die( $results );
}

add_action('wp_head', 'woo_ajax_load_post_rating_output' );

function woo_ajax_load_post_rating_output() {
  	// Define custom JavaScript function
	?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery.noConflict();
		
		//Ajax write a comment button click event	
		function woo_ajax_load_rating_output( post_id, ip_address ) {
    		// function body defined below
			//jQuery('#ajax-loader').show();
			
			var serializedReturn = 'postid=' + post_id + '&ipaddress=' + ip_address;
			 
			var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
			
			var data = {
			    action: 'woo_ajax_load_rating_output',
			    data: serializedReturn
			};
			
			jQuery.post(ajax_url, data, function(response) {
			    //jQuery('#ajax-loader').hide();
			    jQuery('#vote-' + post_id).html(response);
			});
			
			return false; 
			
		} // end of JavaScript function 
		//]]>
	</script>
	<?php
} // end of PHP function 

add_action('wp_ajax_woo_ajax_load_rating_output', 'woo_handle_ajax_load_rating_output');
add_action('wp_ajax_nopriv_woo_ajax_load_rating_output', 'woo_handle_ajax_load_rating_output');

function woo_handle_ajax_load_rating_output() {
	//variables from the post
	$data = $_POST['data'];
	parse_str($data, $output);
	$post_id = $output['postid'];
	$ip_address = $output['ipaddress'];
	//Get the updated voting html
	$results = woo_get_post_rating_form($post_id, $ip_address);
	// Compose JavaScript for return
	die( $results );
}

add_action('wp_head', 'woo_ratings_jquery' );

function woo_ratings_jquery() {
	$template_url = get_template_directory_uri();
	?>
	<script type="text/javascript">
		jQuery(document).ready(function(){
   				
   				//VOTE CLICK EVENT
   				jQuery('.meal-vote').live('click', function () {
   				
   					var votevalue = jQuery(this).attr('alt');
   					var postid = jQuery(this).parent().parent().parent().attr('id');
   					var ipaddress = '<?php echo getenv(REMOTE_ADDR); ?>';
   					//save the vote
   					woo_ajax_save_vote(postid, ipaddress, votevalue);
   					
   				});
   				
   				//Check if allowed to vote
   				if (jQuery('.meal-vote').length != 0) {
   					
   					//VOTE HOVER EVENT
   					jQuery('.rating-images img.meal-vote').live('mouseover', function () {
   				
   						var ratingvalue = parseInt(jQuery(this).attr('alt'));
   						//reload the output
   					
   						jQuery(this).parent().find('img').each(function() {
   							var temprating = parseInt(jQuery(this).attr('alt'));
   							if (temprating <= ratingvalue) {
   								jQuery(this).attr('src', "<?php echo $template_url . '/images/ratings/rating_on.gif'; ?>");
   							} else {
   								jQuery(this).attr('src', "<?php echo $template_url . '/images/ratings/rating_off.gif'; ?>");
   							} 
   						});
   					
   					});
   				
   					//VOTE UNHOVER EVENT
   					jQuery('.rating-images').live('mouseout', function () {
   						
   						if (jQuery(this).find('img.meal-vote').length != 0) {
   							var ipaddress = '<?php echo getenv(REMOTE_ADDR); ?>';
   							var postid = jQuery(this).parent().parent().attr('id');
   							//reload the output
   							woo_ajax_load_rating_output( postid, ipaddress );
   						}
   						
   					});
   				}
   		});
	</script>
	<?php

}

/*-----------------------------------------------------------------------------------*/
/* Woo Menu template ajax functions */
/*-----------------------------------------------------------------------------------*/

add_action('wp_head', 'woo_ajax_email_menu' );

function woo_ajax_email_menu() {
  	// Define custom JavaScript function
	?>
	<script type="text/javascript">
		//<![CDATA[
		jQuery.noConflict();
		
		//gets comment form post elements and serializes
		function newValues() {
			  var serializedValues = jQuery("#modal-email-fields").serialize();
			  return serializedValues;
		}
		
		//Ajax write a comment button click event	
		function woo_ajax_email_js()
		{
    		// function body defined below
			
			var serializedReturn = newValues();
			
			var ajax_url = '<?php echo admin_url( "admin-ajax.php" ); ?>';
			
			var data = {
			    action: 'woo_ajax_email_js',
			    data: serializedReturn
			};
			
			jQuery.post(ajax_url, data, function(response) {
				//prepares comment form for adding comment
			    //code here
			});
			
			return false; 
			
		} // end of JavaScript function 
		//]]>
	</script>
	<?php
} // end of PHP function 

add_action( 'wp_ajax_woo_ajax_email_js', 'woo_handle_ajax_email' );
add_action( 'wp_ajax_nopriv_woo_ajax_email_js', 'woo_handle_ajax_email' );

function woo_handle_ajax_email() {

	global $woo_options;

	//variables from the post
	$post_id = $_POST['post_id'];
	//variables from the post
	$data = $_POST['data'];
	parse_str($data, $output);

	$email_friend_address = esc_attr($output['email']);
	$email_friend_yourname = esc_attr($output['yourname']);
	$email_friend_theirname = esc_attr($output['theirname']);
	
	// $page_url = esc_attr($output['url']); // Incorrect formatting function call. http://forum.woothemes.com/topic.php?id=31184 // 2010-11-08.
	$page_url = esc_html( $output['url'] ); // Corrected formatting function call. http://forum.woothemes.com/topic.php?id=31184 // 2010-11-08.
	
	$action = esc_attr($output['action']);
	
	//set error msg	
	$error = "";
	//prepare comment preview results
	
	$myurl = get_option('siteurl'); 
	preg_match("/^(http:\/\/)?([^\/]+)/i", $myurl, $domain_only );
	
	$message_from_name = $email_friend_yourname . ' via ' . get_bloginfo('name');
	$message_from_email = 'no-reply@' . $domain_only[2];
	
	$message_from_name = $woo_options['woo_sendtofriend_from_name'];
	$message_from_email = $woo_options['woo_sendtofriend_from_email'];
	
	$message_from_name = apply_filters( 'woo_diner_sendtofriend_fromname', $message_from_name );
	$message_from_email = apply_filters( 'woo_diner_sendtofriend_fromemail', $message_from_email );
	
	if ($action == 'location') {
		$message_subject = '';
		
		$message_subject_default = 'Check out this Diner : ';
		
		$message_subject = $woo_options['woo_sendtofriend_location_subject'];
		
		$message_subject = apply_filters( 'woo_diner_sendtofriend_location_subject', $message_subject );
	    
	    if ( ! $message_subject ) { $message_subject = $message_subject_default; } // End IF Statement
	    		
		// Original XHTML version.
		// $message_content = '<p>Hi ' .$email_friend_theirname . '</p>' . '<p>I thought you would enjoy this Diner. Find Directions to the Diner here: ' . $page_url . '</p>' . '<p>Kind Regards</p>' . '<p>' . $email_friend_yourname . '</p>';
		
		// Updated plain text version. // 2010-11-08.
		$message_content = '';
		
		$message_content_default = 'Hi ' .$email_friend_theirname . "\n\n" . 'I thought you would enjoy this Diner. Find Directions to the Diner here: ' . $page_url . "\n\n" . 'Kind Regards,' . "\n" . $email_friend_yourname;
		
		$message_content = $woo_options['woo_sendtofriend_location_message'];
		
		$message_content = apply_filters( 'woo_diner_sendtofriend_location_message', $message_content );
		
		if ( ! $message_content ) { $message_content = $message_content_default; } // End IF Statement
		
	} else {
		$message_subject = '';
		
		$message_subject_default = 'Check out this menu : ';
	    
	    $message_subject = $woo_options['woo_sendtofriend_menu_subject'];
	    
	    $message_subject = apply_filters( 'woo_diner_sendtofriend_menu_subject', $message_subject );
	    
	    if ( ! $message_subject ) { $message_subject = $message_subject_default; } // End IF Statement
	    
	    // Original XHTML version.	
		// $message_content = '<p>Hi ' .$email_friend_theirname . '</p>' . '<p>I thought you would enjoy this menu. Check it out here: ' . $page_url . '</p>' . '<p>Kind Regards</p>' . '<p>' . $email_friend_yourname . '</p>';
		
		// Updated plain text version. // 2010-11-08.
		$message_content = '';
		
		$message_content_default = 'Hi ' .$email_friend_theirname . "\n\n" . 'I thought you would enjoy this menu. Check it out here: ' . $page_url . "\n\n" . 'Kind Regards,' . "\n" . $email_friend_yourname;
		
		$message_content = $woo_options['woo_sendtofriend_menu_message'];
		
		$message_content = apply_filters( 'woo_diner_sendtofriend_menu_message', $message_content );
		
		if ( ! $message_content ) { $message_content = $message_content_default; } // End IF Statement

	} // End IF Statement
	
	// Replace the "shortcodes" with dynamic data.
	
	$codes = array(
					'%theirname%' => $email_friend_theirname, 
					'%yourname%' => $email_friend_yourname, 
					'%url%' => $page_url
				  );
				  
	$strings = array( 'message_from_name', 'message_subject', 'message_content' );
				  
	foreach ( $codes as $c => $d ) {
	
		foreach ( $strings as $s  ) {
		
			if ( $c == '%url%' && $s == 'message_subject' ) {} else {
		
				${$s} = str_replace( $c, $d, ${$s} );
		
			} // End IF Statement
		
		} // End FOREACH Loop
	
	} // End FOREACH Loop
	
	$message_headers = 'From: ' . $message_from_name . ' <' . $message_from_email . '>' . "\r\n";
	
	$message_headers = apply_filters( 'woo_diner_sendtofriend_headers', $message_headers );
			
	$message_addresses = $email_friend_address;
	$results = wp_mail( $message_addresses, $message_subject, $message_content, $message_headers );

	//check for errors
	if( $error ) {
   		die( "alert('$error')" );
	} 
	// Compose JavaScript for return
	die( $results );
}

function woo_curPageURL() {
	$pageURL = 'http';
 	if ($_SERVER["HTTPS"] == "on") {$pageURL .= "s";}
 	$pageURL .= "://";
 	if ($_SERVER["SERVER_PORT"] != "80") {
  		$pageURL .= $_SERVER["SERVER_NAME"].":".$_SERVER["SERVER_PORT"].$_SERVER["REQUEST_URI"];
 	} else {
  		$pageURL .= $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
 	}
 return $pageURL;
}


/*-----------------------------------------------------------------------------------*/
/* Custom Post Type - Slides */
/*-----------------------------------------------------------------------------------*/

add_action( 'init', 'woo_add_slides' );
function woo_add_slides() 
{
  $labels = array(
    'name' => _x( 'Slides', 'post type general name', 'woothemes' ),
    'singular_name' => _x( 'Slide', 'post type singular name', 'woothemes' ),
    'add_new' => _x( 'Add New', 'slide', 'woothemes'),
    'add_new_item' => __( 'Add New Slide', 'woothemes'),
    'edit_item' => __( 'Edit Slide', 'woothemes'),
    'new_item' => __( 'New Slide', 'woothemes'),
    'view_item' => __( 'View Slide', 'woothemes'),
    'search_items' => __( 'Search Slides', 'woothemes'),
    'not_found' =>  __( 'No slides found', 'woothemes'),
    'not_found_in_trash' => __( 'No slides found in Trash', 'woothemes' ), 
    'parent_item_colon' => ''
  );
  $args = array(
    'labels' => $labels,
    'public' => true,
    'publicly_queryable' => true,
    'show_ui' => true, 
    'query_var' => true,
    'rewrite' => true,
    'capability_type' => 'post',
    'hierarchical' => false,
    'menu_icon' => get_template_directory_uri() .'/includes/images/slides.png',
    'menu_position' => null,
    'supports' => array('title','editor',/*'author','thumbnail','excerpt','comments'*/)
  ); 
  register_post_type('slide',$args);
}


function woo_search_filter($query) {
	if ( ( isset($_GET['action']) ) && ( ( $_GET['action'] == 'blog' ) || ( $_GET['action'] == 'global' ) ) )
	{	
		if ($query->is_search) {
			if ($_GET['action'] == 'blog') {
				$query->set('post_type', array('post','page') );
			}
			
		}
	}
	return $query;
}

add_filter('pre_get_posts','woo_search_filter');
?>