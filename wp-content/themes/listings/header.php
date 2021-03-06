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

<?php print_r(get_option('framework_woo_font_stack')); ?>

<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
    
</head>

<body <?php body_class(); ?>>
<?php woo_top(); ?>

<div id="wrapper">
           
	<div id="header" class="col-full">
 		       
		<div id="logo">
	       
		<?php if ($woo_options['woo_texttitle'] <> "true") : $logo = $woo_options['woo_logo']; ?>
            <a href="<?php bloginfo('url'); ?>" title="<?php bloginfo('description'); ?>">
                <img src="<?php if ($logo) echo $logo; else { bloginfo('template_directory'); ?>/images/logo.png<?php } ?>" alt="<?php bloginfo('name'); ?>" />
            </a>
        <?php endif; ?> 
        
        <?php if( is_singular() && !is_front_page() ) : ?>
            <span class="site-title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></span>
        <?php else : ?>
            <h1 class="site-title"><a href="<?php bloginfo('url'); ?>"><?php bloginfo('name'); ?></a></h1>
        <?php endif; ?>
            <span class="site-description"><?php bloginfo('description'); ?></span>
	      	
		</div><!-- /#logo -->
	       
		<?php if ( $woo_options['woo_ad_top'] == 'true' ) { ?>
        <div id="topad">
			<?php if ($woo_options['woo_ad_top_adsense'] <> "") { echo stripslashes($woo_options['woo_ad_top_adsense']); ?>
			
			<?php } else { ?>
			
				<a href="<?php echo $woo_options['woo_ad_top_url']; ?>"><img src="<?php echo $woo_options['woo_ad_top_image']; ?>" width="468" height="60" alt="advert" /></a>
				
			<?php } ?>		   	
		</div><!-- /#topad -->
        <?php } elseif ($woo_options['woo_social_twitter'] != '')  { ?>
        <div id="twitter-top">
        <?php
        	$limit = 1;
			$username = $woo_options['woo_social_twitter'];
			$unique_id = 'twitter_feed';
		?>
		<h3 class="tlogo"><img src="<?php bloginfo('template_directory'); ?>/images/twitter.png" /></h3>
		<span class="bird"><a href="http://www.twitter.com/<?php echo $username; ?>" title="<?php echo $username; ?>"><img src="<?php bloginfo('template_directory'); ?>/images/ico-twitter.png" alt="Twitter" /></a></span>
		<ul id="twitter_update_list_<?php echo $unique_id; ?>"><li></li></ul>
		<?php echo woo_twitter_script($unique_id,$username,$limit); //Javascript output function ?>	 
        </div><!-- /#twitter -->
        <?php } ?>
	   
	</div><!-- /#header -->

<div id="container" class="col-full">

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
	    			wp_list_pages('sort_column=menu_order&depth=6&title_li=&exclude='.get_option('woo_listings_cpt_page')); 

			}
			?>
        </ul><!-- /#nav -->
        <?php } ?>
        
	</div><!-- /#navigation -->
       