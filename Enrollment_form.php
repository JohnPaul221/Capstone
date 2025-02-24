<?php
global $conn;
session_start();
require_once 'Config/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $course_id = $_POST['course'];
    $year_level = $_POST['year_level']; // Capture year level

    $allowed_courses = ['BSCS', 'BSENTREP', 'BSAIS', 'ACT'];
    if (!in_array($course_id, $allowed_courses)) {
        die("Invalid course");
    }

    // Update SQL query to exclude academic_year
    $stmt = $conn->prepare("INSERT INTO students (first_name, middle_name, last_name, email, contact, course_id, year_level) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $first_name, $middle_name, $last_name, $email, $contact, $course_id, $year_level);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Enrollment successful!";
        switch ($course_id) {
            case 'BSCS':
                header("Location: BSCS.php");
                break;
            case 'BSENTREP':
                header("Location: BSENTREP.php");
                break;
            case 'BSAIS':
                header("Location: BSAIS.php");
                break;
            case 'ACT':
                header("Location: ACT.php");
                break;
            default:
                header("Location: index.php");
                break;
        }
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Form</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .enrollment-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 50px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .enrollment-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .enrollment-container input,
        .enrollment-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .enrollment-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .enrollment-container button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            color: green;
        }
    </style>
</head>
<body>

<div class="enrollment-container">
    <h2>Enrollment Form</h2>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="message">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="message" style="color: red;">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
    ?>

    <form action="" method="POST">
        <input type="text" name="first_name" placeholder="First Name:" required>
        <input type="text" name="middle_name" placeholder="Middle Name:" required>
        <input type="text" name="last_name" placeholder="Last Name:" required>
        <input type="email" name="email" placeholder="Email:" required>
        <input type="text" name="contact" placeholder="Contact Number:" required>

        <select name="year_level" required>
            <option value="" disabled selected>Select Year Level:</option>
            <option value="First Year">First Year</option>
            <option value="Second Year">Second Year</option>
            <option value="Third Year">Third Year</option>
            <option value="Fourth Year">Fourth Year</option>
        </select>

        <select name="course" required>
            <option value="" disabled selected>Select Course:</option>
            <option value="BSCS">BS Computer Science (BSCS)</option>
            <option value="BSENTREP">Bachelor of Science in Entrepreneurship (BSENTREP)</option>
            <option value="BSAIS">Bachelor of Science in Accounting Information System (BSAIS)</option>
            <option value="ACT">Associate in Computer Technology (ACT)</option>
        </select>

        <button type="submit">Enroll</button>
    </form>
</div>

</body>
</html>