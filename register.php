<?php
include "db.php";

if(isset($_POST['register'])){
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];

    $sql = "INSERT INTO users (name,email,password,phone)
            VALUES ('$name','$email','$password','$phone')";

    if(mysqli_query($conn,$sql)){
        echo "Registered Successfully!";
    } else {
        echo "Error!";
    }
}
?>

<h2>Register</h2>
<form method="POST">
    <input type="text" name="name" placeholder="Name"><br><br>
    <input type="email" name="email" placeholder="Email"><br><br>
    <input type="password" name="password" placeholder="Password"><br><br>
    <input type="text" name="phone" placeholder="Phone"><br><br>
    <button name="register">Register</button>
</form>