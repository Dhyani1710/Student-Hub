document.addEventListener("DOMContentLoaded", function () {

    let form = document.querySelector("form");

    let name = document.querySelector("input[type='text']");
    let email = document.querySelector("input[type='email']");
    let mobile = document.querySelector("input[type='tel']");
    let fatherMobile = document.querySelectorAll("input[type='tel']")[1];
    let motherMobile = document.querySelectorAll("input[type='tel']")[2];
    let emergencyMobile = document.querySelectorAll("input[type='tel']")[3];
    let dob = document.querySelector("input[type='date']");
    let pin = document.querySelector("input[value='390001']");

    name.addEventListener("change", function () {

        let value = name.value.trim();

        if (value === "") {
            alert("Please enter your name");
            name.focus();
        }
        else if (value.charAt(0) !== value.charAt(0).toUpperCase()) {
            alert("First letter of Name must be CAPITAL");
            name.focus();
        }
    });

    email.addEventListener("change", function () {

        let value = email.value.trim();

        if (value === "") {
            alert("Please enter your Email");
            email.focus();
        }
        else if (!value.includes("@")) {
            alert("Please enter a valid Email");
            email.focus();
        }
    });

    mobile.addEventListener("change", function () {

        let value = mobile.value.trim();

        if (value.length != 10) {
            alert("Mobile Number must be 10 digits");
            mobile.focus();
        }
    });

    fatherMobile.addEventListener("change", function () {

        let value = fatherMobile.value.trim();

        if (value.length != 10) {
            alert("Father's Mobile Number must be 10 digits");
            fatherMobile.focus();
        }
    });

    motherMobile.addEventListener("change", function () {

        let value = motherMobile.value.trim();

        if (value.length != 10) {
            alert("Mother's Mobile Number must be 10 digits");
            motherMobile.focus();
        }
    });

    emergencyMobile.addEventListener("change", function () {

        let value = emergencyMobile.value.trim();

        if (value.length != 10) {
            alert("Emergency Contact must be 10 digits");
            emergencyMobile.focus();
        }
    });

    pin.addEventListener("change", function () {

        let value = pin.value.trim();

        if (value.length != 6) {
            alert("PIN Code must be 6 digits");
            pin.focus();
        }
    });

    form.addEventListener("submit", function (event) {

        event.preventDefault();

        if (name.value.trim() === "") {
            alert("Please enter your Name");
            name.focus();
            return;
        }

        if (email.value.trim() === "") {
            alert("Please enter your Email");
            email.focus();
            return;
        }

        if (mobile.value.trim().length != 10) {
            alert("Mobile Number must be 10 digits");
            mobile.focus();
            return;
        }

        if (fatherMobile.value.trim().length != 10) {
            alert("Father's Mobile Number must be 10 digits");
            fatherMobile.focus();
            return;
        }

        if (motherMobile.value.trim().length != 10) {
            alert("Mother's Mobile Number must be 10 digits");
            motherMobile.focus();
            return;
        }

        if (emergencyMobile.value.trim().length != 10) {
            alert("Emergency Contact must be 10 digits");
            emergencyMobile.focus();
            return;
        }

        if (pin.value.trim().length != 6) {
            alert("PIN Code must be 6 digits");
            pin.focus();
            return;
        }

        alert("Profile updated successfully!");

    });

});