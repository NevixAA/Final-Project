"use strict";

const productInfoCart = document.querySelector('#productInfoCart'); // Products in cart container

const updateCartBtn = document.querySelector('#updateCartBtn'); // Update cart button

let updatedProducts = {}; // This object contains product objects 


/**
 * Increment/decrement product quantity click event
 * if id incQntty clicked - increment value
 * else decQntty clicked - decrement value
 */
function incDecEvent(action,product,decCallback,incCallback)
{
    if (action === "increment") {
        checkProductMaxQntty(product,incCallback);       // Check maximum product quantity using AJAX CALL
                                                        // If newProductQnttyValue < maxProductQntty , increment value
    }                                       
    else if (action === "decrement") {
        decCallback(product,MIN_QUANTITY);
    }

}


/**
 * AJAX call - Check product max quantity
 * After Response - if incrementedValue < maxQntty than increment
 * @param {*Product object} product 
 */
function checkProductMaxQntty(product,callback)
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {  
            let maxQntty = parseInt(JSON.parse(xhttp.responseText));
            callback(product,maxQntty);
        }
    }
    xhttp.open("POST", "../../cart/findProductQntty", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['product=' + JSON.stringify(product)]);
}


/**
 * Decrement single product quantity value
 * @param {Product object} product 
 * @param {Max product quantity to increment} maxQntty 
 */
function incProductValueInCart(product,maxQntty)
{
    let qnttyValue = document.querySelector('#qnttyValue-'+product.id+'-'+product.color+'-'+product.size); 
    let newQntty = parseInt(qnttyValue.innerHTML) + 1;
    if (newQntty <= maxQntty) {
        qnttyValue.innerHTML = newQntty;
        product['qnty'] = newQntty;
        updatedProducts[product.id+'-'+product.color+'-'+product.size] = product;
    }else {
        addFlashMessage(MAX_QNTY);
    }
}


/**
 * Decrement single product quantity value
 * @param {Product object} product 
 * @param {Min product quantity to decrement} minQntty 
 */
function decProductValueInCart(product,minQntty)
{
    let qnttyValue = document.querySelector('#qnttyValue-'+product.id+'-'+product.color+'-'+product.size); 
    let newQntty = parseInt(qnttyValue.innerHTML) - 1;
    if (newQntty >= minQntty) {
        qnttyValue.innerHTML = newQntty;
        product['qnty'] = newQntty;
        updatedProducts[product.id+'-'+product.color+'-'+product.size] = product;
    }else {
        addFlashMessage(MIN_QNTY);
    }
}


/**
 * Control increment decrement products quantity value
 */
productInfoCart.addEventListener('click',function(event){
    let result = event.target.id.split('-');
    if (result[0] == "incQntty" || result[0] == "decQntty") {
        product['id']    = result[1];
        product['color'] = result[2];
        product['size']  = result[3];
        let productCopy = JSON.parse(JSON.stringify(product));
        incDecEvent(result[0] == 'incQntty' ? 'increment' : 'decrement', productCopy ,decProductValueInCart,incProductValueInCart);
    }
});


/**
 * Update products quantities values
 */
updateCartBtn.addEventListener('click',function(event){
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "../../cart/update", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['updatedProducts='+JSON.stringify(updatedProducts)]);
    window.location.reload(); // Reload page so PHP will render updated products from SESSION
});










