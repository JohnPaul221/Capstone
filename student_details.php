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
    <title>Student Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #e9ecef;
            margin: 0;
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .page {
            background: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 800px;
            margin: 20px 0;
        }
        h2 {
            text-align: center;
            color: #343a40;
        }
        .detail {
            margin: 10px 0;
            padding: 10px;
            border-bottom: 1px solid #ced4da;
        }
        label {
            font-weight: bold;
            color: #495057;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ced4da;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            padding: 10px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .right-align {
            text-align: right;
        }
        .button-container {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }
    </style>
    <script>
        function printPage() {
            window.print();
        }
        function printReceipt() {
            var receiptContent = document.getElementById('receipt-section').innerHTML;
            var originalContent = document.body.innerHTML;

            document.body.innerHTML = receiptContent;
            window.print();
            document.body.innerHTML = originalContent;
        }
    </script>
</head>
<body>
<div class="page">
    <h2>Student Details</h2>
    <div class="detail">
        <label>USN:</label> <?= htmlspecialchars($student['usn']) ?>
    </div>
    <div class="detail">
        <label>Last Name:</label> <?= htmlspecialchars($student['last_name']) ?>
    </div>
    <div class="detail">
        <label>First Name:</label> <?= htmlspecialchars($student['first_name']) ?>
    </div>
    <div class="detail">
        <label>Middle Name:</label> <?= htmlspecialchars($student['middle_name']) ?>
    </div>
    <div class="detail">
        <label>Email:</label> <?= htmlspecialchars($student['email']) ?>
    </div>
    <div class="detail">
        <label>Contact:</label> <?= htmlspecialchars($student['contact']) ?>
    </div>
    <div class="detail">
        <label>LRN:</label> <?= htmlspecialchars($student['lrn']) ?>
    </div>
    <div class="detail">
        <label>Date of Birth:</label> <?= htmlspecialchars($student['dob']) ?>
    </div>
    <div class="detail">
        <label>Place of Birth:</label> <?= htmlspecialchars($student['pob']) ?>
    </div>
    <div class="detail">
        <label>Age:</label> <?= htmlspecialchars($student['age']) ?>
    </div>
    <div class="detail">
        <label>Course ID:</label> <?= htmlspecialchars($student['course_id']) ?>
    </div>
    <div class="detail">
        <label>Year Level:</label> <?= htmlspecialchars($student['year_level']) ?>
    </div>
    <div class="detail">
        <label>Upon Enrollment:</label> <?= htmlspecialchars($student['upon_enrollment']) ?>
    </div>
    <div class="detail">
        <label>Sex:</label> <?= htmlspecialchars($student['sex']) ?>
    </div>
    <div class="detail">
        <label>Civil Status:</label> <?= htmlspecialchars($student['civil_status']) ?>
    </div>
    <div class="detail">
        <label>Guardian Name:</label> <?= htmlspecialchars($student['guardian_name']) ?>
    </div>
    <div class="detail">
        <label>Guardian Contact:</label> <?= htmlspecialchars($student['guardian_contact']) ?>
    </div>
    <div class="detail">
        <label>Created At:</label> <?= htmlspecialchars($created_at) ?>
    </div>
    <div class="detail">
        <label>Address:</label> <?= htmlspecialchars($student['address']) ?>
    </div>
</div>
<div class ="page">
    <h2>Subject Details</h2>
    <div class="detail">
        <label>Selected Subjects:</label>
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
                echo '<tr><td>Remaining Balance:</td><td>₱' . number_format($remainingBalance, 2) . '</td></tr>';
                echo '<tr><td colspan="2">
                        <form method="POST" action="">
                            <label for="tuition">Enter Tuition Amount:</label>
                            <input type="number" id="tuition" name="tuition" placeholder="Enter tuition amount" min="0" step="0.01" required>
                            <button type="submit" class="button">Submit Payment</button>
                        </form>
                    </td></tr>';
                echo '<tr><td colspan="2" style="text-align: left;"><strong>Tuition Payment History:</strong></td></tr>';
                echo '<tr><td colspan="2">';
                echo '<table style="width: 100%;">';
                echo '<thead><tr><th style="text-align: left;">Payment Amount</th><th class="right-align">Payment Date</th></tr></thead>';
                echo '<tbody>';
                if ($student['upon_enrollment'] > 0) {
                    echo '<tr><td>₱' . number_format($student['upon_enrollment'], 2) . '</td><td class="right-align">' . date('F j, Y', strtotime($created_at)) . '</td></tr>';
                }
                if (!empty($tuition_payments)) {
                    foreach ($tuition_payments as $payment) {
                        echo '<tr><td>₱' . number_format($payment['amount'], 2) . '</td><td class="right-align">' . date('F j, Y', strtotime($payment['payment_date'])) . '</td></tr>';
                    }
                } else {
                    echo '<tr><td colspan="2">No tuition payments recorded.</td></tr>';
                }
                echo '</tbody></table>';
                echo '</td></tr>';
            } else {
                echo '<tr><td colspan="2">No subjects found.</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
    <div class="detail button-container">
        <a href="<?= htmlspecialchars(strtolower($course_id)) ?>.php?course_id=<?= htmlspecialchars($course_id) ?>" class="button">Back to <?= htmlspecialchars($course_id) ?> Students</a>
        <button class="button print-button" onclick="printPage()">Print Details</button>
        <a href="reciept.php?id=<?= htmlspecialchars($student_id) ?>" class="button print-button">Print Receipt</a>
    </div>
</div>
</body>
</html>