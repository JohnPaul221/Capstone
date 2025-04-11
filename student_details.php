<?php
session_start();
require_once 'Config/Database.php';
global $conn;

function fetchStudentData($student_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT usn, last_name, first_name, middle_name, email, contact, lrn, dob, pob, age, course_id, year_level, upon_enrollment, sex, civil_status, guardian_name, guardian_contact, created_at, address, subjects FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return null;
    }

    return $result->fetch_assoc();
}

function fetchAllStudentData($student_id) {
    global $conn;

    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $student_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return null;
    }

    return $result->fetch_assoc();
}

if (!isset($_GET['id'])) {
    die("Student ID not provided.");
}

$student_id = intval($_GET['id']);
$student = fetchStudentData($student_id);

if ($student === null) {
    die("Student not found.");
}
$allStudentData = fetchAllStudentData($student_id);

$created_at = $student['created_at'];
$course_id = $student['course_id'];
$subjectFees = [
    'Euthenics 2' => 1500,
    'Computer Programming 2 (Lab)' => 1200,
    'Computer Programming 2 (Lec)' => 1000,
    'Math in the Modern World' => 1800,
    'National Service Training Program 2' => 800,
    'PATHFIT 2' => 3000,
    'Ethics' => 4000,
    'Discrete Structure 1' => 3000,
    'Data Communication and Networking 2' => 3000,
];

function calculateTotalFees($selectedSubjects, $subjectFees) {
    $total = 0;
    foreach ($selectedSubjects as $subject) {
        $subject = trim($subject);
        if (isset($subjectFees[$subject])) {
            $total += $subjectFees[$subject];
        }
    }
    return $total;
}

$submitted_tuition = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tuition'])) {
    $tuition_amount = floatval($_POST['tuition']);
    if ($tuition_amount > 0) {
        $current_date = date('Y-m-d H:i:s');
        $stmt_payment = $conn->prepare("INSERT INTO tuition_payment (student_id, amount, payment_date) VALUES (?, ?, ?)");
        $stmt_payment->bind_param("ids", $student_id, $tuition_amount, $current_date);
        $stmt_payment->execute();
        $stmt_payment->close();
        if (empty($student['created_at'])) {
            $stmt_update = $conn->prepare("UPDATE students SET upon_enrollment = ?, enrollment_date = ? WHERE id = ?");
            $stmt_update->bind_param("dsi", $tuition_amount, $current_date, $student_id);
            $stmt_update->execute();
            $stmt_update->close();
        }

        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $student_id);
        exit();
    }
}

$stmt_payments = $conn->prepare("SELECT amount, payment_date FROM tuition_payment WHERE student_id = ?");
$stmt_payments->bind_param("i", $student_id);
$stmt_payments->execute();
$result_payments = $stmt_payments->get_result();
$tuition_payments = [];
while ($row = $result_payments->fetch_assoc()) {
    $tuition_payments[] = $row;
}
$stmt_payments->close();
$conn->close();

$selected_subjects = $student['subjects'];
$totalFees = calculateTotalFees(explode(',', $selected_subjects), $subjectFees);
$totalPayments = array_sum(array_column($tuition_payments, 'amount')) + $student['upon_enrollment'];
$remainingBalance = $totalFees - $totalPayments;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Details</title>
    <style>
        /* General Styles */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 20px;
        }

        /* Container Styles */
        .container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
            max-width: 800px; /* Limit the maximum width */
            margin-left: auto; /* Center the container */
            margin-right: auto; /* Center the container */
        }

        /* Heading Styles */
        h2 {
            color: #007BFF;
            border-bottom: 2px solid #007BFF;
            padding-bottom: 10px;
            font-size: 24px;
        }

        /* Detail Styles */
        .detail {
            margin-bottom: 20px;
            padding: 15px;
            border-bottom: 1px solid #dee2e6;
        }

        /* Label Styles */
        .detail label {
            font-weight: bold;
            color: #495057;
        }

        /* List Styles */
        ul {
            list-style-type: none;
            padding: 0;
        }

        /* Table Styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        /* Table Cell Styles */
        th, td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: left;
        }

        /* Table Header Styles */
        th {
            background-color: #007BFF;
            color: white;
        }

        /* Button Container Styles */
        .button-container {
            margin-top: 25px;
        }

        /* Button Styles */
        .button {
            background-color: #007BFF;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
            font-size: 16px;
        }

        /* Button Hover Styles */
        .button:hover {
            background-color: #0056b3;
        }

        /* Print Button Styles */
        .print-button {
            margin-left: 10px;
        }

        /* Right Align Styles */
        .right-align {
            text-align: right;
        }

        /* Responsive Styles */
        @media (max-width: 600px) {
            .container {
                padding: 15px; /* Reduce padding on smaller screens */
            }
            h2 {
                font-size: 20px; /* Smaller heading on mobile */
            }
            .button {
                width: 100%; /* Full width buttons on mobile */
                margin-bottom: 10px; /* /* Space between buttons */
            }
            th, td {
                padding: 8px; /* Smaller padding in tables */
            }
        }
    </style>
</head>
<body>
<div class="container" id="student-details">
    <h2>Student Details</h2>
    <div class="detail">
        <label>USN:</label> <?= htmlspecialchars($student['usn']) ?> &nbsp; &nbsp; <label>Date:</label> <?= htmlspecialchars($created_at) ?>
    </div>
    <div class="detail">
        <label>Last Name:</label> <?= htmlspecialchars($student['last_name']) ?> &nbsp; &nbsp; <label>First Name:</label> <?= htmlspecialchars($student['first_name']) ?> &nbsp; &nbsp; <label>Middle Name:</label> <?= htmlspecialchars($student['middle_name']) ?>
    </div>
    <div class="detail">
        <label>Address:</label> <?= htmlspecialchars($student['address']) ?>
    </div>
    <div class="detail">
        <label>Email:</label> <?= htmlspecialchars($student['email']) ?> &nbsp; &nbsp; <label>Contact:</label> <?= htmlspecialchars($student['contact']) ?> &nbsp; &nbsp; <label>LRN:</label> <?= htmlspecialchars($student['lrn']) ?>
    </div>
    <div class="detail">
        <label>Date of Birth:</label> <?= htmlspecialchars($student['dob']) ?> &nbsp; &nbsp; <label>Place of Birth:</label> <?= htmlspecialchars($student['pob']) ?>
    </div>
    <div class="detail">
        <label>Age:</label> <?= htmlspecialchars($student['age']) ?> &nbsp; &nbsp; <label>Sex:</label> <?= htmlspecialchars($student['sex']) ?> &nbsp; &nbsp; <label>Civil Status:</label> <?= htmlspecialchars($student['civil_status']) ?>
    </div>
    <div class="detail">
        <label>Guardian Name:</label> <?= htmlspecialchars($student['guardian_name']) ?> &nbsp; &nbsp; <label>Guardian Contact:</label> <?= htmlspecialchars($student['guardian_contact']) ?>
    </div>
    <div class="detail">
        <label>Course ID:</label> <?= htmlspecialchars($student['course_id']) ?> &nbsp; &nbsp; <label>Year Level:</label> <?= htmlspecialchars($student['year_level']) ?>
        <label>Selected Subjects:</label>
        <ul>
            <?php
            if (!empty($selected_subjects)) {
                $subjects = explode(',', $selected_subjects);
                foreach ($subjects as $subject) {
                    echo '<li>' . htmlspecialchars(trim($subject)) . '</li>';
                }
            } else {
                echo '<li>No subjects found.</li>';
            }
            ?>
        </ul>
    </div>
</div>

<div class="container" id="subject-details">
    <h2>Subject Details</h2>
    <div class="detail">
        <label>Subjects details :</label>
        <table>
            <thead>
            <tr>
                <th>Subject</th>
                <th>Fees</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if (!empty($selected_subjects)) {
                $subjects = explode(',', $selected_subjects);
                foreach ($subjects as $subject) {
                    $subject = trim($subject);
                    if (!empty($subject)) {
                        $fee = isset($subjectFees[$subject]) ? '₱' . number_format($subjectFees[$subject], 2) : 'N/A';
                        echo '<tr><td>' . htmlspecialchars($subject) . '</td><td>' . $fee . '</td></tr>';
                    }
                }
                echo '<tr><td>Total Fees:</td><td>₱' . number_format($totalFees, 2) . '</td></tr>';
                echo '<tr><td colspan="2">
                        <form method="POST" action="">
                            <label for="tuition">Enter Tuition Amount:</label>
                            <input type="number" id="tuition" name="tuition" placeholder="Enter tuition amount" min="0" step="0.01" required>
                            <button type="submit" class="button">Submit Payment</button>
                        </form>
                    </td></tr>';
            } else {
                echo '<tr><td colspan="2">No subjects found.</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</div>

<div class="container" id="tuition-payment-history">
    <h2>Tuition Payment History</h2>
    <table>
        <thead>
        <tr>
            <th>Payment Date</th>
            <th>Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if (!empty($tuition_payments)) {
            foreach ($tuition_payments as $payment) {
                echo '<tr><td>' . htmlspecialchars($payment['payment_date']) . '</td><td>₱' . number_format($payment['amount'], 2) . '</td></tr>';
            }
        } else {
            echo '<tr><td colspan="2">No payment history found.</td></tr>';
        }
        ?>
        <tr>
            <td><strong>Remaining Balance:</strong></td>
            <td><strong>₱<?php echo number_format($remainingBalance, 2); ?></strong></td>
        </tr>
        </tbody>
    </table>
    <div class="button-container">
        <a href="<?= htmlspecialchars(strtolower($course_id)) ?>.php?course_id=<?= htmlspecialchars($course_id) ?>" class="button">Back to <?= htmlspecialchars($course_id) ?> Students</a>
        <button class="button print-button" onclick="printSection('student-details')">Print Student Details</button>
        <button class="button print-button" onclick="printSection('subject-details')">Print Subject Details</button>
        <a href="reciept.php?id=<?= htmlspecialchars($student_id) ?>" class="button print-button">Print Receipt</a>
        <br>
        <br>
        <button class="button print-button" onclick="printSection('tuition-payment-history')">Print tuition-payment-history</button>
    </div>
</div>
<script>
    function printSection(sectionId) {
        var printContents = document.getElementById(sectionId).innerHTML;
        var originalContents = document.body.innerHTML;

        document.body.innerHTML = printContents;
        window.print();
        document.body.innerHTML = originalContents;
        location.reload(); // Reload the page to restore the original content
    }
</script>
</body>
</html>