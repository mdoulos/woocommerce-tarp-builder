<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

/**
 * Circle Tarp Builder Product Type
 */
class WC_Product_CircleTarpBuilder extends WC_Product_Simple {

    /**
     * Get the SKU (Stock Keeping Unit) of the product.
     *
     * @param string $context The context in which the SKU is being requested.
     * @return string
     */
    public function get_sku( $context = 'view' ) {
        $sku = $this->get_prop( 'sku', $context );
        return $sku;
    }

    /**
     * Set the SKU (Stock Keeping Unit) of the product.
     *
     * @param string $sku
     */
    public function set_sku( $sku ) {
        $sku = (string) $sku;
        $this->set_prop('sku', wc_clean($sku));
    }

    /**
     * Get the product type.
     *
     * @return string
     */
    public function get_type() {
        return 'circletarpbuilder';
    }
    
}