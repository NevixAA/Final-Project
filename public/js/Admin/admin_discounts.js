"use strict";

const dscntSelect = document.querySelector('#dscntSelect')

const percentInp = document.querySelector('#percentInp');

const fromStartInp = document.querySelector('#fromStartInp');


let elem1,elem2;

/**
 * Show/Hide from inputs depending on type of discount
 * @param {*} event 
 */
dscntSelect.onchange = (event) => {
  let target = event.target;
  if (elem1) {
    elem1.classList.add('d-none');
  }
  if (elem2) {
    elem2.classList.add('d-none');
  }

  if (target.value == '3') {
    elem1 = percentInp;
    elem2 = fromStartInp;
    percentInp.classList.remove('d-none');
    fromStartInp.classList.remove('d-none');
  }

  if (target.value == '2') {
    elem2 = fromStartInp;
    percentInp.classList.remove('d-none');
  }
}

