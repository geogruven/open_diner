<?php get_header(); ?>
<?php global $woo_options; ?>
	
	<?php $search_test = get_option('woo_listings_search_results'); if ( $search_test != '' ) { ?>
		<?php get_template_part('search-form'); ?>
	<?php } else { ?>
		<div class="search_module">
			<p class="woo-sc-box note"><?php _e('Please setup the Search Options in your options panel first.','woothemes'); ?></p>
		</div>
	<?php } ?>
	
	<?php $showfeatured = $woo_options['woo_featured']; if ($showfeatured <> "true") { if (get_option('woo_exclude')) update_option("woo_exclude", ""); } ?>
    <?php if ( !$paged && $showfeatured == "true" ) get_template_part ( 'includes/featured' ); ?>
    
    <div id="content" class="col-full home-content">

		<div id="main" class="fullwidth">      
                    
		<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>

        	<?php get_template_part('includes/categories-panel'); ?>
        	<?php get_template_part('includes/latest-listings'); ?>
        	
		</div><!-- /#main -->

    </div><!-- /#content -->
		
<?php get_footer(); ?>