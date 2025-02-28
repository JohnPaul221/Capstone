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
        <link rel="stylesheet" href="styles.css">
        <style>
            body {
                font-family: 'Arial', sans-serif;
                background-color: #f4f4f4; /* Light gray background for the body */
                margin: 0;
                color: #333;
            }

            .container {
                background: rgba(255, 255, 255, 0.9);
                padding: 40px;
                border-radius: 10px;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
                width: 400px;
                margin: auto; /* Center the container */
                transition: transform 0.3s;
            }

            .container:hover {
                transform: scale(1.02);
            }

            .container h2 {
                text-align: center;
                margin-bottom: 20px;
                font-size: 24px;
                color: #ff0000; /* Red color for the heading */
            }

            .detail {
                margin: 10px 0;
                padding: 10px;
                border-bottom: 1px solid #e0e0e0;
            }

            .detail label {
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
                background-color: #007bff; /* Blue background for table headers */
                color: white; /* White text for table headers */
            }

            .total-fee, .payment-enrollment, .remaining-balance {
                font-weight: bold;
                background-color: #ffcccb; /* Light red background for total fees */
            }

            .tuition-input {
                margin: 20px 0;
                padding: 10px;
                background-color: #e7f3ff; /* Light blue background for tuition input */
                border: 1px solid #007bff; /* Blue border */
                border-radius: 5px;
            }

            .tuition-input input {
                width: 100%;
                padding: 12px;
                margin: 10px 0;
                border: 1px solid #007bff;
                border-radius: 5px;
                font-size: 16px;
                transition: border-color 0.3s;
            }

            .tuition-input input:focus {
                border-color: #ff0000; /* Change border color on focus */
                outline: none;
            }

            .submit-button {
                margin-top: 10px;
            }

            .button {
                width: 100%;
                padding: 12px;
                background-color: #007bff; /* Blue background for buttons */
                color: white;
                border: none;
                border-radius: 5px;
                font-size: 16px;
                cursor: pointer;
                transition: background-color 0.3s;
            }

            .button:hover {
                background-color: #0056b3; /* Darker blue on hover */
            }

            .tuition-history {
                margin-top: 20px;
            }

            .tuition-history h3 {
                color: #007bff; /* Blue color for tuition history heading */
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
                    echo '<tr class="total-fee"><td>Total Fees:</td><td id="total-fees">₱' . number_format($totalFees, 2) . '</td></tr>';
                    echo '<tr class="payment-enrollment"><td>Payment Upon Enrollment:</td><td id="payment-upon-enrollment">₱' . number_format($payment_upon_enrollment, 2) . '</td></tr>';
                    echo '<tr class="remaining-balance"><td>Remaining Balance:</td><td id="remaining-balance"> ₱' . number_format($remainingBalance, 2) . '</td></tr>';
                    echo '<tr><td colspan="2" class="tuition-input"><label for="tuition">Enter Tuition Amount:</label>
                        <form method="POST" action="">
                            <input type="number" id="tuition" name="tuition" placeholder="Enter tuition amount" min="0" step="0.01">
                            <div class="submit-button">
                                <button type="submit" class="button">Submit Payment</button>
                            </div>
                        </form>
                    </td></tr>';
                    // Tuition Payment History Section
                    echo '<tr><td colspan="2" class="tuition-history"><strong>Tuition Payment History</strong></td></tr>';
                    echo '<tr><td colspan="2">
                            <table style="width: 100%; border-collapse: collapse;">
                                <thead>
                                    <tr>
                                        <th>Tuition Amount</th>
                                    </tr>
                                </thead>
                                <tbody>';
                    if (!empty($tuition_payments)) {
                        foreach ($tuition_payments as $payment) {
                            echo '<tr><td>₱' . number_format($payment, 2) . '</td></tr>';
                        }
                    } else {
                        echo '<tr><td>No tuition payments recorded.</td></tr>';
                    }
                    echo '        </tbody>
                            </table>
                        </td></tr>';
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