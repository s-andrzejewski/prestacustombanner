const ajaxUrl = ajax_link;
const xhr = new XMLHttpRequest();

xhr.onload = () => {
    if (xhr.status >= 200 && xhr.status < 300) {
        const response = JSON.parse(xhr.responseText);
        if (response.status) {
            const container = document.querySelector(".presta-custom-message__ajax-response");
            container.classList.add("skeleton");
            container.classList.add("skeleton-paragraph");

            // setTimeout - only for demonstration purposes:
            setTimeout(()=>{
                if (container) {
                    container.innerHTML = response.message;
                    container.classList.remove("skeleton");
                    container.classList.remove("skeleton-paragraph");
                }
            },1000);
        }

    }
};

xhr.open("POST", ajaxUrl, true);
const data = "action=get_message";

xhr.setRequestHeader("Content-Type", "application/json");
xhr.send(data);
