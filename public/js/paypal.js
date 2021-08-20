

let total_price;
getTotalPrice();

// Get cart total price
function getTotalPrice()
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {  
            total_price = JSON.parse(xhttp.responseText);
            total_price = Number((total_price).toFixed(1))
        }
    }
    xhttp.open("POST", "../../cart/total_price", false);
    xhttp.send();
}

// Render the PayPal button into #paypal-button-container
paypal.Buttons({
    // Set up the transaction
    createOrder: function(data, actions) {
        return actions.order.create({
            purchase_units: [{
                amount: {
                    value: total_price.toString()
                }
            }]
        });
    },
    // Finalize the transaction
    onApprove: function(data, actions) {
        return actions.order.capture().then(function(details) {
            // Show a success message to the buyer
            alert('העסקה הושלמה בהצלחה!');
            setOrder();

        });
    }
}).render('#paypal-button-container');


// After purchase completes redirect to success page
function setOrder()
{
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {  
            if (JSON.parse(xhttp.responseText) == "true") {
                window.location.href = "/cart/success";
            }
        }
    }
    xhttp.open("POST", "../../cart/set", true);
    xhttp.send();
}
