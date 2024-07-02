<?php // This file outputs the options visible on the product edit page. 
    if ( ! defined( 'ABSPATH' ) ) {
        exit; // Exit if accessed directly.
    }
    
    if (!current_user_can('edit_posts')) {
        wp_die('You do not have sufficient permissions to access this page.');
    }
?>

<div class="wctb-edit-container">

    <!-- This is the container for the material options. -->
    <div class="wctb-edit-materials">

        <div class="wctb-edit-materiallabel-row flex-row">
            <label class="wctb-edit-materiallabel-1">Materials</label>
            <label class="wctb-edit-materiallabel-2">Base Price</label>
            <label class="wctb-edit-materiallabel-3">Hemmed Price</label>
            <label class="wctb-edit-materiallabel-4">Shown Colors</label>
        </div>
        <div class="wctb-edit-material-rows"> <?php
            $materials_count = get_post_meta($post->ID, "wctb-materials-count", true);
            if (!$materials_count) { $materials_count = 1; } // if there are no materials, set the count to 1 so that the blank row is shown.
            $materials_count = intval($materials_count);
            ?>

            <input type="hidden" name="wctb-materials-count" value="<?php echo esc_attr($materials_count); ?>" />

            <?php
            // loop through each material and output the input fields if there are materials to loop through, otherwise show a blank row.
            for ($i = 1; $i <= $materials_count; $i++) {
                $material_name = get_post_meta($post->ID, "wctb-material-name-$i", true);
                $material_baseprice = get_post_meta($post->ID, "wctb-material-baseprice-$i", true);
                $material_hemmedprice = get_post_meta($post->ID, "wctb-material-hemmedprice-$i", true);
                $material_colors = get_post_meta($post->ID, "wctb-material-colors-$i", true);
                
                ?>
                <div class="material-input-row mir-<?php echo $i; ?>">
                    <input type="text" name="wctb-material-name-<?php echo $i; ?>" value="<?php echo esc_attr($material_name); ?>" class="material-name" placeholder="Material Name"/>
                    <input type="text" name="wctb-material-baseprice-<?php echo $i; ?>" value="<?php echo esc_attr($material_baseprice); ?>" class="material-baseprice" placeholder="Base Price (ex: 0.99)" pattern="[0-9]+(\.[0-9]{1,2})?"/>
                    <input type="text" name="wctb-material-hemmedprice-<?php echo $i; ?>" value="<?php echo esc_attr($material_hemmedprice); ?>" class="material-hemmedprice" placeholder="Hemmed Price (ex: 0.20)" pattern="[0-9]+(\.[0-9]{1,2})?"/>
                    <input type="text" name="wctb-material-colors-<?php echo $i; ?>" value="<?php echo esc_attr($material_colors); ?>" class="material-colors" placeholder="List all shown colors like: Color 1, Color 2, Color 3"/>
                    <?php if ($i > 1) { ?>
                        <button class="remove-material wctb-edit-remove-btn">-</button>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <button id="add-material" class="wctb-edit-add-btn">+ Add Material</button>

        <script>
            var addMaterialBtn = document.querySelector('#add-material');
            var materialsContainer = document.querySelector('.wctb-edit-material-rows');
            var hiddenMaterialsCount = document.getElementsByName("wctb-materials-count")[0];
            var numMaterials = parseInt(hiddenMaterialsCount.value);

            // Adds functionality to the remove buttons that are already on the page.
            document.querySelectorAll('.remove-material').forEach(addFunctionalityToMaterialRemoveBtn);

            // Adds functionality to the add material button.
            addMaterialBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Prevents the button from saving and reloading the product edit page.

                var lastRow = document.querySelector('.material-input-row:last-of-type'); // clone the last material input row
                var newRow = lastRow.cloneNode(true);
                var inputs = newRow.querySelectorAll('input[type="text"]');
                var removeBtn = newRow.querySelector('.remove-material');
                materialsContainer.appendChild(newRow);

                numMaterials++;
                hiddenMaterialsCount.value = parseInt(numMaterials);
                newRow.classList.remove('mir-' + (numMaterials-1));
                newRow.classList.add('mir-' + numMaterials);
                
                inputs.forEach(function(input) {
                    input.value = '';
                    var oldName = input.getAttribute('name');
                    input.setAttribute('name', oldName.replace(/\-\d+$/, '-' + numMaterials));
                });

                if(!removeBtn) {
                    removeBtn = document.createElement('button');
                    removeBtn.textContent = '-';
                    removeBtn.classList.add('remove-material');
                    removeBtn.classList.add('wctb-edit-remove-btn');
                    newRow.appendChild(removeBtn);
                }

                addFunctionalityToMaterialRemoveBtn(removeBtn);
            });

            // The functionality of the material remove buttons.
            function addFunctionalityToMaterialRemoveBtn(removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var unwantedRow = removeBtn.parentElement;
                    materialsContainer.removeChild(unwantedRow);

                    // iterate over remaining rows and update name attributes to correct numbering
                    var materialRows = document.querySelectorAll('.material-input-row');
                    numMaterials = 0;
                    materialRows.forEach(function(row) {
                        numMaterials++;
                        
                        row.classList.forEach(function(className) {
                            if (className.startsWith('mir-')) {
                                row.classList.remove(className);
                            }
                        });

                        row.classList.add('mir-' + numMaterials);

                        var inputs = row.querySelectorAll('input[type="text"]');
                        inputs.forEach(function(input) {
                            var oldName = input.getAttribute('name');
                            input.setAttribute('name', oldName.replace(/\-\d+$/, '-' + numMaterials));
                        });
                    });

                    hiddenMaterialsCount.value = parseInt(numMaterials);
                });
            }
        </script>
    </div>

    <!-- This is the container for the edge options (Hem and Grommets). -->
    <div class="wctb-edit-edge">

        <div class="wctb-edit-edgelabel-row flex-row">
            <label class="wctb-edit-edgelabel-1">Edge Options</label>
            <label class="wctb-edit-edgelabel-2">Price</label>
        </div>
        <div class="wctb-edit-edge-rows"> <?php
            $edges_count = get_post_meta($post->ID, "wctb-edges-count", true);
            if (!$edges_count) { $edges_count = 1; } // if there are no edges, set the count to 1 so that the blank row is shown.
            $edges_count = intval($edges_count);
            ?>

            <input type="hidden" name="wctb-edges-count" value="<?php echo esc_attr($edges_count); ?>" />

            <?php
            // loop through each edge and output the input fields if there are edges to loop through, otherwise show a blank row.
            for ($i = 1; $i <= $edges_count; $i++) {
                $edge_name = get_post_meta($post->ID, "wctb-edge-name-$i", true);
                $edge_baseprice = get_post_meta($post->ID, "wctb-edge-baseprice-$i", true);

                ?>
                <div class="edge-input-row eir-<?php echo $i; ?>">
                    <input type="text" name="wctb-edge-name-<?php echo $i; ?>" value="<?php echo esc_attr($edge_name); ?>" class="edge-name" placeholder="Edge Name"/>
                    <input type="text" name="wctb-edge-baseprice-<?php echo $i; ?>" value="<?php echo esc_attr($edge_baseprice); ?>" class="edge-baseprice" placeholder="Edge Price (ex: 0.2)" pattern="[0-9]+(\.[0-9]{1,2})?"/>
                    <?php if ($i > 1) { ?>
                        <button class="remove-edge wctb-edit-remove-btn">-</button>
                    <?php } ?>
                </div>
            <?php } ?>
        </div>

        <button id="add-edge" class="wctb-edit-add-btn">+ Add Edge Option</button>

        <script>
            var addEdgeBtn = document.querySelector('#add-edge');
            var edgesContainer = document.querySelector('.wctb-edit-edge-rows');
            var hiddenEdgesCount = document.getElementsByName("wctb-edges-count")[0];
            var numEdges = parseInt(hiddenEdgesCount.value);

            // Adds functionality to the edge remove buttons that are already on the page.
            document.querySelectorAll('.remove-edge').forEach(addFunctionalityToEdgeRemoveBtn);

            // Adds functionality to the add edge button.
            addEdgeBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Prevents the button from saving and reloading the product edit page.

                var lastRow = document.querySelector('.edge-input-row:last-of-type'); // clone the last edge input row
                var newRow = lastRow.cloneNode(true);
                var inputs = newRow.querySelectorAll('input[type="text"]');
                var removeBtn = newRow.querySelector('.remove-edge');
                edgesContainer.appendChild(newRow);

                numEdges++;
                hiddenEdgesCount.value = parseInt(numEdges);
                newRow.classList.remove('eir-' + (numEdges-1));
                newRow.classList.add('eir-' + numEdges);
                
                inputs.forEach(function(input) {
                    input.value = '';
                    var oldName = input.getAttribute('name');
                    input.setAttribute('name', oldName.replace(/\-\d+$/, '-' + numEdges));
                });

                if(!removeBtn) {
                    removeBtn = document.createElement('button');
                    removeBtn.textContent = '-';
                    removeBtn.classList.add('remove-edge');
                    removeBtn.classList.add('wctb-edit-remove-btn');
                    newRow.appendChild(removeBtn);
                }
                
                addFunctionalityToEdgeRemoveBtn(removeBtn);
            });

            // The functionality of the edge remove buttons.
            function addFunctionalityToEdgeRemoveBtn(removeBtn) {
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    var unwantedRow = removeBtn.parentElement;
                    edgesContainer.removeChild(unwantedRow);

                    // iterate over remaining rows and update name attributes to correct numbering
                    var edgeRows = document.querySelectorAll('.edge-input-row');
                    numEdges = 0;
                    edgeRows.forEach(function(row) {
                        numEdges++;
                        
                        row.classList.forEach(function(className) {
                            if (className.startsWith('eir-')) {
                                row.classList.remove(className);
                            }
                        });

                        row.classList.add('eir-' + numEdges);

                        var inputs = row.querySelectorAll('input[type="text"]');
                        inputs.forEach(function(input) {
                            var oldName = input.getAttribute('name');
                            input.setAttribute('name', oldName.replace(/\-\d+$/, '-' + numEdges));
                        });
                    });

                    hiddenEdgesCount.value = parseInt(numEdges);
                });
            }
        </script>
    </div>

    <!-- This is the container for editing the headings on the front end, ie "Choose a Color", "Choose a Material", etc. -->
    <div class="wctb-edit-headings">
        <div class="wctb-edit-heading-colors flex-row">
            <label>Color Option Heading:</label>
            <?php $heading_colors = get_post_meta($post->ID, "wctb-heading-colors", true); ?>
            <input type="text" name="wctb-heading-colors" value="<?php echo esc_attr($heading_colors); ?>" placeholder="Choose a Color"/>
        </div>
        <div class="wctb-edit-heading-materials flex-row">
            <label>Material Option Heading:</label>
            <?php $heading_materials = get_post_meta($post->ID, "wctb-heading-materials", true); ?>
            <input type="text" name="wctb-heading-materials" value="<?php echo esc_attr($heading_materials); ?>" placeholder="Choose a Material"/>
        </div>
        <div class="wctb-edit-heading-edges flex-row">
            <label>Edge Option Heading:</label>
            <?php $heading_edges = get_post_meta($post->ID, "wctb-heading-edges", true); ?>
            <input type="text" name="wctb-heading-edges" value="<?php echo esc_attr($heading_edges); ?>" placeholder="Hem & Grommets"/>
        </div>
        <div class="wctb-edit-heading-dimensions flex-row">
            <label>Dimension Option Heading:</label>
            <?php $heading_dimensions = get_post_meta($post->ID, "wctb-heading-dimensions", true); ?>
            <input type="text" name="wctb-heading-dimensions" value="<?php echo esc_attr($heading_dimensions); ?>" placeholder="Dimensions"/>
        </div>
    </div>

    <!-- This is the container for the custom edge options. -->
    <div class="wctb-edit-customedge">
        <label class="wctb-edit-customedge-heading">Grommet and Edge Customization</label>

        <div class="wctb-edit-heading-customedgeoption flex-row">
            <div class="wctb-edit-customedgeoption-name flex-row">
                <label>Custom Grommet Option:</label>
                <?php $customedge_name = get_post_meta($post->ID, "wctb-heading-customedgeoption", true); ?>
                <input type="text" name="wctb-heading-customedgeoption" value="<?php echo esc_attr($customedge_name); ?>" placeholder="Specify Grommets Per Side"/>
            </div>
            <div class="wctb-edit-customedgeoption-price flex-row">
                <label>Edge Price:</label>
                <?php $customedge_price = get_post_meta($post->ID, "wctb-edge-baseprice-custom", true); ?>
                <input type="text" name="wctb-edge-baseprice-custom" value="<?php echo esc_attr($customedge_price); ?>" class="edge-baseprice" placeholder="Edge Price (ex: 0.2)" pattern="[0-9]+(\.[0-9]{1,2})?"/>
            </div>
        </div>
        <div class="wctb-edit-heading-customedge flex-row">
            <label>Custom Grommet Heading:</label>
            <?php $heading_customedge = get_post_meta($post->ID, "wctb-heading-customedge", true); ?>
            <input type="text" name="wctb-heading-customedge" value="<?php echo esc_attr($heading_customedge); ?>" placeholder="Number of Grommets Along Each Side"/>
        </div>
        <div class="wctb-edit-customedge-description flex-row">
            <label>Description:</label>
            <?php $customedge_description = get_post_meta($post->ID, "wctb-customedge-description", true); ?>
            <textarea name="wctb-customedge-description" rows="4" cols="50" placeholder="Enter an explanation of the custom grommet option. This is what the customer will see to help them understand."><?php echo esc_textarea($customedge_description); ?></textarea>
        </div>
        <div class="wctb-edit-customedge-enabled flex-row">
            <label>Enable Custom Edge Option:</label>
            <?php $customedge_enabled = get_post_meta($post->ID, "wctb-customedge-enabled", true); ?>
            <input type="checkbox" name="wctb-customedge-enabled" value="1" <?php checked($customedge_enabled, 1); ?>/>
        </div>
    </div>

    <!-- This is the container that details the pricing formulas being used. -->
    <div class="wctb-edit-formulas">
        <label>Pricing Formula</label>
        <p><span>Price =</span><span>Circumscribed Square Footage x (Material Base + Material Edge Premium + Edge Spacing Premium)</span></p>
        <p><span>Price (if unhemmed) =</span><span>Square Footage x Material Base</span></p>
        <p><span>If Price is Less than $85, Price =</span><span>$85</span></p>
        <p><span>Circumscribed Square Footage =</span><span>Diameter of the circle x Diameter of the circle.</span></p>
    </div>

</div>

<?php
