<?php

// Register widgetized areas

if (!function_exists('the_widgets_init')) {
	function the_widgets_init() {
	    if ( !function_exists('register_sidebars') )
	        return;
	
	    register_sidebar(array('name' => 'Home 1','id' => 'home-1','description' => "Homepage Widgetized Space 1", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array('name' => 'Home 2','id' => 'home-2','description' => "Homepage Widgetized Space 2", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array('name' => 'Home 3','id' => 'home-3','description' => "Homepage Widgetized Space 3", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array('name' => 'Primary','id' => 'primary','description' => "Normal full width Sidebar", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));   
	    register_sidebar(array('name' => 'Footer 1','id' => 'footer-1', 'description' => "Widetized footer", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array('name' => 'Footer 2','id' => 'footer-2', 'description' => "Widetized footer", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array('name' => 'Footer 3','id' => 'footer-3', 'description' => "Widetized footer", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	    register_sidebar(array('name' => 'Footer 4','id' => 'footer-4', 'description' => "Widetized footer", 'before_widget' => '<div id="%1$s" class="widget %2$s">','after_widget' => '</div>','before_title' => '<h3>','after_title' => '</h3>'));
	}
}

add_action( 'init', 'the_widgets_init' );


    
?>