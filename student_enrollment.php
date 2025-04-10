<?php
session_start();
require_once 'Config/Database.php'; // Ensure this file connects to your database
global $conn;

$success_message = '';
$error_message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve input values
    $usn = filter_var(trim($_POST['usn']), FILTER_SANITIZE_STRING);
    $first_name = filter_var(trim($_POST['first_name']), FILTER_SANITIZE_STRING);
    $middle_name = filter_var(trim($_POST['middle_name']), FILTER_SANITIZE_STRING);
    $last_name = filter_var(trim($_POST['last_name']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
    $contact = filter_var(trim($_POST['contact']), FILTER_SANITIZE_STRING);
    $lrn = isset($_POST['lrn']) ? filter_var(trim($_POST['lrn']), FILTER_SANITIZE_STRING) : null;

    // Check if course_id is set and not empty
    $course_id = isset($_POST['course_id']) ? $_POST['course_id'] : null;
    if (empty($course_id)) {
        $error_message = "Course ID is required.";
    }

    // Validate year level and address
    $year_level = filter_var(trim($_POST['year_level']), FILTER_SANITIZE_STRING);
    $address = filter_var(trim($_POST['address']), FILTER_SANITIZE_STRING);
    if (empty($year_level)) {
        $error_message = "Year level is required.";
    } elseif (empty($address)) {
        $error_message = "Address is required.";
    }

    // Validate date of birth
    $dob = filter_var(trim($_POST['dob']), FILTER_SANITIZE_STRING);
    if (empty($dob)) {
        $error_message = "Date of birth is required.";
    }

    // Validate place of birth
    $pob = filter_var(trim($_POST['pob']), FILTER_SANITIZE_STRING);
    if (empty($pob)) {
        $error_message = "Place of birth is required.";
    }

    // Validate selected subjects
    $selectedSubjects = isset($_POST['subjects']) ? $_POST['subjects'] : [];
    if (empty($selectedSubjects)) {
        $error_message = "At least one subject must be selected.";
    }

    // Retrieve the payment upon enrollment
    $upon_enrollment = filter_var(trim($_POST['payment_upon_enrollment']), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Capture additional fields
    $age = filter_var(trim($_POST['age']), FILTER_SANITIZE_NUMBER_INT);
    $sex = filter_var(trim($_POST['sex']), FILTER_SANITIZE_STRING);
    $civil_status = filter_var(trim($_POST['civil_status']), FILTER_SANITIZE_STRING);

    // Allow guardian fields to be optional
    $guardian_name = !empty($_POST['guardian_name']) ? filter_var(trim($_POST['guardian_name']), FILTER_SANITIZE_STRING) : NULL;
    $guardian_contact = !empty($_POST['guardian_contact']) ? filter_var(trim($_POST['guardian_contact']), FILTER_SANITIZE_STRING) : NULL;
    $created_at = date('Y-m-d H:i:s'); // Set the created_at timestamp

    // Prepare the SQL statement for inserting into the students table
    if (empty($error_message)) {
        $stmt = $conn->prepare("INSERT INTO students (usn, last_name, first_name, middle_name, email, contact, lrn, dob, pob, age, course_id, year_level, upon_enrollment, sex, civil_status, guardian_name, guardian_contact, created_at, address, subjects) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        // Check if the statement was prepared successfully
        if ($stmt === false) {
            $error_message = "Error preparing statement: " . $conn->error;
        } else {
            // Concatenate selected subjects into a string
            $selectedSubjectsString = implode(',', $selectedSubjects);

            // Bind parameters, including the optional guardian fields
            $stmt->bind_param("sssssssssissssssssss",
                $usn, $last_name, $first_name, $middle_name, $email, $contact, $lrn, $dob, $pob, $age, $course_id, $year_level, $upon_enrollment, $sex, $civil_status, $guardian_name, $guardian_contact, $created_at, $address, $selectedSubjectsString);

            // Execute the statement
            if ($stmt->execute()) {
                $success_message = "Student record added successfully.";
            } else {
                $error_message = "Error executing statement: " . $stmt->error;
            }
        }
        // Close the statement
        $stmt->close();
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styles.css">
    <title>Application Form</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1, h2 {
            text-align: center;
        }

        fieldset {
            border: 1px solid #cccccc;
            border-radius: 5px;
            margin: 15px 0;
            padding: 10px;
        }

        legend {
            font-weight: bold;
        }

        label {
            display: block;
            margin: 10px 0 5px;
        }

        input[type="text"],
        input[type="date"],
        input[type="email"],
        input[type="number"],
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            border: 1px solid #cccccc;
            border-radius: 5px;
        }

        .name-container,
        .contact-container,
        .dob-pob-container,
        .age-sex-civil-container,
        .parent-guardian-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .contact-container div,
        .dob-pob-container div,
        .age-sex-civil-container div,
        .parent-guardian-container div {
            flex: 1;
            margin-right: 10px;
        }

        .contact-container div:last-child,
        .dob-pob-container div:last-child,
        .age-sex-civil-container div:last-child,
        .parent-guardian-container div:last-child {
            margin-right: 0;
        }

        button {
            width: 100%;
            padding: 10px;
            background: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }

        button:hover {
            background: #0056b3;
        }

        .education-container {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #cccccc;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .container {
            background-color: #ffffff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .other-input {
            display: none; /* Initially hidden */
        }

        .course-year-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
        }

        .error-message {
            color: red;
            margin-bottom: 15px;
        }

        .success-message {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
<h1>ACLC COLLEGE</h1>
<h2>SENIOR HIGH SCHOOL APPLICATION FORM</h2>
    <p>Fill out completely</p>

    <form action="" method="POST">
        <?php if (!empty($success_message)): ?>
            <div class="success-message">
                <?php echo $success_message; ?>
            </div>
        <?php endif; ?>
        <?php if (!empty($error_message)): ?>
            <div class="error-message">
                <?php echo $error_message; ?>
            </div>
        <?php endif; ?>

        <fieldset>
            <legend>PERSONAL INFORMATION:</legend>
            <label for="usn">USN No:</label>
            <input type="text" id="usn" name="usn" required>

            <label for="name">NAME:</label>
            <div class="name-container">
                <input type="text" id="last-name" name="last_name" placeholder="Last" required>
                <input type="text" id="first-name" name="first_name" placeholder="First" required>
                <input type="text" id="middle-name" name="middle_name" placeholder="Middle" required>
            </div>

            <label for="address">ADDRESS:</label>
            <input type="text" id="address" name="address" required>

            <div class="contact-container">
                <div>
                    <label for="email">E-Mail Address:</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div>
                    <label for="contact">Contact No:</label>
                    <input type="text" id="contact" name="contact" required>
                </div>
                <div>
                    <label for="lrn">LRN:</label>
                    <input type="text" id="lrn" name="lrn" required>
                </div>
            </div>

            <div class="dob-pob-container">
                <div>
                    <label for="dob">Date of Birth :</label>
                    <input type="date" id="dob" name="dob" required>
                </div>
                <div>
                    <label for="pob">Place of Birth:</label>
                    <input type="text" id="pob" name="pob" required>
                </div>
            </div>

            <div class="age-sex-civil-container">
                <div>
                    <label for="age">Age:</label>
                    <input type="number" id="age" name="age" required>
                </div>
                <div>
                    <label for="sex">Sex:</label>
                    <select id="sex" name="sex" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select>
                </div>
                <div>
                    <label for="civil-status">Civil Status:</label>
                    <select id="civil-status" name="civil_status" required>
                        <option value="single">Single</option>
                        <option value="married">Married</option>
                        <option value="widowed">Widowed</option>
                    </select>
                </div>
            </div>

            <div class="parent-guardian-container">
                <div>
                    <label for="guardian-name">Guardian's Name:</label>
                    <input type="text" id="guardian-name" name="guardian_name" required>
                </div>
                <div>
                    <label for="guardian-contact">Guardian's Contact No:</label>
                    <input type="text" id="guardian-contact" name="guardian_contact" required>
                </div>
            </div>
        </fieldset>

        <div class="education-container">
            <fieldset>
                <legend>EDUCATIONAL BACKGROUND:</legend>
                <table>
                    <thead>
                    <tr>
                        <th>School Name</th>
                        <th>Year Graduated</th>
                        <th>Address</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><input type="text" name="school_name_1" required></td>
                        <td><input type="text" name="year_graduated_1" required></td>
                        <td><input type="text" name="address_1" required></td>
                    </tr>
                    <tr>
                        <td><input type="text" name="school_name_2"></td>
                        <td><input type="text" name="year_graduated_2"></td>
                        <td><input type="text" name="address_2"></td>
                    </tr>
                    </tbody>
                </table>
            </fieldset>

            <fieldset>
                <legend>How did you happen to know about ACLC College of Iriga?</legend>
                <div style="display: flex; justify-content: space-between ;">
                    <div>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-1')"> Newspaper Ads</label><br>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-2')"> Friends/Relatives</label>
                    </div>
                    <div>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-3')"> Radio Ads</label><br>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-4')"> TV Ads</label>
                    </div>
                    <div>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-5')"> ACLC Students/Graduates</label><br>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-6')"> Others (please specify):</label>
                        <input type="text" id="other-input-6" class="other-input" placeholder="Specify...">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>What made you decide to enroll ACLC College of Iriga, Inc.?</legend>
                <div style="display: flex; justify-content: space-between;">
                    <div>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-7')">Parents</label><br>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-8')"> Other Relatives</label>
                    </div>
                    <div>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-9')"> Friends</label><br>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-10')"> School Images</label>
                    </div>
                    <div>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-11')"> Advertisements</label><br>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-12')"> Proximity of Place</label>
                    </div>
                    <div>
                        <label><input type="checkbox" onclick="toggleOtherInput(this, 'other-input-13')"> Others (please specify):</label>
                        <input type="text" id="other-input-13" class="other-input" placeholder="Specify...">
                    </div>
                </div>
            </fieldset>

            <fieldset>
                <legend>What Course do you intend to enroll?</legend>
                <div class="course-year-container">
                    <select name="course_id" required>
                        <option value="" disabled selected>Select Course:</option>
                        <option value="BSCS">BS Computer Science (BSCS)</option>
                        <option value="BSENTREP">Bachelor of Science in Entrepreneurship (BSENTREP)</option>
                        <option value="BSAIS">Bachelor of Science in Accounting Information System (BSAIS)</option>
                        <option value="ACT">Associate in Computer Technology (ACT)</option>
                    </select>
                    <select name="year_level" required>
                        <option value="" disabled selected>Select Year Level:</option>
                        <option value="First Year">First Year</option>
                        <option value="Second Year">Second Year</option>
                        <option value="Third Year">Third Year</option>
                        <option value="Fourth Year">Fourth Year</option>
                    </select>
                </div>
                <input type="number" name="payment_upon_enrollment" placeholder="Upon Enrollment (₱):" required min="0" step="0.01">

                <div id="subjects-display" style="display: none;">
                    <h4>
                        Subjects for <span id="course-year-title"></span>
                        <span class="close-icon" onclick="closeSubjects()">&times;</span>
                    </h4>
                    <div id="subject-list-content"></div>
                </div>
                <input type="hidden" name="subjects[]" id="selected-subjects" value="">
                <button type="submit">Enroll</button>
            </fieldset>
        </div>
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
            'BSENTREP': {
                'First Year': [
                    { name: 'Introduction to Entrepreneurship' },
                    { name: 'Business Mathematics' },
                    { name: 'Fundamentals of Marketing' },
                    { name: 'Business Communication' },
                ],
                'Second Year': [
                    { name: 'Entrepreneurial Finance' },
                    { name: 'Operations Management' }
                ]
            },
            'BSAIS': {
                'First Year': [
                    { name: 'Introduction to Accounting' },
                    { name: 'Business Law' },
                    { name: 'Financial Management' },
                ],
                'Second Year': [
                    { name: 'Cost Accounting' },
                    { name: 'Management Accounting' }
                ]
            },
            'ACT': {
                'First Year': [
                    { name: 'Computer Fundamentals' },
                    { name: 'Introduction to Programming' },
                ],
                'Second Year': [
                    { name: 'Web Development' },
                    { name: 'Database Management' }
                ]
            }
        };

        const yearSelect = document.querySelector('select[name="year_level"]');
        const courseSelect = document.querySelector('select[name="course_id"]');
        const subjectsDiv = document.getElementById('subjects-display');
        const subjectListContent = document.getElementById('subject-list-content');
        const selectedSubjectsInput = document.getElementById('selected-subjects');
        const courseYearTitle = document.getElementById('course-year-title');

        function updateSubjectsDisplay() {
            const selectedCourse = courseSelect.value;
            const selectedYear = yearSelect.value;

            if (selectedCourse && selectedYear) {
                const subjects = subjectsByCourse[selectedCourse]?.[selectedYear] || [];
                courseYearTitle.textContent = `${selectedYear} (${selectedCourse})`;
                subjectListContent.innerHTML = `
            <ul class="subject-list">
                ${subjects.map((subject, index) => `
                    <li>
                        <input type="checkbox" id="subject-${index}" value="${subject.name}" onchange="updateSelectedSubjects()">
                        <label for="subject-${index}">${subject.name}</label>
                    </li>
                `).join('')}
            </ul>
        `;
                subjectsDiv.style.display = 'block';
            } else {
                subjectListContent.innerHTML = '';
                subjectsDiv.style.display = 'none';
            }
        }

        function updateSelectedSubjects() {
            const checkboxes = subjectListContent.querySelectorAll('input[type="checkbox"]');
            const selectedSubjects = [];

            checkboxes.forEach(checkbox => {
                if (checkbox.checked) {
                    selectedSubjects.push(checkbox.value);
                }
            });
            selectedSubjectsInput.value = selectedSubjects.join(',');
        }

        function closeSubjects() {
            subjectsDiv.style.display = 'none';
        }

        yearSelect.addEventListener('change', updateSubjectsDisplay);
        courseSelect.addEventListener('change', updateSubjectsDisplay);
    </script>
</body>
</html>