<?php
global $conn;
session_start();
require_once 'Config/Database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = $_POST['first_name'];
    $middle_name = $_POST['middle_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $course_id = $_POST['course'];
    $year_level = $_POST['year_level']; // Capture year level
    $selected_subjects = $_POST['subjects'] ?? []; // Capture selected subjects

    $allowed_courses = ['BSCS', 'BSENTREP', 'BSAIS', 'ACT'];
    if (!in_array($course_id, $allowed_courses)) {
        die("Invalid course");
    }

    // Convert selected subjects to JSON
    $selected_subjects_json = json_encode($selected_subjects);

    // Check for JSON encoding errors
    if ($selected_subjects_json === false) {
        die("JSON encoding error: " . json_last_error_msg());
    }

    $stmt = $conn->prepare("INSERT INTO students (first_name, middle_name, last_name, email, contact, course_id, year_level, selected_subjects) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssssss", $first_name, $middle_name, $last_name, $email, $contact, $course_id, $year_level, $selected_subjects_json);

    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Enrollment successful!";
        header("Location: student_details.php?id=" . $conn->insert_id); // Redirect to student details page
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
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .enrollment-container {
            background: rgba(255, 255, 255, 0.9);
            padding: 50px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 400px;
        }
        .enrollment-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .enrollment-container input,
        .enrollment-container select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .enrollment-container button {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .enrollment-container button:hover {
            background-color: #0056b3;
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            color: green;
        }
        .subject-list {
            list-style-type: none;
            padding-left: 0;
        }
        .subject-list li {
            padding: 8px 0;
            border-bottom: 1px solid #ddd;
        }
    </style>
</head>
<body>

<div class="enrollment-container">
    <h2>Enrollment Form</h2>

    <?php
    if (isset($_SESSION['success_message'])) {
        echo '<div class="message">' . $_SESSION['success_message'] . '</div>';
        unset($_SESSION['success_message']);
    }
    if (isset($_SESSION['error_message'])) {
        echo '<div class="message" style="color: red;">' . $_SESSION['error_message'] . '</div>';
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

        <div id="subjects-display"></div>
        <input type="hidden" name="subjects" id="selected-subjects" value="">
        <button type="submit">Enroll</button>
    </form>
</div>
<script>
    const subjectsByCourse = {
        'BSCS': {
            'First Year': [
                { name: 'Euthenics 2', price: 1500 },
                { name: 'Computer Programming 2 (Lab)', price: 1200 },
                { name: 'Computer Programming 2 (Lec)', price: 1000 },
                { name: 'Math in the Modern World', price: 1800 },
                { name: 'National Service Training Program 2', price: 800 },
                { name: 'PATHFIT 2', price: 3000 },
                { name: 'Ethics', price: 4000},
                { name: 'Discrete Structure 1', price: 3000},
            ],
            'Second Year': [
                { name: 'Data Communication and Networking 2', price: 3000 }
            ]
        },
        // Add other courses similarly...
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

        selectedSubjectsInput.value = JSON.stringify(selectedSubjects);
    }
    yearSelect.addEventListener('change', updateSubjectsDisplay);
    courseSelect.addEventListener('change', updateSubjectsDisplay);
</script>

</body>
</html>