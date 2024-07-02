<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class WC_TarpBuilder_Type_Plugin {
            
    public function __construct() {
        add_action( 'woocommerce_loaded', array( $this, 'load_wctb_plugin') );
        add_filter( 'product_type_selector', array( $this, 'add_wctb_type' ) );
        register_activation_hook( __FILE__, array( $this, 'wctb_install' ) );
        add_action( 'woocommerce_product_options_general_product_data', 'add_wctb_options_group' );
        add_action( 'admin_footer', array( $this, 'enable_js_on_wctb_product' ) );
    }

    // load the plugin's required files
    public function load_wctb_plugin() {
        require_once dirname(__FILE__).'/class-wc-square-tarp-builder.php';
        require_once dirname(__FILE__).'/class-wc-circle-tarp-builder.php';
        require_once dirname(__FILE__).'/wctb-base-functions.php';
        require_once dirname(__FILE__).'/wctb-square-functions.php';
        require_once dirname(__FILE__).'/wctb-circle-functions.php';
    }

    // add the type to the product type selector ui on the product edit page
    public function add_wctb_type( $types ) {
        $types['squaretarpbuilder'] = __( 'Square Tarp Builder', 'wcsquaretarpbuilder' );
        $types['circletarpbuilder'] = __( 'Circle Tarp Builder', 'wccircletarpbuilder' );
        return $types;
    }

    // add the type to the terms list for woocommerce taxonomy
    public function wctb_install() {
        if ( ! get_term_by( 'slug', 'squaretarpbuilder', 'product_type' ) ) {
            wp_insert_term( 'squaretarpbuilder', 'product_type' );
        }
        if ( ! get_term_by( 'slug', 'circletarpbuilder', 'product_type' ) ) {
            wp_insert_term( 'circletarpbuilder', 'product_type' );
        }
    }

    public function enable_js_on_wctb_product() {
        global $post, $product_object;

        if ( ! $post ) { return; }

        if ( 'product' != $post->post_type ) :
            return;
        endif;

        if ( ! $product_object instanceof WC_Product ) {
            return;
        }

        $type = $product_object->get_type();
        $is_tarpbuilder = ($type == 'squaretarpbuilder' || $type == 'circletarpbuilder');

        ?>
        <script type='text/javascript'>
            jQuery(document).ready(function() {
                // for Price tab
                jQuery('#general_product_data .pricing').addClass('show_if_tarpbuilder');

                <?php if ( $is_tarpbuilder ) { ?>
                    jQuery('#general_product_data .pricing').show();
                <?php } ?>
            });
        </script>
        <?php
    }

}

new WC_TarpBuilder_Type_Plugin();


/** This will be called via woocommerce_template_single_add_to_cart when it does
*   do_action( 'woocommerce_' . $product->get_type() . '_add_to_cart' );
*   adding the function via add_action is required to load on page.
**/

if ( ! function_exists( 'woocommerce_squaretarpbuilder_add_to_cart' ) ) {

	/**
	 * Output the builder product add to cart area.
	 */
	function woocommerce_squaretarpbuilder_add_to_cart() {
		wc_get_template( 'single-product/add-to-cart/tarpbuildersquare.php' );
	}

    add_action( 'woocommerce_squaretarpbuilder_add_to_cart', 'woocommerce_squaretarpbuilder_add_to_cart', 30 );

}

if ( ! function_exists( 'woocommerce_circletarpbuilder_add_to_cart' ) ) {

    /**
     * Output the builder product add to cart area.
     */
    function woocommerce_circletarpbuilder_add_to_cart() {
        wc_get_template( 'single-product/add-to-cart/tarpbuildercircle.php' );
    }

    add_action( 'woocommerce_circletarpbuilder_add_to_cart', 'woocommerce_circletarpbuilder_add_to_cart', 30 );

}


/**
 * Add SKU field to product data meta box for custom product type.
 */
function add_wctb_options_group() {
    global $post, $product_object;

    if ( ! $post ) { return; }

    if ( 'product' != $post->post_type ) :
        return;
    endif;

    if ( ! $product_object instanceof WC_Product ) {
        return;
    }

    $product = wc_get_product( $post->ID );
    
    echo '<div class="options_group show_if_tarpbuilder clear">';

    woocommerce_wp_text_input( array(
        'id'          => 'wctb_sku',
        'label'       => __( 'SKU', 'woocommerce' ),
        'placeholder' => '',
        'desc_tip'    => 'true',
        'description' => __( 'Enter a SKU for this product.', 'woocommerce' ),
        'value'       => $product->get_sku()
    ) );

    echo '</div>';
}

