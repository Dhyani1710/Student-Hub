document.getElementById("fname").addEventListener("change", function () {

    let v = this.value.trim();

    if (v && v.charAt(0) !== v.charAt(0).toUpperCase()) {

        alert("First Name first letter must be CAPITAL");

        this.value = "";
        this.focus();
    }
});


document.getElementById("sname").addEventListener("change", function () {

    let v = this.value.trim();

    if (v && v.charAt(0) !== v.charAt(0).toUpperCase()) {

        alert("Surname first letter must be CAPITAL");

        this.value = "";
        this.focus();
    }
});


document.getElementById("username").addEventListener("change", function () {

    let v = this.value.trim();

    if (v && !v.startsWith("25DCS")) {

        alert("Username must start with 25DCS");

        this.value = "";
        this.focus();
    }
});


document.getElementById("password").addEventListener("change", function () {

    let v = this.value;

    if (v.length < 8) {

        alert("Password must be at least 8 characters");

        this.value = "";
        this.focus();
    }
});


document.getElementById("confirmPassword").addEventListener("change", function () {

    let password = document.getElementById("password").value;
    let confirmPassword = this.value;

    if (password !== confirmPassword) {

        alert("Password and Confirm Password must be same");

        this.value = "";
        this.focus();
    }
});


document.getElementById("createAccount").addEventListener("click", function () {

    let fname = document.getElementById("fname").value.trim();
    let sname = document.getElementById("sname").value.trim();
    let username = document.getElementById("username").value.trim();
    let password = document.getElementById("password").value;
    let confirmPassword = document.getElementById("confirmPassword").value;
    let agree = document.getElementById("agree").checked;


    if (fname === "") {

        alert("Please enter First Name");

        document.getElementById("fname").focus();

        return;
    }


    if (sname === "") {

        alert("Please enter Surname");

        document.getElementById("sname").focus();

        return;
    }

    if (username === "") {

        alert("Please enter Username");

        document.getElementById("username").focus();

        return;
    }

    if (!username.startsWith("25DCS")) {

        alert("Username must start with 25DCS");

        document.getElementById("username").focus();

        return;
    }

    if (password === "") {

        alert("Please enter Password");

        document.getElementById("password").focus();

        return;
    }

    if (password.length < 8) {

        alert("Password must be at least 8 characters");

        document.getElementById("password").focus();

        return;
    }

    if (confirmPassword === "") {

        alert("Please confirm your Password");

        document.getElementById("confirmPassword").focus();

        return;
    }

    if (password !== confirmPassword) {

        alert("Password and Confirm Password must be same");

        document.getElementById("confirmPassword").focus();

        return;
    }

    if (!agree) {

        alert("Please agree to the Terms & Conditions");

        document.getElementById("agree").focus();

        return;
    }

    alert("Account created successfully");

});