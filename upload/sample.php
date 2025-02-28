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

// Define subject fees in Pesos
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

// Initialize submitted tuition amount
$submitted_tuition = 0;

// Handle tuition payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tuition'])) {
    $tuition_amount = floatval($_POST['tuition']);
    if ($tuition_amount > 0) {
        $stmt_payment = $conn->prepare("INSERT INTO tuition_payment (student_id, amount) VALUES (?, ?)");
        $stmt_payment->bind_param("id", $student_id, $tuition_amount);
        $stmt_payment->execute();
        $stmt_payment->close();
    }
}

// Retrieve all tuition payments for the student
$stmt_payments = $conn->prepare("SELECT amount FROM tuition_payment WHERE student_id = ?");
$stmt_payments->bind_param("i", $student_id);
$stmt_payments->execute();
$result_payments = $stmt_payments->get_result();

$tuition_payments = [];
while ($row = $result_payments->fetch_assoc()) {
    $tuition_payments[] = $row['amount'];
}

$stmt->close();
$stmt_payments->close();
$conn->close();

// Calculate total fees and payment upon enrollment
$selected_subjects = $student['selected_subjects'];
$payment_upon_enrollment = $student['upon_enrollment'];
$totalFees = calculateTotalFees(explode(',', $selected_subjects), $subjectFees);
$totalPayments = array_sum($tuition_payments) + $payment_upon_enrollment; // Total payments made
$remainingBalance = $totalFees - $totalPayments; // Remaining balance after payments
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }

        .container {
            background: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: auto;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .detail {
            margin: 10px 0;
            padding: 10px;
            border-bottom: 1px solid #e0e0e0;
        }

        label {
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }

        th, td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
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
            width: 100%;
            margin-top: 10px;
        }

        .button:hover {
            background-color: #0056b3;
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

                echo '<tr>
                        <td>Payment Upon Enrollment:</td>
                        <td>₱' . number_format($payment_upon_enrollment, 2) . '</td>
                      </tr>';
                echo '<tr>
                        <td>Total Payments:</td>
                        <td>₱' . number_format($totalPayments, 2) . '</td>
                      </tr>';
                echo '<tr>
                        <td>Tuition Payment History:</td>
                        <td>';

                // Display Tuition Payment History
                if (!empty($tuition_payments)) {
                    echo '<ul>';
                    foreach ($tuition_payments as $payment) {
                        echo '<li>₱' . number_format($payment, 2) . '</li>';
                    }
                    echo '</ul>';
                } else {
                    echo 'No tuition payments recorded.';
                }

                echo '</td></tr>';
            } else {
                echo '<tr><td colspan="2">No subjects found.</td></tr>';
            }
            ?>
            </tbody>
        </table>
    </div>
</div>
</body>
</html>