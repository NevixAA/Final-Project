"use strict";

const newUsers      = document.querySelector('#newUsersCountValue');
const newOrders     = document.querySelector('#newOrdersCountValue');
const soldProducts = document.querySelector('#soldProductsCountValue');

// Get data about new users statistics
function getNewUsersCount()
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let stats = JSON.parse(xhttp.responseText);
            let subTitle = newUsers.parentNode.parentNode.children[1];
            subTitle.innerHTML = " ב- " + stats.monthNum + " החודשים האחרונים";
            newUsers.innerHTML = stats.newUsers;
        }
    };
    xhttp.open("POST", "../../statistics/new_users", true);
    xhttp.send();
}

getNewUsersCount();



// Get data about new orders statistics
function getNewOrdersCount()
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let stats = JSON.parse(xhttp.responseText);
            let subTitle = newOrders.parentNode.parentNode.children[1];
            subTitle.innerHTML = " ב- " + stats.monthNum + " החודשים האחרונים";
            newOrders.innerHTML = stats.newOrders;
        }
    };
    xhttp.open("POST", "../../statistics/new_orders", true);
    xhttp.send();
}

getNewOrdersCount();


// Get data about sold products count
function getSoldProductsCount()
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
        if (this.readyState == 4 && this.status == 200) {
            let stats = JSON.parse(xhttp.responseText);
            let subTitle = soldProducts.parentNode.parentNode.children[1];
            subTitle.innerHTML = " ב- " + stats.monthNum + " החודשים האחרונים";
            soldProducts.innerHTML = stats.soldProducts;
        }
    };
    xhttp.open("POST", "../../statistics/sold_products", true);
    xhttp.send();
}

getSoldProductsCount();