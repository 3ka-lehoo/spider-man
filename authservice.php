<?php
session_start();

// Kapcsolódás az adatbázishoz.
$conn = new mysqli('localhost', 'root', '', 'spiderman_db');
if ($conn->connect_error) {
    http_response_code(500);
    exit('Adatbázis-kapcsolati hiba.');
}
$conn->set_charset('utf8mb4');

// A belépés és a regisztráció is POST-kéréssel működik.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit('Nem támogatott kérés.');
}

$action = $_POST['action'] ?? '';

if ($action === 'login') {
    // Felhasználónév vagy e-mail alapján keresi a fiókot.
    $login = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare('SELECT id, username, password FROM users WHERE username = ? OR email = ? LIMIT 1');
    $stmt->bind_param('ss', $login, $login);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    // Az új jelszóhash mellett a régi, sima jelszót is elfogadja és frissíti.
    $validPassword = $user && (password_verify($password, $user['password']) || hash_equals($user['password'], $password));
    if (!$validPassword) {
        $conn->close();
        header('Location: login.html?error=invalid');
        exit;
    }

    if (!password_get_info($user['password'])['algo']) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $update = $conn->prepare('UPDATE users SET password = ? WHERE id = ?');
        $update->bind_param('si', $hash, $user['id']);
        $update->execute();
        $update->close();
    }

    // Új munkamenetet és belépési tokent hoz létre.
    $token = bin2hex(random_bytes(32));
    $update = $conn->prepare('UPDATE users SET token = ? WHERE id = ?');
    $update->bind_param('si', $token, $user['id']);
    $update->execute();
    $update->close();

    session_regenerate_id(true);
    $_SESSION['username'] = $user['username'];
    $_SESSION['token'] = $token;
    $conn->close();
    header('Location: spidey_tracker_chat.html');
    exit;
}

if ($action === 'register') {
    // Elmenti az új fiókot hash-elt jelszóval.
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $email === '' || $password === '') {
        http_response_code(400);
        exit('Minden mező kitöltése kötelező.');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare('INSERT INTO users (email, password, username) VALUES (?, ?, ?)');
    $stmt->bind_param('sss', $email, $hash, $username);

    if (!$stmt->execute()) {
        $stmt->close();
        $conn->close();
        http_response_code(400);
        exit('A regisztráció nem sikerült. Ellenőrizd az adatokat.');
    }

    $stmt->close();
    $conn->close();
    header('Location: login.html');
    exit;
}

$conn->close();
http_response_code(400);
echo 'Ismeretlen kérés.';
?>
