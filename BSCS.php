<?php

global $conn;
session_start();

require_once 'Config/Database.php';

$query = "SELECT first_name, middle_name, last_name, email, contact FROM students WHERE course_code = 'BSCS'";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BSCS Enrollment Information</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        #newStudentBtn {
            font-size: 16px;
            background-color: #4CAF50; /* Green background */
            color: white; /* White text */
            border: none; /* No border */
            padding: 10px 15px; /* Padding */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
            float: right; /* Align to the right */
            margin-bottom: 20px; /* Space below the button */
        }
    </style>
</head>
<body>

<div class="container">
    <h2 style="display: inline-block;">BSCS Enrolled Information</h2>
    <a id="newStudentBtn" href="Enrollment_form.php">+ New Student</a> <!-- Button outside the table but aligned with the heading -->

    <?php
    if ($result->num_rows > 0) {
        echo '<table>';
        echo '<tr>';
        echo '<th>First Name</th>';
        echo '<th>Middle Name</th>';
        echo '<th>Last Name</th>';
        echo '<th>Email</th>';
        echo '<th>Contact</th>';
        echo '</tr>';
        while ($row = $result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . htmlspecialchars($row['first_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['middle_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['last_name']) . '</td>';
            echo '<td>' . htmlspecialchars($row['email']) . '</td>';
            echo '<td>' . htmlspecialchars($row['contact']) . '</td>';
            echo '</tr>';
        }
        echo '</table>';
    } else {
        echo '<p>No students enrolled in BSCS.</p>';
    }
    $conn->close();
    ?>
</div>

</body>
</html>