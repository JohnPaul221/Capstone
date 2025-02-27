<?php
global $conn;
session_start();
require_once 'Config/Database.php';

$course_id = 'BSENTREP';
$stmt = $conn->prepare("SELECT * FROM students WHERE course_id = ?");
$stmt->bind_param("s", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);

$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Students in <?= htmlspecialchars($course_id) ?></title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
            color: #333;
        }
        h2 {
            text-align: center;
            color: #ff0000; /* Red color for the heading */
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff; /* Blue background for header */
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2; /* Light gray for even rows */
        }
        tr:hover {
            background-color: #e9ecef; /* Light gray on hover */
        }
        a {
            text-decoration: none;
            color: #007bff; /* Blue color for links */
            transition: color 0.3s;
        }
        a:hover {
            color: #ff0000; /* Red color on link hover */
        }
        .no-students {
            text-align: center;
            font-size: 18px;
            color: #ff0000; /* Red color for no students message */
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            padding: 10px;
            background-color: #007bff; /* Blue button */
            color: white;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.3s;
        }
        .back-link:hover {
            background-color: #0056b3; /* Darker blue on hover */
        }
    </style>
</head>
<body>
<h2>Students Enrolled in <?= htmlspecialchars($course_id) ?></h2>

<?php if (count($students) > 0): ?>
    <table>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Contact</th>
        </tr>
        <?php foreach ($students as $student): ?>
            <tr>
                <td>
                    <a href="student_details.php?id=<?= $student['id'] ?>">
                        <?= htmlspecialchars($student['last_name']) ?>, <?= htmlspecialchars($student['first_name']) ?> <?= htmlspecialchars($student['middle_name']) ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($student['email']) ?></td>
                <td><?= htmlspecialchars($student['contact']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p class="no-students">No students enrolled in this course yet.</p>
<?php endif; ?>

<a href="dashboard.php" class="back-link">Back to Enrollment</a>
</body>
</html>