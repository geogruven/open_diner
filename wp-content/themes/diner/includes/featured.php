<?php if ( !$paged && get_option('woo_featured') == "true" ) { ?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery("#loopedSlider").loopedSlider({
	<?php
		$autoStart = 0;
		$slidespeed = 600;
		if ( get_option("woo_slider_auto") == "true" ) 
		   $autoStart = get_option("woo_slider_interval") * 1000;
		else 
		   $autoStart = 0;
		if ( get_option("woo_slider_speed") <> "" ) 
			$slidespeed = get_option("woo_slider_speed") * 1000;
	?>
		autoStart: <?php echo $autoStart; ?>, 
		slidespeed: <?php echo $slidespeed; ?>, 
		autoHeight: true
	});
});
</script>
<?php } ?>
<div id="sliderWrap">
<div class="innerwrap">
<div id="loopedSlider">
    <?php $img_pos = get_option('woo_featured_img_pos'); ?>
    <?php $saved = $wp_query; query_posts('suppress_filters=0&post_type=slide&order=ASC&orderby=date&showposts=20'); ?>
	<?php if (have_posts()) : $count = 0; $postcount = $wp_query->post_count; ?>

    <div class="container">
    
        <div class="slides">
        
            <?php while (have_posts()) : the_post(); ?>
            <?php if (!woo_image('return=true')) continue; // Don't show slides without image ?>
   		 	<?php $count++; ?>
   		 	
            <div id="slide-<?php echo $count; ?>" class="slide">
        		
        		<div class="slide-content <?php if($img_pos == "Left") { echo "fr"; } else { echo "fl"; } ?>">
            	
       		     	<h2 class="slide-title">
       		     		<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
			     		<?php if ($postcount > 1) echo '<span class="controlsbg">&nbsp;</span>'; ?>
       		     	</h2>
       		     	
       		     	<p><?php the_content(); ?></p>
       		     	       		 		
       		 	</div><!-- /.slide-content -->
       		 	
       		 	<?php if (woo_image('return=true')) { ?>
       		 	<div class="image <?php if($img_pos == "Left") { echo "fl"; } else { echo "fr"; } ?>">
	            	<?php woo_image('width=515&height=245&class=feat-image&link=img'); ?>
            	</div>
            	<?php } ?>
       		     	
       		    <div class="fix"></div>
        
            </div>
            
			<?php endwhile; ?> 
			
		</div><!-- /.slides -->
		<?php if($count > 1) { ?>
			<ul class="nav-buttons <?php if($img_pos == "Left") { echo "right"; } else { } ?>">
        	    <li id="p"><a href="#" class="previous">&lt;</a></li>
    			<li id="n"><a href="#" class="next">&gt;</a></li>
        	</ul>
        <?php } ?>
		
    </div><!-- /.container -->
    
	<div class="fix"></div>
    
     <?php else :  ?>
     	<p class="note"><?php _e( 'Please add some "Slides" to the featured slider.', 'woothemes' ); ?></p>
     <?php endif; $wp_query = $saved;?>   
</div><!-- /#loopedSlider -->
</div>
</div><!-- /#sliderWrap -->
