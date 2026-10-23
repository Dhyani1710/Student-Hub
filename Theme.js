document.addEventListener("DOMContentLoaded", function () {

    let toggle = document.createElement("button");
    toggle.id = "studentHubThemeToggle";
    document.body.appendChild(toggle);

    let style = document.createElement("style");
    style.innerHTML = `
        #studentHubThemeToggle {
            position: fixed;
            top: 15px;
            right: 15px;
            width: 38px;
            height: 20px;
            border: none;
            border-radius: 20px;
            background: #cbd5e1;
            cursor: pointer;
            z-index: 99999;
            transition: 0.3s ease;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        #studentHubThemeToggle::before {
            content: "🌙";
            position: absolute;
            width: 16px;
            height: 16px;
            left: 2px;
            top: 2px;
            border-radius: 50%;
            background: white;
            font-size: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        body.dark-mode #studentHubThemeToggle {
            background: #0466c8;
        }
        body.dark-mode #studentHubThemeToggle::before {
            content: "☀️";
            transform: translateX(18px);
        }

        body.dark-mode {
            background: #101820 !important;
            color: #f5f7fa !important;
        }

        body.dark-mode .container,
        body.dark-mode .main,
        body.dark-mode section {
            background: #101820 !important;
        }

        body.dark-mode .header,
        body.dark-mode form2,
        body.dark-mode form1,
        body.dark-mode .small-card,
        body.dark-mode .medium-card,
        body.dark-mode .Hallticket,
        body.dark-mode .about,
        body.dark-mode .profile-card,
        body.dark-mode .academic-card,
        body.dark-mode .parent-card,
        body.dark-mode .address-card,
        body.dark-mode .college-card,
        body.dark-mode .detail,
        body.dark-mode .event-card,
        body.dark-mode .rules-box,
        body.dark-mode .box,
        body.dark-mode .Payment1,
        body.dark-mode .Login,
        body.dark-mode .add-leave,
        body.dark-mode .leave-table,
        body.dark-mode .add-application,
        body.dark-mode .application-list,
        body.dark-mode .program-box,
        body.dark-mode .welcome-box,
        body.dark-mode .services-box,
        body.dark-mode .objective-box {
            background: #000000 !important;   
            color: #f5f7fa !important;
        }
        body.dark-mode .academic-box{
            background: #1f1f1f !important;
        }
        body.dark-mode .detail{
            background: #1f1f1f !important;
        }

        body.dark-mode h1, body.dark-mode h2, body.dark-mode h3,
        body.dark-mode h4, body.dark-mode h5, body.dark-mode h6 {
            color: #ffffff !important;
        }
        body.dark-mode p, body.dark-mode li, body.dark-mode label {
            color: #c4ced9 !important;
        }
          
        body.dark-mode h1,
        body.dark-mode h2,
        body.dark-mode h3,
        body.dark-mode h4,
        body.dark-mode h5,
        body.dark-mode h6 {
            color: #e0e0e0 !important;   /* headings softer gray */
        }

        body.dark-mode p,
        body.dark-mode li,
        body.dark-mode label {
            color: #b0b0b0 !important;   /* body text medium gray */
        }

        body.dark-mode .Profile h2 { color: #ffffff !important; }
        body.dark-mode .Profile h3 { color: #aeb9c6 !important; }

        body.dark-mode table {
            background: #1b2633 !important;
            color: #ffffff !important;
        }
        body.dark-mode tbody td {
            background: #1b2633 !important;
            color: #c4ced9 !important;
            border-bottom-color: #354352 !important;
        }
        body.dark-mode tbody td:first-child { color: #ffffff !important; }
        body.dark-mode tbody tr:hover td { background: #263442 !important; }
        body.dark-mode thead {
            background: linear-gradient(90deg,#0466c8,#023e7d) !important;
            color: white !important;
        }

        body.dark-mode input,
        body.dark-mode select,
        body.dark-mode textarea {
            background: #1b2633 !important;
            color: #ffffff !important;
            border-color: #405162 !important;
        }
        body.dark-mode input::placeholder,
        body.dark-mode textarea::placeholder {
            color: #8996a5 !important;
        }

        body.dark-mode a { color: #5eaeff; }
        body.dark-mode .sidebar a,
        body.dark-mode .sidebar button { color: white !important; }

        body.dark-mode footer {
            color: white !important;
        }

        body.dark-mode .header,
        body.dark-mode .card,
        body.dark-mode .small-card,
        body.dark-mode .medium-card,
        body.dark-mode .about,
        body.dark-mode table {
            box-shadow: 0 4px 15px rgba(0,0,0,0.35) !important;
        }
    `;
    document.head.appendChild(style);

    let savedTheme = localStorage.getItem("studentHubTheme");
    if (savedTheme === "dark") document.body.classList.add("dark-mode");

    toggle.addEventListener("click", function () {
        if (document.body.classList.contains("dark-mode")) {
            document.body.classList.remove("dark-mode");
            localStorage.setItem("studentHubTheme", "light");
        } else {
            document.body.classList.add("dark-mode");
            localStorage.setItem("studentHubTheme", "dark");
        }
    });

    // ================= HAMBURGER MENU =================

let hamburger = document.createElement("button");

hamburger.id = "studentHubHamburger";
hamburger.innerHTML = "☰";
document.body.appendChild(hamburger);


// Hamburger CSS
let menuStyle = document.createElement("style");

menuStyle.innerHTML = `

    #studentHubHamburger {
        display: none !important;
        position: fixed !important;
        top: 15px !important;
        left: 15px !important;

        width: 45px !important;
        height: 40px !important;

        background: #0466c8 !important;
        color: white !important;

        border: none !important;
        border-radius: 6px !important;

        font-size: 25px !important;
        line-height: 40px !important;
        text-align: center !important;

        cursor: pointer !important;

        z-index: 999999 !important;
    }


    @media screen and (max-width: 700px) {

        #studentHubHamburger {
            display: block !important;
        }

        .sidebar {
            position: fixed !important;

            top: 0 !important;
            left: -260px !important;

            width: 245px !important;
            height: 100vh !important;

            z-index: 999998 !important;

            transition: left 0.3s ease !important;

            overflow-y: auto !important;
        }

        .sidebar.show {
            left: 0 !important;
        }

        .main {
            width: 100% !important;
            margin-left: 0 !important;
        }

    }

`;

document.head.appendChild(menuStyle);


// Find sidebar
let sidebar = document.querySelector(".sidebar");


// If sidebar exists
if (sidebar) {

    hamburger.addEventListener("click", function () {

        sidebar.classList.toggle("show");

        if (sidebar.classList.contains("show")) {

            hamburger.innerHTML = "✕";

        } else {

            hamburger.innerHTML = "☰";

        }

    });


    // Close menu after clicking any sidebar link

    let sidebarLinks = sidebar.querySelectorAll("a");

    sidebarLinks.forEach(function (link) {

        link.addEventListener("click", function () {

            sidebar.classList.remove("show");

            hamburger.innerHTML = "☰";

        });

    });

}
});
// ================= STUDENT SESSION DETAILS =================

document.addEventListener("DOMContentLoaded", async function () {

    const studentPages = [
        "Dashboard.php",
        "Profile.html",
        "Course.html",
        "Attendence.html",
        "Fees.html",
        "HallTicket.html",
        "Scholarship.html",
        "Medical.html",
        "Event.html",
        "Rule.html"
    ];

    const currentPage = window.location.pathname.split("/").pop();

    if (!studentPages.includes(currentPage)) {
        return;
    }

    try {

        const response = await fetch("studentData.php", {
            credentials: "same-origin",
            cache: "no-store",
            redirect: "error"
        });

        if (!response.ok) {
            window.location.replace("StudentLogin.html");
            return;
        }

        const student = await response.json();

        // Update student name and username everywhere on the page

const walker = document.createTreeWalker(
    document.body,
    NodeFilter.SHOW_TEXT
);

const textNodes = [];

while (walker.nextNode()) {
    textNodes.push(walker.currentNode);
}

textNodes.forEach(function (node) {

    const parent = node.parentElement;

    if (!parent) return;

    if (
        parent.closest("script, style, textarea, input, option")
    ) {
        return;
    }

    let text = node.nodeValue;

    text = text.replaceAll("Dhyani Parmar", student.name);
    text = text.replaceAll("25DCS072", student.username);

    node.nodeValue = text;

});

    } catch (error) {

        console.error("Unable to load student details:", error);
        window.location.replace("StudentLogin.html");

    }

});