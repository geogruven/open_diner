<?php
/**
 * Header Template
 *
 * Here we setup all logic and XHTML that is required for the header section of all screens.
 *
 * @package WooFramework
 * @subpackage Template
 */
 
 global $woo_options, $woocommerce;
 
?><!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>

<meta charset="<?php bloginfo( 'charset' ); ?>" />

<title><?php woo_title(''); ?></title>
<?php woo_meta(); ?>
<link rel="stylesheet" type="text/css" href="<?php bloginfo( 'stylesheet_url' ); ?>" media="screen" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<?php
	wp_head();
	woo_head();
?>

</head>

<body <?php body_class(); ?>>
<?php woo_top(); ?>

<div id="wrapper">

	<?php if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'top-menu' ) ) { ?>

	<div id="top">
		<nav class="col-full" role="navigation">
			<?php wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'top-nav', 'menu_class' => 'nav fl', 'theme_location' => 'top-menu' ) ); ?>
		</nav>
	</div><!-- /#top -->

    <?php } ?>
    
    <?php woo_header_before(); ?>
    
    <div id="header-wrap">

		<header id="header" class="col-full">
		
			<div id="logo" class="fl">
				<?php
				    $logo = get_template_directory_uri() . '/images/logo.png';
				    if ( isset( $woo_options['woo_logo'] ) && $woo_options['woo_logo'] != '' ) { $logo = $woo_options['woo_logo']; }
				?>
				<?php if ( ! isset( $woo_options['woo_texttitle'] ) || $woo_options['woo_texttitle'] != 'true' ) { ?>
				    <a id="logo" href="<?php bloginfo( 'url' ); ?>" title="<?php bloginfo( 'description' ); ?>">
				    	<img src="<?php echo $logo; ?>" alt="<?php bloginfo( 'name' ); ?>" />
				    </a>
			    <?php } ?>
			    
			    <hgroup>
			        
					<h1 class="site-title"><a href="<?php bloginfo( 'url' ); ?>"><?php bloginfo( 'name' ); ?></a></h1>
					<h2 class="site-description"><?php bloginfo( 'description' ); ?></h2>
					<h3 class="nav-toggle"><a href="#navigation"><?php _e('Navigation', 'woothemes'); ?></a></h3>
				      	
				</hgroup>
			</div><!-- /#logo -->		
	        
	        <?php woo_nav_before(); ?>
	
	        <div id="header-right" class="fr">

				<nav id="navigation" role="navigation">
					
					<?php
					if ( function_exists( 'has_nav_menu' ) && has_nav_menu( 'primary-menu' ) ) {
						wp_nav_menu( array( 'depth' => 6, 'sort_column' => 'menu_order', 'container' => 'ul', 'menu_id' => 'main-nav', 'menu_class' => 'nav fl', 'theme_location' => 'primary-menu' ) );
					} else {
					?>
			        <ul id="main-nav" class="nav">
						<?php if ( is_page() ) $highlight = 'page_item'; else $highlight = 'page_item current_page_item'; ?>
						<li class="<?php echo $highlight; ?>"><a href="<?php echo home_url( '/' ); ?>"><?php _e( 'Home', 'woothemes' ); ?></a></li>
						<?php wp_list_pages( 'sort_column=menu_order&depth=6&title_li=&exclude=' ); ?>
					</ul><!-- /#nav -->
			        <?php } ?>
			        
			        <ul class="mini-cart">
						<li>
							<a href="<?php echo $woocommerce->cart->get_cart_url(); ?>" title="<?php _e('View your shopping cart', 'woothemes'); ?>" class="cart-parent">
								<span> 
							<?php 
							echo $woocommerce->cart->get_cart_total();;
							echo sprintf(_n('<mark>%d</mark>', '<mark>%d</mark>', $woocommerce->cart->cart_contents_count, 'woothemes'), $woocommerce->cart->cart_contents_count);
							?>
							</span>
							</a>
							<?php
					
					        echo '<ul class="cart_list">';
					           if (sizeof($woocommerce->cart->cart_contents)>0) : foreach ($woocommerce->cart->cart_contents as $cart_item_key => $cart_item) :
						           $_product = $cart_item['data'];
						           if ($_product->exists() && $cart_item['quantity']>0) :
						               echo '<li class="cart_list_product"><a href="'.get_permalink($cart_item['product_id']).'">';
						               
						               echo $_product->get_image();
						               
						               echo '</a>';
						               
						               if($_product instanceof woocommerce_product_variation && is_array($cart_item['variation'])) :
						                   echo woocommerce_get_formatted_variation( $cart_item['variation'] );
						                 endif;
						               
						               echo '<span class="details quantity"><a href="'.get_permalink($cart_item['product_id']).'">'.apply_filters('woocommerce_cart_widget_product_title', $_product->get_title(), $_product).'</a>' .$cart_item['quantity'].' &times; '.woocommerce_price($_product->get_price()).'</span></li>';
						           endif;
						       endforeach;
					
					        	else: echo '<li class="empty">'.__('No products in the cart.','woothemes').'</li>'; endif;
					        	if (sizeof($woocommerce->cart->cart_contents)>0) :
					            echo '<li class="total"><strong>';
					
					            if (get_option('js_prices_include_tax')=='yes') :
					                _e('Total', 'woothemes');
					            else :
					                _e('Subtotal', 'woothemes');
					            endif;
										
									
										
					            echo ':</strong>'.$woocommerce->cart->get_cart_total();'</li>';
					
					            echo '<li class="buttons"><a href="'.$woocommerce->cart->get_cart_url().'" class="button">'.__('View Cart &rarr;','woothemes').'</a> <a href="'.$woocommerce->cart->get_checkout_url().'" class="button checkout">'.__('Checkout &rarr;','woothemes').'</a></li>';
					        endif;
					        
					        echo '</ul>';
					
					    ?>
						</li>
					</ul>
			      
				</nav><!-- /#navigation -->

				<?php
					if ( isset($woo_options['woo_header_social']) && $woo_options['woo_header_social'] == 'true' ) 
						get_template_part( 'includes/header-social' );
				?>				

			</div><!-- /#header-right -->
			
			<?php woo_nav_after(); ?>
		
		</header><!-- /#header -->
	
	</div><!-- /#header-wrap -->
	
	<?php
	// Output the Features Area	
	if ( /*( $paged == 1 ) && $settings['features_area'] == 'true'*/ is_front_page() || is_home() ) { get_template_part( 'includes/featured' ); } 
	?>

	<?php woo_content_before(); ?>