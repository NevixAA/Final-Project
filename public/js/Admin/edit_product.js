"use strict";

const prodBlackOptions = document.querySelector('#prodBlackOptions');
const prodBlueOptions  = document.querySelector('#prodBlueOptions');
const prodWhiteOptions = document.querySelector('#prodWhiteOptions');
const prodRedOptions   = document.querySelector('#prodRedOptions');

const prodMainUpload = document.querySelector('#prodMainUpload');
const prodBlackUpload = document.querySelector('#prodBlackUpload');
const prodBlueUpload  = document.querySelector('#prodBlueUpload');
const prodWhiteUpload = document.querySelector('#prodWhiteUpload');
const prodRedUpload   = document.querySelector('#prodRedUpload');

const pickColorsBtn = document.querySelector('#pickColorsBtn');

const colorsSelect = document.querySelector('#colorsSelect');

const changeImgMain = document.querySelector('#changeImgMain');
const changeImgBlue = document.querySelector('#changeImgBlue');
const changeImgRed = document.querySelector('#changeImgRed');
const changeImgBlack = document.querySelector('#changeImgBlack');
const changeImgWhite = document.querySelector('#changeImgWhite');

let changeImgEffects = (uploadElem,btn,show) => {
    if (show) {
        uploadElem.parentElement.parentElement.classList.add('d-none');
        uploadElem.type = 'hidden';
        btn.innerHTML = 'שנה תמונה..';
    }else {
        uploadElem.parentElement.parentElement.classList.remove('d-none');
        uploadElem.type = 'file';
        btn.innerHTML = 'בטל תמונה';
    }
};

let chooseEffect = (cb,effectType,uploadElem,btn) => {
    if (effectType == 'בטל תמונה') {
        return cb(uploadElem,btn,true);
    }
    return cb(uploadElem,btn,false);
};

if (changeImgMain &&
    changeImgBlue && 
    changeImgRed && 
    changeImgBlack 
    && changeImgWhite) {
    changeImgMain.addEventListener('click',(event)=> {
        chooseEffect(changeImgEffects,event.target.innerHTML,prodMainUpload,changeImgMain);
    });
    
    changeImgBlue.addEventListener('click',(event) => {
        chooseEffect(changeImgEffects,event.target.innerHTML,prodBlueUpload,changeImgBlue);
    });
    
    changeImgRed.addEventListener('click',(event) => {
        chooseEffect(changeImgEffects,event.target.innerHTML,prodRedUpload,changeImgRed);
    });
    
    changeImgBlack.addEventListener('click',(event) => {
        chooseEffect(changeImgEffects,event.target.innerHTML,prodBlackUpload,changeImgBlack);
    });

    changeImgWhite.addEventListener('click',(event) => {
        chooseEffect(changeImgEffects,event.target.innerHTML,prodWhiteUpload,changeImgWhite);
    });
}










if (pickColorsBtn != null) {
    pickColorsBtn.onclick = (event) => {
        let opt = colorsSelect.options;
        
        if (opt[0].selected) {
            prodBlackOptions.classList.remove('d-none');
            prodBlackUpload.type = 'file';
        }else {
            prodBlackOptions.classList.add('d-none');
            prodBlackUpload.type = 'hidden';
        }
    
        if (opt[1].selected) {
            prodBlueOptions.classList.remove('d-none');
            prodBlueUpload.type = 'file';
    
        }else {
            prodBlueOptions.classList.add('d-none');
            prodBlueUpload.type = 'hidden';
    
        }
    
        if (opt[2].selected) {
            prodWhiteUpload.type = 'file';
            prodWhiteOptions.classList.remove('d-none');
        }else {
            prodWhiteOptions.classList.add('d-none');
            prodWhiteUpload.type = 'hidden';
        }
    
        if (opt[3].selected) {
            prodRedUpload.type = 'file';
            prodRedOptions.classList.remove('d-none');
        }else {
            prodRedOptions.classList.add('d-none');
            prodRedUpload.type = 'hidden';
        }
       
    };
}

