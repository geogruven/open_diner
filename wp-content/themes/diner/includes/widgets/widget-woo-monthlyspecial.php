<?php
/*---------------------------------------------------------------------------------*/
/* Monthly Special Widget */
/*---------------------------------------------------------------------------------*/


class Woo_SpecialsMonthly extends WP_Widget {

	function Woo_SpecialsMonthly() {
		$widget_ops = array('description' => 'Display your Monthly Restaurant specials' );
		parent::WP_Widget(false, __('Woo - Monthly Special', 'woothemes'),$widget_ops);      
	}

	function widget($args, $instance) { 
		extract( $args );
		$title = $instance['title'];
		$tag = $instance['tag'];
		$order = $instance['order'];
		$pageid = $instance['pageid'];
		
        echo $before_widget; ?>
       	
       	<?php echo $before_title .$title. $after_title; ?>
       	<?php 
        $query_args = array( 'posts_per_page' => 1,'post_type' => 'woo_menu', 'menutype' => $term->slug, 'tag' => $tag, 'orderby' => $orderby );      
		$the_query = new WP_Query($query_args);
		if ($the_query->have_posts()) : $count = 0;
		?>
		<?php
		while ($the_query->have_posts()) : $the_query->the_post();
			global $post;
			$postid = get_the_ID();
			$price = get_post_meta($postid,'price',true);
			$meal_price = number_format($price , 2 , '.', ',');
		?>
       	<div class="monthly-inside">
        
        	<?php if ($pageid > 0) { ?><a href="<?php echo get_permalink($pageid).'#'.$postid; ?>" title="<?php the_title(); ?>"><?php } ?>
        		<?php woo_image('link=img&key=thumbnail&width=278&height=114&class=thumb'); ?>
        	<?php if ($pageid > 0) { ?></a><?php } ?>
        	<h4>
        	    <?php if ($pageid > 0) { ?><a href="<?php echo get_permalink($pageid).'#'.$postid; ?>" title="<?php the_title(); ?>"><?php } ?>
        	    	<span class="special-title"><?php the_title(); ?></span>
        	    	<span class="price"><?php echo get_option('woo_diner_currency').''.$meal_price; ?></span>
        	    <?php if ($pageid > 0) { ?></a><?php } ?>
        	</h4>
        	
        	<?php the_excerpt(); ?>
		
		</div><!-- /.monthly-inside -->
		<?php endwhile; ?>
        
		<?php if ($pageid > 0) { ?><a class="widget-footlink" href="<?php echo get_permalink($pageid); ?>" title="View Menu"><?php _e('View Our Complete Menu','woothemes'); ?></a><?php } ?>
		<?php endif; ?>
        <?php
			
		echo $after_widget;

	}

	function update($new_instance, $old_instance) {                
		return $new_instance;
	}

	function form($instance) {
	
		$title = esc_attr($instance['title']);
		$tag = esc_attr($instance['tag']);
		$order = esc_attr($instance['order']);
		$pageid = esc_attr($instance['pageid']);
		
		?>
        <p>
            <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woothemes'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('title'); ?>" value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
        </p>
        <p>
            <label for="<?php echo $this->get_field_id('tag'); ?>"><?php _e('Specials Tag:','woothemes'); ?></label>
            <input type="text" name="<?php echo $this->get_field_name('tag'); ?>" value="<?php echo $tag; ?>" class="widefat" id="<?php echo $this->get_field_id('tag'); ?>" />
        </p>
        <p>
        	<label for="<?php echo $this->get_field_id('order'); ?>"><?php _e('Specials Ordering:','woothemes'); ?></label>
        	<select class="widefat" id="<?php echo $this->get_field_id('order'); ?>" name="<?php echo $this->get_field_name('order'); ?>">
				<option value="std" <?php selected('std', $order) ?>><?php echo 'Standard'; ?></option>
				<option value="rand" <?php selected('rand', $order) ?>><?php echo 'Random'; ?></option>
			</select>
		</p>
		<p>
			<label for="<?php echo $this->get_field_id('pageid'); ?>"><?php _e('Menu Page Template:','woothemes'); ?></label>
			<?php $args = array(
								'show_option_none'  => __('Select a Page:'), 
    							'depth'            => 0,
    							'child_of'         => 0,
    							'selected'         => $pageid,
    							'echo'             => 1,
    							'name'             => $this->get_field_name('pageid'),
    							'id'               => $this->get_field_name('pageid'),
    							); ?>
    		<?php wp_dropdown_pages($args); ?>
    							
		</p>
        <?php
	
	}
} 

register_widget('Woo_SpecialsMonthly');


?>