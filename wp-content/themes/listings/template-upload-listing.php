<?php
	/*
		Template Name: Upload A Listing
		Template Author: Matty @ WooThemes
		Template Description:
		
		This template displays a dynamically generated form for users
		to easily upload their own listings to your listings website.
		
	*/
?>
<?php
	// Load the file to call the required JavaScripts.
	require_once( TEMPLATEPATH . '/includes/upload-listing/load_js.php' );
?>
<?php get_header(); ?>
<?php global $woo_options; ?>
    <div id="content" class="page col-full">
		<div id="main" class="col-left">
		           
			<?php if ( function_exists('yoast_breadcrumb') ) { yoast_breadcrumb('<div id="breadcrumb"><p>','</p></div>'); } ?>

            <?php if (have_posts()) : $count = 0; ?>
            <?php while (have_posts()) : the_post(); $count++; ?>
                                                                        
                <div id="upload-listing" class="post">

                    <h1 class="title cufon">
                    	<a href="<?php the_permalink() ?>" rel="bookmark" title="<?php the_title(); ?>"><?php the_title(); ?></a>
                    	
                    	<?php
                    	
                    	// Display the logout link if the user is logged in.
	
							if ( is_user_logged_in() ) {
								
								echo '<span class="logout-link">';
								wp_loginout( $redirect_url );
								echo '</span><!--/.logout-link-->' . "\n";
							
							} // End IF Statement
						
						?>
                    	
                    </h1>

                    <div class="entry">
	                	<?php the_content(); ?>

						<?php wp_link_pages( array( 'before' => '<div class="page-link">' . __( 'Pages:', 'woothemes' ), 'after' => '</div>' ) ); ?>
						<?php
							// Load the "Upload a listing" form,
							// allowing users to override the form in their child theme.

							$templates = array( 'includes/woo-upload-listing-form.php' );
							$form = locate_template( $templates, true );
						?>
	               	</div><!-- /.entry -->
                    
                </div><!-- /.post -->
                
                <?php $comm = $woo_options['woo_comments']; if ( ($comm == "page" || $comm == "both") ) : ?>
                    <?php comments_template(); ?>
                <?php endif; ?>
                                                    
			<?php endwhile; else: ?>
				<div class="post">
                	<p><?php _e('Sorry, no posts matched your criteria.', 'woothemes') ?></p>
                </div><!-- /.post -->
            <?php endif; ?>  
        
		</div><!-- /#main -->

        <?php get_sidebar(); ?>

    </div><!-- /#content -->
		
<?php get_footer(); ?>