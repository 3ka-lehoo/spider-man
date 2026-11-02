<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

// Bejelentkezés nélkül a chat API nem használható.
if (empty($_SESSION['username']) || empty($_SESSION['token'])) {
	http_response_code(401);
	echo json_encode(["error" => "A chat használatához be kell jelentkezni."], JSON_UNESCAPED_UNICODE);
	exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_GET['action'] ?? '') === 'session') {
	// Visszaadja az aktuális munkamenet felhasználónevét.
	echo json_encode(["username" => $_SESSION['username']], JSON_UNESCAPED_UNICODE);
	exit;
}

$servername = "localhost";
$dbusername = "root";
$dbpassword = "";
$dbname = "spiderman_db";

// Kapcsolódás a chat adatbázisához.
$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
	http_response_code(500);
	echo json_encode(["error" => "Nem sikerült csatlakozni az adatbázishoz."], JSON_UNESCAPED_UNICODE);
	exit;
}

// Az üzeneteket magyar karakterekkel tárolja és küldi vissza.
$conn->set_charset("utf8mb4");

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
	// Kezdéskor az összes üzenetet, frissítéskor csak az újakat kéri le.
	$afterId = max(0, (int)($_GET['after'] ?? 0));
	if ($afterId > 0) {
		$stmt = $conn->prepare("SELECT id, sender_name, message, created_at FROM chat_messages WHERE id > ? ORDER BY id ASC");
		if ($stmt) {
			$stmt->bind_param("i", $afterId);
			$stmt->execute();
			$result = $stmt->get_result();
		} else {
			$result = false;
		}
	} else {
		$stmt = null;
		$result = $conn->query("SELECT id, sender_name, message, created_at FROM chat_messages ORDER BY id ASC");
	}

	if (!$result) {
		http_response_code(500);
		echo json_encode(["error" => "Nem sikerült lekérni az üzeneteket."], JSON_UNESCAPED_UNICODE);
	} else {
		$messages = [];
		while ($row = $result->fetch_assoc()) {
			$messages[] = $row;
		}
		echo json_encode($messages, JSON_UNESCAPED_UNICODE);
		$result->free();
	}
	if ($stmt) {
		$stmt->close();
	}
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
	// A bejelentkezett felhasználó nevével menti az új üzenetet.
	$senderName = trim($_SESSION['username'] ?? '');
	$message = trim($_POST['message'] ?? '');

	// JSON-kérés esetén is fogadjuk az adatokat.
	if (empty($_POST)) {
		$data = json_decode(file_get_contents('php://input'), true);
		if (is_array($data)) {
			$message = trim($data['message'] ?? '');
		}
	}

	if ($senderName === '') {
		http_response_code(401);
		echo json_encode(["error" => "Üzenetküldéshez be kell jelentkezni."], JSON_UNESCAPED_UNICODE);
	} elseif ($message === '') {
		http_response_code(400);
		echo json_encode(["error" => "Az üzenet megadása kötelező."], JSON_UNESCAPED_UNICODE);
	} elseif (mb_strlen($senderName, 'UTF-8') > 50) {
		http_response_code(400);
		echo json_encode(["error" => "A felhasználónév legfeljebb 50 karakter lehet."], JSON_UNESCAPED_UNICODE);
	} else {
		$stmt = $conn->prepare("INSERT INTO chat_messages (sender_name, message) VALUES (?, ?)");
		if (!$stmt) {
			http_response_code(500);
			echo json_encode(["error" => "Nem sikerült előkészíteni az üzenet mentését."], JSON_UNESCAPED_UNICODE);
		} else {
			$stmt->bind_param("ss", $senderName, $message);
			if ($stmt->execute()) {
				http_response_code(201);
				echo json_encode([
					"id" => $stmt->insert_id,
					"sender_name" => $senderName,
					"message" => $message
				], JSON_UNESCAPED_UNICODE);
			} else {
				http_response_code(500);
				echo json_encode(["error" => "Nem sikerült elmenteni az üzenetet."], JSON_UNESCAPED_UNICODE);
			}
			$stmt->close();
		}
	}
} else {
	header('Allow: GET, POST');
	http_response_code(405);
	echo json_encode(["error" => "Nem támogatott kérési mód."], JSON_UNESCAPED_UNICODE);
}

$conn->close();
?>
