"use strict";
// Inc/Dec product quantity parent
const productIncDecParent = document.querySelector('#productIncDecParent');

// Add to cart button
const addToCartBtn = document.querySelector('#add_to_cart_btn'); 


/**
 * Take the id of the current product
 */
function setProductID()
{
    // Extract product id from url e.g http://localhost/categories/shirts/{id}
    let splittedURL = window.location.href.split("/");   // Split URL by delimiter '/'
    product['id'] = splittedURL[splittedURL.length - 1]; // ProductID in last array cell
}
setProductID();


/**
 * Increment/decrement product quantity click event
 * if id incQntty clicked - increment value
 * else decQntty clicked - decrement value
 */
productIncDecParent.addEventListener('click',function(event){
    incDecEvent(event.target.id,decProductValue,incProductValue);
});


/**
 * Increment/decrement product quantity click event
 * if id incQntty clicked - increment value
 * else decQntty clicked - decrement value
 */
function incDecEvent(productID,decCallback,incCallback)
{
    let res = productID.split("-");
    if (res[0] === "incQntty") {
        incCallback(res[1],MAX_QUANTITY);       // If newProductQnttyValue < maxProductQntty , increment value                                          
    }                                       
    else if (res[0] === "decQntty") {
        decCallback(res[1],MIN_QUANTITY);
    }
}


/**
 * Decrement by quantity value by 1
 * @param {products ID} productID 
 * @param {Minimum decrement quantity} MIN_QUANTITY 
 */
function decProductValue(productID,MIN_QUANTITY)
{
    let qnttyValue = document.querySelector('#qnttyValue-'+productID); // e.g concatinate -3 & take the qntty value
    let newQntty = parseInt(qnttyValue.innerHTML) - 1;
    if (newQntty >= MIN_QUANTITY) {
        qnttyValue.innerHTML = newQntty;
        product['qnty'] = newQntty;
    }else {
        addFlashMessage(MIN_QNTY);
    }
}


/**
 * Increment by quantity value by 1
 * @param {products ID} productID 
 * @param {Maximum quantity to increment} maxQntty 
 */
function incProductValue(productID,maxQntty)
{
    let qnttyValue = document.querySelector('#qnttyValue-'+productID); 
    let qntty = parseInt(qnttyValue.innerHTML);
    if (qntty < maxQntty) {
        qntty+= 1;
        product['qnty'] += 1;
    }else {
        addFlashMessage(MAX_QNTY);
    }
    qnttyValue.innerHTML = qntty;
}


/**
 * Add product to cart
 * PRODUCT_ADDED/UPDATED[0] containes css class, [1] contains msg defined in base.js
 * e.g PRODUCT_ADDED[0] = 'success',PRODUCT_ADDED[1] = 'ADDED SUCCESSFULLY!';
 */
addToCartBtn.addEventListener('click', function(event) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {   
            let action =  JSON.parse(xhttp.responseText);
            if (action === 'added') {
                addFlashMessage(PRODUCT_ADDED);
                openShowMiniCart();
            }else if (action == 'updated') {
                addFlashMessage(PRODUCT_UPDATED);
                openShowMiniCart();
            }else if (action == 'out_of_stock') {
                addFlashMessage(OUT_OF_STOCK);
            }
        }
    }
    xhttp.open("POST", "../../cart/add", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['addToCart=' + JSON.stringify(product) ]);
    isProductInStock();
});

/**
 * Show mini shopping cart and 
 * Load all products in it
 */
function openShowMiniCart()
{
    getCartProducts();
    // Open mini cart
    shoppingCartContainer.style.display = 'block';
}





const productColors = document.querySelector('#productColors');     // Parent of product color selection

const productSizes = document.querySelector('#productSizes');       // Parent of product sizes selection

const divColorsIDs = {  // Colors div id's
    "productBlue" : 1,
    "productRed"  : 2,
    "productBlack": 3,
    "productWhite": 4
};


const divSizesIDs = {   // Sizes div id's
    'size-small'     : 1,
    'size-medium'    : 2,
    'size-large'     : 3,
    'size-extraLarge': 4,
};





/**
 * Apply selected effect for product color types
 * STYLE CLASS - productColor-selected
 */
productColors.addEventListener('click',function(event) {
    for(const color in divColorsIDs) {
        if (event.target.id == color) {
            let colorPickedId = divColorsIDs[color];
            checkProductImage(colorPickedId);
            let pickedColor = document.querySelector('#'+event.target.id);
            if (pickedColor) {
                pickedColor.classList.add('productColor-selected');
            }
            product['color'] = divColorsIDs[color];
        }else {
            
            let unPickColor = document.querySelector('#'+color);
            if (unPickColor) {
                unPickColor.classList.remove('productColor-selected');
            }
        }
    }
});



/**
 * Check if product in stock
 * If in stock - show inStock div
 * Otherwise - hide inStock and show outOfStock div
 */
productSizes.addEventListener('click',function(event){
    for (const size in divSizesIDs) {
        if (event.target.id == size) {
            document.querySelector('#'+event.target.id).classList.remove('btn-outline-dark');
            document.querySelector('#'+event.target.id).classList.add('btn-dark');
            product['size'] = divSizesIDs[size];
            isProductInStock();
        }else {
            document.querySelector('#'+size).classList.remove('btn-dark');
            document.querySelector('#'+size).classList.add('btn-outline-dark');
        }
    }
})


/**
 * Show in stock / out stock div 
 */
function isProductInStock()
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {   
            let result = JSON.parse(xhttp.responseText);
            if (result == "true") {
                document.querySelector('#inStock').style = "display: flex;";
                document.querySelector('#outOfStock').style = "display: none;";
            }else {
                document.querySelector('#inStock').style = "display: none;";
                document.querySelector('#outOfStock').style = "display: flex;";
            }         
        }
    }

    xhttp.open("POST", "../../cart/isProductInStockAJAX", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['product=' + JSON.stringify(product) ]);
}


const viewProductImg = document.querySelector('#viewProductImg');
const productIdField = document.querySelector('#productIdField');

function checkProductImage(colorId)
{
    let prodId = productIdField.value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {   
            let action =  xhttp.responseText;
            viewProductImg.src = '/images/'+action;
        }
    }
    xhttp.open("POST", "../../categories/product-imgs", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['colorId=' + JSON.stringify(colorId) +'&prodId=' + JSON.stringify(prodId)]);

}

