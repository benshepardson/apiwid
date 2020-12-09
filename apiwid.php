<?php
/*
    Plugin Name: WIDAPI
    Plugin URI: Ben-SEO.com
    Description: Test plugin based on code from envoto themes and wpbeginner
    Author: Ben
    Version: 1.2
    Author URI: http://ben-seo.com
*/

  /**
 * Registers a stylesheet.
 */

 //////convert kelvin to farhenheit scottnelle.com/
function k_to_f($temp) {
    if ( !is_numeric($temp) ) { return false; }
    return round((($temp - 273.15) * 1.8) + 32);
}

/// add styles
function wpdocs_register_plugin_styles() {
    wp_register_style( 'my-plugin', plugins_url( 'apiwid/style.css' ) );
    wp_enqueue_style( 'my-plugin' );
}
// Register style sheet.
add_action( 'wp_enqueue_scripts', 'wpdocs_register_plugin_styles' );

// Creating the widget 
class wpb_widget extends WP_Widget {
  
function __construct() {
parent::__construct(
  
// Base ID of your widget
'wpb_widget', 
  
// Widget name will appear in UI
__('WID API', 'wpb_widget_domain'), 
  
// Widget description
array( 'description' => __( 'Sample widget', 'wpb_widget_domain' ), ) 
);
}
  
// Creating widget front-end
  
public function widget( $args, $instance ) {

  


$title = apply_filters( 'widget_title', $instance['title'] );
  
// before and after widget arguments are defined by themes
echo $args['before_widget'];
if ( ! empty( $title ) )
echo $args['before_title'] . $title . $args['after_title'];
  
// This is where you run the code and display the output

$request = wp_remote_get("http://api.openweathermap.org/data/2.5/weather?q=Miami,USA&appid=64675cd21933f373374bac2d5748ceb9");

$body = wp_remote_retrieve_body($request);

$data = json_decode($body);

if( ! empty( $data ) ) {

    ?>
    <div class="cityhead">Current Weather in <?php echo  $data->name ; ?></div>
     <div class="grid-container">
     <div class="grid-head">Temperture</div>
  <div class="grid-head">Wind Speed</div>
  <div class="grid-head">Cloud Cover</div>
  <div class="grid-item"><?php echo  k_to_f($data->main->temp) ; ?>&#8457;<hr style="border-color:whitesmoke;"><img src='<?php echo plugin_dir_url( __FILE__ ); ?>icons/thermometer.png'></div>
  <div class="grid-item"><?php echo  round($data->wind->speed/0.44704) ; ?><font size='1px'>MPH</font> <hr style="border-color:whitesmoke;"><img src='<?php echo plugin_dir_url( __FILE__ ); ?>icons/wind.png'></div>
  <div class="grid-item"><?php echo  $data->clouds->all ; ?>%<hr style="border-color:whitesmoke;"><img src='<?php echo plugin_dir_url( __FILE__ ); ?>icons/cloud.png'></div>
</div> 

    <?php
	

}
 
echo $args['after_widget'];
}
          
// Widget Backend 
public function form( $instance ) {
if ( isset( $instance[ 'title' ] ) ) {
$title = $instance[ 'title' ];
}
else {
$title = __( 'New title', 'wpb_widget_domain' );
}
// Widget admin form
?>
<p>
<label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:' ); ?></label> 
<input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<?php 
}
      
// Updating widget replacing old instances with new
public function update( $new_instance, $old_instance ) {
$instance = array();
$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
return $instance;
}
 
// Class wpb_widget ends here
} 
 
 
// Register and load the widget
function wpb_load_widget() {
    register_widget( 'wpb_widget' );
}
add_action( 'widgets_init', 'wpb_load_widget' );