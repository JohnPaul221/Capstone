<?php
global $conn;
session_start();
require_once 'Config/Database.php';

if (!isset($_GET['id'])) {
    die("Student ID not provided.");
}

$student_id = $_GET['id'];
$stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
$stmt->bind_param("i", $student_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Student not found.");
}
$student = $result->fetch_assoc();
$course_id = $student['course_id'];

// Decode selected subjects
$selected_subjects = json_decode($student['selected_subjects'], true);

// Check if decoding was successful
if (json_last_error() !== JSON_ERROR_NONE) {
    // Handle JSON error
    echo "Error decoding JSON: " . json_last_error_msg();
    $selected_subjects = []; // Set to empty array to avoid foreach error
}

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
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h2 {
            margin-top: 0;
            color: #007bff;
        }
        .detail {
            margin-bottom: 15px;
        }
        .detail label {
            font-weight: bold;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .button:hover {
            background-color: #0056b3;
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
        <?php if (is_array($selected_subjects) && !empty($selected_subjects)): ?>
            <ul>
                <?php foreach ($selected_subjects as $subject): ?>
                    <li><?= htmlspecialchars($subject) ?></li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No subjects selected.</p>
        <?php endif; ?>
    </div>
    <a href="<?= strtolower($course_id) ?>.php?course=<?= urlencode($course_id) ?>" class="button">Back to Student List</a>
    <a href="javascript:window.print();" class="button">Print Details</a>
</div>
</body>
</html>