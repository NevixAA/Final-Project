"use strict";

const flashMessages = document.querySelector('#flash_messages'); // flash messages container

// Clean flash messages every page load
window.addEventListener('load',function(event){
    setTimeout(function(){
        flashMessages.innerHTML = "";
    },3000);
});


// Add product message 
const PRODUCT_ADDED = [];
PRODUCT_ADDED[0] = 'success'; // css classes
PRODUCT_ADDED[1] = 'המוצר נוסף בהצלחה לעגלה';

// Update product message 
const PRODUCT_UPDATED = [];
PRODUCT_UPDATED[0] = 'info'; 
PRODUCT_UPDATED[1] = 'כמות המוצר בעגלה התעדכנה';

// Not logged in msg
const NOT_LOGGED = [];
NOT_LOGGED[0] = 'warning'; 
NOT_LOGGED[1] = 'עלייך להתחבר כדי לצפות בדף זה..';

// Out of stock msg
const OUT_OF_STOCK = [];
OUT_OF_STOCK[0] = 'warning'; 
OUT_OF_STOCK[1] = 'המוצר אינו קיים במלאי!';

/**
 * In /cart page
 * If product qntty value incremented beyond max qntty display msg
 */
const MAX_QNTY = [];
MAX_QNTY[0] = 'warning'; 
MAX_QNTY[1] = 'הכמות שבחרת גדולה מידי..';

/**
 * In /cart page
 * If product qntty value decremented below min qntty display msg
 */
const MIN_QNTY = [];
MIN_QNTY[0] = 'warning'; 
MIN_QNTY[1] = 'הכמות שבחרת קטנה מידי..';