<?php
global $conn;
require_once 'Config/Database.php';
session_start();

$course_id = 'BSCS';
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];
}

$usn = '';
if (isset($_GET['usn'])) {
    $usn = $_GET['usn'];
}
$stmt = $conn->prepare("SELECT id, usn, first_name, middle_name, last_name, email, contact, year_level FROM students WHERE course_id = ? AND (usn LIKE ? OR usn IS NULL)");
$searchTerm = '%' . $usn . '%';
$stmt->bind_param("ss", $course_id, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
$students = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
$conn->close();

$students_by_year = [];
foreach ($students as $student) {
    $students_by_year[$student['year_level']][] = $student;
}

$year_order = ['First Year', 'Second Year', 'Third Year', 'Fourth Year'];
$sorted_students_by_year = [];
foreach ($year_order as $year) {
    if (isset($students_by_year[$year])) {
        $sorted_students_by_year[$year] = $students_by_year[$year];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students in <?= htmlspecialchars($course_id) ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            background-color: #e9ecef;
            font-family: 'Arial', sans-serif;
            transition: margin-left 0.3s ease;
        }
        #main {
            padding: 30px;
            transition: margin-left 0.3s ease;
        }
        .sidebar {
            display: none;
            height: 100%;
            width: 250px;
            position: fixed;
            top: 0;
            left: -250px;
            background-color: #343a40;
            color: white;
            transition: left 0.3s ease;
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.5);
            padding: 20px;
        }
        .sidebar.open {
            display: block;
            left: 0;
        }
        .sidebar h2 {
            margin-top: 0;
            color: #ffffff;
        }
        .sidebar ul {
            list-style: none;
            padding: 0;
        }
        .sidebar li {
            margin: 10px 0;
        }
        .sidebar a {
            background-color: #495057;
            text-decoration: none;
            color: #ffffff;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        .sidebar a:hover {
            background-color: #6c757d;
            color: #ffffff;
        }
        #openBtn {
            margin: 20px;
            font-size: 24px;
            background: none;
            border: none;
            color: #007bff;
            cursor: pointer;
        }
        #closeBtn {
            background: none;
            border: none;
            color: #ffffff;
            cursor: pointer;
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
        }
        table {
            width:  100%;
            border-collapse: collapse;
            margin: 20px 0;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #d1ecf1;
        }
        h2, h3 {
            color: #343a40;
        }
        .no-students {
            text-align: center;
            font-size: 18px;
            color: #6c757d;
        }
    </style>
</head>
<body>
<div id="sidebar" class="sidebar" aria-hidden="true">
    <button id="closeBtn"><i class="fa-solid fa-xmark"></i></button>
    <h2>Menu</h2>
    <ul>
        <li><a href="home.php">Home</a></li>
        <li><a href="?course_id=BSCS">BSCS</a></li>
        <li><a href="?course_id=BSENTREP">BSENTEP</a></li>
        <li><a href="?course_id=BSAIS">BSAIS</a></li>
        <li><a href="?course_id=ACT">ACT</a></li>
    </ul>
</div>
<div id="main">
    <button id="openBtn"><i class="fa-solid fa-bars"></i></button>
    <h1>Students in <?= htmlspecialchars($course_id) ?></h1>

    <form method="GET" action="">
        <input type="hidden" name="course_id" value="<?= htmlspecialchars($course_id) ?>">
        <input type="text" name="usn" placeholder="Search by USN" class="form-control" style="width: 300px; display: inline-block;">
        <button type="submit" class="btn btn-primary">Search</button>
    </form>

    <?php if (!empty($sorted_students_by_year)): ?>
        <?php foreach ($sorted_students_by_year as $year_level => $students): ?>
            <h3><?= htmlspecialchars($year_level) ?> Students</h3>
            <table>
                <tr>
                    <th>USN</th>
                    <th>First Name</th>
                    <th>Middle Name</th>
                    <th>Last Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>Action</th>
                </tr>
                <?php foreach ($students as $student): ?>
                    <tr>
                        <td><?= htmlspecialchars($student['usn']) ?></td>
                        <td><?= htmlspecialchars($student['first_name']) ?></td>
                        <td><?= htmlspecialchars($student['middle_name']) ?></td>
                        <td><?= htmlspecialchars($student['last_name']) ?></td>
                        <td><?= htmlspecialchars($student['email']) ?></td>
                        <td><?= htmlspecialchars($student['contact']) ?></td>
                        <td><a href="student_details.php?id=<?= htmlspecialchars($student['id']) ?>" class="btn btn-primary">View</a></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-students">No students found matching your search.</p>
    <?php endif; ?>
</div>
<script>
    function adjustMainContent(isOpen) {
        const mainContent = document.getElementById("main");
        if (isOpen) {
            mainContent.style.marginLeft = "250px";
        } else {
            mainContent.style.marginLeft = "0";
        }
    }

    document.getElementById("openBtn").onclick = function() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.add("open");
        sidebar.style.display = "block";
        adjustMainContent(true);
    };

    document.getElementById("closeBtn").onclick = function() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.remove("open");
        sidebar.style.display = "none";
        adjustMainContent(false);
    };
</script>
</body>
</html>