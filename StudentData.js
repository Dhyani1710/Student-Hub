document.addEventListener("DOMContentLoaded", async function () {

    try {

        const response = await fetch("studentData.php", {
            credentials: "same-origin",
            cache: "no-store"
        });

        if (!response.ok) {
            window.location.replace("StudentLogin.html");
            return;
        }

        const student = await response.json();

        document.querySelectorAll(".student-name").forEach(function (element) {
            element.textContent = student.name;
        });

        document.querySelectorAll(".student-username").forEach(function (element) {
            element.textContent = student.username;
        });

    } catch (error) {

        console.error("Unable to load student details:", error);

    }

});