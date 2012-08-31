<?php
/*---------------------------------------------------------------------------------*/
/* News Widget */
/*---------------------------------------------------------------------------------*/


class Woo_News extends WP_Widget {

	function Woo_News() {
		$widget_ops = array('description' => 'Display your Restaurant News' );
		parent::WP_Widget(false, __('Woo - News', 'woothemes'),$widget_ops);      
	}

	function widget($args, $instance) { 
		extract( $args );
		$title = $instance['title'];
		$catid = $instance['catid'];
		$cat_link = get_category_link($catid);
        echo $before_widget; ?>
       	
       	<?php echo $before_title .$title. $after_title; ?>
        
        <ul>
        	<?php 
        	$query_args = array( 'posts_per_page' => 3,'post_type' => 'post', 'cat' => $catid, 'orderby' => $orderby );      
			$the_query = new WP_Query($query_args);
			if ($the_query->have_posts()) : $count = 0;
			?>
			<?php
			while ($the_query->have_posts()) : $the_query->the_post();
				global $post;
				
				?>
			<li>
        		<?php woo_image('key=image&width=60&height=60&class=thumb'); ?>
        		<span class="details">
        		
        			<span class="date"><?php the_time( get_option( 'date_format' ) ); ?></span>
        			<h4><a href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><?php the_title(); ?></a></h4>
        			<span class="cats"><?php the_category(', ') ?></span>
        		</span>
        		<a class="click-through" href="<?php the_permalink(); ?>" title="<?php the_title(); ?>"><img src="<?php bloginfo('template_directory'); ?>/images/ico-widget-clickthrough.png" alt="<?php the_title(); ?>" /></a>
        	</li>
				<?php
			endwhile; endif;
			?>
        </ul>
        
        <div class="fix"></div>
        
        <a class="widget-footlink" href="<?php echo $cat_link; ?>" title="News"><?php _e('More news on the blog','woothemes'); ?></a>

        <?php
			
		echo $after_widget;

	}

	function update($new_instance, $old_instance) {                
		return $new_instance;
	}

	function form($instance) {
	
		$title = esc_attr($instance['title']);
		$cat_id = esc_attr($instance['catid']);
		
		?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woothemes'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>
        <p>
        	<label for="<?php echo $this->get_field_id('catid'); ?>"><?php _e('News Category:','woothemes'); ?></label>
        	<?php
        	$dropdown_options = array	(	
            											'show_option_all'	=> __('Select a Category'), 
            											'hide_empty' 		=> 0, 
            											'hierarchical' 		=> 1,
														'show_count' 		=> 0, 
														'orderby' 			=> 'name',
														'name' 				=> $this->get_field_name('catid'),
														'id' 				=> $this->get_field_name('catid'),
														'taxonomy' 			=> 'category', 
														'hide_if_empty'		=> 1,
														'selected' 			=> $cat_id,
														'class'				=> 'last'
														);
			wp_dropdown_categories($dropdown_options);
        	?>
        </p>
        <?php
	
	}
} 

register_widget('Woo_News');


?>