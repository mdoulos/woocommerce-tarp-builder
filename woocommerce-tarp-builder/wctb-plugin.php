<?php
/**
* Plugin Name: WooCommerce Tarp Builder
* Plugin URI: https://www.studiodoulos.com/
* Description: A code repository for the custom tarp builders.
* Version: 1.0
* Author: Micah Doulos
* Author URI: http://www.studiodoulos.com/
**/

/** **/

if ( ! defined ('ABSPATH') ) {
    return;
}

require_once dirname( __FILE__ ) . '/tarp-builder/construct.php';


// Enqueue styles for the admin area.
add_action( 'admin_enqueue_scripts', 'enqueue_wctb_custom_admin_styles' );
function enqueue_wctb_custom_admin_styles() {
    wp_enqueue_style( 'wctb-admin', plugin_dir_url( __FILE__ ) . '/css/wctb-admin-styles.css' );
}

// Enqueue styles for the front end.
add_action( 'wp_enqueue_scripts', 'enqueue_wctb_custom_styles' );
function enqueue_wctb_custom_styles() {
	wp_enqueue_style( 'wctb-styles', plugin_dir_url( __FILE__ ) . '/css/wctb-styles.css' );
}

// Required to load the tarpbuildersquare.php and tarpbuildercircle.php template files.
add_filter( 'woocommerce_locate_template', 'wctb_intercept_wc_template', 10, 3 );
function wctb_intercept_wc_template( $template, $template_name, $template_path ) {

	$template_directory = trailingslashit( plugin_dir_path( __FILE__ ) ) . 'woocommerce/';
	$path = $template_directory . $template_name;

	return file_exists( $path ) ? $path : $template;

}