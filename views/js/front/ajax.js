const ajaxUrl = ajax_link;
const data = "action=get_message";
const xhr = new XMLHttpRequest();

fetch(ajaxUrl, {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify({ action: data }),
})
    .then((response) => {
        if (response.ok) {
        return response.json();
        } else {
        throw new Error(`HTTP error! Status: ${response.status}`);
        }
    })
    .then((response) => {
        if (response.status) {
          const container = document.querySelector(".presta-custom-message__ajax-response");
          container.classList.add("skeleton");
          container.classList.add("skeleton-paragraph");
    
          // setTimeout - only for demonstration purposes:
          setTimeout(() => {
            if (container) {
              container.innerHTML = response.message;
              container.classList.remove("skeleton");
              container.classList.remove("skeleton-paragraph");
            }
          }, 1000);
        }
    })
    .catch((error) => {
        console.error("Fetch error:", error);
    });
