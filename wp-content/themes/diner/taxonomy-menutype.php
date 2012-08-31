<?php get_header(); ?>
<?php 
// Global query variable
global $wp_query; 
// Get taxonomy query object
$taxonomy_archive_query_obj = $wp_query->get_queried_object();
// Taxonomy term name
$taxonomy_term_nice_name = $taxonomy_archive_query_obj->name;
// Taxonomy term id
$term_id = $taxonomy_archive_query_obj->term_taxonomy_id;
// Get taxonomy object
$taxonomy_short_name = $taxonomy_archive_query_obj->taxonomy;
$taxonomy_raw_obj = get_taxonomy($taxonomy_short_name);
// You can alternate between these labels: name, singular_name
$taxonomy_full_name = $taxonomy_raw_obj->labels->name;
?>    
    <div id="content" class="col-full">
		<div id="main" class="col-left">
            
		<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
		<?php if (have_posts()) : $count = 0; ?>

            <span class="archive_header"><?php _e($taxonomy_full_name.' Archives:', 'woothemes'); ?> <?php _e($taxonomy_term_nice_name,'woothemes'); ?></span>
            
            <?php if (trim($taxonomy_archive_query_obj->description) != '') { ?><p><?php echo $taxonomy_archive_query_obj->description; ?></p><?php } ?>
            
            <div class="fix"></div>
        
        <?php while (have_posts()) : the_post(); $count++; ?>
           	
           	<?php 	$price = get_post_meta(get_the_ID(),'price',true);
					$meal_price = number_format($price , 2 , '.', ','); ?>                                                         
            <!-- Post Starts -->
            <div class="post<?php if ($meal_price > 0) { ?> woo_menu<?php } ?>">

                <h2 class="title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h2>
                
                <div class="fix"></div>
                
                <?php woo_image('key=thumbnail&width='.$woo_options['woo_thumb_w'].'&height='.$woo_options['woo_thumb_h'].'&class=thumbnail '.$woo_options['woo_thumb_align']); ?> 
                
                <div class="entry">
                    <?php if ( $woo_options['woo_post_content'] == "content" ) the_content(__('Read More...', 'woothemes')); else the_excerpt(); ?>
                    <p class="ratings"><?php _e('Average Rating:', 'woothemes'); ?> <?php echo woo_get_post_rating_average(get_the_ID()); ?></p>
                </div><!-- /.entry -->

                <div class="post-more menu-comments">      
                	<?php if ( $woo_options['woo_post_content'] == "excerpt" ) { ?>
                    <span class="read-more"><a href="<?php the_permalink() ?>" title="<?php _e('Continue Reading','woothemes'); ?>"><?php _e('Continue Reading','woothemes'); ?></a></span>
                    <?php } ?>
                    <span class="comments"><?php _e('Price per Serving:', 'woothemes'); ?> <?php echo get_option('woo_diner_currency').''.$meal_price; ?></span>
                </div>   

            </div><!-- /.post -->
            
        <?php endwhile; else: ?>
        
            <div class="post">
                <p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
            </div><!-- /.post -->
        
        <?php endif; ?>  
    
			<?php woo_pagenav(); ?>
                
		</div><!-- /#main -->

        <?php get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>