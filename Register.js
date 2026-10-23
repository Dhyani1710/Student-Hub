window.addEventListener("load", function () {
    const params = new URLSearchParams(window.location.search);

    if (params.get("success") === "1") {
        alert("Registration Successful!");
        window.location.href = "StudentLogin.html";
    }
});

const form = document.querySelector("form");

form.addEventListener("submit", function (e) {

    let fname = document.getElementById("fname").value.trim();
    let sname = document.getElementById("sname").value.trim();
    let username = document.getElementById("username").value.trim();
    let email = document.getElementById("email").value.trim();
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;
    let agree = document.getElementById("agree").checked;

    if (fname === "") {
        alert("Please enter First Name");
        document.getElementById("fname").focus();
        e.preventDefault();
        return;
    }

    if (!/^[A-Z]/.test(fname)) {
        alert("First Name first letter must be CAPITAL");
        document.getElementById("fname").focus();
        e.preventDefault();
        return;
    }

    if (sname === "") {
        alert("Please enter Surname");
        document.getElementById("sname").focus();
        e.preventDefault();
        return;
    }

    if (!/^[A-Z]/.test(sname)) {
        alert("Surname first letter must be CAPITAL");
        document.getElementById("sname").focus();
        e.preventDefault();
        return;
    }

    if (username === "") {
        alert("Please enter Username");
        document.getElementById("username").focus();
        e.preventDefault();
        return;
    }

    if (!username.startsWith("25DCS")) {
        alert("Username must start with 25DCS");
        document.getElementById("username").focus();
        e.preventDefault();
        return;
    }

    if (email === "") {
        alert("Please enter Email Address");
        document.getElementById("email").focus();
        e.preventDefault();
        return;
    }

    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (!emailPattern.test(email)) {
        alert("Please enter a valid Email Address");
        document.getElementById("email").focus();
        e.preventDefault();
        return;
    }

    if (password === "") {
        alert("Please enter Password");
        document.getElementById("password").focus();
        e.preventDefault();
        return;
    }

    if (password.length < 8) {
        alert("Password must be at least 8 characters");
        document.getElementById("password").focus();
        e.preventDefault();
        return;
    }

    if (confirmPassword === "") {
        alert("Please confirm your Password");
        document.getElementById("confirmPassword").focus();
        e.preventDefault();
        return;
    }

    if (password !== confirmPassword) {
        alert("Password and Confirm Password must be same");
        document.getElementById("confirmPassword").focus();
        e.preventDefault();
        return;
    }

    if (!agree) {
        alert("Please agree to the Terms & Conditions");
        document.getElementById("agree").focus();
        e.preventDefault();
        return;
    }

});