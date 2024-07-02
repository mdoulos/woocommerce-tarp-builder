<?php

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

// Assign Edit Boxes to Square Tarp Builder's Product Edit Page.
add_action( 'add_meta_boxes', 'custom_square_tarpbuilder_postboxes' );
function custom_square_tarpbuilder_postboxes() {
    global $post;
    if ( !is_object( $post ) ) {
        return;
    }

    $product = wc_get_product( $post->ID );
    if ( !$product || !is_a( $product, 'WC_Product' ) ) {
        return;
    }

    if ('squaretarpbuilder' === $product->get_type()) {
        add_meta_box(
            'postbox_for_square_tarpbuilders',
            'Square Tarp Builder Options',
            'render_postbox_inputs_for_square_tarpbuilders',
            'product',
            'advanced',
            'high'
        );
    }
}

// Code for the Edit Boxes on the Product Edit Page.
function render_postbox_inputs_for_square_tarpbuilders( $post ) {
    require_once('postbox-inputs-square.php'); // Outputs the inputs on the product edit page which in turn defines what shows up on the front end.
}

// Save the inputs from the Edit Boxes on the Product Edit Page.
add_action('save_post', 'save_postbox_inputs_for_square_tarpbuilders');
function save_postbox_inputs_for_square_tarpbuilders($post_id) {

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) { return; }
    if (!current_user_can('edit_post', $post_id)) { return; }
    if ('product' !== get_post_type($post_id)) { return; }
    if (!wc_get_product($post_id) || 'squaretarpbuilder' !== wc_get_product($post_id)->get_type()) { return; }


    $materials_count = filter_input(INPUT_POST, 'wctb-materials-count', FILTER_SANITIZE_NUMBER_INT);
    $materials_count = intval($materials_count);
    update_post_meta($post_id, "wctb-materials-count", $materials_count);

    $edges_count = filter_input(INPUT_POST, 'wctb-edges-count', FILTER_SANITIZE_NUMBER_INT);
    $edges_count = intval($edges_count);
    update_post_meta($post_id, "wctb-edges-count", $edges_count);

    for ($i = 1; $i <= $materials_count; $i++) {
        $material_name = sanitize_text_field($_POST["wctb-material-name-$i"]);
        update_post_meta($post_id, "wctb-material-name-$i", $material_name);

        $material_baseprice = filter_input(INPUT_POST, "wctb-material-baseprice-$i", FILTER_VALIDATE_FLOAT);
        update_post_meta($post_id, "wctb-material-baseprice-$i", $material_baseprice);

        $material_hemmedprice = filter_input(INPUT_POST, "wctb-material-hemmedprice-$i", FILTER_VALIDATE_FLOAT);
        update_post_meta($post_id, "wctb-material-hemmedprice-$i", $material_hemmedprice);

        $material_colors = sanitize_text_field($_POST["wctb-material-colors-$i"]);
        update_post_meta($post_id, "wctb-material-colors-$i", $material_colors);
    }

    for ($i = 1; $i <= $edges_count; $i++) {
        $edge_name = sanitize_text_field($_POST["wctb-edge-name-$i"]);
        update_post_meta($post_id, "wctb-edge-name-$i", $edge_name);

        $edge_baseprice = filter_input(INPUT_POST, "wctb-edge-baseprice-$i", FILTER_VALIDATE_FLOAT);
        update_post_meta($post_id, "wctb-edge-baseprice-$i", $edge_baseprice);
    }

    $heading_colors = sanitize_text_field($_POST["wctb-heading-colors"]);
    update_post_meta($post_id, "wctb-heading-colors", $heading_colors);
    $heading_materials = sanitize_text_field($_POST["wctb-heading-materials"]);
    update_post_meta($post_id, "wctb-heading-materials", $heading_materials);
    $heading_edges = sanitize_text_field($_POST["wctb-heading-edges"]);
    update_post_meta($post_id, "wctb-heading-edges", $heading_edges);
    $heading_dimensions = sanitize_text_field($_POST["wctb-heading-dimensions"]);
    update_post_meta($post_id, "wctb-heading-dimensions", $heading_dimensions);

    $heading_customedge = sanitize_text_field($_POST["wctb-heading-customedge"]);
    update_post_meta($post_id, "wctb-heading-customedge", $heading_customedge);
    $customedge_name = sanitize_text_field($_POST["wctb-heading-customedgeoption"]);
    update_post_meta($post_id, "wctb-heading-customedgeoption", $customedge_name);
    $customedge_price = sanitize_text_field($_POST["wctb-edge-baseprice-custom"]);
    update_post_meta($post_id, "wctb-edge-baseprice-custom", $customedge_price);
    $customedge_description = sanitize_text_field($_POST["wctb-customedge-description"]);
    update_post_meta($post_id, "wctb-customedge-description", $customedge_description);

    $customedge_enabled = filter_input(INPUT_POST, "wctb-customedge-enabled", FILTER_VALIDATE_BOOLEAN);
    $customedge_enabled = $customedge_enabled !== null ? $customedge_enabled : false;
    update_post_meta($post_id, "wctb-customedge-enabled", $customedge_enabled);

    $sku = sanitize_text_field($_POST['wctb_sku']);
    $product = wc_get_product($post_id);

    if ($product) {
        $product->set_sku($sku);
        $product->save(); // Ensure the product is saved
    }
}

// Add the cart item data to the cart item when the product is added to the cart.
add_filter( 'woocommerce_add_cart_item_data', 'add_wctb_square_cart_item_data', 10, 3 );
function add_wctb_square_cart_item_data( $cart_item_data, $product_id, $variation_id ) {
    $product = wc_get_product( $product_id );

	if( $product->is_type( 'squaretarpbuilder' ) ) {

        // Chosen Options
        $chosen_color_name = sanitize_text_field($_POST["wctb-fe-color-name"]);
        $chosen_material_num = filter_input(INPUT_POST, 'wctb-fe-material-number', FILTER_SANITIZE_NUMBER_INT);
        $chosen_material_num = intval($chosen_material_num);
        $chosen_edge_num = filter_input(INPUT_POST, 'wctb-fe-edge-number', FILTER_SANITIZE_NUMBER_INT);
        $chosen_edge_num = intval($chosen_edge_num);
        $square_footage = filter_input(INPUT_POST, "wctb-fe-square-footage", FILTER_VALIDATE_FLOAT);
        $square_yards = $square_footage / 9;
        $dimensions = sanitize_text_field($_POST["wctb-fe-dimensions"]);
        $special_instructions = sanitize_text_field($_POST["wctb-fe-special-instructions"]);
        $grommetspacing_1 = sanitize_text_field($_POST["wctb-fe-grommet-spacing-1"]);
        $grommetspacing_2 = sanitize_text_field($_POST["wctb-fe-grommet-spacing-2"]);
        
        $customgrommetsidepricing = 0;
        // $grommetspacing_inches1 = sanitize_text_field($_POST["wctb-fe-grommet-spacing-lengthinches1"]);
        // $grommetspacing_inches2 = sanitize_text_field($_POST["wctb-fe-grommet-spacing-lengthinches2"]);
        // $grommetspacing_inches3 = sanitize_text_field($_POST["wctb-fe-grommet-spacing-widthinches1"]);
        // $grommetspacing_inches4 = sanitize_text_field($_POST["wctb-fe-grommet-spacing-widthinches2"]);

        // A method for adding custom grommet side pricing based on the grommet spacing.
        // for ($i = 1; $i <= 4; $i++) {
        //     $grommetspacing_inches = ${"grommetspacing_inches$i"};
        //     if ($grommetspacing_inches > 24) {
        //         $customgrommetsidepricing += 0.26;
        //     } else if ($grommetspacing_inches > 18) {
        //         $customgrommetsidepricing += 0.31;
        //     } elseif ($grommetspacing_inches > 12) {
        //         $customgrommetsidepricing += 0.36;
        //     } elseif ($grommetspacing_inches > 6) {
        //         $customgrommetsidepricing += 0.39;
        //     } else if ($grommetspacing_inches > 0) {
        //         $customgrommetsidepricing += 0.52;
        //     }
        // }

        $edge_option_price = 0;

        // Get Option Values
        $material_name = get_post_meta($product_id, "wctb-material-name-$chosen_material_num", true);
        $material_baseprice = get_post_meta($product_id, "wctb-material-baseprice-$chosen_material_num", true);
        $material_hemmedprice = get_post_meta($product_id, "wctb-material-hemmedprice-$chosen_material_num", true);
        $material_weight_ounces = preg_replace("/[^0-9]/", "", $material_name);
        $material_weight_ounces = intval($material_weight_ounces);

        if ($material_weight_ounces < 10) {
            $material_weight_ounces = 10;
        }

        $dynamic_weight = ($material_weight_ounces * $square_yards) / 16;
        $dynamic_weight = $dynamic_weight + $dynamic_weight * 0.02;
        $dynamic_weight = round($dynamic_weight, 2);
        if ($dynamic_weight > 149) {
            $dynamic_weight = 149;
        }
        $cart_item_data['dynamic_weight'] = $dynamic_weight;

        if ($chosen_edge_num == 99) {
            $edge_name = get_post_meta($product_id, "wctb-heading-customedgeoption", true);
            $edge_baseprice = get_post_meta($product_id, "wctb-edge-baseprice-custom", true);
            $cart_item_data['special_instructions'] = $special_instructions;
            $cart_item_data['grommetspacing_1'] = $grommetspacing_1;
            $cart_item_data['grommetspacing_2'] = $grommetspacing_2;

            $edge_option_price = $edge_baseprice + $customgrommetsidepricing;
        } else {
            $edge_name = get_post_meta($product_id, "wctb-edge-name-$chosen_edge_num", true);
            $edge_baseprice = get_post_meta($product_id, "wctb-edge-baseprice-$chosen_edge_num", true);

            $edge_option_price = $edge_baseprice;
        }

        if ($edge_option_price > 0 || $chosen_edge_num > 1) {
            $dynamicPrice = $square_footage * ($material_baseprice + $material_hemmedprice + $edge_option_price);
        } else {
            $dynamicPrice = $square_footage * $material_baseprice;
        }

        if ($dynamicPrice < 85) {
            $dynamicPrice = 85;
        }

        // Save Item Data such as option names and price.
        $cart_item_data['chosen_color_name'] = $chosen_color_name;
        $cart_item_data['material_name'] = $material_name;
        $cart_item_data['edge_name'] = $edge_name;
        $cart_item_data['dimensions'] = $dimensions;
        $cart_item_data['square_footage'] = $square_footage;
        $cart_item_data['dynamicPrice'] = $dynamicPrice;
	}
    

	return $cart_item_data;
}

// Modify the price of the cart item based upon the saved selected options.
add_action( 'woocommerce_before_calculate_totals', 'wctb_adjust_square_cart_item_price', 10, 1 );
function wctb_adjust_square_cart_item_price( $cart_obj ) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) {
		return;
	}

	foreach( $cart_obj->get_cart() as $key=>$value ) {
        $product_type = $value['data']->get_type();

		if( $product_type == 'squaretarpbuilder' && isset( $value['dynamicPrice'] ) ) {
			$price = $value['dynamicPrice'];
			$value['data']->set_price( ( $price ) );
		}

        if( $product_type == 'squaretarpbuilder' && isset( $value['dynamic_weight'] ) ) {
            $dynamic_weight = $value['dynamic_weight'];
            $value['data']->set_weight( $dynamic_weight );
        }
	}
}

// Modify the displayed price of the cart item based upon the saved selected options.
add_filter( 'woocommerce_cart_item_subtotal', 'wctb_square_cart_item_subtotal', 10, 3 );
function wctb_square_cart_item_subtotal( $subtotal, $cart_item, $cart_item_key ) {
    $product_type = $cart_item['data']->get_type();

    if( $product_type == 'squaretarpbuilder' && isset( $cart_item['dynamicPrice'] ) ) {
        $subtotal = wc_price( $cart_item['dynamicPrice'] * $cart_item['quantity'] );
    }
    return $subtotal;
}

// Display the selected option details on the cart items in the cart and at checkout.
add_filter( 'woocommerce_get_item_data', 'display_wctb_square_item_details_cart_checkout', 10, 2 );
function display_wctb_square_item_details_cart_checkout( $item_data, $cart_item ) {
    $product_type = $cart_item['data']->get_type();

    if( $product_type != 'squaretarpbuilder' ) {
        return $item_data;
    }

    $color_name = isset( $cart_item['chosen_color_name'] ) ? $cart_item['chosen_color_name'] : '';
    $material_name = isset( $cart_item['material_name'] ) ? $cart_item['material_name'] : '';
    $edge_name = isset( $cart_item['edge_name'] ) ? $cart_item['edge_name'] : '';
    $dimensions = isset( $cart_item['dimensions'] ) ? $cart_item['dimensions'] : '';
    $square_footage = isset( $cart_item['square_footage'] ) ? $cart_item['square_footage'] : '';
    $special_instructions = isset( $cart_item['special_instructions'] ) ? $cart_item['special_instructions'] : '';
    $grommetspacing_1 = isset( $cart_item['grommetspacing_1'] ) ? $cart_item['grommetspacing_1'] : '';
    $grommetspacing_2 = isset( $cart_item['grommetspacing_2'] ) ? $cart_item['grommetspacing_2'] : '';

    if ( ! empty( $color_name ) ) {
        $item_data[] = array(
            'key'     => 'Color',
            'value'   => $color_name,
        );
    }

    if ( ! empty( $material_name ) ) {
        $item_data[] = array(
            'key'     => 'Material',
            'value'   => $material_name,
        );
    }

    if ( ! empty( $edge_name ) ) {
        $item_data[] = array(
            'key'     => 'Edge',
            'value'   => $edge_name,
        );
    }

    if ( ! empty( $dimensions ) ) {
        $item_data[] = array(
            'key'     => 'Dimensions',
            'value'   => $dimensions,
        );
    }

    if ( ! empty( $square_footage ) ) {
        $item_data[] = array(
            'key'     => 'Square Footage',
            'value'   => $square_footage,
        );
    }

    if ( ! empty( $special_instructions ) ) {
        $item_data[] = array(
            'key'     => 'Special Instructions',
            'value'   => $special_instructions,
        );
    }

    if ( ! empty( $grommetspacing_1 ) ) {
        $item_data[] = array(
            'key'     => 'Grommets Side 1',
            'value'   => $grommetspacing_1,
        );
    }

    if ( ! empty( $grommetspacing_2 ) ) {
        $item_data[] = array(
            'key'     => 'Grommets Side 2',
            'value'   => $grommetspacing_2,
        );
    }

    return $item_data;
}

// Display the selected option details on the order items, thank you page, and email notifications.
add_action( 'woocommerce_add_order_item_meta', 'display_wctb_square_item_details_order_thankyou_emails', 10, 3 );
function display_wctb_square_item_details_order_thankyou_emails( $item_id, $values, $cart_item_key ) {
    if ( isset( $values['data'] ) ) {
        $product_type = $values['data']->get_type();
    } else {
        return;
    }

    if( $product_type == 'squaretarpbuilder' ) {
        $color_name = isset( $values['chosen_color_name'] ) ? $values['chosen_color_name'] : '';
        $material_name = isset( $values['material_name'] ) ? $values['material_name'] : '';
        $edge_name = isset( $values['edge_name'] ) ? $values['edge_name'] : '';
        $dimensions = isset( $values['dimensions'] ) ? $values['dimensions'] : '';
        $square_footage = isset( $values['square_footage'] ) ? $values['square_footage'] : '';
        $special_instructions = isset( $values['special_instructions'] ) ? $values['special_instructions'] : '';
        $grommetspacing_1 = isset( $values['grommetspacing_1'] ) ? $values['grommetspacing_1'] : '';
        $grommetspacing_2 = isset( $values['grommetspacing_2'] ) ? $values['grommetspacing_2'] : '';

        if ( ! empty( $color_name ) ) {
            wc_add_order_item_meta( $item_id, 'Color', $color_name );
        }

        if ( ! empty( $material_name ) ) {
            wc_add_order_item_meta( $item_id, 'Material', $material_name );
        }

        if ( ! empty( $edge_name ) ) {
            wc_add_order_item_meta( $item_id, 'Edge', $edge_name );
        }

        if ( ! empty( $dimensions ) ) {
            wc_add_order_item_meta( $item_id, 'Dimensions', $dimensions );
        }

        if ( ! empty( $square_footage ) ) {
            wc_add_order_item_meta( $item_id, 'Square Footage', $square_footage );
        }

        if ( ! empty( $special_instructions ) ) {
            wc_add_order_item_meta( $item_id, 'Special Instructions', $special_instructions );
        }

        if ( ! empty( $grommetspacing_1 ) ) {
            wc_add_order_item_meta( $item_id, 'Grommets Side 1', $grommetspacing_1 );
        }

        if ( ! empty( $grommetspacing_2 ) ) {
            wc_add_order_item_meta( $item_id, 'Grommets Side 2', $grommetspacing_2 );
        }
    }
}
