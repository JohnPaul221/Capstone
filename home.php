    <?php
    global $conn;
    require_once 'Config/Database.php';
    $courses = ['BSCS', 'BSENTREP', 'BSAIS', 'ACT'];
    $student_counts = [];

    foreach ($courses as $course_id) {
        $query = "SELECT COUNT(*) as student_count FROM students WHERE course_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $student_counts[$course_id] = $row['student_count'];

        // Close the statement
        $stmt->close();
    }
    $conn->close();
    $current_date = date("l, F j, Y");
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Home - Student Count</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <style>
            body {
                background-color: #f8f9fa;
                transition: margin-left 0.3s ease;
            }
            #main {
                padding: 20px;
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
            .course-box {
                background-color: #ffffff;
                border: 1px solid #ddd;
                border-radius: 8px;
                padding: 20px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
                text-align: center;
                min-height: 150px;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }
            .course-box:hover {
                transform: translateY(-5px);
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            }
            .course-box h3 {
                margin: 0 0 10px;
                color: #333;
                font-size: 1.5em;
            }
            .course-box p {
                margin: 0;
                color: #555;
                font-size: 1.2em;
            }
        </style>
    </head>
    <body>
    <div id="sidebar" class="sidebar">
        <button id="closeBtn"><i class="fa-solid fa-xmark"></i></button>
        <h2>Menu</h2>
        <ul>
            <li><a href="home.php">Home</a></li>
            <li><a href="BSCS.php">BSCS</a></li>
            <li><a href="BSENTREP.php">BSENTEP</a></li>
            <li><a href="BSAIS.php">BSAIS</a></li>
            <li><a href="ACT.php">ACT</a></li>
        </ul>
    </div>
    <div id="main" class="container">
        <button id="openBtn" class="btn btn-outline-primary"><i class="fa-solid fa-bars"></i></button>
        <h2 class="mt-4">Welcome to the Home Page</h2>
        <p>Today is <?php echo htmlspecialchars($current_date); ?></p>

        <div class="row course-counts">
            <?php foreach ($courses as $course_id): ?>
                <div class="col-md-3 mb-4">
                    <div class="course-box p-3">
                        <h3><?php echo htmlspecialchars($course_id); ?></h3>
                        <p>Student Count: <?php echo htmlspecialchars($student_counts[$course_id]); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.getElementById("openBtn").onclick = function() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.add("open");
            document.getElementById("main").style.marginLeft = "250px";
        };
        document.getElementById("closeBtn").onclick = function() {
            const sidebar = document.getElementById("sidebar");
            sidebar.classList.remove("open");
            document.getElementById("main").style.marginLeft = "0";
        };
    </script>
    </body>
    </html>