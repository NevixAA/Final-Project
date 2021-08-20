"use strict";

const loginsParent = document.querySelector('#loginsParent');

const avatarLogin = document.querySelector('#avatarLogin');
const btnLogin = document.querySelector('#btnLogin');
const titleLogin = document.querySelector('#titleLogin');
const paragLogin = document.querySelector('#paragLogin');

const GREY = '#343A40';
const BLUE = '#4169E1';
const PURPLE = '#9932CC';



loginsParent.addEventListener('click',function(event) {
    if (event.target.id == 'userLogin') {
        setColorToLoginForm('input-group-text',GREY)
        setTestToLoginForm("התחברות לאתר","ברוכים הבאים לאתר FEMIN") 
    }else if (event.target.id == 'adminLogin') {
        setColorToLoginForm('input-group-text',BLUE)
        setTestToLoginForm("שלום מנהל יקר","הזן את פרטייך על מנת להתחבר");
    }else if (event.target.id == 'orderSupervisorLogin') {
        setColorToLoginForm('input-group-text',PURPLE)
        setTestToLoginForm("שלום אחראי הזמנות","הזן את פרטייך על מנת להתחבר"); 
    }
});

/**
 * Change elements by the className to color passed by parameter
 * @param {This class elements color will be changed} className 
 * @param {Color to change CSS} color 
 */
function setColorToLoginForm(className,color)
{
    setColorToInputsIcons(className,color);
    avatarLogin.style.color = color;
    btnLogin.style.backgroundColor = color;
}

/**
 * Change login page title and paragraph
 * @param {New title} title 
 * @param {New paragraph} parag 
 */
function setTestToLoginForm(title,parag) 
{
    titleLogin.innerHTML = title;
    paragLogin.innerHTML = parag;
}

/**
 * Set elements to a specific color
 * @param {Get all the elements by the className} className 
 * @param {Change elements to this color} color 
 */
function setColorToInputsIcons(className,color) {
    let elements = document.getElementsByClassName(className);
    for (let i = 0; i < elements.length; i++) {
        elements[i].style.backgroundColor = color;
    }
}