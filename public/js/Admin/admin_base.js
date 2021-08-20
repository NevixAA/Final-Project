"use strict";
const spinnerGear = document.querySelector('#spinnerGear');

/* Spin the gear spinner above sidenav */
spinnerGear.addEventListener('click', function(event) {
    spinGear();
    toggleNav();
});


/* Set the width of the side navigation to 250px and the left margin of the page content to 250px */
function toggleNav() {
    if (document.getElementById("mySidenav").style.width == '0px' &&
        document.getElementById("main").style.marginRight == '0px'
    ) {
        openNav();
    } else {
        closeNav();
    }
}


/**
 * Open sidenav 
 */
function openNav() {
    if (screen.width < 1920) {
        document.getElementById("mySidenav").style.width = "100%";
    } else {
        document.getElementById("mySidenav").style.width = "250px";
        document.getElementById("main").style.marginRight = "250px";
    }
}


/**
 * Close side navigation
 *  Set the width of the side navigation to 0 and the left margin of the page content to 0
 */
function closeNav() {
    document.getElementById("mySidenav").style.width = "0";
    document.getElementById("main").style.marginRight = "0";
}


/**
 * Spin gear function
 * 15ms delay
 */
function spinGear() {
    let i = 0;
    let spin = setInterval(function () {
        spinnerGear.style.transform += "rotate(10deg)";
        i += 10;
        if (i == 360)
            clearTimeout(spin);
    }, 15);
}








