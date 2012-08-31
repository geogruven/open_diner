<?php global $woo_options; 

	$total = 4;
	if ( isset( $woo_options['woo_footer_sidebars'] ) && ( $woo_options['woo_footer_sidebars'] != '' ) ) {
		$total = $woo_options['woo_footer_sidebars'];
	}

	if ( ( woo_active_sidebar( 'footer-1' ) ||
		   woo_active_sidebar( 'footer-2' ) ||
		   woo_active_sidebar( 'footer-3' ) ||
		   woo_active_sidebar( 'footer-4' ) ) && $total > 0 ) {

?>
	<div id="footer-widgets" class="col-full col-<?php echo $total; ?>">

		<?php $i = 0; while ( $i < $total ) { $i++; ?>
			<?php if ( woo_active_sidebar( 'footer-' . $i ) ) { ?>

		<div class="block footer-widget-<?php echo $i; ?>">
        	<?php woo_sidebar( 'footer-' . $i ); ?>
		</div>

	        <?php } ?>
		<?php } // End WHILE Loop ?>

		<div class="fix"></div><!--/.fix-->

	</div><!-- /#footer-widgets  -->
<?php } // End IF Statement ?>
    
	<div id="footer">
	
		<div id="copyright" class="col-left">
		<?php if($woo_options['woo_footer_left'] == 'true'){
		
				echo stripslashes($woo_options['woo_footer_left_text']);	

		} else { ?>
			<p>&copy; <?php echo date('Y'); ?> <?php bloginfo(); ?>. <?php _e('All Rights Reserved.', 'woothemes') ?></p>
		<?php } ?>
		</div>
		
		<div id="credit" class="col-right">
        <?php if($woo_options['woo_footer_right'] == 'true'){
		
        	echo stripslashes($woo_options['woo_footer_right_text']);
       	
		} else { ?>
			<p><?php _e('Powered by', 'woothemes') ?> <a href="http://www.wordpress.org">Wordpress</a>. <?php _e('Designed by', 'woothemes') ?> <a href="<?php $aff = $woo_options['woo_footer_aff_link']; if(!empty($aff)) { echo $aff; } else { echo 'http://www.woothemes.com'; } ?>"><img src="<?php bloginfo('template_directory'); ?>/images/woothemes.png" width="74" height="19" alt="Woo Themes" /></a></p>
		<?php } ?>
		</div>
		
		<div class="fix"></div>
		
	</div><!-- /#footer  -->

</div><!-- /#wrapper -->
<?php if(get_option('woo_cufon_disabled') != 'true') { ?>
	<script type="text/javascript">
		//Cufon.replace('#navigation > .nav > li > a', { textShadow: '1px 1px #000', hover: true });
		Cufon.replace('.widget-footlink')
	</script>
<?php } ?>
<?php
$key = get_option('woo_maps_apikey');
if(!empty($key) && ( is_home() || is_page_template('template-location.php') || is_active_widget( false,false,'woo_location', true ) ) ){ ?>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;key=<?php echo $key; ?>" type="text/javascript"></script>
    
<?php } ?>
<?php wp_footer(); ?>
<?php woo_foot(); ?>
</body>
</html>