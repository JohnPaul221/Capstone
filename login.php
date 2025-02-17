<?php
require_once 'Config/Database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Form</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url('upload/snapedi.jpeg');
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            flex-direction: column;
        }

        .header {
            background-color: rgba(0, 0, 0, 0.0);
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            margin-bottom: 20px;
            width: 100%;
            max-width: 400px;
            display: flex; /* Use flexbox for alignment */
            align-items: center; /* Center items vertically */
            justify-content: space-between; /* Space between logo and text */
        }

        .header img {
            height: 8em; /* Set the height of the logo */
            width: auto; /* Maintain aspect ratio */
            margin-right: 10px; /* Space between logo and text */
        }

        .header h1 {
            margin: 0; /* Remove default margin */
            font-size: 2em; /* Set font size */
            text-align: center; /* Center text */
            flex-grow: 1; /* Allow the h1 to take up available space */
            line-height: 1; /* Adjust line height to remove extra space */
        }

        .login-container {
            background: rgba(255, 255, 255, 0.2);
            padding: 50px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
        }

        .login-container h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        .login-container input {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        .login-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        .login-container button:hover {
            background-color: #0056b3;
        }

        .success {
            color: green;
            text-align: center;
            font-size: 1.5em; /* Increase font size for success message */
            margin-top: 10px; /* Add some space above the message */
        }

        .error {
            color: red;
            text-align: center;
            font-size: 1.5em; /* Increase font size for error message */
            margin-top: 10px; /* Add some space above the message */
        }
    </style>
</head>
<body>

<div class="header">
    <img src="upload/aclc_logo.png" alt="ACLC Logo">
    <h1>ACLC College Admin</h1>
</div>

<div class="login-container">
    <form action="login.php" method="POST">
        <input type="text" name="username" placeholder="Username" required>
        <input type="password" name="password" placeholder="Password" required>
        <button type="submit">Login</button>
    </form>

    <?php
    $valid_username = "admin";
    $valid_password = "admin1";

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $username = $_POST['username'];
        $password = $_POST['password'];

        if ($username === $valid_username && $password === $valid_password) {
            echo "<p class='success'>Login successful!</p>";
        } else {
            echo "<p class='error'>Invalid username or password.</p>";
        }
    }
    ?>
</div>

</body>
</html>