<?php global $woo_options; ?>
<div class="search_module">

	<div class="search_title<?php if ( $woo_options['woo_keywords'] <> 'true' ) echo ' full'; ?> fl">
    	<h2 class="cufond"><?php echo stripslashes( $woo_options['woo_search_panel_header'] ) ?></h2>
    </div>

    <?php if ( $woo_options['woo_keywords'] == 'true' ) { ?>	
    	<div class="tags_title fr">
    		<h2 class="cufond"><?php echo stripslashes( $woo_options['woo_popular_keywords_header'] ) ?></h2>
    	</div>
    <?php } ?>


    <div class="fix"></div>

    <div class="search_main fl">
    
    	<div class="panel<?php if ( $woo_options['woo_keywords'] <> 'true' ) echo ' full'; ?>">
    	
    	<form method="get" class="searchform" action="<?php bloginfo('url'); ?>/" >
            
            <div id="controls">
            
            <?php
           	$number_of_search_fields = get_option('woo_number_of_search_fields');
           	$term_names_list = '';
           	$hierarchical_value = 0;
           	$cmb_meta_type = '';
           	$cmb_meta_options = array();
           	$content_type = '';
           	$term_names_array = array();
            for ( $counter = 1; $counter <= $number_of_search_fields; $counter += 1) {
            	$content_type_input = get_option('woo_search_input_content_type_'.$counter);
            	if ($content_type_input != 'none') {
            		
            		$content_type = get_option('woo_search_content_type_'.$counter);
            		
            		switch ($content_type) {
            			case 'ctx' :
            				$content_type_value = get_option('woo_search_content_type_ctx_'.$counter);
            				
            				$content_label = get_option('woo_search_content_type_label_'.$counter);
            				$taxonomy_obj = get_taxonomy($content_type_value);
            				if ($content_label == '') {
            					$label_output = $taxonomy_obj->label;
            				} else {
            					$label_output = $content_label;
            				}
            				
            				// heirarchy check
            				if ( ($taxonomy_obj) && ($taxonomy_obj->hierarchical > 0) ) {
            					$hierarchical_value = 1;
            				} else {
            					$hierarchical_value = 0;
            				}
            				break;
            			case 'cmb' :
            				$content_type_value = get_option('woo_search_content_type_cmb_'.$counter);
            				$content_label = get_option('woo_search_content_type_label_'.$counter);
            				if ($content_label == '') {
            					$label_output = $content_type_value;
            				} else {
            					$label_output = $content_label;
            				}
            				$woo_metaboxes = get_option('woo_custom_template');
            				foreach ($woo_metaboxes as $woo_metabox) {
            					if ($woo_metabox['name'] == $content_type_value) {
            						$cmb_meta_type = $woo_metabox['type'];
            						$cmb_meta_options = $woo_metabox['options'];
            					}
            				}
            				break;
            			case 'cpt' :
            				$content_type_value = get_option('woo_search_content_type_cpt_'.$counter);
            				$content_label = get_option('woo_search_content_type_label_'.$counter);
            				if ($content_label == '') {
            					$label_output = $content_type_value;
            				} else {
            					$label_output = $content_label;
            				}
            				break;
            			default :
            				$content_type_value = '';	
            				break;
            		
            		}
            		
            		if ($content_type_value != '') {
            			switch ($content_type_input) {
            				case 'text' :
            					?>
            					<div class="control">
            						<label for="field_<?php echo $counter; ?>"><?php echo $label_output; ?></label>
            						<input type="text" class="field" name="field_<?php echo $counter; ?>" id="field_<?php echo $counter; ?>" value="<?php _e('Enter text keywords', 'woothemes') ?>" onfocus="if (this.value == '<?php _e('Enter text keywords', 'woothemes') ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Enter text keywords', 'woothemes') ?>';}" />
            					</div><!-- /.control -->
            					<?php
            					break;
            				case 'autocomplete' :
            					?>
            					<div class="control">
            						<label for="field_<?php echo $counter; ?>"><?php echo $label_output; ?></label>
            						<input type="text" class="field" name="field_<?php echo $counter; ?>" id="field_<?php echo $counter; ?>" value="<?php _e('Enter autocomplete keywords', 'woothemes') ?>" onfocus="if (this.value == '<?php _e('Enter autocomplete keywords', 'woothemes') ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php _e('Enter autocomplete keywords', 'woothemes') ?>';}" />
            					</div>
            					<?php 
        						$term_item_list = '';
        						switch ($content_type) {
            						case 'ctx' :
            							// Taxonomies
            							$taxonomy_data_set = get_terms(array($content_type_value), array('fields' => 'names'));
        								$taxonomy_data_set = woo_multidimensional_array_unique($taxonomy_data_set);
        								if ( is_array($taxonomy_data_set) ) {
        									foreach ($taxonomy_data_set as $data_item) { 
        										// Convert string to UTF-8
    											$str_converted = woo_encoding_convert($data_item);
    											// Add category name to data string
    											$term_item_list .= htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8').',';
    										} // End For Loop
    									} // End If Statement
            							break;
            						case 'cmb' :
            							// Post Custom Fields
            							if ($cmb_meta_type == 'select2' || $cmb_meta_type == 'radio') {
            								if ( is_array($cmb_meta_options) ) {
            									foreach ($cmb_meta_options as $cmb_meta_item) {
            										// Convert string to UTF-8
    												$str_converted = woo_encoding_convert($cmb_meta_item);
    												// Add category name to data string
    												$term_item_list .= htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8').',';
            									} // End For Loop
            								} // End If Statement
            							} else {
        									$meta_data_fields = array($content_type_value);
        									$meta_data_set = woo_get_custom_post_meta_entries($meta_data_fields);
        									$meta_data_set = woo_multidimensional_array_unique($meta_data_set);
        									if ( is_array($meta_data_set) ) {
        										foreach ($meta_data_set as $data_item) { 
        											// Convert string to UTF-8
    												$str_converted = woo_encoding_convert($data_item->meta_value);
    												// Add category name to data string
    												$term_item_list .= htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8').',';
    											} // End For Loop
    										} // End If Statement
    									}
            							break;
            						case 'cpt' :
            							// Custom Post Types
        								/*$meta_data_fields = array('price');
        								$meta_data_set = woo_get_custom_post_meta_entries($meta_data_fields);
        								$meta_data_set = woo_multidimensional_array_unique($meta_data_set);
        								foreach ($meta_data_set as $data_item) { 
        									//Convert string to UTF-8
    										$str_converted = woo_encoding_convert($data_item->meta_value);
    										//Add category name to data string
    										$price_list .= htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8').',';
        								}*/
            							break;
            						default :
            							// Do nothing	
            							break;
            					}
        						
        						?>
        								   			
        						<script type="text/javascript">
      								jQuery(document).ready(function($) {
    									
    									// GET PHP data items
        								var itemdataset = "<?php echo $term_item_list; ?>".split(",");
        								// Set autocomplete(s)
    									$("#field_<?php echo $counter; ?>").autocomplete(itemdataset);
    									$("#field_<?php echo $counter; ?>").result(function(event, data, formatted) {
        									// Do Nothing
    									});
    									
    										
     								});
      							</script>
            					<?php
            					break;
            				case 'select2' :
            					switch ($content_type) {
            						case 'ctx' :
            							// taxonomy drop down
            							if ( isset( $_GET['field_'.$counter] ) ) {
            								$category_ID = $_GET['field_'.$counter];
            							} else {
            								$category_ID = '0';
            							} // End If Statement
        								$dropdown_options = array	(	
                											'show_option_all'	=> __('All', 'woothemes'), 
                											'hide_empty' 		=> 0, 
                											'hierarchical' 		=> $hierarchical_value,
    														'show_count' 		=> 0, 
    														'orderby' 			=> 'name',
    														'name' 				=> 'field_'.$counter,
    														'id' 				=> 'field_'.$counter,
    														'taxonomy' 			=> $content_type_value, 
    														'hide_if_empty'		=> 1,
    														'selected' 			=> $category_ID,
    														'class'				=> 'last'
    														);
    									echo '<div class="control">';
    									echo '<label for="field_'.$counter.'">'.$label_output.'</label>';
    										wp_dropdown_categories($dropdown_options);
            							echo '</div>';
            							break;
            						case 'cmb' :
            							if ($cmb_meta_type == 'select2' || $cmb_meta_type == 'radio') {
            							} else {
            								// Post Custom Fields
        									$meta_data_fields = array($content_type_value);
        									$meta_data_set = woo_get_custom_post_meta_entries($meta_data_fields);
        									$meta_data_set = woo_multidimensional_array_unique($meta_data_set);
        								}
        								?>
        								<div class="control">
        									<label for="field_<?php echo $counter; ?>"><?php echo $label_output; ?></label>
            								<select name="field_<?php echo $counter; ?>" id="field_<?php echo $counter; ?>">
            									<option value="All"><?php _e('All', 'woothemes') ?></option>
            									<?php  
            									if ($cmb_meta_type == 'select2' || $cmb_meta_type == 'radio') {
            										foreach ($cmb_meta_options as $key => $cmb_meta_item) { 
        												// Convert string to UTF-8
    													$str_converted = woo_encoding_convert($cmb_meta_item);
    													// Output Option Element
    													?>
        										<option value="<?php echo $key; ?>"><?php echo $str_converted; ?></option>
        										<?php }
            									} else { 
            										foreach ($meta_data_set as $data_item) { 
        												// Convert string to UTF-8
    													$str_converted = woo_encoding_convert($data_item->meta_value);
    													// Output Option Element
            										?>
        										<option value="<?php echo $str_converted; ?>"><?php echo $str_converted; ?></option>
        										<?php }
            									} 
            									?>
            								</select>
            							</div><!-- /.control -->
            							<?php
            							break;
            						case 'cpt' :
            							// Custom Post Types
        								$cpt_posts = get_posts('post_status=publish&post_type='.$content_type_value);
        								?>
        								<div class="control">
        									<label for="field_<?php echo $counter; ?>"><?php echo $label_output; ?></label>
            								<select name="field_<?php echo $counter; ?>" id="field_<?php echo $counter; ?>">
            									<option value="All"><?php _e('All', 'woothemes') ?></option>
            									<?php  
            									foreach ($cpt_posts as $data_item) { 
        											// Convert string to UTF-8
    												$str_converted = woo_encoding_convert($data_item->post_title);
    												// Output Option Element
        											?>
        										<option value="<?php echo $data_item->ID; ?>"><?php echo $str_converted; ?></option>
        										<?php } ?>
            								</select>
            							</div><!-- /.control -->
            							<?php 
            							break;
            						default :
            							break;
            						
            					}				
            					
            					break;
            				default :
            					$content_type_value = '';	
            					break;
            		
            			}
            			
            		}
            		
            	}
            	
       			switch ($content_type) {
           			case 'ctx' :
           				// Taxonomies
           				$taxonomy_data_set = get_terms(array($content_type_value), array('fields' => 'names'));
           				$taxonomy_data_set = woo_multidimensional_array_unique($taxonomy_data_set);
           				
           				if ( is_array( $taxonomy_data_set ) && count( $taxonomy_data_set ) ) {
           				
	           				foreach ($taxonomy_data_set as $data_item) { 
	           					// Convert string to UTF-8
	           					$str_converted = woo_encoding_convert($data_item);
	           					// Add category name to data string
	           					array_push($term_names_array, $str_converted);
	           				} // End FOREACH Loop
           				
           				} // End IF Statement
           				
           				break;
           			case 'cmb' :
           				// Post Custom Fields
           				$meta_data_fields = array($content_type_value);
           				$meta_data_set = woo_get_custom_post_meta_entries($meta_data_fields);
           				$meta_data_set = woo_multidimensional_array_unique($meta_data_set);
           				
           				if ( is_array( $meta_data_set ) && count( $meta_data_set ) ) {
           				
	           				foreach ($meta_data_set as $data_item) { 
	           					// Convert string to UTF-8
	           					$str_converted = woo_encoding_convert($data_item->meta_value);
	           					// Add category name to data string
	           					array_push($term_names_array, $str_converted);
	           				} // End FOREACH Loop
           				
           				} // End IF Statement
           				
           				break;
           			case 'cpt' :
           				// Custom Post Types
           				/*$meta_data_fields = array('price');
           				$meta_data_set = woo_get_custom_post_meta_entries($meta_data_fields);
           				$meta_data_set = woo_multidimensional_array_unique($meta_data_set);
           				foreach ($meta_data_set as $data_item) { 
           					//Convert string to UTF-8
           					$str_converted = woo_encoding_convert($data_item->meta_value);
           					//Add category name to data string
           					$price_list .= htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8').',';
           				}*/
           				break;
           			default :
           				// Do nothing	
           				break;
           		}
           		
           	
            }
            // SUPER UNIQUE KEYWORD AUTOCOMPLETE DATA
           	$term_names_array = array_unique($term_names_array);
           	foreach ($term_names_array as $data_item) { 
	        	// Convert string to UTF-8
	            $str_converted = woo_encoding_convert($data_item);
	            // Add category name to data string
	            $term_names_list .= htmlspecialchars($str_converted, ENT_QUOTES, 'UTF-8').',';
	        } // End FOREACH Loop
            ?>
            
    		<div class="fix"></div>
            
            </div><!-- /#controls -->
            		
            <div class="main-control">
            	
            	<input type="text" class="field full s" id="s-main" name="s" value="<?php echo stripslashes( $woo_options['woo_search_panel_keyword_text'] ); ?>" onfocus="if (this.value == '<?php echo stripslashes( $woo_options['woo_search_panel_keyword_text'] ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo stripslashes( $woo_options['woo_search_panel_keyword_text'] ); ?>';}" />
            <input type="submit" class="submit button" name="submit" value="<?php echo stripslashes( $woo_options['woo_search_panel_listings_button_text'] ); ?>" />
            	<div class="fix"></div>
            	
            </div><!-- /.main-control -->
            
            <script type="text/javascript">
      			jQuery(document).ready(function($) {
    	    		
    	    		//GET PHP data items
            		var keyworddataset = "<?php echo $term_names_list; ?>".split(",");
    	    		//Set autocomplete(s)
    	    		$("#s-main").autocomplete(keyworddataset);
    	    		//Handle autocomplete result
    	    		$("#s-main").result(function(event, data, formatted) {
            			//Do Nothing
    	    		});
    	    			
     	    	});
      	    </script>
        </form>    
        
        <form name="listings-webref-search" id="listings-webref-search" method="get" action="<?php bloginfo('url'); ?>/">
    		
    		<label for="s-webref"><?php _e('Search by web reference','woothemes'); ?>:</label>
    		
    		<input type="text" class="field webref" id="s-webref" name="s" value="<?php echo stripslashes( $woo_options['woo_search_panel_webref_text'] ); ?>" onfocus="if (this.value == '<?php echo stripslashes( $woo_options['woo_search_panel_webref_text'] ); ?>') {this.value = '';}" onblur="if (this.value == '') {this.value = '<?php echo stripslashes( $woo_options['woo_search_panel_webref_text'] ); ?>';}" />
        			 
        	<input type="submit" class="submit button" name="listings-search-webref-submit" value="<?php echo stripslashes( $woo_options['woo_search_panel_webref_button_text'] ); ?>" /> 
        			
        </form>
        
        </div>
    	
    </div>

	<?php if ( $woo_options['woo_keywords'] == 'true' ) { ?>
		<div class="tag_cloud fr">
			<?php 
			$keyword_limit = 20;
			if ( isset( $woo_options['woo_popular_keywords_limit'] ) && is_numeric( $woo_options['woo_popular_keywords_limit'] ) ) {
			
				$keyword_limit = $woo_options['woo_popular_keywords_limit'];
			
			} // End IF Statement
			$wp_custom_taxonomy_args = array();
			$woo_wp_custom_taxonomies = get_taxonomies($wp_custom_taxonomy_args,'objects');  
			foreach ($woo_wp_custom_taxonomies as $tax_item) {
				$cpt_test = get_option('woo_search_post_types_'.$tax_item->name);
				if ($cpt_test == 'true') {
					wp_tag_cloud( array('number' => $keyword_limit, 'taxonomy' => $tax_item->name) );?>&nbsp<?php
				}
			}
			?>
		</div>
	<?php } ?>
					
	<div class="fix"></div>
		
</div>
	
<div id="panbut" class="open">
	<span><?php _e('Close Search','woothemes'); ?></span>
</div>

<script type="text/javascript">
    
    jQuery(document).ready(function(){
    
    	<?php if ( ( is_home() ) && ( $woo_options['woo_search_panel_state'] == 'true' ) && ( !isset($_GET['s']) ) ) { ?>
    		// search panel is open
    			
    	<?php } elseif ($woo_options['woo_search_panel_state_global'] == 'true') {
    		//search panel is always open
    	} else { ?>
    		// search panel is closed
    		jQuery("#panbut").removeClass('open');
    	    jQuery("#panbut").addClass('closed');	
    		jQuery("div.search_module").toggle();
    		jQuery('#panbut span').text('<?php _e('Open Search','woothemes'); ?>');
    	<?php } ?>
    						
    	jQuery("#panbut span").click(function(){
    		var open = jQuery(this).parent().hasClass('open');
    		var closed = jQuery(this).parent().hasClass('closed');
    		
    	    // toggle advanced search
    	    jQuery("div.search_module").slideToggle();
    		    
    	    if(open == true){
    	    	jQuery("#panbut").removeClass('open');
    	    	jQuery("#panbut").addClass('closed');
    	    	jQuery('#panbut span').text('<?php _e('Open Search','woothemes'); ?>');
    	    } else if ( closed == true ) {
    	    	jQuery("#panbut").removeClass('closed');
    	    	jQuery("#panbut").addClass('open');
    	    	jQuery('#panbut span').text('<?php _e('Close Search','woothemes'); ?>');
    	    }
    		    
    	});
    						
    });
    	
   </script>