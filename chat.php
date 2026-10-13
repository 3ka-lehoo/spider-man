<?php
session_start();

if (isset($_GET['logout'])) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.html');
    exit;
}

if (empty($_SESSION['username']) || empty($_SESSION['token'])) {
    header('Location: login.html');
    exit;
}

$username = htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8');
?>
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
            <div class="chat-user-area">
                <span class="chat-user"><?php echo $username; ?></span>
                <a class="logout-link" href="chat.php?logout=1">Kilépés</a>
            </div>
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
