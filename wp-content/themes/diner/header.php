<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head profile="http://gmpg.org/xfn/11">

<title><?php woo_title(); ?></title>
<?php woo_meta(); ?>
<?php global $woo_options; ?>

<link rel="stylesheet" type="text/css" href="<?php bloginfo('stylesheet_url'); ?>" media="screen" />
<link rel="alternate" type="application/rss+xml" title="RSS 2.0" href="<?php if ( $woo_options['woo_feed_url'] ) { echo $woo_options['woo_feed_url']; } else { echo get_bloginfo_rss('rss2_url'); } ?>" />
<link rel="pingback" href="<?php bloginfo('pingback_url'); ?>" />
      
<?php wp_head(); ?>
<?php woo_head(); ?>

</head>

<body <?php body_class(); ?>>
<?php woo_top(); ?>

<div id="wrapper">
           
	<div id="header" class="col-full">
	
		<div class="inside-header">
 		       
			<div id="logo">
	    	   
			<?php if ($woo_options['woo_texttitle'] <> "true") : $logo = $woo_options['woo_logo']; ?>
        	    <a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('description'); ?>">
        	        <img src="<?php if ($logo) echo $logo; else { bloginfo('template_directory'); ?>/images/logo.png<?php } ?>" alt="<?php bloginfo('name'); ?>" />
        	    </a>
        	<?php endif; ?> 
        	
        	<?php if( is_singular() ) : ?>
        	    <span class="site-title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></span>
        	<?php else : ?>
        	    <h1 class="site-title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
        	<?php endif; ?>
        	    <span class="site-description"><?php bloginfo('description'); ?></span>
	    	  	
			</div><!-- /#logo -->
			
			<?php if( ($woo_options['woo_reservations'] == 'true') && ($woo_options['woo_ad_top'] == 'false')) { ?>
			
				<div id="reservations">
				
					<?php if ( $woo_options['woo_reservations_heading'] ) { ?><span class="heading"><?php echo stripslashes($woo_options['woo_reservations_heading']); ?></span><?php } ?>
					<?php if ( $woo_options['woo_reservations_text'] ) { ?><span class="number"><?php echo stripslashes($woo_options['woo_reservations_text']); ?></span><?php } ?>
					<a class="button" href="<?php global $wootable; echo get_permalink( $wootable->frontend->bookings_page ); ?>" title="<?php echo stripslashes($woo_options['woo_reservations_button']); ?>"><?php echo stripslashes($woo_options['woo_reservations_button']); ?></a>
				
				</div><!-- /#reservations -->
			
	    	   
			<?php } else if ( $woo_options['woo_ad_top'] == 'true' ) { ?>
        	<div id="topad">
				<?php if ($woo_options['woo_ad_top_adsense'] <> "") { echo stripslashes($woo_options['woo_ad_top_adsense']); ?>
				
				<?php } else { ?>
				
					<a href="<?php echo $woo_options['woo_ad_top_url']; ?>"><img src="<?php echo $woo_options['woo_ad_top_image']; ?>" width="468" height="60" alt="advert" /></a>
					
				<?php } ?>		   	
			</div><!-- /#topad -->
        	<?php } ?>
        
        </div><!-- /.inside -->
       
	</div><!-- /#header -->
    
	<div id="navigation" class="col-full">
		<?php
		if ( function_exists('has_nav_menu') && has_nav_menu('primary-menu') ) {
			wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl', 'theme_location' => 'primary-menu' ) );
		} else {
		?>
        <ul id="main-nav" class="nav fl">
			<?php 
        	if ( isset($woo_options['woo_custom_nav_menu']) AND $woo_options['woo_custom_nav_menu'] == 'true' ) {
        		if ( function_exists('woo_custom_navigation_output') )
					woo_custom_navigation_output();

			} else { ?>
            	
	            <?php if ( is_page() ) $highlight = "page_item"; else $highlight = "page_item current_page_item"; ?>
	            <li class="<?php echo $highlight; ?>"><a href="<?php bloginfo('url'); ?>"><?php _e('Home', 'woothemes') ?></a></li>
	            <?php 
	            	//Take out manage reservations page
	    			//$reservation_make_page = get_option('wootable_page_booking');
	            	$reservation_manage_page = get_option('wootable_page_manage');
	    			wp_list_pages('sort_column=menu_order&depth=6&title_li=&exclude='.$reservation_make_page.','.$reservation_manage_page); 

			}
			?>
        </ul><!-- /#nav -->
        <?php } ?>
        
	</div><!-- /#navigation -->
       