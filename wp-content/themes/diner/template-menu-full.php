<?php
/*
Template Name: Menu Full
*/
?>

<?php get_header(); ?>
    
    <script type="text/javascript">
    jQuery(document).ready(function(){
		
		
		var yourname = jQuery( "#yourname" ),
			email = jQuery( "#email" ),
			theirname = jQuery( "#theirname" ),
			allFields = jQuery( [] ).add( yourname ).add( email ).add( theirname ),
			tips = jQuery( ".validateTips" );

		function updateTips( t ) {
			tips
				.text( t )
				.addClass( "ui-state-highlight" );
			setTimeout(function() {
				tips.removeClass( "ui-state-highlight", 1500 );
			}, 500 );
		}

		function checkLength( o, n, min, max ) {
			if ( o.val().length > max || o.val().length < min ) {
				o.addClass( "ui-state-error" );
				updateTips( "Length of " + n + " must be between " +
					min + " and " + max + "." );
				return false;
			} else {
				return true;
			}
		}

		function checkRegexp( o, regexp, n ) {
			if ( !( regexp.test( o.val() ) ) ) {
				o.addClass( "ui-state-error" );
				updateTips( n );
				return false;
			} else {
				return true;
			}
		}

		jQuery('#modal-email').hide();
	
		// Initialise the dialog box
		jQuery('#modal-email').dialog({
								autoOpen: false, 
								title: '<?php _e('Send to a friend', 'woothemes'); ?>', 
								resizable: false,
								height: 175,
								width: 300, 
								modal: true, 
								buttons: {
									Send: function() {
										//jQuery('#modal-email-fields').validate();
										
										var bValid = true;
										allFields.removeClass( 'ui-state-error' );

										bValid = bValid && checkLength( yourname, "<?php _e('your name', 'woothemes'); ?>", 3, 16 );
										bValid = bValid && checkLength( theirname, "<?php _e('your friends name', 'woothemes'); ?>", 5, 16 );
										bValid = bValid && checkLength( email, "<?php _e('email', 'woothemes'); ?>", 6, 80 );
										
										bValid = bValid && checkRegexp( yourname, /^[a-z]([0-9a-z _])+$/i, "<?php _e('Your name may consist of a-z, 0-9, underscores, begin with a letter.', 'woothemes'); ?>" );
										bValid = bValid && checkRegexp( theirname, /^[a-z]([0-9a-z _])+$/i, "<?php _e('Your friends name may consist of a-z, 0-9, underscores, begin with a letter.', 'woothemes'); ?>" );
										// From jquery.validate.js (by joern), contributed by Scott Gonzalez: http://projects.scottsplayground.com/email_address_validation/
										bValid = bValid && checkRegexp( email, /^((([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+(\.([a-z]|\d|[!#\$%&'\*\+\-\/=\?\^_`{\|}~]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])+)*)|((\x22)((((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(([\x01-\x08\x0b\x0c\x0e-\x1f\x7f]|\x21|[\x23-\x5b]|[\x5d-\x7e]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(\\([\x01-\x09\x0b\x0c\x0d-\x7f]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF]))))*(((\x20|\x09)*(\x0d\x0a))?(\x20|\x09)+)?(\x22)))@((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)+(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?$/i, "eg. ui@jquery.com" );
										
										if ( bValid ) {
											woo_ajax_email_js();
											jQuery( this ).dialog( "close" );
										}

	 									
									}, 
									Cancel: function() {
										jQuery(this).dialog("close");
										
									}
								}
							});
		
	});
    </script>
    <?php $ipaddress = getenv(REMOTE_ADDR); ?>
       
    <div id="content" class="page menu col-full">
    	
    	<div class="post-wrap">
            
   			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>
   			<?php if (have_posts()) : $count = 0; ?>
   			<?php while (have_posts()) : the_post(); $count++; ?>
   			    <?php
   			    //custom field
   			    $menu_additional_info = get_post_meta($post->ID,'menu_additional_info',true);
   			    $menu_pdf = get_post_meta($post->ID,'menu_pdf',true);
   			    ?>                                                        
   			    <div class="post fl">
   			
   			        <h1 class="title"><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a></h1>
   			        
   			        <div class="entry">
   			        	<?php the_content(); ?>
   			       	</div><!-- /.entry -->
   			
   			    </div><!-- /.post -->
   			    
   			    <div class="additional fr">
   			    
   			    	<h2><?php _e('Additional Info', 'woothemes'); ?></h2>
   			    
   			    	<p><?php echo $menu_additional_info; ?></p>
   			    
   			    </div>
   			    
   			    <div class="fix"></div>
   			                                        
   			<?php endwhile; endif; ?>
   		
   		</div><!-- /.post-wrap -->
   		
   		<div id="menu" class="full-width-menu">
   		
   			<div class="info-bar">
   				
   				<ul class="fl">
   					<?php if ($menu_pdf != '') { ?><li class="pdf"><a href="<?php echo $menu_pdf; ?>" title="Menu"><?php _e('Download PDF', 'woothemes'); ?></a></li><?php } ?>
   					<li class="email <?php if ($menu_pdf == '') { echo 'no-pdf'; } ?>"><a href="#" title="#"><?php _e('Email to Friend', 'woothemes'); ?></a></li>
   				</ul>
   				
   				<div class="order fr">
   					<span><?php _e('Order by:', 'woothemes'); ?></span>
   					<a id="sort-default" class="active" title="#"><?php _e('Default', 'woothemes'); ?></a>
   					<a id="sort-price" title="#"><?php _e('Price', 'woothemes'); ?></a>
   					<a id="sort-rating" title="#"><?php _e('Rating', 'woothemes'); ?></a>
   				</div>
   				
   				<div class="fix"></div>
   				
   			</div><!-- /.info-bar -->
   			<?php 
   			
   			// Make menu system recursive. - 2010-11-14.
   			
   			global $more;
   						
			// Set the global "more" value to activate/deactivate automated "More" links in the_content().
			$more = 0; // Automated "More" links are set to "on". 0 = on, 1 = off.
   			
   			// Add custom ordering to the menu types. - 2010-11-30.
   			
   			$menu_ordering = get_option( 'woo_diner_menutype_orders' );
   			
   			// If the menu ordering values returned aren't in an array, reset them as they're not correct.
   			
   			if ( ! is_array( $menu_ordering ) ) {
   			
   				$menu_ordering = array();
   			
   			} // End IF Statement
   			
   			function woo_food_menu_display( $parent_id, $menu_ordering ) {
   			
   			$terms = get_terms( 'menutype', 'parent=' . $parent_id . '&hide_empty=0' );
			
			$_terms = array();
			
			// Loop through and sort the terms by their sort ordering setting.
			if ( count( $terms ) ) {
			
				foreach ( $terms as $t ) {
				
					if ( ! in_array( $t->term_id, array_keys( $menu_ordering ) ) ) {
					
						$_terms['0-' . $t->term_id] = $t;
					
					} else {
					
						$_ordering_value = $menu_ordering[$t->term_id];
						
						if ( $_ordering_value < 10 ) {
						
							$_ordering_value = '0' . $_ordering_value;
						
						} // End IF Statement

						$_terms[$_ordering_value . '-' . $t->term_id] = $t;
					
					} // End IF Statement
				
				} // End FOREACH Loop
			
			} // End IF Statement
			
			// Sort the terms by key.
			ksort( $_terms );
			
			if ( count( $_terms ) ) {
				
			foreach ( $_terms as $k => $term ) {
					
			// foreach ($terms as $term) {
						
				// query_posts('post_type=woo_menu&menutype='.$term->slug);
			    $category_link = get_term_link( $term, 'menutype' );    
   			?>	
   			<table>
   			
   			    <thead>
   			    	<tr>
   			    		<th colspan="3"><h2><?php echo $term->name; ?> <?php if ($term->description != '') { ?><span class="asterix"><a href="#info-1">*</a></span> <?php } ?><span class="about-section"><a href="<?php echo $category_link; ?>" title="<?php _e('More', 'woothemes'); ?>"><?php _e('More about this section', 'woothemes'); ?></a></span></h2></th>
   			    	</tr>
   			    </thead>
   			    <?php if ($term->description != '') { ?>
   			    <tfoot>
   			    	<tr class="asterix-info">
   			    		<td  colspan="3"><span id="info-1">*</span> <?php echo $term->description; ?></td>
   			    	</tr>
   			    </tfoot>
   			    <?php } ?>
   			    <tbody>
   			    	<?php
   			    	$query_args = array( 'post_type' => 'woo_menu','paged' => $paged, 'menutype' => $term->slug, 'orderby' => 'menu_order', 'order' => 'ASC', 'posts_per_page' => '-1' );      
					$the_query = new WP_Query($query_args);
					if ($the_query->have_posts()) : $count = 0;
					while ($the_query->have_posts()) : $the_query->the_post();
						global $post;
						$postid = get_the_ID();
						$price = get_post_meta($postid,'price',true);
						$meal_price = number_format($price , 2 , '.', ',');
						$img_link = woo_image('return=true&link=img&key=thumbnail&width=170&class=thumbnail thickbox');
						$img_url = get_post_meta($postid,'thumbnail',true);
						
						// Get the terms assigned to this post.
						$_terms_obj = get_the_terms( $postid, 'menutype' );
						// Create an array of the assigned term IDs.
						$_terms = array_keys( $_terms_obj );
						
						if ( in_array( $term->term_id, $_terms ) ) :
					?>
   			    	<tr id="<?php echo $postid; ?>">
   			    		<td class="image"><a href="<?php echo $img_url; ?>" rel="lightbox-group" class="thickbox" title="<?php the_title_attribute(); ?>"><?php echo $img_link; ?></a></td>
   			    		<td class="details">
   			    			<h3><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h3>
   			    			<?php the_content( __( '(more...)', 'woothemes' ) ); ?>
   			    		</td>
   			    		<td class="price"><?php echo get_option('woo_diner_currency').''.$meal_price; ?><br /><span id="vote-<?php echo $postid; ?>" class="rating-images"><?php echo woo_get_post_rating_form($postid,$ipaddress); ?></span></td>
   			    	</tr>
   			    	<?php
	   							endif; // End IF ( in_array() ) IF Statement
	   						endwhile;
   						endif;
   					?>
   			    	
   			    </tbody>
   			    
   			
   			</table>
   			<?php
   						woo_food_menu_display( $term->term_id, $menu_ordering );
   						
   					} // End FOREACH Loop
   			
   				} // End IF Statement
   			
   				} // End woo_food_menu_display()
   				
   				
   				
   				woo_food_menu_display( 0, $menu_ordering );
   				
   			?>
   			
   			<script type="text/javascript">
   			jQuery(document).ready(function(){
   				
   				jQuery('.email').click(function(){
   					jQuery('#modal-email').dialog('open');
   				});
   				
   				jQuery('#sort-price').click(function(){
   					
   					jQuery(this).addClass('active');
   					jQuery('#sort-rating').removeClass('active');
   					jQuery('#sort-default').removeClass('active');
   					
   					jQuery('#menu table').each(function(){
   						
   						var meals = [];
   						var mealshtml = [];
   						
   						jQuery(this).find('tbody tr').each(function(){
   						
   							// var parentid = parseFloat(jQuery(this).find('td.price').text().substr(1)); // 2010-11-03.
   							
   							var priceString = jQuery(this).find('td.price').text().substr(1); // 2010-11-03.
   							
   							priceString = priceString.replace(',', ''); // 2010-11-03.
   							
   							// Remove all non-integers from the price. // 2010-11-04.
   							
							priceString = priceString.replace(/[^0-9.]/g, '');
   							
   							var parentid = parseFloat( priceString ); // 2010-11-03.
   							
   							if (parentid > 0) {
   								mealshtml[parentid] = '<tr id="' + jQuery(this).attr('id') + '">' + jQuery(this).html() + '</tr>';
   							}
   					
   						});
   						
   						jQuery(this).find('tbody tr td.price').each(function(){
   						
   							var parentid = jQuery(this).parent().attr('id');
   							if (parentid > 0) {
   								// meals[parentid] = parseFloat(jQuery(this).text().substr(1)); // 2010-11-03.
   								var priceString = jQuery(this).text().substr(1); // 2010-11-03.
	   							
	   							priceString = priceString.replace(',', ''); // 2010-11-03.
	   							
	   							// Remove all non-integers from the price. // 2010-11-04.
   							
								priceString = priceString.replace(/[^0-9.]/g, '');
	   							
	   							meals[parentid] = parseFloat( priceString ); // 2010-11-03.
   							}
   					
   						});
   						
   						function sortNumber(a, b)
						{
							return a - b;
						}

   						if (meals.length > 0) {
   						
   							meals.sort(sortNumber);
   							jQuery(this).find('tbody').html('');
   							for (j in meals) {
   								jQuery(this).find('tbody').append(mealshtml[meals[j]])
   							}
   						}
   						
   					});
   					
   					
   				});
   				
   			
   				jQuery('#sort-default').click(function(){
   					
   					jQuery(this).addClass('active');
   					jQuery('#sort-rating').removeClass('active');
   					jQuery('#sort-price').removeClass('active');
   					
   					jQuery('#menu table').each(function(){
   						
   						var meals = [];
   						var mealshtml = [];
   						
   						jQuery(this).find('tbody tr').each(function(){
   						
   							var parentid = parseFloat(jQuery(this).attr('id'));
   							
   							if (parentid > 0) {
   								mealshtml[parentid] = '<tr id="' + jQuery(this).attr('id') + '">' + jQuery(this).html() + '</tr>';
   							}
   					
   						});
   						
   						jQuery(this).find('tbody tr td.price').each(function(){
   						
   							var parentid = parseFloat(jQuery(this).parent().attr('id'));
   							if (parentid > 0) {
   								meals[parentid] = parentid;
   							}
   					
   						});
   						
   						function sortNumber(a, b)
						{
							return a - b;
						}
						
						if (meals.length > 0) {
   						
   							meals.sort(sortNumber);
   							jQuery(this).find('tbody').html('');
   							for (j in meals) {
   								jQuery(this).find('tbody').append(mealshtml[meals[j]])
   							}
   						}
   						
   					});
   					
   					
   				});
   				
   				jQuery('#sort-rating').click(function(){
   					
   					jQuery(this).addClass('active');
   					jQuery('#sort-price').removeClass('active');
   					jQuery('#sort-default').removeClass('active');
   					
   					jQuery('#menu table').each(function(){
   						
   						var meals = [];
   						var mealshtml = [];
   						var loopcounter = 0;
   						//make output array
   						jQuery(this).find('tbody tr').each(function(){
   						
   							var ratingvalue = parseFloat(jQuery(this).find('td.price span input.rating-hidden').val());
   							if (ratingvalue > 0) {
   								mealshtml[ratingvalue] = '<tr id="' + jQuery(this).attr('id') + '">' + jQuery(this).html() + '</tr>';
   							} else {
   								mealshtml[loopcounter] = '<tr id="' + jQuery(this).attr('id') + '">' + jQuery(this).html() + '</tr>';
   								loopcounter++;
   							}
   					
   						});
   						//reset loop
   						loopcounter = 0;
   						//make ordering array
   						jQuery(this).find('tbody tr td.price span input.rating-hidden').each(function(){
   						
   							var ratingvalue = jQuery(this).val();
   							if (ratingvalue > 0) {
   								meals[ratingvalue] = parseFloat(jQuery(this).val());
   							} else {
   								meals[loopcounter] = loopcounter;
   								loopcounter++;
   							}
   					
   						});
   						//sorting function helper
   						function sortNumber(a, b)
						{
							return a - b;
						}
						//outputs ordered html
						if (meals.length > 0) {
   						
   							meals.sort(sortNumber);
   							jQuery(this).find('tbody').html('');
   							for (j in meals) {
   								jQuery(this).find('tbody').append(mealshtml[meals[j]])
   							}
   						}
   						
   					});
   					
   				});
   				
   			});
   			</script>	
   		
   		</div><!-- /#menu -->
         <div id="modal-email">
			<form id="modal-email-fields" method="post" action="">
				<fieldset>
					<p class="validateTips"><?php _e('All form fields are required.', 'woothemes'); ?></p>
					<p class="form-field">
						<label for="yourname"><?php _e('Your Name', 'woothemes'); ?></label>
						<input type="text" name="yourname" id="yourname" class="text ui-widget-content ui-corner-all input-text required" />
					</p>
					<p class="form-field">
						<label for="theirname"><?php _e('Your Friends Name', 'woothemes'); ?></label>
						<input type="text" name="theirname" id="theirname" class="text ui-widget-content ui-corner-all input-text required" />
					</p>
					<p class="form-field">
						<label for="email"><?php _e('Email', 'woothemes'); ?></label>
						<input type="text" name="email" id="email" value="" class="text ui-widget-content ui-corner-all input-text required email" />
						<input type="hidden" name="url" id="url" value="<?php echo woo_curPageURL(); ?>" />
						<input type="hidden" name="action" id="action" value="menu" />
					</p>
				</fieldset>
			</form>
	
		</div>    
    </div><!-- /#content -->
		
<?php get_footer(); ?>