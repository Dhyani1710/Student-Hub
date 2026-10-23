const usernameBox = document.getElementById("username");
const passwordBox = document.getElementById("password");

function login(event) {

    const username = usernameBox.value.trim();
    const password = passwordBox.value;

    if (username === "") {
        alert("Please enter your username!");
        usernameBox.focus();
        return false;
    }

    if (password === "") {
        alert("Please enter your password!");
        passwordBox.focus();
        return false;
    }

    return true;
}