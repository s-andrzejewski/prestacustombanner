async function fetchAjaxData(ajaxUrl) {
    try {
        const data = "action=get_message";

        const response = await fetch(ajaxUrl, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
            },
            body: JSON.stringify({ action: data }),
        });

        if (response.ok) {
            const responseData = await response.json();

            if (responseData.status) {
                const container = document.querySelector(".presta-custom-message__ajax-response");
                container.classList.add("skeleton");
                container.classList.add("skeleton-paragraph");

                // setTimeout - only for demonstration purposes:
                setTimeout(() => {
                    if (container) {
                        container.innerHTML = responseData.message;
                        container.classList.remove("skeleton");
                        container.classList.remove("skeleton-paragraph");
                    }
                }, 1000);
            }
        } else {
            console.error(`HTTP error! Status: ${response.status}`);

            // Handle the HTTP error gracefully, e.g., display an error message to the user
            const errorMessage = "An error occurred while fetching data. Please try again later.";
            // You can display the errorMessage in the DOM or use other means to notify the user.
        }
    } catch (error) {
        console.error("Fetch error:", error);

        // Handle any other errors gracefully, e.g., display an error message to the user
        const errorMessage = "An error occurred while fetching data. Please try again later.";
        // You can display the errorMessage in the DOM or use other means to notify the user.
    }
}

const ajaxUrl = ajax_link;
fetchAjaxData(ajaxUrl);
