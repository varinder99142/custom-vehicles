<?php
/*
Plugin Name: Custom Vehicles 
Plugin URI: ...
Description: Custom Vehicle Information and Booking plugin.
Version: 1.1.1
Author: ....
Author URI: ...
License: GPLv2 or later
*/



/*
*Function to create our Custom Post Type
*/
 
include( plugin_dir_path( __FILE__ ) . 'admin_page.php');
function custom_post_create() {
 	
//labels for Custom Post Type
    $labels = array(
        'name'                => __( 'Vehicles', 'Post Type General Name' ),
        'singular_name'       => __( 'Vehicle', 'Post Type Singular Name'),
        'all_items'           => __( 'All Vehicles' ),
        'view_item'           => __( 'View Vehicle' ),
        'add_new_item'        => __( 'Add New Vehicle' ),
        'add_new'             => __( 'Add New Vehicle' ),
        'edit_item'           => __( 'Edit Vehicle' ),
        'update_item'         => __( 'Update Vehicle' ),
        'search_items'        => __( 'Search Vehicle'),
        'not_found'           => __( 'Not Found' ),
        'not_found_in_trash'  => __( 'Not found in Trash' ),
    );
     
// Set options for Custom Post Type
     
    $args = array(
        'label'               => __( 'vehicles' ),
        'description'         => __( 'Vehicle information' ),
        'labels'              => $labels,
        // Features this CPT supports in Post Editor
        'supports'            => array( 'title', 'editor', 'excerpt', 'author', 'thumbnail', 'comments', 'revisions', 'custom-fields', ),
        // You can associate this CPT with a taxonomy or custom taxonomy. 
        'taxonomies'          => array( 'vehicle_type_categories' ),
        /* A hierarchical CPT is like Pages and can have
        * Parent and child items. A non-hierarchical CPT
        * is like Posts.
        */ 
        
        'public'              => true,
        'menu_position'       => 10,
		'hierarchical' => true,
        'has_archive'         => true,
        'capability_type'     => 'post',
		'rewrite'              => array( 'slug' => 'vehicles' ),
		'register_meta_box_cb' => 'add_vechiles_metaboxes',
 
    );
     
    // Registering your Custom Post Type
    register_post_type( 'vehicles', $args );
 
}
 
/* Hook  'init' action */
 
add_action( 'init', 'custom_post_create', 0 );
add_action( 'init', 'vehicle_type_categories' );
function custom_data_options_install() {
	global $wpdb;
	$sql = "CREATE TABLE IF NOT EXISTS booking_list_vehicle (
	id int(11) NOT NULL AUTO_INCREMENT,
	
	fname varchar(50)  NULL,
	lname varchar(50)  NULL,
	email varchar(50)  NULL,
	phone varchar(50)  NULL,
	vehicle varchar(50)  NULL,
	vehicle_type varchar(50)  NULL,
	message varchar(250)  NULL,
	price varchar(50)  NULL,
	status varchar(50)  NULL,
	time TIMESTAMP NOT NULL,
	PRIMARY KEY id (id)
	)";
	
	$wpdb->query($sql);
	}
	// run the install scripts upon plugin activation
	register_activation_hook(__FILE__,'custom_data_options_install');
/**
 * Output the HTML for the metabox.
 */
function custom_field_data() {
	global $post;
	
	// Get the price_per_day data if it's already been entered
	$price_per_day = get_post_meta( $post->ID, 'price_per_day', true );
	// Output the field
	echo '<input type="text" name="price_per_day" value="' . esc_textarea( $price_per_day )  . '" class="widefat">';
}
function add_vechiles_metaboxes() {
	add_meta_box(
		'custom_field_data',
		'Vehicle starting price per day',
		'custom_field_data',
		'vehicles',
		'normal',
		'high'
	);
}

add_action( 'save_post', 'save_custom_meta', 1, 2 );

/**
 * Save the metabox data
 */
function save_custom_meta( $post_id, $post ) {
	// Return if the user doesn't have edit permissions.
	if ( ! current_user_can( 'edit_post', $post_id ) ) {
		return $post_id;
	}
	// Now that we're authenticated, time to save the data.
	$value=$_POST['price_per_day'];
	$key='price_per_day';
	if ( get_post_meta( $post_id, $key, false ) ) {
			// If the custom field already has a value, update it.
			update_post_meta( $post_id, $key, $value );
		} else {
			// If the custom field doesn't have a value, add it.
			add_post_meta( $post_id, $key, $value);
		}
}
function vehicle_type_categories() {
	register_taxonomy(
		'vehicle_type_categories',
		'vehicles',
		array(
			'label' => __( 'Vehicle Type' ),
			'rewrite' => array('slug' => 'vehicles'),
			'hierarchical' => true,
		)
	);
}

// function that runs when shortcode is called
function booking_shortcode() { 
 
 $terms = get_terms( array(
    'taxonomy' => 'vehicle_type_categories',
    'hide_empty' => false,
) );

$output.= '<option > Select Type</option>';
 foreach( $terms as $category ) {
        if( $category->parent == 0 ) {
            $output.= '<option value="'.$category->term_id.'">'. esc_attr( $category->name ) .'</option>';
		}
		}
// Things that you want to do. 
	$ajaxUrl=admin_url('admin-ajax.php');
	$admin_url=admin_url( 'admin-post.php' );
$form = '<form >

  <label  >First name:</label><br>
  <input type="text" id="fname" name="fname"><br>
  <label >Last name:</label><br>
  <input type="text" id="lname" name="lname"><br>
   <label >Email:</label><br>
  <input type="email" id="email" name="email"><br>
  
   <label >Phone:</label><br>
  <input type="text" id="phone" name="phone"><br>
   <label > Select Vehicle Type:</label><br>
 <select name="vehicle_type" id="vehicle_type" onChange="get_posts_vehicle()">
 '.$output.'
 </select><br>
  <label >Select Vehicle:</label><br>
 <select name="vehicle" id="vehicle_id" onChange="get_rates_vehicle()">
 
 </select><br>
  <label >Starting Price:<span id="starting_price"></span></label><br>
  <label >Message:</label><br>
  <textarea name="message"></textarea><br>
  <input id="ajaxUrl" type="hidden" value="'.$ajaxUrl.'">
  <input name="action" type="hidden" value="submit_form_booking">
  <input type="button" value="Submit" onClick="save_data()">
</form>'; 
 
// Output needs to be return
return $form;
} 
// register shortcode
add_shortcode('booking_form', 'booking_shortcode'); 


function my_load_scripts($hook) {
 
    // create my own version codes
    $my_js_ver  = date("ymd-Gis", filemtime( plugin_dir_path( __FILE__ ) . 'js/custom.js' ));
    wp_enqueue_script( 'custom_js', plugins_url( 'js/custom.js', __FILE__ ), array(), $my_js_ver );
}
add_action('wp_enqueue_scripts', 'my_load_scripts');
add_action("wp_ajax_get_post_vehicle", "get_post_vehicle");
add_action("wp_ajax_nopriv_get_post_vehicle", "get_post_vehicle");
add_action("wp_ajax_get_rate_vehicle", "get_rate_vehicle");
add_action("wp_ajax_nopriv_get_rate_vehicle", "get_rate_vehicle");

add_action("wp_ajax_submit_form_booking", "submit_form_booking");
add_action("wp_ajax_nopriv_submit_form_booking", "submit_form_booking");


function get_post_vehicle() {

$posts_array = get_posts(
    array(
        'posts_per_page' => -1,
        'post_type' => 'vehicles',
        'tax_query' => array(
			array(
			'taxonomy' => 'vehicle_type_categories',
			'field' => 'term_id',
			'terms' => $_POST['cat_id'],
			)
        )
		
		
    )
);
$output='';
echo '<option>Select Vehicle</option>';
foreach($posts_array as $posts)
{
	
	echo '<option value="'.$posts->ID.'">'. esc_attr( $posts->post_title ) .'</option>';
}

die;
}
function get_rate_vehicle()
{
	$price_per_day = get_post_meta( $_POST['post_id'], 'price_per_day', true );
	echo $price_per_day;die;
}
add_action('parse_request', 'my_custom_url_handler');

function submit_form_booking() {
  
   global $wpdb;
   $wpdb->insert('booking_list_vehicle', array(
    'fname' => $_POST['fname'],
    'lname' => $_POST['lname'],
	'email' => $_POST['email'],
    'phone' => $_POST['phone'],
    'vehicle' => $_POST['vehicle_id'],
    'vehicle_type' => $_POST['vehicle_type'],
    'price' => $_POST['starting_price'],
	    'message' => $_POST['message']
));
	$from = get_option('admin_email');
	$message = "Your booking have been submitted.";
	$to = $_POST['email'];
	$subject = "Booking Request Submitted.";
	$headers = 'From: '. $from . "\r\n" .
	'Reply-To: ' . $from . "\r\n";
	$sent = wp_mail($to, $subject, strip_tags($message), $headers);
	$to = get_option('admin_email');
	$message = "New booking have been submitted.";
	$to = $_POST['email'];
	$subject = "Booking Request Submitted.";
	$headers = 'From: '. $from . "\r\n" .
	'Reply-To: ' . $from . "\r\n";
	$sent = wp_mail($to, $subject, strip_tags($message), $headers);
					
					
					
      exit();
}