<?php
	global $wpdb, $title;
?>
<style type="text/css" >

</style>
<div class="wrap" id="woo_container"><?php require('process.php'); ?>
<div id="woo-popup-save" class="woo-save-popup"><div class="woo-save-save">Options Updated</div></div>
<div id="woo-popup-reset" class="woo-save-popup"><div class="woo-save-reset">Options Reset</div></div>
    <form action="" method="post" id="wooform">
    <?php
    	// Add nonce for added security.
    	if ( function_exists( 'wp_nonce_field' ) ) { wp_nonce_field( 'wooframework-theme-options-update' ); } // End IF Statement
    	
    	$woo_nonce = '';
    	
    	if ( function_exists( 'wp_create_nonce' ) ) { $woo_nonce = wp_create_nonce( 'wooframework-theme-options-update' ); } // End IF Statement
    	
    	if ( $woo_nonce == '' ) {} else {
    	
    ?>
    	<input type="hidden" name="_ajax_nonce" value="<?php echo $woo_nonce; ?>" />
    <?php
    	
    	} // End IF Statement
    ?>
        <div id="header">
           <div class="logo">
				<?php if(get_option('framework_woo_backend_header_image')) { ?>
                <img alt="" src="<?php echo get_option('framework_woo_backend_header_image'); ?>"/>
                <?php } else { ?>
                <img alt="WooThemes" src="<?php echo bloginfo('template_url'); ?>/functions/images/logo.png"/>
                <?php } ?>
            </div>
             <div class="theme-info">
             	<span class="theme">WooTable Settings</span>
				<span class="framework">Version <?php echo get_option('woo_table_version'); ?></span>
			</div>
			<div class="clear"></div>
		</div>
		<div id="support-links">
			<ul>
				<li class="changelog"><a title="Plugin Changelog" href="<?php echo get_bloginfo('template_directory'); ?>/woo-table/changelog.txt">View Changelog</a></li>
				<li class="docs"><a title="Plugin Documentation" href="http://www.woothemes.com/support/plugin-documentation/wootable/">View Plugin docs</a></li>
				<li class="forum"><a href="http://forum.woothemes.com" target="_blank">Visit Forum</a></li>
                <li class="right"><img style="display:none" src="<?php echo bloginfo('template_url'); ?>/functions/images/loading-top.gif" class="ajax-loading-img ajax-loading-img-top" alt="Working..." /><a href="#" id="expand_options">[+]</a> <input name="wootable_update" type="submit" value="Save All Changes" class="button submit-button" /></li>
			</ul>
		</div>
        <div id="main">
	        <div id="woo-nav">
				<ul>
					<li class="table general"><a href="#table"><?php _e( 'WooTable Setup', 'woothemes' ); ?></a></li>
					<li class="hours calendar"><a href="#hours"><?php _e( 'Business Hours Setup', 'woothemes' ); ?></a></li>
					<li class="closed-hours calendar"><a href="#closed-hours"><?php _e( 'Closed Hours Setup', 'woothemes' ); ?></a></li>
					<li class="emails ads"><a href="#emails"><?php _e( 'E-mails Setup', 'woothemes' ); ?></a></li>
					<li class="adminemails ads"><a href="#adminemails"><?php _e( 'Admin E-mails Setup', 'woothemes' ); ?></a></li>
				</ul>		
			</div>
			<div id="content">
	        	<div id="table-settings" class="content-section">
	        		
	        		<div class="section">
								
						<h3 class="heading"><?php _e('Interval between reservations at a single table','woothemes'); ?></h3>
						
						<div class="controls">
						
							<select id="<?php echo $matty_prefix; ?>reservation_interval" name="<?php echo $matty_prefix; ?>reservation_interval">
										
							   <?php
							   	$intervals = array( 
							   						'0.5' => __('30 minutes', 'woothemes' ), 
							   						'1' => __('1 hour', 'woothemes' )
							   					);
							   	$interval_options = '';
							   	
							   	foreach ( $intervals as $k => $v ){
							   		$interval_options .= '<option value="' . $k . '"';
							   		if ( ${'v_' . $matty_prefix . 'reservation_interval'} == $k ) { $interval_options .= ' selected="selected"'; }
							   		$interval_options .= '>' . $v . '</option>' . "\n";
							   	} // End FOR Loop
							   	
							   	echo $interval_options;
							   ?>
							   
							</select>
						
						</div><!-- /.controls -->
						
						<div class="explain"><?php _e( 'The average duration of a single reservation at a table.', 'woothemes' ); ?></div><!-- /.explain -->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					
					<div class="section">
					
						<h3 class="heading"><?php echo __( 'Bookings Page', 'woothemes' ) ?></h3>
						
						<div class="controls">
						
							<?php ${$matty_prefix . 'page_booking'} = ${'v_' . $matty_prefix . 'page_booking'}; ${$matty_prefix . 'page_booking_block_name'} = $matty_prefix . 'page_booking'; ?>
								<?php wp_dropdown_pages("selected=" . ${$matty_prefix . 'page_booking'} . "&name=" . ${$matty_prefix . 'page_booking_block_name'} . ""); ?>
						
						</div><!-- /.controls -->
					
						<div class="explain"><?php _e( 'The WordPress page used to display the reservation form.', 'woothemes' ); ?></div><!-- /.explain -->
					
					</div><!-- /.section  -->
					
					<div class="clear"></div>
					
					<div class="section">
						
						<h3 class="heading"><?php echo __( 'Bookings Management Page', 'woothemes' ) ?></h3>
						
						<div class="controls">
						
							<?php ${$matty_prefix . 'page_manage'} = ${'v_' . $matty_prefix . 'page_manage'}; ${$matty_prefix . 'page_manage_block_name'} = $matty_prefix . 'page_manage'; ?>
								<?php wp_dropdown_pages("selected=" . ${$matty_prefix . 'page_manage'} . "&name=" . ${$matty_prefix . 'page_manage_block_name'} . ""); ?>
						
						</div><!-- /.controls -->
						
						<div class="explain"><?php _e( 'The WordPress page used to display and manage a user\'s upcoming reservations.', 'woothemes' ); ?></div><!-- /.explain -->
					
					</div><!-- /.section -->
								
					<div class="clear"></div>
					
					<div class="section">
					
						<h3 class="heading"><?php echo __( 'Time Format', 'woothemes' ) ?></h3>
						
						<div class="controls">
						
							<?php ${$matty_prefix . 'time_format'} = ${'v_' . $matty_prefix . 'time_format'}; ${$matty_prefix . 'time_format_block_name'} = $matty_prefix . 'time_format'; ?>
							
							<select name="<?php echo ${$matty_prefix . 'time_format_block_name'}; ?>">
							<?php
								$_html = '';
								
								foreach ( array( '12', '24' ) as $t ) {
								
									$_selected = '';
									
									if ( ${'v_' . $matty_prefix . 'time_format'} == $t ) { $_selected = ' selected="selected"'; } // End IF Statement
									
									$_html .= '<option value="' . $t . '"' . $_selected . '>' . $t . ' hour format</option>' . "\n";
									
								} // End FOREACH Loop
								
								echo $_html;
							?>
							</select>
						
						</div><!-- /.controls -->
						
						<div class="explain"><?php _e( 'The default format that times are displayed in.', 'woothemes' ); ?></div><!-- /.explain -->
					
					</div><!-- /.section  -->
					
					<div class="clear"></div>
					
					<div class="section">
					
						<h3 class="heading"><?php echo __( 'Date Format', 'woothemes' ) ?></h3>
						
						<div class="controls">
						
							<?php ${$matty_prefix . 'date_format'} = ${'v_' . $matty_prefix . 'date_format'}; ${$matty_prefix . 'date_format_block_name'} = $matty_prefix . 'date_format'; ?>
							
							<select name="<?php echo ${$matty_prefix . 'date_format_block_name'}; ?>">
							<?php
								$_html = '';
								
								$_day = date('d');
								$_month = date('m');
								$_year = date('Y');
								
								foreach ( array( 'Y-m-d', 'Y-d-m', 'd-m-Y', 'm-d-Y', 'j F Y', 'jS F Y', 'F j, Y', 'F jS, Y' ) as $t ) {
								
									$_selected = '';
									
									if ( ${'v_' . $matty_prefix . 'date_format'} == $t ) { $_selected = ' selected="selected"'; } // End IF Statement
									
									$_html .= '<option value="' . $t . '"' . $_selected . '>' . date( $t, mktime( 0, 0, 0, $_month, $_day, $_year ) ) . '</option>' . "\n";
									
								} // End FOREACH Loop
								
								echo $_html;
							?>
							</select>
						
						</div><!-- /.controls -->
						
						<div class="explain"><?php _e( 'The default format that dates are displayed in.', 'woothemes' ); ?></div><!-- /.explain -->
					
					</div><!-- /.section  -->
					
					<div class="clear"></div>
					
					<div class="section">
						
						<h3 class="heading"><?php _e('Reservation confirmation popup message','woothemes'); ?></h3>
						
						<div class="controls">
						
							<textarea id="<?php echo $matty_prefix; ?>confirmation_box_message" name="<?php echo $matty_prefix; ?>confirmation_box_message" rows="5" cols="40"><?php echo ${'v_' . $matty_prefix . 'confirmation_box_message'}; ?></textarea>
						
						</div><!-- /.controls -->
						
						<div class="explain"><?php _e( 'The message displayed in the popup when the user submits his or her reservation.', 'woothemes' ); ?></div><!-- /.explain -->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					
					<div class="section">
						
						<h3 class="heading"><?php _e('"Reserve Table" Button Text','woothemes'); ?></h3>
						
						<div class="controls">
						
							<input type="text" id="<?php echo $matty_prefix; ?>reserve_button_text" name="<?php echo $matty_prefix; ?>reserve_button_text" value="<?php echo ${'v_' . $matty_prefix . 'reserve_button_text'}; ?>" />
						
						</div><!-- /.controls -->
						
						<div class="explain"><?php _e( 'The text on the "Reserve Table" button when reserving a table.', 'woothemes' ); ?></div><!-- /.explain -->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					
					<div class="section">
						
						<h3 class="heading"><?php _e('"View Reservations" Button Text','woothemes'); ?></h3>
						
						<div class="controls">
						
							<input type="text" id="<?php echo $matty_prefix; ?>view_button_text" name="<?php echo $matty_prefix; ?>view_button_text" value="<?php echo ${'v_' . $matty_prefix . 'view_button_text'}; ?>" />
						
						</div><!-- /.controls -->
						
						<div class="explain"><?php _e( 'The text on the "View Reservations" button when reserving a table.', 'woothemes' ); ?></div><!-- /.explain -->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					
					<div class="section">
					
						<h3 class="heading"><?php echo __( 'Default Reservation Status', 'woothemes' ) ?></h3>
						
						<div class="controls">
						
							<?php ${$matty_prefix . 'default_status'} = ${'v_' . $matty_prefix . 'default_status'}; ${$matty_prefix . 'default_status_block_name'} = $matty_prefix . 'default_status'; ?>
							
							<select name="<?php echo ${$matty_prefix . 'default_status_block_name'}; ?>">
							<?php
								$_html = '';
								
								foreach ( array( 'unconfirmed', 'confirmed' ) as $t ) {
								
									$_selected = '';
									
									if ( ${'v_' . $matty_prefix . 'default_status'} == $t ) { $_selected = ' selected="selected"'; } // End IF Statement
									
									$_html .= '<option value="' . $t . '"' . $_selected . '>' . ucfirst( $t ) . '</option>' . "\n";
									
								} // End FOREACH Loop
								
								echo $_html;
							?>
							</select>
						
						</div><!-- /.controls -->
						
						<div class="explain"><?php _e( 'The default status of a reservation when it is placed on the frontend.', 'woothemes' ); ?></div><!-- /.explain -->
					
					</div><!-- /.section  -->
					
					<div class="clear"></div>
								
				</div><!-- /#table-settings -->
				
	        	<div id="hours-settings" class="content-section">
					
						<?php
							foreach ( $days as $k => $v ) {
						?>
							<div class="section">
								
								<h3 class="heading"><?php echo $v; ?></h3>
								
								<div class="controls">
							
								<label for="<?php echo $k; ?>_openingtime"><?php _e( 'Opening Time','woothemes' ); ?></label>
								<select name="<?php echo $k; ?>_openingtime">
									<?php
										$hours = '';
										
										for ( $i = 0; $i <= 23; $i++ ) {
										
											$hour = $i;
											if ( $hour < 10 ) { $hour = '0' . $i; } // End IF Statement
											
											$full_hour = $hour . ':00';
											$half_hour = $hour . ':30';
											
											$selected_hour = ${'v_' . $matty_prefix . 'business_hours'}[$k]['openingtime'];
											
											$selected_full = '';
											$selected_half = '';
																						
											if ( $full_hour == $selected_hour ) { $selected_full = ' selected="selected" '; } // End IF Statement
											if ( $half_hour == $selected_hour ) { $selected_half = ' selected="selected" '; } // End IF Statement										
											
											$hours .= '<option value="' . $full_hour . '"' . $selected_full . '>' . $full_hour . '</option>' . "\n";
											$hours .= '<option value="' . $half_hour . '"' . $selected_half . '>' . $half_hour . '</option>' . "\n";
											
										} // End FOREACH Loop
										
										echo $hours;
									?>
								</select>
								
								<label for="<?php echo $k; ?>_closingtime"><?php _e( 'Closing Time','woothemes' ); ?></label>
								<select name="<?php echo $k; ?>_closingtime">
									<?php
										$hours = '';
										
										for ( $i = 0; $i <= 24; $i++ ) {
										
											if ($i == 24) { $hour = ($i - 1); } else { $hour = $i; }
											
											if ( $hour < 10 ) { $hour = '0' . $i; } // End IF Statement
											
											if ($i == 24) { /* Do Nothing */ } else { $full_hour = $hour . ':00'; }
											$half_hour = $hour . ':30';
											if ($i == 24) { $half_hour = $hour . ':59'; }
											
											$selected_hour = ${'v_' . $matty_prefix . 'business_hours'}[$k]['closingtime'];
											
											$selected_full = '';
											$selected_half = '';
																						
											if ($i == 24) { /* Do Nothing */ } else {
												if ( $full_hour == $selected_hour ) { $selected_full = ' selected="selected" '; } // End IF Statement
											}
											if ( $half_hour == $selected_hour ) { $selected_half = ' selected="selected" '; } // End IF Statement										
											
											if ($i == 24) { /* Do Nothing */ } else {
												$hours .= '<option value="' . $full_hour . '"' . $selected_full . '>' . $full_hour . '</option>' . "\n";
											}
											$hours .= '<option value="' . $half_hour . '"' . $selected_half . '>' . $half_hour . '</option>' . "\n";
											
										} // End FOREACH Loop
										
										echo $hours;
									?>
								</select>
								
								<label for="<?php echo $k; ?>_closed"><?php _e( 'Closed','woothemes' ); ?></label>
								<input type="checkbox" id="<?php echo $k; ?>_closed" name="<?php echo $k; ?>_closed" value="1" <?php if ( ${'v_' . $matty_prefix . 'business_hours'}[$k]['closed'] == '1' ) { echo ' checked="checked"'; } ?> />
								
								</div><!-- /.controls -->
								
								</div><!-- /.section -->
								
								<div class="clear"></div>
								
						<?php		
							} // End FOREACH Loop
						?>
	        	
	        	</div><!-- /#hours-settings -->
	        	
	        	<div id="closed-hours-settings" class="content-section">
					
					<div class="section">
					
						<h3 class="heading"><?php _e( 'We\'re closed on these hours', 'woothemes' ); ?></h3>
				
							
						<div class="section section-info ">
							<h3 class="heading"><?php _e( 'Please Read', 'woothemes' ); ?></h3>
							<div class="option">
								<div class="controls">
									<?php _e( 'The hours displayed on this screen rely on the "Business Hours" settings to be setup correctly. If you adjust the times on the "Business Hours" page, please be sure to save them and refresh the page before editing the times on this screen.', 'woothemes' ); ?>
								</div>
							<div class="clear"> </div>
							</div>
						</div>
						
						<?php
							foreach ( $days as $k => $v ) {
						?>
							<div class="section">
								
								<h3 class="heading"><?php echo $v; ?></h3>
								
								<div class="controls">
							
								<?php
								
									if ( ${'v_' . $matty_prefix . 'business_hours'}[$k]['closed'] == '1' ) {
									
										_e( 'Closed', 'woothemes' );
									
									} else {
										
										$day_num = array( '', 'sun', 'mon', 'tues', 'wed', 'thurs', 'fri', 'sat' );
										
										$current_day =  strtolower( date('D') );
										
										if ( $current_day == 'tue' ) { $current_day = 'tues'; } // End IF Statement
										if ( $current_day == 'thu' ) { $current_day = 'thurs'; } // End IF Statement
										
										$current_day_num = 0;
										$main_day_num = 0;
										
										foreach ( $day_num as $i => $day ) {
										
											if ( $day == $current_day ) { $current_day_num = $i; } // End IF Statement
											
											if ( $k == $day ) { $main_day_num = $i; } // End IF Statement
										
										} // End FOREACH Loop
										
										$current_timestamp = strtotime( date( 'Y-m-d' ) );
										
										$result = 0;
										
										if ( $main_day_num > $current_day_num ) {
										
											$result = $current_day_num - ( $current_day_num - $main_day_num ) - $current_day_num;
										
										} else if ( $main_day_num < $current_day_num ) {
										
											$result = $current_day_num + ( $main_day_num - $current_day_num ) - $current_day_num;
										
										} // End IF Statement
																				
										$date = date( 'Y-m-d', mktime( 0, 0, 0, date( 'm', $current_timestamp ), date( 'd', $current_timestamp ) + $result, date( 'Y', $current_timestamp ) ) );
										
										$times = WTFactory::get_times_between( ${'v_' . $matty_prefix . 'business_hours'}[$k]['openingtime'], ${'v_' . $matty_prefix . 'business_hours'}[$k]['closingtime'], $matty_prefix, $date );
										
										// If we have times available...
										
										if ( count( $times ) > 1 ) {
										
											$_html = '';
										
											$_html .= '<select name="closed_hours_' . $k . '[]" multiple="multiple" style="height: auto;">' . "\n";
										
											foreach ( $times as $t ) {
											
												// Don't display the divider lines...
												
												if ( substr( $t, 0, 1 ) != '_' ) {
													
													$_selected = '';
													
													if ( isset( ${'v_' . $matty_prefix . 'closed_hours'}[$k] ) && in_array( $t, ${'v_' . $matty_prefix . 'closed_hours'}[$k] ) ) {
													
														$_selected = ' selected="selected"';
													
													} // End IF Statement
													
													$_html .= '<option value="' . $t . '"' . $_selected . '>' . $t . '</option>';
													
												} // End IF Statement	
											
											} // End FOREACH Loop
											
											$_html .= '</select>' . "\n";
											
											echo $_html;
										
										} else {
										
											// Only one time is available. The restaurant can't be open for one session and closed for that session... sheesh!
											
											if ( count( $times ) == 1 ) {
											
												foreach ( $times as $t ) {
												
													echo $t . ' <small>(' . __( 'Only one time slot is available.', 'woothemes' ) . ')</small>';
												
												} // End FOREACH Loop
											
											} else {
											
												_e( 'No time slots were found. Please check your business hours setup.', 'woothemes' );
											
											} // End IF Statement
										
										} // End IF Statement
									
									} // End IF Statement
								
								?>
								
								</div><!-- /.controls -->
								<?php
									if ( count( $times ) > 1 ) {
								?>
								<div class="explain">
									<?php echo sprintf( __( 'Use %s (Windows) or %s (Mac) to select the times for which your restaurant is closed on a %s.', 'woothemes' ), '<strong>CTRL+Click</strong>', '<strong>CMD+Click</strong>', $v ); ?>
								</div><!--/.explain-->
								<?php
									} // End IF Statement
								?>
								</div><!-- /.section -->
								
								<div class="clear"></div>
								
						<?php		
							} // End FOREACH Loop
						?>
	        	
	        		</div><!-- /.section -->
	        	
	        	</div><!-- /#closed-hours-settings -->
	        	
	        	<div id="emails-settings" class="content-section">
	        		
	        		<div class="section">
								
						<h3 class="heading"><?php _e('"Please Confirm" E-mail Message','woothemes'); ?></h3>
						
						<div class="controls">
						
							<textarea name="<?php echo $matty_prefix; ?>email_pleaseconfirm" rows="14"><?php echo ${'v_' . $matty_prefix . 'email_pleaseconfirm'}; ?></textarea>
						
						</div><!-- /.controls -->
						<div class="explain">
							<?php _e( 'The e-mail sent to the user, requesting that they please confirm their reservation.' , 'woothemes' ); ?><br /><br />
							<small>(<?php _e( 'Sent only if the default reservation status is set to "Unconfirmed".' , 'woothemes' ); ?>)</small>
						</div><!--/.explain-->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					
					<div class="section">
								
						<h3 class="heading"><?php _e('"Thank You" E-mail Message','woothemes'); ?></h3>
						
						<div class="controls">
						
							<textarea name="<?php echo $matty_prefix; ?>email_thankyou" rows="12"><?php echo ${'v_' . $matty_prefix . 'email_thankyou'}; ?></textarea>
						
						</div><!-- /.controls -->
						<div class="explain">
							<?php _e( 'The e-mail sent to the user, thanking them for confirming their reservation.' , 'woothemes' ); ?><br /><br />
							<small>(<?php _e( 'Sent when the reservation status is set to "Unconfirmed", either by default or by the user.' , 'woothemes' ); ?>)</small>
						</div><!--/.explain-->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					
					<div class="section">
								
						<h3 class="heading"><?php _e('"Status Has Changed" E-mail Message', 'woothemes'); ?></h3>
						
						<div class="controls">
						
							<textarea name="<?php echo $matty_prefix; ?>email_statuschange" rows="12"><?php echo ${'v_' . $matty_prefix . 'email_statuschange'}; ?></textarea>
						
						</div><!-- /.controls -->
						<div class="explain">
							<?php _e( 'The e-mail sent to the user when the status of their reservation is changed in the frontend.', 'woothemes'); ?><br /><br />
							<?php echo sprintf( __( 'A special %s shortcode is available here, to display the updated status of the reservation.' , 'woothemes' ), '<code>[reservation_status]</code>' ); ?>
						</div><!--/.explain-->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					
					<div class="section">
								
						<h3 class="heading"><?php _e('"Special Request" E-mail Message','woothemes'); ?></h3>
						
						<div class="controls">
						
							<textarea name="<?php echo $matty_prefix; ?>email_specialrequest" rows="14"><?php echo ${'v_' . $matty_prefix . 'email_specialrequest'}; ?></textarea>
						
						</div><!-- /.controls -->
						<div class="explain">
							<?php _e('The e-mail sent to the user when a special reservation request is made from the frontend.','woothemes'); ?>
						</div><!--/.explain-->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					
					<?php /*
					<div class="section section-checkbox collapsed">
						<div class="option">
							<div class="controls">
								<input type="checkbox" class="checkbox woo-input" name="<?php echo $matty_prefix; ?>adminsend[]" id="<?php echo $matty_prefix; ?>adminsend[]" value="pleaseconfirm" />
							</div>
							<div class="explain">Send E-mail to Administrator</div>
							<div class="clear"></div>
						</div>
					</div><!--/.section-->
					*/ ?>
					<div class="section">
								
						<h3 class="heading"><?php _e('E-mail Shortcodes','woothemes'); ?></h3>
						
						<div class="explain" style="width: 100%;">
							<?php _e( 'The following shortcodes are available for use in all e-mail messages:', 'woothemes' ); ?><br /><br />
							<dl>
								<dt><code>[restaurant_name]</code> - <?php _e( "The name of your restaurant", 'woothemes' ); ?></dt>
		 						<dt><code>[number_of_people]</code> - <?php _e( "The number of people the reservation is for", 'woothemes' ); ?></dt>
		 						<dt><code>[reservation_time]</code> - <?php _e( "The time of the reservation", 'woothemes' ); ?></dt>
		 						<dt><code>[reservation_date]</code> - <?php _e( "The date of the reservation", 'woothemes' ); ?></dt>
		 						<dt><code>[reservation_instructions]</code> - <?php _e( "Any instructions left by the user", 'woothemes' ); ?></dt>
		 						<dt><code>[contact_name]</code> - <?php _e( "The user's contact name", 'woothemes' ); ?></dt>
		 						<dt><code>[contact_tel]</code> - <?php _e( "The user's contact telephone number", 'woothemes' ); ?></dt>
		 						<dt><code>[contact_email]</code> - <?php _e( "The user's contact e-mail address", 'woothemes' ); ?></dt>
							</dl>
						</div><!--/.explain-->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					<br />
				</div><!-- /#emails-settings -->
				
				<div id="adminemails-settings" class="content-section">
					
					<div class="section">
								
						<h3 class="heading"><?php _e('"Reservation Made" E-mail Message','woothemes'); ?></h3>
						
						<div class="controls">
						
							<textarea name="<?php echo $matty_prefix; ?>adminemail_reservationmade" rows="16"><?php echo ${'v_' . $matty_prefix . 'adminemail_reservationmade'}; ?></textarea>
						
						</div><!-- /.controls -->
						<div class="explain">
							<?php _e( 'The e-mail sent to the administrator when a reservation is made on the frontend.', 'woothemes' ); ?>
						</div><!--/.explain-->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					
					<div class="section">
								
						<h3 class="heading"><?php _e('"Status Has Changed" E-mail Message','woothemes'); ?></h3>
						
						<div class="controls">
						
							<textarea name="<?php echo $matty_prefix; ?>adminemail_statuschange" rows="16"><?php echo ${'v_' . $matty_prefix . 'adminemail_statuschange'}; ?></textarea>
						
						</div><!-- /.controls -->
						<div class="explain">
							<?php _e ( 'The e-mail sent to the administrator when the status of a reservation is changed in the frontend.', 'woothemes' ); ?><br /><br />
							<?php echo sprintf( __( 'A special %s shortcode is available here, to display the updated status of the reservation.' , 'woothemes' ), '<code>[reservation_status]</code>' ); ?>
						</div><!--/.explain-->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					
					<div class="section">
								
						<h3 class="heading"><?php _e('"Special Request" E-mail Message','woothemes'); ?></h3>
						
						<div class="controls">
						
							<textarea name="<?php echo $matty_prefix; ?>adminemail_specialrequest" rows="14"><?php echo ${'v_' . $matty_prefix . 'adminemail_specialrequest'}; ?></textarea>
						
						</div><!-- /.controls -->
						<div class="explain">
							<?php _e( 'The e-mail sent to the administrator when a special reservation request is made from the frontend.', 'woothemes' ); ?>
						</div><!--/.explain-->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>

					<div class="section">
								
						<h3 class="heading"><?php _e('E-mail Shortcodes','woothemes'); ?></h3>
						
						<div class="explain" style="width: 100%;">
							The following shortcodes are available for use in all e-mail messages:<br /><br />
							<dl>
								<dt><code>[restaurant_name]</code> - <?php _e( "The name of your restaurant", 'woothemes' ); ?></dt>
		 						<dt><code>[number_of_people]</code> - <?php _e( "The number of people the reservation is for", 'woothemes' ); ?></dt>
		 						<dt><code>[reservation_time]</code> - <?php _e( "The time of the reservation", 'woothemes' ); ?></dt>
		 						<dt><code>[reservation_date]</code> - <?php _e( "The date of the reservation", 'woothemes' ); ?></dt>
		 						<dt><code>[reservation_instructions]</code> - <?php _e( "Any instructions left by the user", 'woothemes' ); ?></dt>
		 						<dt><code>[contact_name]</code> - <?php _e( "The user's contact name", 'woothemes' ); ?></dt>
		 						<dt><code>[contact_tel]</code> - <?php _e( "The user's contact telephone number", 'woothemes' ); ?></dt>
		 						<dt><code>[contact_email]</code> - <?php _e( "The user's contact e-mail address", 'woothemes' ); ?></dt>
							</dl>
						</div><!--/.explain-->
					
					</div><!-- /.section -->
					
					<div class="clear"></div>
					<br />		
				</div><!-- /#adminemails-settings -->


	        </div>
	        <div class="clear"></div>
	        
        </div>
        
        				
        <div class="save_bar_top">
        <img style="display:none" src="<?php echo bloginfo('template_url'); ?>/functions/images/loading-bottom.gif" class="ajax-loading-img ajax-loading-img-bottom" alt="Working..." />
        <input name="wootable_update" type="submit" value="Save All Changes" class="button submit-button" />        
        
            <span class="submit-footer-reset">
            <?php /*/ ?><input name="reset" type="submit" value="Reset Options" class="button submit-button reset-button" onclick="return confirm('Click OK to reset. Any settings will be lost!');" /><?php */ ?>
            <?php /*<input type="hidden" name="woo_save" value="reset" />*/ ?>
            </span>
        </form>
       
        </div>
        <?php  if (!empty($update_message)) echo $update_message; ?>    

<div style="clear:both;"></div>    
</div><!--wrap-->

<script type="text/javascript">

	jQuery(document).ready(function(){
	
		// Show the first section.
		
		jQuery( '#woo-nav ul li:first' ).addClass( 'current' );
		
		jQuery( '#content .content-section:first' ).hide().fadeIn();
		jQuery( '#content .content-section:not(:first)' ).hide();
		
		// Toggle to the desired section on click.
		
		jQuery( '#woo-nav ul li a' ).click( function () {
		
			jQuery( '#woo-nav ul li.current' ).removeClass( 'current' );
			
			jQuery( this ).parents( 'li' ).addClass( 'current' );
			
			var sectionId = jQuery( this ).attr( 'href' );
			
			sectionId = sectionId.replace( '#', '' ) + '-settings';
			
			jQuery( '.content-section:not( #' + sectionId + ' )' ).fadeOut( 'fast', function () {
			
				jQuery( '#' + sectionId ).fadeIn();
			
			});
		
			return false;
		
		});
	
	});

</script>