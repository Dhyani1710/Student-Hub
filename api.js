async function getEvents() {
    try {
        const response = await fetch("event.json");

        if (!response.ok) {
            throw new Error("Failed to load events");
        }

        const events = await response.json();

        return events;

    } catch (error) {
        console.error("Error:", error);
        return [];
    }
}