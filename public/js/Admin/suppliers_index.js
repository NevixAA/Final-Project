const searchInputID = document.querySelector('#searchInputSupplierID'); // search result
const searchInputCategory = document.querySelector('#search_category_input'); //category search result
const searchInputEmail = document.querySelector('#searchInputEmail');
const searchInputContactName=document.querySelector('#searchInputContactName')// supllier- company name result

const searchInputCompName = document.querySelector('#search_company_name_input'); // 

// search result for supplier comapny name
searchInputCompName.addEventListener('change',function(event) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('suppliersTable').innerHTML = xhttp.responseText;
        }
    };
    xhttp.open("POST", "suppliers/search/companyname", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['supplier_id='+event.target.value]);
});


// search result for supplier id
searchInputID.addEventListener('input',function(event) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('suppliersTable').innerHTML = xhttp.responseText;
        }
    };
    xhttp.open("POST", "suppliers/search/id", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['supplier_id='+event.target.value]);
});


// Send AJAX call to get suppliers by contact name
searchInputContactName.addEventListener('input',function(event) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('suppliersTable').innerHTML = xhttp.responseText;
        }
    };
    xhttp.open("POST", "suppliers/search/contactname", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['contact_name='+event.target.value]);
});



// Send AJAX call to get suppliers by email address
searchInputEmail.addEventListener('input',function(event) {
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            document.getElementById('suppliersTable').innerHTML = xhttp.responseText;
        }
    };
    xhttp.open("POST", "suppliers/search/email", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(['email='+event.target.value]);
});


