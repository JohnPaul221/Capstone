<?php
session_start();
require_once 'Config/Database.php';

global $conn;

if (!isset($_GET['id'])) {
    die("Student ID not provided.");
}

$student_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Student not found.");
}

$student = $result->fetch_assoc();
$course_id = $student['course_id'];

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Details</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-top: 0;
            color: #ff0000; /* Red color for the heading */
            text-align: center;
            border-bottom: 2px solid #007bff; /* Blue underline */
            padding-bottom: 10px;
        }
        .detail {
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #ffffff; /* White background for details */
            transition: background-color 0.3s;
        }
        .detail:hover {
            background-color: #e9ecef; /* Light gray on hover */
        }
        .detail label {
            font-weight: bold;
            color: #007bff; /* Blue color for labels */
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff; /* Blue button */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
        @media print {
            .button {
                display: none;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Student Details</h2>
    <div class="detail">
        <label>Name:</label> <?= htmlspecialchars($student['first_name']) ?> <?= htmlspecialchars($student['middle_name']) ?> <?= htmlspecialchars($student['last_name']) ?>
    </div>
    <div class="detail">
        <label>Email:</label> <?= htmlspecialchars($student['email']) ?>
    </div>
    <div class="detail">
        <label>Contact:</label> <?= htmlspecialchars($student['contact']) ?>
    </div>
    <div class="detail">
        <label>Course ID:</label> <?= htmlspecialchars($student['course_id']) ?>
    </div>
    <div class="detail">
        <label>Year Level:</label> <?= htmlspecialchars($student['year_level']) ?>
    </div>
    <div class="detail">
        <label>Enrollment Date:</label> <?= htmlspecialchars($student['created_at']) ?>
    </div>
    <div class="detail">
        <label>Selected Subjects:</label>
        <ul>
            <?php
            $selected_subjects = $student['selected_subjects'];

            if (!empty($selected_subjects)) {
                $subjects = explode(',', $selected_subjects);
                foreach ($subjects as $subject) {
                    $subject = trim($subject);
                    if (!empty($subject)) {
                        echo '<li>' . htmlspecialchars($subject) . '</li>';
                    }
                }
            } else {
                echo '<li>No subjects found.</li>';
            }
            ?>
        </ul>
    </div>

    <a href="<?= strtolower($course_id) ?>.php?course=<?= urlencode($course_id) ?>" class="button">Back to Student List</a>
    <a href="javascript:window.print();" class="button">Print Details</a>
</div>
</body>
</html>