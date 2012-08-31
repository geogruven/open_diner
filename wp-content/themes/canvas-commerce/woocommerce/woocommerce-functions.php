<?php

// Insert HTML5 Shiv
add_action('wp_head', 'woocommerce_html5');

function woocommerce_html5() {
	echo '<!--[if lt IE 9]><script src="https://html5shiv.googlecode.com/svn/trunk/html5.js"></script><![endif]-->'; 
}