<?php
/*---------------------------------------------------------------------------------*/
/* Search widget */
/*---------------------------------------------------------------------------------*/
class Woo_Search extends WP_Widget {

   function Woo_Search() {
	   $widget_ops = array('description' => 'This is a WooThemes standardized search widget that searches Posts and Pages.' );
       parent::WP_Widget(false, __('Woo - Standard Search', 'woothemes'),$widget_ops);      
   }

   function widget($args, $instance) {  
    extract( $args );
   	$title = $instance['title'];
	?>
		<?php echo $before_widget; ?>
        <?php if ($title) { echo $before_title . $title . $after_title; } ?>
        <div class="search_main">
		    <form method="get" class="searchform" action="<?php bloginfo('url'); ?>" >
		        <input type="text" class="field s" name="s" value="<?php _e('Search...', 'woothemes') ?>" onfocus="if (this.value == '<?php _e('Search...', 'woothemes') ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Search...', 'woothemes') ?>';}" />
		        <input type="image" src="<?php bloginfo('template_url'); ?>/images/ico-search.png" class="submit" name="submit" value="<?php _e('Go', 'woothemes'); ?>" />
		        <input type="hidden" name="blog_search" id="blog_search" value="true" />
		    </form>    
		    <div class="fix"></div>
		</div>
		<?php echo $after_widget; ?>   
   <?php
   }

   function update($new_instance, $old_instance) {                
       return $new_instance;
   }

   function form($instance) {        
   
       $title = esc_attr($instance['title']);

       ?>
       <p>
	   	   <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:','woothemes'); ?></label>
	       <input type="text" name="<?php echo $this->get_field_name('title'); ?>"  value="<?php echo $title; ?>" class="widefat" id="<?php echo $this->get_field_id('title'); ?>" />
       </p>
      <?php
   }
} 

register_widget('Woo_Search');
?>