<?php
/*---------------------------------------------------------------------------------*/
/* Staff Widget */
/*---------------------------------------------------------------------------------*/


class Woo_Staff extends WP_Widget {

	function Woo_Staff() {
		$widget_ops = array( 'description' => __( 'Display your Restaurant Staff', 'woothemes' ) );
		parent::WP_Widget( false, __( 'Woo - Staff', 'woothemes' ), $widget_ops );
	}

	function widget( $args, $instance ) {
		extract( $args );
		$title = $instance['title'];
		$pageid = $instance['pageid'];

		echo $before_widget; ?>

       	<?php echo $before_title .$title. $after_title; ?>
        <ul>
        	<?php

		global $wpdb;

		$args = array( 'orderby' => 'display_name' );
		$authors = get_users( $args );

		foreach( $authors as $author ) { ?>

			<li>
        		<?php if ( $pageid > 0 ) { ?><a href="<?php echo get_permalink( $pageid ).'#'.$author->ID; ?>" title="<?php the_author_meta( 'user_firstname', $author->ID ); ?> <?php the_author_meta( 'user_lastname', $author->ID ); ?>"><?php } ?><?php echo get_avatar( $author->ID, '60' ); ?><?php if ( $pageid > 0 ) { ?></a><?php } ?>
        		<span class="details">
        			<h4><?php if ( $pageid > 0 ) { ?><a href="<?php echo get_permalink( $pageid ).'#'.$author->ID; ?>" title="<?php the_author_meta( 'user_firstname', $author->ID ); ?> <?php the_author_meta( 'user_lastname', $author->ID ); ?>"><?php } ?><?php the_author_meta( 'user_firstname', $author->ID ); ?> <?php the_author_meta( 'user_lastname', $author->ID ); ?><?php if ( $pageid > 0 ) { ?></a><?php } ?></h4>
        			<span class="description"><?php the_author_meta( 'description', $author->ID ); ?></span>
        		</span>
        		<?php if ( $pageid > 0 ) { ?><a class="click-through" href="<?php echo get_permalink( $pageid ).'#'.$author->ID; ?>" title="<?php the_author_meta( 'user_firstname', $author->ID ); ?> <?php the_author_meta( 'user_lastname', $author->ID ); ?>"><img src="<?php echo get_template_directory_uri(); ?>/images/ico-widget-clickthrough.png" alt="#" /></a><?php } ?>
        	</li>

			<?php } ?>


        </ul>

        <div class="fix"></div>

        <?php if ( $pageid > 0 ) { ?><a class="widget-footlink" href="<?php echo get_permalink( $pageid ); ?>" title="Meet the Team"><?php _e( 'Meet the Rest of the Team', 'woothemes' ); ?></a><?php } ?>

        <?php

		echo $after_widget;

	}

	function update( $new_instance, $old_instance ) {
		return $new_instance;
	}

	function form( $instance ) {

		$title = esc_attr( $instance['title'] );
		$pageid = esc_attr( $instance['pageid'] );
?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'woothemes' ); ?></label>
            <input type="text" name="<?php echo $this->get_field_name( 'title' ); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" />
        </p>
        <p>
			<label for="<?php echo $this->get_field_id( 'pageid' ); ?>"><?php _e( 'Staff Page Template:', 'woothemes' ); ?></label>
			<?php $args = array(
			'show_option_none'  => __( 'Select a Page:', 'woothemes' ),
			'depth'            => 0,
			'child_of'         => 0,
			'selected'         => $pageid,
			'echo'             => 1,
			'name'             => $this->get_field_name( 'pageid' ),
			'id'               => $this->get_field_name( 'pageid' ),
		); ?>
    		<?php wp_dropdown_pages( $args ); ?>

		</p>
        <?php

	}
}

register_widget( 'Woo_Staff' );


?>