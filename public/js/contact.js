"use strict";
const sendEmailBtn = document.querySelector('#sendEmailBtn');       // Send email button

const subjectInput = document.querySelector('#subjectInput');       // Email subject

const bodyInput = document.querySelector('#bodyInput');             // Email body

const sendEmailErrors = document.querySelector('#sendEmailErrors'); // Email form errors

const contactErrorsMsgs = [         // Error messages when submitting contact form
    "יש למלא נושא וגוף הודעה",
    "יש למלא נושא",
    "יש למלא את גוף הנושא"
]


/**
 * Send email inputs validations
 */
sendEmailBtn.addEventListener('click',function(event) {
    if (subjectInput.value === "" && bodyInput.value === "") 
        printErrors(contactErrorsMsgs[0]);
    else if (subjectInput.value === "") 
        printErrors(contactErrorsMsgs[1]);
    else if (bodyInput.value === "" ) 
        printErrors(contactErrorsMsgs[2]); 
    else {
        sendEmail({"subject" : subjectInput.value,"body" : bodyInput.value});
        clearEmailForm();
        window.location = "/contact/success"; // If mail sent successfuly goto success page
    }
});


/**
 * Insert errors to email errors ul
 * @param {error string} text 
 */
function printErrors(text)
{
    let listItem = document.createElement("LI");
    listItem.appendChild(document.createTextNode(text))
    if (sendEmailErrors.hasChildNodes()) 
        sendEmailErrors.removeChild(sendEmailErrors.childNodes[0]);
    sendEmailErrors.appendChild(listItem);
}


/**
 * Send email using AJAX CALL
 * @param {Object {"subject":"body"} email 
 */
function sendEmail(email)
{
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "/contact/sendEmail", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhttp.send(["email="+JSON.stringify(email)]);
}

/**
 * Clear contact us form
 */
function clearEmailForm()
{
    subjectInput.value = "";
    bodyInput.value    = "";
}
