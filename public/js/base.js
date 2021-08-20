"use strict";
const dscntMsgStr = document.querySelector('#dscntMsgStr');
const dscntCloseBtn = document.querySelector('#dscntCloseBtn');
const dscntMsgContainer = document.querySelector('#dscntMsgContainer');

const cartProducts = document.querySelector('#cartProducts'); // Shopping cart products container
const shoppingCartIcon = document.querySelector('#shoppingCartIcon');
const shoppingCartContainer = document.querySelector('#shoppingCartContainer');
const shoppingCartCloseBtn = document.querySelector('#shoppingCartCloseBtn');

const userInfoIcon = document.querySelector('#userInfoIcon');
const userInfoContainer = document.querySelector('#userInfoContainer');
const userInfoCloseBtn = document.querySelector('#userInfoCloseBtn');

// Discount msg close btn
dscntCloseBtn.onclick = (event) => {
    dscntMsgContainer.classList.toggle('d-none');
}

/**
 * Discounts
 */
getActiveDiscounts();

// Get all store discount to set discount message
function getActiveDiscounts() {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
     
            if (xhttp.responseText != '') {
                let result = JSON.parse(xhttp.responseText);
                console.log(result);
                if (result) {
                    dscntMsgContainer.style.display = "block";

                    if (result['all_store'] == '1' && result['discount_id'] == '2') { // All store discount in percent
                        dscntMsgStr.innerHTML = result['percent'] + '%' + ' ' + result['discount_name'];

                    }else if (result['all_store'] == '0' && result['discount_id'] == '2') {
                        dscntMsgStr.innerHTML = result['percent'] + '%' + ' ' + result['discount_name'] + ' ב' + result['display_name'];

                    }else {
                        dscntMsgStr.innerHTML = result['discount_name'];
                    }
                }
            }else {
                dscntMsgContainer.style.display = "none";
            }
        }
    }
    xhttp.open("POST", "../../admin/discounts/active_dscnts", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
}




// Add flash message,default delay is 1500ms
function addFlashMessage(msg,delay = 1500)
{
    flashMessages.innerHTML = '<div class="alert alert-'+ msg[0] +' m-0">'+msg[1]+'</div>';
    let test = setTimeout(function () {
        flashMessages.innerHTML ="";
    }, delay);
}


// Click on shopping cart icon open/close cart
shoppingCartIcon.addEventListener('click',function(event){
    getCartProducts()
    openCloseShoppingCart();
});

// Click on shopping cart close icon close cart
shoppingCartCloseBtn.addEventListener('click',function(event){
    openCloseShoppingCart();
});


// Open close shopping cart 
function openCloseShoppingCart()
{

    if (shoppingCartContainer.style.display == 'block') {
        shoppingCartContainer.style.display = 'none';
    }else {
        shoppingCartContainer.style.display = 'block';
    }
}


/**
 * Send AJAX call to get products from shopping cart
 */
function getCartProducts()
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            loadProductsInCart(JSON.parse(xhttp.responseText));
        }
    }
    xhttp.open("POST", "../../cart/products", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['products=true']);
}

/**
 * Append products to shopping cart 
 * @param {array of product objects} obj 
 */
function loadProductsInCart(obj)
{
    var products = []; 

    for(var i in obj)           // Build products array
        products.push(obj[i]); 

    cartProducts.innerHTML =""; // Print products to shopping cart box
    for(var i = 1; i<products.length; i++)  {
        cartProducts.innerHTML += `<div class="d-flex mb-2 mt-4">
                                        <div>
                                            <a href="/categories/${products[i]['category_name']+'/'+products[i]['product_id']}"><img src="/images/${products[i]['product_img']}" alt="productInCart" style="width: 4.5em; height: 4.5em;"></a>
                                        </div>
                                        <div class="col d-flex flex-column justify-content-center">
                                            <div style="line-height: 16px;">
                                                <div>
                                                ${products[i]['product_name']}
                                                </div>
                                                <div>
                                                ${products[i]['display_name']}
                                                </div>
                                                <div>
                                                ${products[i]['colorDisplayName']}
                                                </div>
                                                <div>
                                                ${products[i]['sizeDisplayName']}
                                                </div>
                                            </div>
                                            <div>
                                                כמות: <span>${products[i]['quantity']}</span>
                                            </div>
                                        </div>
                                    </div>`;
    }
    var cartSummery = document.querySelector('#cart-summery');
    cartSummery.innerHTML = `<div>סה"כ</div>
    <div><span id="shoppingCartTotal">0</span>ש"ח</div>`;
    document.querySelector('#shoppingCartTotal').innerHTML = products[0]['totalItemsPrice'].toFixed(2);
}

// Click on user info icon open/close user details links
userInfoIcon.addEventListener('click',function(event){
    if (userInfoContainer.style.display == 'block') {
        userInfoContainer.style.display = 'none';
    }else {
        userInfoContainer.style.display = 'block';
    }
    
});

// Click on user info close icon closes user info container
userInfoCloseBtn.addEventListener('click',function(event){
    userInfoContainer.style.display = 'none';
});





