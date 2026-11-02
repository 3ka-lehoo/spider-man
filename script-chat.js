
// A chat elemei és a kiszolgáló címe.
const form = document.getElementById("chatForm");
const input = document.getElementById("chatInput");
const messages = document.getElementById("messages");
const chatServiceUrl = "chatservice.php";
const seenMessageIds = new Set();
let lastMessageId = 0;

// Biztonságosan megjelenít egy üzenetet.
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

// Betölti az üzeneteket, vagy csak az újakat frissítéskor.
async function loadMessages() {
    try {
        const url = lastMessageId ? `${chatServiceUrl}?after=${lastMessageId}` : chatServiceUrl;
        const response = await fetch(url, { credentials: "same-origin", cache: "no-store" });
        if (response.status === 401) {
            window.location.href = "login.html";
            return;
        }
        if (!response.ok) {
            throw new Error("Nem sikerült betölteni az üzeneteket.");
        }

        const savedMessages = await response.json();
        savedMessages.forEach((item) => {
            const id = Number(item.id) || 0;
            if (!seenMessageIds.has(String(id))) {
                renderMessage(item.sender_name, item.message);
                seenMessageIds.add(String(id));
            }
            lastMessageId = Math.max(lastMessageId, id);
        });
        messages.scrollTop = messages.scrollHeight;
    } catch (error) {
        console.error(error);
    }
}

// Elküldi az üzenetet, majd frissíti a megjelenítést.
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

        const id = Number(result.id) || 0;
        if (!seenMessageIds.has(String(id))) {
            renderMessage(result.sender_name, result.message);
            seenMessageIds.add(String(id));
        }
        lastMessageId = Math.max(lastMessageId, id);
        input.value = "";
        messages.scrollTop = messages.scrollHeight;
        input.focus();
    } catch (error) {
        alert(error.message);
    }
});

// Betöltés után két másodpercenként keresi az új üzeneteket.
loadMessages();
window.setInterval(loadMessages, 2000);
