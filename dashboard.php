<?php
require_once 'Config/Database.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sliding Sidebar Example</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
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
            background-color: #ffffff;
            color: black;
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
            color: #333;
        }

        .sidebar ul {
            list-style: none;
            padding: 0;
        }

        .sidebar li {
            margin: 10px 0;
        }

        .sidebar a {
            background-color: #f7f7f7;
            text-decoration: none;
            color: #555;
            display: flex;
            align-items: center;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
            border: 1px solid transparent;
        }

        .sidebar a:hover {
            background-color: #eaeaea;
            color: #000;
        }

        .sidebar a i {
            margin-right: 10px;
        }

        #openBtn {
            margin: 20px;
            font-size: 24px;
            background: none;
            border: none;
            color: #111;
            cursor: pointer;
        }

        #closeBtn {
            background: none;
            border: none;
            color: #111;
            cursor: pointer;
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 24px;
        }

        #newStudentBtn {
            position: absolute;
            top: 20px;
            right: 20px;
            font-size: 16px;
            background-color: #4CAF50; /* Green background */
            color: white; /* White text */
            border: none; /* No border */
            padding: 10px 15px; /* Padding */
            border-radius: 5px; /* Rounded corners */
            cursor: pointer; /* Pointer cursor on hover */
        }
    </style>
</head>
<body>
<div id="sidebar" class="sidebar">
    <button id="closeBtn"><i class=" fa-solid fa-xmark"></i></button>
    <h2>Menu</h2>
    <ul>
        <li><a href="BSCS.php">BSCS</a></li>
        <li><a href="BSENTREP.php">BSENTEP</a></li>
        <li><a href="BSAIS.php">BSAIS</a></li>
        <li><a href="ACT.php">ACT</a></li>
    </ul>
</div>


<div id="main">
    <button id="openBtn"><i class="fa-solid fa-bars"></i></button>
    <a id="newStudentBtn" href="Enrollment_form.php">+ New Student</a>
</div>

<script>
    document.getElementById("openBtn").onclick = function() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.add("open");
        sidebar.style.display = "block";
    };

    document.getElementById("closeBtn").onclick = function() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.remove("open");
        sidebar.style.display = "none";
    };
</script>
</body>
</html>