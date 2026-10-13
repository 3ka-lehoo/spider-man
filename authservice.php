<?php
session_start();

$servername = "localhost";
$dbusername = "root";   // change if needed
$dbpassword = "";       // change if needed
$dbname = "spiderman_db";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if(isset($_GET['username']) and isset($_GET['password'])){
    $username = $_GET['username'];
    $password = $_GET['password'];
    $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    // Execute the query
    if(!($result = mysqli_query($conn, $sql))){
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
    }else{
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0){
            while ($row = mysqli_fetch_assoc($result)){
                $token = bin2hex(random_bytes(32));
                $tokenStmt = $conn->prepare("UPDATE users SET token = ? WHERE id = ?");

                if ($tokenStmt && $tokenStmt->bind_param("si", $token, $row['id']) && $tokenStmt->execute()) {
                    session_regenerate_id(true);
                    $_SESSION['username'] = $row['username'];
                    $_SESSION['token'] = $token;
                    $tokenStmt->close();
                    header('Location: http://localhost/spider/chat.php');
                    exit;
                }

                if ($tokenStmt) {
                    $tokenStmt->close();
                }
                http_response_code(500);
                echo "Nem sikerült létrehozni a bejelentkezési tokent.";
                exit;
            }
        }else{
            header('Location: http://localhost/spider/login.html');
            exit;
        }
    }

}
if(isset($_POST['username']) and isset($_POST['password']) and isset($_POST['email'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $sql = "INSERT INTO `users`(`email`, `password`, `username`) VALUES ('$email','$password','$username')";
    if(!($result = mysqli_query($conn, $sql))){
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
    }else{
        header('Location: http://localhost/spider/login.html');
        exit;
    }
}
// Close the database connection
mysqli_close($conn);
?>