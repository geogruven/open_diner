<?php
/*
Template Name: Map Full Template
*/
?>

<?php get_header(); ?>
       
    <div id="content" class="page col-full">
		<div id="main" class="fullwidth">
            
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
            <?php if (have_posts()) : $count = 0; ?>
            <?php while (have_posts()) : the_post(); $count++; ?>
                                                                        
                <div class="post">

                    <h1 class="title cufon"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
                    
                    	<?php

						$maps_active = get_post_meta(get_the_id(),'woo_maps_enable',true);
						$src = get_post_meta(get_the_id(),'image',true);
						$maps_overview = get_option('woo_show_overview');
						
						//if($maps_active == 'on' OR !empty($src) OR $maps_overview == 'true') {
						
						// Load Coords from Posts
						$coords = array();
						$pin_amount = 1000;
												
						$search_page = false;
						
						$list_posts = get_posts('post_type=any&post_status=publish&numberposts=-1');
						//print_r($list_posts);
						$x = 0;
						foreach($list_posts as $single_post){
							$x++;
							$id = $single_post->ID;
							$lat = get_post_meta($id, 'woo_maps_lat',true);
							//echo $lat;
							if(!empty($lat)){
								$coords[$id] = array(	'coords' => get_post_meta($id, 'woo_maps_lat',true) . ', ' . get_post_meta($id, 'woo_maps_long',true),
																'color' => $woo_options['woo_cat_colors_pages']);
							} else {
								$x--;
							}
							//echo cat_to_color(get_the_category( $id ));
							if($x == $pin_amount) { break; }
						};
						
						
						if(!empty($coords)) :
						
 						?>
						
    					<div id="featured-map" class="" style="width:880px;">
    					    <div class="woo_map_single_output">
    					    <?php
    					    
    					    //	if($maps_overview == 'true'){
    					    	
						    		$zoom = get_option('woo_archive_zoom');
						    		if(empty($zoom)) $zoom = '6';
						    		$type = get_option('woo_archive_type');
						    		$center = get_option('woo_archive_center');
									
									switch ($type) {
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
			  						}
									
						    		// API KEY NO LONGER NEEDED
						    		
						    		$feat_h = get_option('woo_archive_featured_h');
						    		if(empty($feat_h)){ $feat_h = 250;}
						    		?>
						    		<div id="featured_overview" style="height:<?php echo $feat_h; ?>px; width:100%"></div>
						    		<?php		
						
						    		/* Maps Bit */
						    		
						    		$coord_keys = array_keys($coords);
						    		$first_key = $coord_keys[0];
						
						    		$center_final = $coords[$first_key]['coords'];
						    		if(!empty($center)) { $center_final = $center; }
						
						    		?>
						    		<script src="<?php bloginfo('template_url'); ?>/includes/js/markers.js" type="text/javascript"></script>
						    		<script type="text/javascript">
						    		jQuery(document).ready(function(){
						    			function initialize() {
						    			 	var myLatlng = new google.maps.LatLng(<?php echo $center_final; ?>);
											var myOptions = {
											  zoom: <?php echo $zoom; ?>,
											  center: myLatlng,
											  mapTypeId: google.maps.MapTypeId.<?php echo $type; ?>
											};
			  								var map = new google.maps.Map(document.getElementById("featured_overview"),  myOptions);
											<?php if(get_option('woo_maps_archive_scroll') == 'true'){ ?>
			  								map.scrollwheel = false;
			  								<?php } ?>
						    			 	
						    			 	<?php foreach($coords as $c_key => $c_value) { ?>
						    			 		var point = new google.maps.LatLng(<?php echo $c_value['coords']; ?>);
	  											var root = "<?php bloginfo('template_url'); ?>";
	  											var the_link = '<?php echo get_permalink($c_key); ?>';
	  											<?php $title = str_replace(array('&#8220;','&#8221;'),'"',get_the_title($c_key)); ?>
	  											<?php $title = str_replace('&#8211;','-',$title); ?>
	  											<?php $title = str_replace('&#8217;',"`",$title); ?>
	  											<?php $title = str_replace('&#038;','&',$title); ?>
	  											var the_title = '<?php echo html_entity_decode($title) ?>';
						    			 		
						    			 		var color = '<?php echo $c_value['color']; ?>';
			 									createMarker(map,point,root,the_link,the_title,color);
						    			 	<?php } ?>
						    		}
						    	initialize();
						    	});
						    	</script>
						    </div>
    					</div>
    					
    					<?php else : ?><div class="spacer"></div><?php
    					endif;  ?>

					<?php edit_post_link( __('{ Edit }', 'woothemes'), '<span class="small">', '</span>' ); ?>

                </div><!-- /.post -->
                                                    
			<?php endwhile; else: ?>
				<div class="post">
                	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
                </div><!-- /.post -->
            <?php endif; ?>  
        
		</div><!-- /#main -->
		
    </div><!-- /#content -->
		
<?php get_footer(); ?>