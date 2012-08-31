<div id="sidebar" class="col-right">

	<?php if (woo_active_sidebar('primary')) : ?>
    <div class="primary">
		<?php woo_sidebar('primary'); ?>		           
	</div>        
	<?php endif; ?>

	<?php if (woo_active_sidebar('secondary-1') || 
			  woo_active_sidebar('secondary-2') ) : ?>
    <div class="secondary">
		<?php woo_sidebar('secondary-1'); ?>		           
	</div>        
    <div class="secondary last">
		<?php woo_sidebar('secondary-2'); ?>		           
	</div>        
	<?php endif; ?>
    
	
</div><!-- /#sidebar -->