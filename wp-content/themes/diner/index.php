<?php get_header(); ?>
<?php global $woo_options; ?>

    <div id="content" class="col-full">
    
		<?php if ( get_option('woo_featured') == "true" ) include ( TEMPLATEPATH . '/includes/featured.php' ); ?>
		
		<?php if ((woo_active_sidebar('home-1')) || (woo_active_sidebar('home-2')) || (woo_active_sidebar('home-3'))) : ?>
    		<div id="home-widgets">
    		
			    <?php if (woo_active_sidebar('home-1')) : ?>
    				<div class="col">
					    <?php woo_sidebar('home-1'); ?>		           
					</div>        
				<?php endif; ?>
				
				<?php if (woo_active_sidebar('home-2')) : ?>
    				<div class="col">
					    <?php woo_sidebar('home-2'); ?>		           
					</div>        
				<?php endif; ?>
				
				<?php if (woo_active_sidebar('home-3')) : ?>
    				<div class="col last">
					    <?php woo_sidebar('home-3'); ?>		           
					</div>        
				<?php endif; ?>
				
				<div class="fix"></div>
				
			</div>        
		<?php endif; ?>
		
		<?php if ( $woo_options['woo_ad_home'] == 'true' ) { ?>
        	<div id="home-ad">
			    <?php if ($woo_options['woo_ad_home_adsense'] <> "") { echo stripslashes($woo_options['woo_ad_home_adsense']); ?>
			    
			    <?php } else { ?>
			    
			    	<a href="<?php echo $woo_options['woo_ad_home_url']; ?>"><img src="<?php echo $woo_options['woo_ad_home_image']; ?>" width="728" height="90" alt="advert" /></a>
			    	
			    <?php } ?>		   	
			</div><!-- /#home-ad -->
        <?php } ?>
    
		<?php if ( $woo_options['woo_info_home'] == 'true' ) { ?>
		<div id="more-info">
		
			<?php if (get_option('woo_diner_intro_text_image')) { ?>
			<div class="image fl">				
            	<?php woo_image('width=275&height=245&src='.get_option('woo_diner_intro_text_image')); ?>
			</div><!-- /.image -->
			<?php } ?>
			
			<div class="text fr">
				
				<h3><?php echo get_option('woo_diner_intro_text_header'); ?></h3>
				
				<div class="entry">
					<p><?php echo get_option('woo_diner_intro_text'); ?></p>
				</div>
			
			</div>
		
		</div><!-- /#more-info -->
        <?php } ?>
		
		<div class="fix"></div>

    </div><!-- /#content -->
		
<?php get_footer(); ?>