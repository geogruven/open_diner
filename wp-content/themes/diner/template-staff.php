<?php
/*
Template Name: Staff
*/

get_header();
?>

    <div id="content" class="page menu col-full">

    	<div class="post-wrap">

   			<?php if ( function_exists( 'yoast_breadcrumb' ) ) { yoast_breadcrumb( '<div id="breadcrumb"><p>', '</p></div>' ); } ?>
   			<?php if ( have_posts() ) : $count = 0; ?>
   			<?php while ( have_posts() ) : the_post(); $count++; ?>
   			    <?php
//custom field
$menu_additional_info = get_post_meta( $post->ID, 'menu_additional_info', true );
$menu_pdf = get_post_meta( $post->ID, 'menu_pdf', true );
?>
   			    <div class="post">

   			        <h1 class="title"><a href="<?php the_permalink(); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>"><?php the_title(); ?></a></h1>

   			        <div class="entry">
   			        	<?php the_content(); ?>
   			       	</div><!-- /.entry -->

   			    </div><!-- /.post -->

   			<?php endwhile; endif; ?>

   		</div><!-- /.post-wrap -->

   		<div id="menu" class="full-width-menu">

   			<table>

   			    <thead>
   			    	<tr>
   			    		<th colspan="3"></th>
   			    	</tr>
   			    </thead>
   			    <?php if ( $term->description != '' ) { ?>
   			    <tfoot>
   			    	<tr class="asterix-info">
   			    		<td  colspan="3"><span id="info-1">*</span> <?php echo $term->description; ?></td>
   			    	</tr>
   			    </tfoot>
   			    <?php } ?>
   			    <tbody>

        		<?php
$args = array( 'orderby' => 'display_name' );
$authors = get_users( $args );

foreach( $authors as $author ) { ?>

   			    	<tr id="<?php echo $author->ID; ?>">
   			    		<td class="image"><a href="<?php echo home_url() . '/?author=' . $author->ID; ?>" title="<?php esc_attr( the_author_meta( 'user_firstname', $author->ID ) ); ?> <?php esc_attr( the_author_meta( 'user_lastname', $author->ID ) ); ?>"><?php echo get_avatar( $author->ID, '170' ); ?></a></td>
   			    		<td class="details">
   			    			<h3><?php the_author_meta( 'user_firstname', $author->ID ); ?> <?php the_author_meta( 'user_lastname', $author->ID ); ?></h3>
   			    			<?php the_author_meta( 'description', $author->ID ); ?>
   			    		</td>
   			    	</tr>

   			    <?php } ?>

   			    </tbody>


   			</table>

   		</div><!-- /#menu -->

    </div><!-- /#content -->

<?php get_footer(); ?>