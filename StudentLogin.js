const usernameBox = document.getElementById("username");
const passwordBox = document.getElementById("password");


passwordBox.addEventListener("mousedown", function (event) {

    const username = usernameBox.value;
    const usernameRegex = /^25DCS\d{3}$/;

    if (!usernameRegex.test(username)) {

        event.preventDefault();

        alert("Username must start with 25DCS followed by 3 digits! (e.g., 25DCS001)");

        usernameBox.focus();
    }

});



function login(event) {

    event.preventDefault();

    const username = usernameBox.value;
    const password = passwordBox.value;

    const usernameRegex = /^25DCS\d{3}$/;
    const passwordRegex = /^(?=.*[A-Z])(?=.*[a-z]).{8}$/;


    if (!usernameRegex.test(username)) {

        alert("Username must start with 25DCS followed by 3 digits! (e.g., 25DCS001)");

        usernameBox.focus();

        return false;
    }


    if (!passwordRegex.test(password)) {

        alert("Password must be exactly 8 characters and contain uppercase and lowercase letters!");

        passwordBox.focus();

        return false;
    }


    alert("Login Successful!");

    window.location.href = "Dashboard.html";

    return true;
}