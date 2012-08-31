<?php get_header(); ?>
<?php global $woo_options; ?>
       
    <div id="content" class="col-full">
		<div id="main" class="col-left">
		           
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>

            <?php if (have_posts()) : $count = 0; ?>
            <?php while (have_posts()) : the_post(); $count++; ?>
            	<?php 	$price = get_post_meta(get_the_ID(),'price',true);
						$meal_price = number_format($price , 2 , '.', ','); ?>
					
				<div <?php post_class(); ?>>

                    <h1 class="title"><?php the_title(); ?></h1>
                    
                    <div class="fix"></div>
                    
                    <div class="entry">
                    	<?php the_content(); ?>
                    </div>
					
					<?php woo_image('key=thumbnail&width=600&class=thumbnail ' . $woo_options['woo_thumb_single_align'] . '&force=true' ); ?>
					
					
					<?php
					$results = '';
					$attach_args = array(
							'post_type' => 'attachment',
							'post_mime_type' => 'image',
							'numberposts' => -1,
							'post_status' => null,
							'post_parent' => get_the_ID()
							); 
					$attach_posts = get_posts($attach_args);
					
					if ( count( $attach_posts ) > 1 ) { ?>
					<div class="gallery">
					<?php	
						foreach ($attach_posts as $attach_post) {
							$mime_type = $attach_post->post_mime_type;
							$link_type_array = explode('/', $mime_type);
							$link_type = strtolower($link_type_array[0]);
						
							if ($link_type == 'image') {
								$img = wp_get_attachment_image($attach_post->ID, $size='thumbnail', $icon = false);
								$link = wp_get_attachment_image_src($attach_post->ID, $size='full', $icon = false);
								$link_url= $link[0];
								// Prep Output		
								$results .= '<a href="'.$link_url.'" class="thickbox" rel="serving-group" title="'.$attach_post->post_excerpt.'">'.$img.'</a>' . "\n";							
								$num_imgs++;
							}
						}
						echo $results; ?>
					</div>
					<?php } ?>
					<?php
						$_params = array(
								    'before'           => '',
								    'after'            => '',
								    'link_before'      => '',
								    'link_after'       => '',
								    'next_or_number'   => 'number',
								    'nextpagelink'     => __('Next page'),
								    'previouspagelink' => __('Previous page'),
								    'pagelink'         => '%',
								    'more_file'        => '',
								    'echo'             => 1
								    );
						
						wp_link_pages( $_params );
					?>
					<div class="fix"></div>
					
										
					<?php the_tags('<p class="tags">'.__('Tags: ', 'woothemes'), ', ', '</p>'); ?>
					<p class="ratings"><?php _e('Average Rating:', 'woothemes'); ?> <?php echo woo_get_post_rating_average(get_the_ID()); ?></p> 
					
					<span class="comments"><?php _e('Price per Serving:', 'woothemes'); ?> <?php echo get_option('woo_diner_currency').''.$meal_price; ?></span>
					
                </div><!-- /.post -->
				
				<div class="fix"></div>
				
                <?php woo_postnav(); ?>
                                                    
			<?php endwhile; else: ?>
				<div class="post">
                	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
  				</div><!-- /.post -->             
           	<?php endif; ?>  
        
		</div><!-- /#main -->

        <?php get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>