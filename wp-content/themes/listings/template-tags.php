<?php
/*
Template Name: Tags
*/
?>

<?php get_header(); ?>
       
    <div id="content" class="page col-full">
		<div id="main" class="fullwidth">
            
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
                                                                        
                <div class="post">

                    <h1 class="title cufon"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
                    
		            <?php if (have_posts()) : the_post(); ?>
	            	<div class="entry">
	            		<?php the_content(); ?>
	            	</div>	            	
		            <?php endif; ?>  
		            
                    <div class="tags">
            			<?php wp_tag_cloud('number=0'); ?>
        			</div>

                </div><!-- /.post -->
        
		</div><!-- /#main -->
		
    </div><!-- /#content -->
		
<?php get_footer(); ?>