<?php
/*---------------------------------------------------------------------------------*/
/* Search widget */
/*---------------------------------------------------------------------------------*/
class Woo_GlobalSearch extends WP_Widget {

   function Woo_GlobalSearch() {
	   $widget_ops = array('description' => 'This is a WooThemes Global Search widget.' );
       parent::WP_Widget(false, __('Woo - Global Search', 'woothemes'),$widget_ops);      
   }

   function widget($args, $instance) {  
    extract( $args );
   	$title = $instance['title'];
	?>
		<?php echo $before_widget; ?>
        <?php if ($title) { echo $before_title . $title . $after_title; } ?>
        <?php
			if (isset($_GET['s'])) { $keyword = strip_tags($_GET['s']);  } else { $keyword = '';  }
			if ( $keyword == 'View More' ) { $keyword = ''; }
		?>

        <div class="search_main widget">
    		<form method="get" class="searchform" action="<?php bloginfo('url'); ?>/" >
        		<input type="text" class="field s" name="s" value="<?php _e('Enter search keywords', 'woothemes') ?>" onfocus="if (this.value == '<?php _e('Enter search keywords', 'woothemes') ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Enter search keywords', 'woothemes') ?>';}" />
        		<input type="hidden" name="action" id="action" value="global" />
        		<input type="submit" class="submit button" name="submit" value="<?php _e('Search', 'woothemes'); ?>" />
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

register_widget('Woo_GlobalSearch');
?>