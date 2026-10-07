<?php
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
    $sql = "SELECT * FROM felhasznalok WHERE username = '$username' AND password = '$password'";
    // Execute the query
    if(!($result = mysqli_query($conn, $sql))){
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
    }else{
        $resultCheck = mysqli_num_rows($result);
        if($resultCheck > 0){
            while ($row = mysqli_fetch_assoc($result)){
                header('Location: http://localhost/spiderman/chat.php');
            }
        }else{
            header('Location: http://localhost/spiderman/login.html');
        }
    }

}
if(isset($_POST['username']) and isset($_POST['password']) and isset($_POST['email'])){
    $username = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $sql = "INSERT INTO `felhasznalok`(`email`, `password`, `username`) VALUES ('$email','$password','$username')";
    if(!($result = mysqli_query($conn, $sql))){
        echo "ERROR: Could not able to execute $sql. " . mysqli_error($conn);
    }else{
        header('Location: http://localhost/spiderman/login.html');
    }
}
// Close the database connection
mysqli_close($conn);
?>