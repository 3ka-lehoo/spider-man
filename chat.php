```html
<!DOCTYPE html>
<html lang="hu">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spider-Man Chat</title>
    <link rel="stylesheet" href="style-chat.css">
</head>
<body>
    <main class="chat-container">
        <header class="chat-header">
            <div class="online-dot"></div>
            <h1 class="chat-title">Spider-Man Chat</h1>
        </header>

        <div class="messages" id="messages"></div>

        <div class="chat-input-area">
            <form id="chatForm" class="chat-form">
                <input
                    type="text"
                    id="chatInput"
                    class="chat-input"
                    placeholder="Írj egy üzenetet..."
                    maxlength="500"
                    autocomplete="off"
                >
                <button type="submit" class="send-button">Küldés</button>
            </form>
        </div>
    </main>
    <script src="script-chat.js"></script>
</body>
</html>
