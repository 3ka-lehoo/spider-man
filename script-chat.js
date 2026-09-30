
const form = document.getElementById("chatForm");
const input = document.getElementById("chatInput");
const messages = document.getElementById("messages");

form.addEventListener("submit", function (event) {
    event.preventDefault();

    const text = input.value.trim();

    if (text === "") {
        return;
    }

    const message = document.createElement("div");
    message.className = "message";

    const username = document.createElement("span");
    username.className = "username";
    username.textContent = "Te:";

    const messageText = document.createElement("span");
    messageText.className = "message-text";
    messageText.textContent = text;

    message.appendChild(username);
    message.appendChild(messageText);
    messages.appendChild(message);

    input.value = "";
    messages.scrollTop = messages.scrollHeight;
    input.focus();
});
