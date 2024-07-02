/**
 * File wctb-frontend-square.js.
 *
 * Contains all of the code for the frontend of the WCTB plugin, ie the Tarp Builder product page.
 * 
 */

// ---------------------------------------------------------
// ---------------- Variable Definitions -------------------
// ---------------------------------------------------------

const summarySpan = {};
summarySpan['wctb-color-summary'] = document.getElementById('wctb-color-summary');
summarySpan['wctb-material-summary'] = document.getElementById('wctb-material-summary');
summarySpan['wctb-edge-summary'] = document.getElementById('wctb-edge-summary');
summarySpan['wctb-dimensions-summary'] = document.getElementById('wctb-dimensions-summary');
summarySpan['wctb-sqft-summary'] = document.getElementById('wctb-sqft-summary');
var colorsContainer = document.querySelector('.wctb-fe-color-swatches');
var productSummary = document.querySelector('.product-page-summary');
var firstPrice = productSummary.querySelector('.price');
const inputLengthInFeet = document.querySelector('input[name="wctb-fe-length-ft"]');
const inputLengthInInches = document.querySelector('input[name="wctb-fe-length-in"]');
const inputWidthInFeet = document.querySelector('input[name="wctb-fe-width-ft"]');
const inputWidthInInches = document.querySelector('input[name="wctb-fe-width-in"]');
const dynamicPrice = document.querySelector('#wctb-fe-price');
const customGrommetPanel = document.querySelector('.wctb-fe-tarpbuilder-customedge-options');
const inputGrommetsLength1 = document.querySelector('input[name="wctb-fe-length-side-1"]');
const inputGrommetsLength2 = document.querySelector('input[name="wctb-fe-length-side-2"]');
const inputGrommetsWidth1 = document.querySelector('input[name="wctb-fe-width-side-1"]');
const inputGrommetsWidth2 = document.querySelector('input[name="wctb-fe-width-side-2"]');
const grommetSpacingSpan1 = document.querySelector('#wctb-fe-side-1-spacing');
const grommetSpacingSpan2 = document.querySelector('#wctb-fe-side-2-spacing');

const colorInput = document.querySelector('input[name="wctb-fe-color-name"]');
const materialNumber = document.querySelector('input[name="wctb-fe-material-number"]');
const edgeNumber = document.querySelector('input[name="wctb-fe-edge-number"]');
const dimensionsInput = document.querySelector('input[name="wctb-fe-dimensions"]');
const squareFootageInput = document.querySelector('input[name="wctb-fe-square-footage"]');
const grommetSpacing1 = document.querySelector('input[name="wctb-fe-grommet-spacing-1"]');
const grommetSpacing2 = document.querySelector('input[name="wctb-fe-grommet-spacing-2"]');

let grommetSpacingInches = {
    1: document.querySelector('input[name="wctb-fe-grommet-spacing-lengthinches1"]'),
    2: document.querySelector('input[name="wctb-fe-grommet-spacing-lengthinches2"]'),
    3: document.querySelector('input[name="wctb-fe-grommet-spacing-widthinches1"]'),
    4: document.querySelector('input[name="wctb-fe-grommet-spacing-widthinches2"]')
};

const materialOptionContainer = document.querySelector('.wctb-fe-materials');


// ---------------------------------------------------------
// -------------------- Code Execution ---------------------
// ---------------------------------------------------------

// Remove the first price element from the product summary. It's not needed because the price is calculated dynamically.
if (firstPrice) { firstPrice.remove(); }

// Update the custom summary and price anytime the user changes an input.
document.querySelectorAll('fieldset input').forEach(function(element) {
    element.addEventListener("input", function(e){
        updateOrderDetails();
    });
});

document.getElementById('wctb-fe-edge-options').addEventListener('change', function (e) {
    if (e.target.options[e.target.selectedIndex].classList.contains('wctb-fe-customedge-option')) {
        customGrommetPanel.classList.remove('hidden');
    } else {
        customGrommetPanel.classList.add('hidden');
    }

    updateOrderDetails();
});

// Change which color swatches are visible based on the selected material.
document.getElementById('wctb-fe-material-options').addEventListener('change', function (e) {
    addColorSwatches(e.target.options[e.target.selectedIndex]);
    updateOrderDetails();
});
addColorSwatches(document.getElementById('wctb-fe-material-options').options[0]);

if (document.getElementById('wctb-fe-material-options').options.length === 1) {
    materialOptionContainer.classList.add('hidden');
}


// ---------------------------------------------------------
// ----------------- Function Definitions ------------------
// ---------------------------------------------------------

function updateOrderDetails() {
    const lengthInFeet = parseInt(inputLengthInFeet.value) || 0;
    const lengthInInches = parseInt(inputLengthInInches.value) || 0;
    const widthInFeet = parseInt(inputWidthInFeet.value) || 0;
    const widthInInches = parseInt(inputWidthInInches.value) || 0;
    const totalLengthInFeet = parseFloat((lengthInFeet + (lengthInInches / 12)).toFixed(2));
    const totalWidthInFeet = parseFloat((widthInFeet + (widthInInches / 12)).toFixed(2));
    const squareFootage = (totalLengthInFeet * totalWidthInFeet).toFixed(2);
    const selectedMaterial = document.getElementById('wctb-fe-material-options').options[document.getElementById('wctb-fe-material-options').selectedIndex];
    const selectedEdge = document.getElementById('wctb-fe-edge-options').options[document.getElementById('wctb-fe-edge-options').selectedIndex];
    let dimensionsText = lengthInFeet + "ft " + (lengthInInches > 0 ? lengthInInches + "in " : "") + "x " + widthInFeet + "ft " + (widthInInches > 0 ? widthInInches + "in" : "");

    // Add the color swatch name to the summary span by using selected-color's data-color-name attribute.
    const selectedColor = document.querySelector('.wctb-fe-color-swatches input[type="radio"]:checked');
    summarySpan['wctb-color-summary'].textContent = selectedColor.getAttribute('data-color-name');


    summarySpan['wctb-material-summary'].textContent = selectedMaterial.textContent;
    summarySpan['wctb-edge-summary'].textContent = selectedEdge.textContent;
    summarySpan['wctb-sqft-summary'].textContent = squareFootage;
    summarySpan['wctb-dimensions-summary'].textContent = dimensionsText;

    // Update Square Footage, Dimensions, and Color Name Inputs
    squareFootageInput.value = squareFootage;
    dimensionsInput.value = dimensionsText;
    colorInput.value = selectedColor.getAttribute('data-color-name');

    // Assign the value of the selected material and edge to hidden inputs.
    materialNumber.value = selectedMaterial.value;
    // If the edge is custom, use the number 99. Otherwise, use the edge number.
    edgeNumber.value = selectedEdge.classList.contains('wctb-fe-customedge-option') ? 99 : selectedEdge.value;


    updateGrommetSpacing(totalLengthInFeet, totalWidthInFeet);
    changePriceOnPage(squareFootage);
}

function changePriceOnPage(sqft) {
    const squareFootage = sqft;

    const selectedMaterial = document.getElementById('wctb-fe-material-options').options[document.getElementById('wctb-fe-material-options').selectedIndex];
    const selectedEdge = document.getElementById('wctb-fe-edge-options').options[document.getElementById('wctb-fe-edge-options').selectedIndex];
    const mBasePrice = parseFloat(selectedMaterial.getAttribute('data-mbp'));
    const mHemmedPrice = parseFloat(selectedMaterial.getAttribute('data-mhp'));
    const eBasePrice = parseFloat(selectedEdge.getAttribute('data-ebp'));

    // for each integer from 1 to 4, if grommetspacing_inches$i is empty set $customside$iprice to 0, else set it to 0.18 if grommetspacing_inches$i is above 18, else .24 if above 12, .28 if above 6, and .31 by default.

    let edgeOptionPrice = 0;
    let customGrommetSidePricing = 0;

    // for (i = 1; i <= 4; i++) {
    //     let grommetSpacing = grommetSpacingInches[i].value;

    //     if (grommetSpacing > 24) {
    //         customGrommetSidePricing += 0.26;
    //     } else if (grommetSpacing > 18) {
    //         customGrommetSidePricing += 0.31;
    //     } else if (grommetSpacing > 12) {
    //         customGrommetSidePricing += 0.36;
    //     } else if (grommetSpacing > 6) {
    //         customGrommetSidePricing += 0.39;
    //     } else if (grommetSpacing > 0) {
    //         customGrommetSidePricing += 0.52;
    //     }
    // }

    let currentPrice = 0

    if (edgeNumber.value == 99) {
        edgeOptionPrice = eBasePrice + customGrommetSidePricing;
    } else {
        edgeOptionPrice = eBasePrice;
    }

    if (edgeOptionPrice > 0 || edgeNumber.value > 1) {
        currentPrice = (squareFootage * (mBasePrice + mHemmedPrice + edgeOptionPrice));
    } else {
        currentPrice = (squareFootage * mBasePrice);
    }

    if (currentPrice < 85) {
        currentPrice = 85;
    }

    if (isNaN(squareFootage)) {
        return;
    } else {
        dynamicPrice.textContent = "$" + parseFloat(currentPrice).toFixed(2);
    }
}

function clearColorSwatches() {
    while (colorsContainer.firstChild) {
        colorsContainer.removeChild(colorsContainer.firstChild);
    }
}

function addColorSwatches(materialOption) {
    // Split the string of colors into an array, removing the commas and spaces. Ex: "Red, Blue, Green" becomes ["Red", "Blue", "Green"]
    const materialColors = materialOption.dataset.materialColors;
    const colorArray = materialColors.split(', ');

    // Clear the color swatches container of any existing swatches.
    clearColorSwatches();

    // Loop through the array of colors, creating and positioning a swatch for each one.
    colorArray.forEach(color => {
        // Create the html elements used for the color swatches.
        const colorSwatch = document.createElement('div');
        const label = document.createElement('label');
        const input = document.createElement('input');
        const span = document.createElement('span');

        // Arrange the elements in the DOM in the correct position.
        colorsContainer.appendChild(colorSwatch);
        colorSwatch.appendChild(input);
        colorSwatch.appendChild(label);
        label.appendChild(span);

        // Add the attributes to the elements.
        colorSwatch.setAttribute('class', 'wctb-fe-color-swatch');
        input.setAttribute('type', 'radio');
        input.setAttribute('name', 'wctb-fe-color');
        input.setAttribute('data-color-name', color);
        input.setAttribute('id', `wctb-fe-color-${color.toLowerCase()}`);
        label.setAttribute('for', `wctb-fe-color-${color.toLowerCase()}`);

        // Set the visible color of the swatch. Makes white visible on a white background.
        if (color.toLowerCase() === 'white') {
            span.setAttribute('style', 'background: #EEE; border: 1px solid #999;');
        } else if (color.toLowerCase() === 'light grey') {
            span.setAttribute('style', 'background: #a1a6a2; border: 1px solid #777;');
        } else if (color.toLowerCase() === 'dark grey') {
            span.setAttribute('style', 'background: #61727c; border: 1px solid #555;');
        } else if (color.toLowerCase() === 'navy blue') {
            span.setAttribute('style', 'background: #21265e;');
        } else if (color.toLowerCase() === 'forest green') {
            span.setAttribute('style', 'background: #236140;');
        } else if (color.toLowerCase() === 'burgundy') {
            span.setAttribute('style', 'background: #732a25;');
        } else if (color.toLowerCase() === 'brown') {
            span.setAttribute('style', 'background: #5d463b;');
        } else if (color.toLowerCase() === 'plum') {
            span.setAttribute('style', 'background: #7e2d50;');
        } else if (color.toLowerCase() === 'yellow') {
            span.setAttribute('style', 'background: #f3f00b;');
        } else if (color.toLowerCase() === 'red') {
            span.setAttribute('style', 'background: #c12a31;');
        } else {
            span.setAttribute('style', `background: ${color.toLowerCase()};`);
        }

        // Set the first color to be checked by default.
        if (color === colorArray[0]) {
            input.setAttribute('checked', '');
        }
    });

    const colorRadios = document.querySelectorAll('.wctb-fe-color-swatches input[type="radio"]');
    colorRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            colorRadios.forEach(radio => {
                radio.removeAttribute('checked');
            });
            if (this.checked) {
                this.setAttribute('checked', '');
            }
            updateOrderDetails();
        });
    });
}

function updateGrommetSpacing(lengthInFeet, widthInFeet) {
    const grommetsAlongLength1 = parseInt(inputGrommetsLength1.value) || 0;
    const grommetsAlongLength2 = parseInt(inputGrommetsLength2.value) || 0;
    const grommetsAlongWidth1 = parseInt(inputGrommetsWidth1.value) || 0;
    const grommetsAlongWidth2 = parseInt(inputGrommetsWidth2.value) || 0;
    const lengthInInches = lengthInFeet * 12;
    const widthInInches = widthInFeet * 12;
    let grommetSpacingLength1 = 0;
    let grommetSpacingLength2 = 0;
    let grommetSpacingWidth1 = 0;
    let grommetSpacingWidth2 = 0;

    let grommetSpacingInInches = {
        1: lengthInInches / grommetsAlongLength1,
        2: lengthInInches / grommetsAlongLength2,
        3: widthInInches / grommetsAlongWidth1,
        4: widthInInches / grommetsAlongWidth2
    };

    if (grommetsAlongLength1 > 0) { grommetSpacingLength1 = convertToFraction(grommetSpacingInInches[1]); }
    if (grommetsAlongLength2 > 0) { grommetSpacingLength2 = convertToFraction(grommetSpacingInInches[2]); }
    if (grommetsAlongWidth1 > 0) { grommetSpacingWidth1 = convertToFraction(grommetSpacingInInches[3]); }
    if (grommetsAlongWidth2 > 0) { grommetSpacingWidth2 = convertToFraction(grommetSpacingInInches[4]); }

    // Update grommetSpacingSpan1 and grommetSpacingSpan2 to have the correct text in the following format Length 1: 0 grommets, 0" apart. | Width 1: 0 grommets, 0" apart.
    grommetSpacing1Text = `Length 1: ${grommetsAlongLength1} grommets, ${grommetSpacingLength1} apart. | Width 1: ${grommetsAlongWidth1} grommets, ${grommetSpacingWidth1} apart.`;
    grommetSpacing2Text = `Length 2: ${grommetsAlongLength2} grommets, ${grommetSpacingLength2} apart. | Width 2: ${grommetsAlongWidth2} grommets, ${grommetSpacingWidth2} apart.`;
    grommetSpacingSpan1.textContent = grommetSpacing1Text;
    grommetSpacingSpan2.textContent = grommetSpacing2Text;

    grommetSpacing1SavedText = `L1: ${grommetsAlongLength1} gr. ${grommetSpacingLength1} apart. | W1: ${grommetsAlongWidth1} gr. ${grommetSpacingWidth1} apart.`;
    grommetSpacing2SavedText = `L2: ${grommetsAlongLength2} gr. ${grommetSpacingLength2} apart. | W2: ${grommetsAlongWidth2} gr. ${grommetSpacingWidth2} apart.`;
    grommetSpacing1.value = grommetSpacing1SavedText;
    grommetSpacing2.value = grommetSpacing2SavedText;

    for (i = 1; i <= 4; i++) {
        if ( grommetSpacingInInches[i] >= 0 && grommetSpacingInInches[i] <= 100 ) {
            grommetSpacingInches[i].value = grommetSpacingInInches[i];
        } else {
            grommetSpacingInches[i].value = 0;
        }
    }
    

    function convertToFraction(spacing) {
        let wholeInches = Math.floor(spacing);
        let inchDecimal = spacing - wholeInches;
        let numerator = (inchDecimal * 16).toFixed(0);
        let denominator = 16;
        let feet = 0;

        // Reduce the fraction of an inch to the lowest possible denominator.
        for (let i = 16; i > 0; i--) {
            if (numerator % i === 0 && denominator % i === 0) {
                numerator /= i;
                denominator /= i;
            }
        }

        if (wholeInches > 11) {
            // create a new variable called feet and set it to the wholeInches divided by 12, modifying the wholeInches variable to be the remainder.
            feet = Math.floor(wholeInches / 12);
            wholeInches = wholeInches % 12;
        }

        if (numerator === 0) {
            return `${feet > 0 ? feet + "ft" : ''} ${wholeInches > 0 ? wholeInches + 'in' : ''}`;
        } else {
            return `${feet > 0 ? feet + "ft" : ''} ${wholeInches > 0 ? wholeInches : ''} ${numerator > 0 ? numerator + '/' + denominator + 'in' : ''}`;
        }
    }
}