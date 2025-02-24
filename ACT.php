<?php
global $conn;
session_start();
require_once 'Config/Database.php';
$course_id = 'ACT';

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
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
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
                    <a href="student_deatails.php?id=<?= $student['id'] ?>">
                        <?= htmlspecialchars($student['last_name']) ?>, <?= htmlspecialchars($student['first_name']) ?> <?= htmlspecialchars($student['middle_name']) ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($student['email']) ?></td>
                <td><?= htmlspecialchars($student['contact']) ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php else: ?>
    <p>No students enrolled in this course yet.</p>
<?php endif; ?>

</body>
</html>