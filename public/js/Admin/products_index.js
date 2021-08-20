"use strict";
const searchInputID = document.querySelector('#searchInputID'); 
const searchInputCategory = document.querySelector('#search_category_input'); 
const searchInputName = document.querySelector('#search_name_input'); 


/**
 * Search by ID 
 */
searchInputID.addEventListener('input', function(event) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('productsTableBody').innerHTML = xhttp.responseText;
        }
    };
    xhttp.open("POST", "products/search/id", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['productID='+event.target.value]);
});

/**
 * Search by Category select
 */
searchInputCategory.addEventListener('change',function(event) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('productsTableBody').innerHTML = xhttp.responseText;
        }
    };
    xhttp.open("POST", "products/search/category", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['categoryID='+event.target.value]);
});

/**
 * Search by product name
 */
searchInputName.addEventListener('input', function(event) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {

            if (xhttp.responseText != "") {

                let responseObj = JSON.parse(xhttp.responseText);
                let products = responseObj.products;
                let colors = responseObj.colors;
                let sizesQnty = responseObj.sizesQnty;

                let searchedProducts = [];
                for (var i = 0; i < products.length; i++) {
                    let prodName = encodeURIComponent(products[i].product_name);
                    if (prodName.indexOf(encodeURIComponent(event.target.value)) > -1) {

                        searchedProducts.push(products[i]);
                    }
                }

                buildProductsTable(searchedProducts);
            }
            
        }
    };
    xhttp.open("POST", "products/search/name", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send();
});


function buildProductsTable(products) {
    const productsTable = document.getElementById('productsTableBody');
    productsTable.innerHTML = "";
    for (var i = 0; i < products.length; i++) {
        productsTable.innerHTML += `
                <tr>
                    <th scope='row'>${products[i].product_id}</th>
                    <td>${products[i].display_name}</td>
                    <td>${products[i].product_name}</td>
                    <td>${products[i].product_price}</td>
                    <td>${products[i].product_quantity}</td>
                    <td>${products[i].uploaded_at}</td>
                    <!--Product row buttons-->
                    <td>
                        <div class='d-flex justify-content-end'>
                            <form action='/admin/products/delete/${products[i].product_id}' method='POST'>
                                <button class='btn btn-primary mr-2' name='deleteProduct'>הסרה</button>
                            </form>
                            <a href='/admin/products/edit/${products[i].product_id}' class='btn btn-primary mr-2'>עדכון</a>
                            <div class='d-flex align-items-end'>
                                <a href='#product_info${products[i].product_id}' class='btn btn-default' data-toggle='collapse'>
                                    <svg class='bi bi-chevron-compact-down' width='1em' height='1em' viewBox='0 0 16 16'
                                        fill='currentColor' xmlns='http://www.w3.org/2000/svg'>
                                        <path fill-rule='evenodd'
                                            d='M1.553 6.776a.5.5 0 01.67-.223L8 9.44l5.776-2.888a.5.5 0 11.448.894l-6 3a.5.5 0 01-.448 0l-6-3a.5.5 0 01-.223-.67z'
                                            clip-rule='evenodd' />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr id='product_info${products[i].product_id}' class='main-container collapse' >
                    <td colspan='7'>
                        <div class='d-flex'>
                            <div class='w-25'>
                                <img src='/images/${products[i].product_img}' alt='${products[i].product_img}' class='img-thumbnail'>
                            </div>
                            <div class='pl-4'>
                                <span class='h6'>תיאור המוצר</span>
                                <div>
                                ${products[i].description}
                                </div>
                            </div>
                        </div>
                    </td>
                    
                </tr>`;
    }

}





