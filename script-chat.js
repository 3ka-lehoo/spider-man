
const form = document.getElementById("chatForm");
const input = document.getElementById("chatInput");
const messages = document.getElementById("messages");
const chatServiceUrl = "chatservice.php";

function renderMessage(senderName, text) {
    const message = document.createElement("div");
    message.className = "message";

    const username = document.createElement("span");
    username.className = "username";
    username.textContent = `${senderName}:`;

    const messageText = document.createElement("span");
    messageText.className = "message-text";
    messageText.textContent = text;

    message.appendChild(username);
    message.appendChild(messageText);
    messages.appendChild(message);
}

async function loadMessages() {
    try {
        const response = await fetch(chatServiceUrl, { credentials: "same-origin" });
        if (!response.ok) {
            throw new Error("Nem sikerült betölteni az üzeneteket.");
        }

        const savedMessages = await response.json();
        messages.replaceChildren();
        savedMessages.forEach((item) => renderMessage(item.sender_name, item.message));
        messages.scrollTop = messages.scrollHeight;
    } catch (error) {
        console.error(error);
    }
}

form.addEventListener("submit", async function (event) {
    event.preventDefault();

    const text = input.value.trim();
    if (text === "") {
        return;
    }

    try {
        const response = await fetch(chatServiceUrl, {
            method: "POST",
            credentials: "same-origin",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ message: text })
        });
        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.error || "Nem sikerült elküldeni az üzenetet.");
        }

        renderMessage(result.sender_name, result.message);
        input.value = "";
        messages.scrollTop = messages.scrollHeight;
        input.focus();
    } catch (error) {
        alert(error.message);
    }
});

loadMessages();
