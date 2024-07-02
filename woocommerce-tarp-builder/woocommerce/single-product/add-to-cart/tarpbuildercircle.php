<?php
/**
* Custom Tarp Builder add to cart
*
* @see https://docs.woocommerce.com/document/template-structure/
* @package WooCommerce\Templates
* @version 3.4.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

global $product;
if ( ! $product->is_purchasable() ) {
	return;
}

$product_id = $product->get_id();

$materials_count = get_post_meta($product_id, "wctb-materials-count", true);
$materials_count = intval($materials_count);
$edges_count = get_post_meta($product_id, "wctb-edges-count", true);
$edges_count = intval($edges_count);
?>

<?php do_action( 'woocommerce_before_add_to_cart_form' ); ?>

<form class="cart" action="<?php echo esc_url( apply_filters( 'woocommerce_add_to_cart_form_action', $product->get_permalink() ) ); ?>" method="post" enctype='multipart/form-data'>
	<fieldset class="wctb-fe-tarpbuilder-options">
		<div class="wctb-fe-colors wctb-fe-option-container">
			<?php $heading_colors = get_post_meta($product_id, "wctb-heading-colors", true); ?>
			<label><?php echo esc_attr($heading_colors); ?></label>
			<div class="wctb-fe-color-swatches flex-row">
			</div>
		</div>
		<div class="wctb-fe-materials wctb-fe-option-container">
			<?php $heading_materials = get_post_meta($product_id, "wctb-heading-materials", true); ?>
			<label for="wctb-fe-material-options"><?php echo esc_attr($heading_materials); ?></label>
			<select id="wctb-fe-material-options" name="wctb-fe-material-option">
				<?php for ($i = 1; $i <= $materials_count; $i++) {
					$material_name = get_post_meta($product_id, "wctb-material-name-$i", true); 
					$material_colors = get_post_meta($product_id, "wctb-material-colors-$i", true);
					$material_baseprice = get_post_meta($product_id, "wctb-material-baseprice-$i", true);
					$material_hemmedprice = get_post_meta($product_id, "wctb-material-hemmedprice-$i", true);
					?>
					<option value="<?php echo $i; ?>" data-material-colors="<?php echo esc_attr($material_colors); ?>" data-mbp="<?php echo esc_attr($material_baseprice); ?>" data-mhp="<?php echo esc_attr($material_hemmedprice); ?>"><?php echo esc_attr($material_name); ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="wctb-fe-edges wctb-fe-option-container">
			<?php $heading_edges = get_post_meta($product_id, "wctb-heading-edges", true); ?>
			<label for="wctb-fe-edge-options"><?php echo esc_attr($heading_edges); ?></label>
			<select id="wctb-fe-edge-options" name="wctb-fe-edge-option">

				<?php for ($i = 1; $i <= $edges_count; $i++) {
					$edge_name = get_post_meta($product_id, "wctb-edge-name-$i", true);
					$edge_baseprice = get_post_meta($product_id, "wctb-edge-baseprice-$i", true);
					?>
					<option value="<?php echo $i; ?>" data-ebp="<?php echo esc_attr($edge_baseprice); ?>"><?php echo esc_attr($edge_name); ?></option>
				<?php } ?>

				<?php $customedge_enabled = get_post_meta($product_id, "wctb-customedge-enabled", true); 
				if ($customedge_enabled) {
					$customedge_name = get_post_meta($product_id, "wctb-heading-customedgeoption", true);
					$edge_baseprice = get_post_meta($product_id, "wctb-edge-baseprice-custom", true); ?>
					<option value="<?php echo esc_attr($edges_count) + 1; ?>" class="wctb-fe-customedge-option" data-ebp="<?php echo esc_attr($edge_baseprice); ?>"><?php echo esc_attr($customedge_name); ?></option>
				<?php } ?>

			</select>
		</div>
		<div class="wctb-fe-dimensions wctb-fe-option-container">
			<?php $heading_dimensions = get_post_meta($product_id, "wctb-heading-dimensions", true); ?>
			<label><?php echo esc_attr($heading_dimensions); ?></label>
			<div class="wctb-fe-dimensions-label-row wctb-fe-circle-diameter flex-row">
				<label for="wctb-fe-diameter-ft">Diameter (ft)<span class="wctb-fe-required">*</span></label>
				<label for="wctb-fe-diameter-in">Diameter (in)</label>
			</div>
			<div class="wctb-fe-dimensions-input-row wctb-fe-circle-diameter flex-row">
				<input type="number" value="0" name="wctb-fe-diameter-ft" min="0" max="100" required>
				<input type="number" value="0" name="wctb-fe-diameter-in" min="0" max="11">
			</div>
		</div>
		<!-- Hidden inputs -->
		<input type="hidden" name="wctb-fe-color-name" value="0"/>
		<input type="hidden" name="wctb-fe-material-number" value="0"/>
		<input type="hidden" name="wctb-fe-edge-number" value="0"/>
		<input type="hidden" name="wctb-fe-dimensions" value="0"/>
		<input type="hidden" name="wctb-fe-square-footage" value="0">
		<input type="hidden" name="wctb-fe-circumscribed-square-footage" value="0">
		<input type="hidden" name="wctb-fe-grommet-spacing" value="0">
		<input type="hidden" name="wctb-fe-grommet-spacing-diameter-inches" value="0">
	</fieldset>
	<fieldset class="wctb-fe-tarpbuilder-customedge-options hidden">
		<?php $heading_customedge = get_post_meta($product_id, "wctb-heading-customedge", true); ?>
		<?php $customedge_description = get_post_meta($product_id, "wctb-customedge-description", true); ?>
		<h3><?php echo esc_attr($heading_customedge); ?></h3>
		<p><?php echo esc_attr($customedge_description); ?></p>
		<div class="wctb-fe-side-input-labels flex-row">
			<label for="wctb-fe-grommet-number"># of Grommets</label>
		</div>
		<div class="wctb-fe-side-inputs flex-row">
			<input type="number" value="0" name="wctb-fe-grommet-number" min="0" max="100">
		</div>
		<div class="wctb-fe-special-instructions-container">
			<label for="wctb-fe-special-instructions">Special Instructions</label>
			<textarea name="wctb-fe-special-instructions" rows="4" cols="50" placeholder="Type any special notes or instructions you have here."></textarea>
		</div>
		<div class="wctb-fe-grommet-spacing-container">
			<label class="wctb-fe-grommet-spacing">Center to Center Grommet Spacing</label>
			<span id="wctb-fe-grommet-spacing-span">Spacing: 0 grommets, 0" apart.</span>
		</div>
	</fieldset>
	<section class="wctb-order-summary">
		<h3>Order Summary</h3>
		<div class="flex-row"><span>Color:</span><span id="wctb-color-summary">-</span></div>
		<div class="flex-row"><span>Material:</span><span id="wctb-material-summary">-</span></div>
		<div class="flex-row"><span>Edge:</span><span id="wctb-edge-summary">-</span></div>
		<div class="flex-row"><span>Dimensions:</span><span id="wctb-dimensions-summary">-</span></div>
		<div class="flex-row"><span>Square Footage:</span><span id="wctb-sqft-summary">-</span></div>
		<div class="wctb-fe-price-container flex-row">
			<label for="wctb-fe-price">Grand total:</label>
			<span id="wctb-fe-price">$85</span>
		</div>
	</section>

	<?php 
		do_action( 'woocommerce_before_add_to_cart_button' );
		do_action( 'woocommerce_before_add_to_cart_quantity' ); ?>

	<div class="wctb-fe-addtocart-container flex-row">
		<?php
		woocommerce_quantity_input(
			array(
				'min_value'   => apply_filters( 'woocommerce_quantity_input_min', $product->get_min_purchase_quantity(), $product ),
				'max_value'   => apply_filters( 'woocommerce_quantity_input_max', $product->get_max_purchase_quantity(), $product ),
				'input_value' => isset( $_POST['quantity'] ) ? wc_stock_amount( wp_unslash( $_POST['quantity'] ) ) : $product->get_min_purchase_quantity(), // WPCS: CSRF ok, input var ok.
			)
		);
		do_action( 'woocommerce_after_add_to_cart_quantity' );
		?>

		<button type="submit" name="add-to-cart" value="<?php echo esc_attr( $product->get_id() ); ?>" class="single_add_to_cart_button button alt<?php echo esc_attr( wc_wp_theme_get_element_class_name( 'button' ) ? ' ' . wc_wp_theme_get_element_class_name( 'button' ) : '' ); ?>"><?php echo esc_html( $product->single_add_to_cart_text() ); ?></button>
	</div>

	<?php do_action( 'woocommerce_after_add_to_cart_button' ); ?>

	<?php do_action( 'wctb_load_fe_scripts' ); ?>
</form>
<?php do_action( 'woocommerce_after_add_to_cart_form' ); ?>