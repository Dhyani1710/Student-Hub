document.addEventListener("DOMContentLoaded", function () {
    const form = document.querySelector("form");
    const cancelBtn = document.querySelector(".cancel");

    form.addEventListener("submit", function (event) {
        event.preventDefault();

        let name = document.querySelector("input[type='text'][value='Dhyani Parmar']").value.trim();
        let email = document.querySelector("input[type='email']").value.trim();
        let mobile = document.querySelector("input[type='tel'][value='9876543210']").value.trim();
        let fatherMobile = document.querySelector("input[type='tel'][value='9876543211']").value.trim();
        let motherMobile = document.querySelector("input[type='tel'][value='9876543212']").value.trim();
        let guardianMobile = document.querySelector("input[placeholder='Enter guardian name']")?.nextElementSibling?.value || "";
        let emergencyMobile = document.querySelector("input[type='tel'][value='9876543213']").value.trim();
        let dob = document.querySelector("input[type='date']").value;
        let pin = document.querySelector("input[value='390001']").value.trim();

        let errors = [];

        if (name === "") errors.push("Name is required.");
        if (!/^[^ ]+@[^ ]+\.[a-z]{2,3}$/.test(email)) errors.push("Invalid email.");

        let mobilePattern = /^[0-9]{10}$/;
        if (!mobilePattern.test(mobile)) errors.push("Student mobile must be 10 digits.");
        if (!mobilePattern.test(fatherMobile)) errors.push("Father's mobile must be 10 digits.");
        if (!mobilePattern.test(motherMobile)) errors.push("Mother's mobile must be 10 digits.");
        if (!mobilePattern.test(emergencyMobile)) errors.push("Emergency contact must be 10 digits.");
        if (guardianMobile && !mobilePattern.test(guardianMobile)) errors.push("Guardian mobile must be 10 digits.");

        if (!/^[0-9]{6}$/.test(pin)) errors.push("PIN must be 6 digits.");

        if (errors.length > 0) {
            alert(errors.join("\n"));
        } else {
            alert("Profile updated successfully!");
            localStorage.setItem("profile", JSON.stringify({
                name, email, mobile, fatherMobile, motherMobile, guardianMobile, emergencyMobile, dob, pin
            }));
        }
    });

    cancelBtn.addEventListener("click", function () {
        if (confirm("Cancel changes?")) {
            window.location.href = "Dashboard.html";
        }
    });
});
