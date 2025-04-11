<?php
session_start();
require_once 'Config/Database.php';
global $conn;

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Invalid Student ID.");
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
$stmt->close();

$payment_upon_enrollment = $student['upon_enrollment'];
$last_tuition_amount = 0;

// Function to get full name
function getFullName($student) {
    return trim($student['first_name'] . ' ' . $student['middle_name'] . ' ' . $student['last_name']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['tuition'])) {
    $tuition_amount = floatval($_POST['tuition']);
    if ($tuition_amount > 0) {
        $stmt_payment = $conn->prepare("INSERT INTO tuition_payment (student_id, amount) VALUES (?, ?)");
        $stmt_payment->bind_param("id", $student_id, $tuition_amount);
        $stmt_payment->execute();
        $stmt_payment->close();
        $last_tuition_amount = $tuition_amount;
        header("Location: " . $_SERVER['PHP_SELF'] . "?id=" . $student_id);
        exit();
    }
}

$stmt_payments = $conn->prepare("SELECT amount FROM tuition_payment WHERE student_id = ?");
$stmt_payments->bind_param("i", $student_id);
$stmt_payments->execute();
$result_payments = $stmt_payments->get_result();
$tuition_payments = [];
while ($row = $result_payments->fetch_assoc()) {
    $tuition_payments[] = $row['amount'];
}
$stmt_payments->close();
$conn->close();

$totalFees = 0; // Calculate this based on selected subjects if needed
$totalPayments = array_sum($tuition_payments) + $payment_upon_enrollment;
$remainingBalance = $totalFees - $totalPayments;

$currentDate = date("F j, Y"); // Get the current date

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combined Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: 'Courier New', monospace;
            margin: 20px;
            font-size: 8px;
        }
        .container {
            display: flex;
            flex-direction: row;
            justify-content: space-between;
            width: 700px;
            height: auto;
            border: 1px solid #000;
            padding: 10px;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.1);
            gap: 0;
        }
        .invoice {
            width: 50%;
            padding: 5px;
            margin: 0;
            overflow: hidden;
        }
        .invoice-table {
            width: 60%;
            border-collapse: collapse;
            margin-bottom: 6px;
        }
        .invoice-table th, .invoice-table td {
            border: 1.5px solid black;
            padding: 1px;
            text-align: left;
            font-size: 7px;
            height: 12px;
        }
        .total-row {
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 5px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .invoice-header {
            text-align: center;
        }
        .invoice-header h1 {
            font-size: 8px;
            font-weight: bold;
            margin: 0;
        }
        .invoice-header p {
            font-size: 4px;
            margin: 0;
        }
        .details p, .signature p {
            font-size: 7px;
        }
        footer p {
            font-size: 7px;
        }
        .footer-text {
            font-size: 3px;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <div class="invoice">
        <table class="invoice-table">
            <thead>
            <tr>
                <th colspan="2" style="font-size: 9px; text-align: center;">IN SETTLEMENT OF THE FOLLOWING:</th>
            </tr>
            <tr>
                <th style="font-size: 7px;">Invoice No.</th>
                <th style="font-size: 7px;">Amount</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <td style="font-size: 7px;">Tuition Fee</td>
                <td style="font-size: 7px;"></td>
            </tr>
            <tr>
                <td style="font-size: 7px;">Laboratory Fee</td>
                <td style="font-size: 7px;"></td>
            </tr>
            <tr>
                <td style="font-size: 7px;">Internet Fee</td>
                <td style="font-size: 7px;"></td>
            </tr>
            <tr>
                <td style="font-size: 7px;">Add: Miscellaneous</td>
                <td style="font-size: 7px;"></td>
            </tr>
            <tr>
                <td style="font-size: 7px;">Upon Enrollment</td>
                <td style="font-size: 7px;">₱<?= number_format($payment_upon_enrollment, 2) ?></td>
            </tr>
            <tr>
                <td style="font-size: 7px;">Total Sales</td>
                <td style="font-size: 7px;"></td>
            </tr>
            <tr>
                <td style="font-size: 7px;">Less: SC/PWD Discount</td>
                <td style="font-size: 7px;"></td>
            </tr>
            <tr class="total-row">
                <td style="font-size: 7px;">Total Due</td>
                <td style="font-size: 7px;"></td>
            </tr>
            <tr class="total-row">
                <td style="font-size: 7px;">Total Payment</td>
                <td style="font-size: 7px;"></td>
            </tr>
            <tr class="total-row">
                <td style="font-size: 7px;">Change</td>
                <td style="font-size: 7px;"></td>
            </tr>
            <tr>
                <th style="font-size: 7px;">Name of Bank Check/Warrant No.</th>
                <th style="font-size: 7px;">Date</th>
            </tr>
            <tr>
                <td style="font-size: 7px;"></td>
                <td style="font-size: 7px;"><?= $currentDate ?></td>
            </tr>
            <tr>
                <td style="font-size: 7px;"></td>
                <td style="font-size: 7px;"></td>
            </tr>
            </tbody>
        </table>

        <footer>
            <div class="footer-text">200 Bklits. (50x4) 0001 - 10000</div>
            <div class="footer-text">BIR Authority to Print No: 066AU20240000002764</div>
            <div class="footer-text">Date Issued: NOVEMBER 26, 2024</div>
            <div class="footer-text">PRIME DIGITAL PRINT CENTER, Panganiban Drive, Naga City</div>
            <div class="footer-text">TIN: 192-763-918-00000</div>
        </footer>
    </div>

    <div class="invoice">
        <div class="invoice-header">
            <h1 class="invoice-title">K & M Management Information Technology, Inc.</h1>
            <p class="invoice-address">2/F Jasaca Center, Highway-I, San Miguel, Iriga City, 4431 Iriga City, Cam. Sur</p>
            <p class="invoice-address">Non-VAT Reg. TIN: 006-358-290-00001</p>
        </div>

        <h2 style="font-size: 9px; text-align: left; font-weight: bold;">SERVICE INVOICE</h2>

        <div class="details">
            <p style="text-align: right; margin-top: -5px;">Date: <?= $currentDate ?>
                      <br>_____________________</p>
            <p>RECEIVED from: <?= getFullName($student) ?>
                ____________________________________________</p>
            <p>with TIN: ___________________________</p>
            <p>with address at: _____________________</p>
            <p>the sum of pesos: <?= number_format($payment_upon_enrollment, 2) ?>
                <br>________________________________</p>
            <p>as partial/full payment of: ____________</p>
        </div>

        <div class="signature">
            <p style="text-align: right; margin-bottom: -10px;">By: _____________________________</p>
            <br>
            <p style="text-align: right;">Cashier/Authorized Representative</p>
        </div>
<br>

        <footer class="text-center mt-2">
            <p style="font-size: 7px;">Printer's Accreditation No: 064MP2024000000012</p>
            <p style="font-size: 7px;">Date Issued: <?= $currentDate ?> | Expiry Date: June 5, 2029</p>
            <p style="font-size: 7px;">"THIS DOCUMENT IS NOT VALID FOR CLAIMING INPUT TAXES"</p>
        </footer>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.0.7/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>