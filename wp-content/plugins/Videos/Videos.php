<?php
/*

  Plugin Name: Wordpress Network Tutorial Videos 
  Plugin URI: http://www.GeorgeAguiar.Com 
  Description: This plugin displays the Wordpress Network Tutorial Training Videos. 
  Author: George Aguiar
  Version: 0.1 
  Author URI: http://georgeaguiar.com/hello-world
 */
 
 /**
  *  <a href="http://wp.tutsplus.com/tutorials/plugins/give-your-clients-personalised-screencasts-in-the-wordpress-admin-panel/http://wp.tutsplus.com/tutorials/plugins/give-your-clients-personalised-screencasts-in-the-wordpress-admin-panel/">
  *	 </a>
  */	 

/**
 *
 * The page Output.
 *
 */
function wp_videos_page() {

  $wp_video_dir       = '/mp4';
  $wp_video_real_path = dirname(__FILE__) . $wp_video_dir;
  $wp_video_url       = video_plugin_path() . $wp_video_dir;

  ?>
<div class="wrap">
  <div id="icon-upload" class="icon32"><br></div>
  <h2>Geo's Content Management Tutorial videos.</h2>

  <?php
  $videos = glob($wp_video_real_path . "/*.[mM][pP]4");

  if (!empty($videos)){

    wp_register_script('flowplayer_js', video_plugin_path() . '/js/flowplayer-3.2.11.min.js' );
    wp_enqueue_script('flowplayer_js');

    $o  = '<p>Please choose a Screencast to watch</p>';
    $o .= '<ul>';

    foreach($videos as $video){

      $video_file   = basename($video);
      $needles      = array('-', '.mp4');
      $replacements = array(' ', '');
      $video_title  = ucwords(str_replace($needles, $replacements, $video_file));

      $o .= sprintf(('<li><a href="" data-video-url="%s" class="video-link">%s</a></li>'),
        $wp_video_url . '/' . $video_file,
        $video_title
      );
    }
    $o .= '</ul>';
    echo $o;
  } else
    echo 'Sorry there are no videos to view yet, I\'ll let you know when there is.';
  ?>
  <div id="player"></div>
</div><!-- #Wrap -->

<script>
  //catch clicks on the video links
  jQuery('.video-link').on('click', function(e){

    var link       = jQuery(this);
    var video_url  = link.data('video-url');
    
    play_video(video_url);

    e.preventDefault();
  });

  var play_video = function(video_url){
    var plugin_url = '<?= video_plugin_path() ?>';
    var swf_url    = plugin_url + '/swf/flowplayer-3.2.12.swf';
    
    flowplayer("player", swf_url, video_url);
    
    //auto scroll to the top of the player
    jQuery('html, body').animate({
      scrollTop: jQuery('#player').offset().top-50},1000);
  }
</script>

<?php
}//end of HTML output.

/** 
*
* Register the Menu Page
*
**/
function wp_video_option_page() {

  add_menu_page('Geo\'s Tutorial Videos', 'Geo\'s Tutorial Videos', 'manage_options', 'wp_tutorial_videos', 'wp_videos_page');
}
add_action('admin_menu', 'wp_video_option_page');

/**
*
* Helper to return the current plugin directory
*
**/
function video_plugin_path(){
  return path_join(WP_PLUGIN_URL, basename(dirname( __FILE__ )));
}
