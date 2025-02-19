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
            background-color: #f4f4f4; /* Light background color for contrast */
        }

        #main {
            padding: 20px;
            transition: margin-left 0.3s ease;
        }

        .sidebar {
            display: none; /* Hide by default */
            height: 100%;
            width: 250px; /* Increased width for sidebar */
            position: fixed;
            top: 0;
            left: -250px; /* Initially hidden off-screen */
            background-color: #ffffff; /* White background for sidebar */
            color: black;
            transition: left 0.3s ease; /* Sliding effect */
            box-shadow: 2px 0 5px rgba(0,0,0,0.5); /* Shadow for depth */
            padding: 20px; /* Spacing inside the sidebar */
        }
        .sidebar.open {
            display: block; /* Show when open */
            left: 0; /* Slide in */
        }

        .sidebar h2 {
            margin-top: 0;
            color: #333; /* Darker text color for headings */
        }

        .sidebar ul {
            list-style: none; /* Remove bullet points */
            padding: 0; /* Remove default padding */
        }

        .sidebar li {
            margin: 15px 0; /* Space between items */
        }

        .sidebar a {
            text-decoration: none; /* Remove underline */
            color: #555; /* Default link color */
            display: block; /* Make the link a block element */
            padding: 10px; /* Add some padding */
            border: 2px solid transparent; /* Default border */
            border-radius: 5px; /* Rounded corners */
            transition: border-color 0.3s ease; /* Smooth transition for border color */
        }

        .sidebar a:hover {
            color: #000; /* Change color on hover */
            border-color: #007BFF; /* Change border color on hover */
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
    </style>
</head>
<body>
<div id="sidebar" class="sidebar">
    <button id="closeBtn"><i class="fa-solid fa-xmark"></i></button>
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
</div>
<script>
    document.getElementById("openBtn").onclick = function() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.add("open");
        sidebar.style.display = "block"; // Show sidebar
    };

    document.getElementById("closeBtn").onclick = function() {
        const sidebar = document.getElementById("sidebar");
        sidebar.classList.remove("open");
        sidebar.style.display = "none"; // Hide sidebar
    };
</script>
</body>
</html>