<?php
session_start();
require_once 'Config/Database.php';

global $conn;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $course_id = $_POST['course'];
    $year_level = $_POST['year_level'];
    $selected_subjects = $_POST['subjects'] ?? [];
    $payment_upon_enrollment = $_POST['payment_upon_enrollment'] ?? 0;

    $allowed_courses = ['BSCS', 'BSENTREP', 'BSAIS', 'ACT'];
    if (!in_array($course_id, $allowed_courses)) {
        $_SESSION['error_message'] = "Invalid course.";
        header("Location: error_page.php");
        exit();
    }

    // Convert selected subjects to a comma-separated string
    $selected_subjects_string = implode(',', $selected_subjects);

    $stmt = $conn->prepare("INSERT INTO students (first_name, middle_name, last_name, email, contact, course_id, year_level, selected_subjects, upon_enrollment) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssss", $first_name, $middle_name, $last_name, $email, $contact, $course_id, $year_level, $selected_subjects_string, $payment_upon_enrollment);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Enrollment successful!";
        header("Location: student_details.php?id=" . $conn->insert_id);
        exit();
    } else {
        $_SESSION['error_message'] = "Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enrollment Form</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right, #007bff, #ffffff);
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            color: #333;
        }
        .enrollment-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.2);
            width: 400px;
            transition: transform 0.3s;
        }
        .enrollment-container:hover {
            transform: scale(1.02);
        }
        .enrollment-container h2 {
            text-align: center;
            margin-bottom: 20px;
            font-size: 24px;
            color: #ff0000;
        }
        .enrollment-container input,
        .enrollment-container select {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: 1px solid #007bff;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        .enrollment-container input:focus,
        .enrollment-container select:focus {
            border-color: #ff0000;
            outline: none;
        }
        .enrollment-container button {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .enrollment-container button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            font-weight: bold;
        }
        .message.success {
            color: green;
        }
        .message.error {
            color: red;
        }
        .subject-list {
            list-style-type: none;
            padding-left: 0;
            margin: 10px 0;
        }
        .subject-list li {
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            background-color: #f9f9f9;
            transition: background-color 0.3s;
            justify-content: space-between;
        }
        .subject-list li:hover {
            background-color: #e9ecef;
        }
        .subject-list input {
            margin-right: 10px;
            accent-color: #007bff;
            transform: scale(1.2);
        }
        .subject-list label {
            font-size: 16px;
            color: #333;
            flex-grow: 1;
            text-align: left;
            width: 100%;
        }
    </style>
</head>
<body>

<div class="enrollment-container">
    <h2>Enrollment Form</h2>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="message success">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="message error">' . $_SESSION['error_message'] . '</div>';
        unset($_SESSION['error_message']);
    }
    ?>

    <form action="" method="POST">
        <input type="text" name="first_name" placeholder="First Name:" required>
        <input type="text" name="middle_name" placeholder="Middle Name:" required>
        <input type="text" name="last_name" placeholder="Last Name:" required>
        <input type="email" name="email" placeholder="Email:" required>
        <input type="text" name="contact" placeholder="Contact Number:" required>

        <select name="year_level" required>
            <option value="" disabled selected>Select Year Level:</option>
            <option value="First Year">First Year</option>
            <option value="Second Year">Second Year</option>
            <option value="Third Year">Third Year</option>
            <option value="Fourth Year">Fourth Year</option>
        </select>

        <select name="course" required>
            <option value="" disabled selected>Select Course:</option>
            <option value="BSCS">BS Computer Science (BSCS)</option>
            <option value="BSENTREP">Bachelor of Science in Entrepreneurship (BSENTREP)</option>
            <option value="BSAIS">Bachelor of Science in Accounting Information System (BSAIS)</option>
            <option value="ACT">Associate in Computer Technology (ACT)</option>
        </select>

        <input type="number" name="payment_upon_enrollment" placeholder="Upon Enrollment (₱):" required min="0" step="0.01">

        <div id="subjects-display"></div>
        <input type="hidden" name="subjects[]" id="selected-subjects" value="">
        <button type="submit">Enroll</button>
    </form>
</div>

<script>
    const subjectsByCourse = {
        'BSCS': {
            'First Year': [
                { name: 'Euthenics 2' },
                { name: 'Computer Programming 2 (Lab)' },
                { name: 'Computer Programming 2 (Lec)' },
                { name: 'Math in the Modern World' },
                { name: 'National Service Training Program 2' },
                { name: 'PATHFIT 2' },
                { name: 'Ethics' },
                { name: 'Discrete Structure 1' },
            ],
            'Second Year': [
                { name: 'Data Communication and Networking 2' }
            ]
        },
        // Add other courses and their subjects here
    };

    const yearSelect = document.querySelector('select[name="year_level"]');
    const courseSelect = document.querySelector('select[name="course"]');
    const subjectsDiv = document.getElementById('subjects-display');
    const selectedSubjectsInput = document.getElementById('selected-subjects');

    function updateSubjectsDisplay() {
        const selectedCourse = courseSelect.value;
        const selectedYear = yearSelect.value;

        if (selectedCourse && selectedYear) {
            const subjects = subjectsByCourse[selectedCourse]?.[selectedYear] || [];
            subjectsDiv.innerHTML = `
                <h4>Subjects for ${selectedYear} (${selectedCourse}):</h4>
                <ul class="subject-list">
                    ${subjects.map((subject, index) => `
                        <li>
                            <input type="checkbox" id="subject-${index}" value="${subject.name}" onchange="updateSelectedSubjects()">
                            <label for="subject-${index}">${subject.name}</label>
                        </li>
                    `).join('')}
                </ul>
            `;
        } else {
            subjectsDiv.innerHTML = '';
        }
    }

    function updateSelectedSubjects() {
        const checkboxes = subjectsDiv.querySelectorAll('input[type="checkbox"]');
        const selectedSubjects = [];

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                selectedSubjects.push(checkbox.value);
            }
        });
        selectedSubjectsInput.value = selectedSubjects.join(',');
    }

    yearSelect.addEventListener('change', updateSubjectsDisplay);
    courseSelect.addEventListener('change', updateSubjectsDisplay);
</script>

</body>
</html>