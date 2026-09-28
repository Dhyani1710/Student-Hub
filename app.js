let events = [];

let currentPage = 1;
const eventsPerPage = 6;

const eventGrid = document.getElementById("eventGrid");
const searchBox = document.getElementById("searchBox");
const categoryFilter = document.getElementById("categoryFilter");
const sortBy = document.getElementById("sortBy");
const pagination = document.getElementById("pagination");


async function loadEvents() {

    events = await getEvents();

    createCategoryFilter();

    displayEvents();
}


function createCategoryFilter() {

    const categories = [...new Set(events.map(event => event.category))];

    categories.forEach(category => {

        const option = document.createElement("option");

        option.value = category;
        option.textContent = category;

        categoryFilter.appendChild(option);
    });
}


function displayEvents() {

    let filteredEvents = [...events];

    const searchText = searchBox.value.toLowerCase();

    if (searchText !== "") {

        filteredEvents = filteredEvents.filter(event =>
            event.name.toLowerCase().includes(searchText) ||
            event.venue.toLowerCase().includes(searchText) ||
            event.category.toLowerCase().includes(searchText)
        );
    }

    const selectedCategory = categoryFilter.value;

    if (selectedCategory !== "all") {

        filteredEvents = filteredEvents.filter(event =>
            event.category === selectedCategory
        );
    }

    if (sortBy.value === "name-asc") {

        filteredEvents.sort((a, b) =>
            a.name.localeCompare(b.name)
        );

    } else if (sortBy.value === "name-desc") {

        filteredEvents.sort((a, b) =>
            b.name.localeCompare(a.name)
        );

    } else if (sortBy.value === "date-asc") {

        filteredEvents.sort((a, b) =>
            new Date(a.date) - new Date(b.date)
        );

    } else if (sortBy.value === "date-desc") {

        filteredEvents.sort((a, b) =>
            new Date(b.date) - new Date(a.date)
        );
    }

    const totalPages = Math.ceil(
        filteredEvents.length / eventsPerPage
    );

    if (currentPage > totalPages) {
        currentPage = 1;
    }

    const start = (currentPage - 1) * eventsPerPage;

    const end = start + eventsPerPage;

    const pageEvents = filteredEvents.slice(start, end);

    eventGrid.innerHTML = "";

    pageEvents.forEach(event => {

        const card = document.createElement("div");

        card.className = "event-card";

        card.innerHTML = `
            <h3>${event.name}</h3>

            <p>Date: ${formatDate(event.date)}</p>

            <p>Venue: ${event.venue}</p>

            <p>Category: ${event.category}</p>

            <button onclick="registerEvent(${event.id})">
                Register
            </button>
        `;

        eventGrid.appendChild(card);
    });


    createPagination(totalPages);
}


function formatDate(date) {

    const eventDate = new Date(date);

    return eventDate.toLocaleDateString("en-GB", {
        day: "numeric",
        month: "short",
        year: "numeric"
    });
}


function createPagination(totalPages) {

    pagination.innerHTML = "";

    if (totalPages <= 1) {
        return;
    }

    const previousButton = document.createElement("button");

    previousButton.textContent = "Previous";

    previousButton.disabled = currentPage === 1;

    previousButton.onclick = function () {

        currentPage--;

        displayEvents();
    };

    pagination.appendChild(previousButton);

    for (let i = 1; i <= totalPages; i++) {

        const pageButton = document.createElement("button");

        pageButton.textContent = i;

        if (i === currentPage) {
            pageButton.classList.add("active");
        }

        pageButton.onclick = function () {

            currentPage = i;

            displayEvents();
        };

        pagination.appendChild(pageButton);
    }

    const nextButton = document.createElement("button");

    nextButton.textContent = "Next";

    nextButton.disabled = currentPage === totalPages;

    nextButton.onclick = function () {

        currentPage++;

        displayEvents();
    };

    pagination.appendChild(nextButton);
}


function registerEvent(id) {

    const event = events.find(event => event.id === id);

    if (event) {

        document.getElementById("registrationModal").style.display = "flex";

        document.getElementById("registrationForm").dataset.eventId = id;
    }
}

document.getElementById("closeModal").onclick = function () {

    document.getElementById("registrationModal").style.display = "none";
};


document.getElementById("registrationForm").onsubmit = function (e) {

    e.preventDefault();

    alert("Registration successful!");

    document.getElementById("registrationModal").style.display = "none";

    this.reset();
};

searchBox.addEventListener("input", function () {

    currentPage = 1;

    displayEvents();
});

categoryFilter.addEventListener("change", function () {

    currentPage = 1;

    displayEvents();
});

sortBy.addEventListener("change", function () {

    currentPage = 1;

    displayEvents();
});

loadEvents();