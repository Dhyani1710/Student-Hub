<?php
require_once "auth.php";
requireRole("student");

$studentName = trim(
    ($_SESSION["fname"] ?? "") . " " .
    ($_SESSION["sname"] ?? "")
);

$studentUsername = $_SESSION["username"] ?? "";
echo "<pre>";

echo "Logged-in Student ID: ";
echo htmlspecialchars((string)($_SESSION["student_id"] ?? "Missing"));

echo "\nFirst Name: ";
echo htmlspecialchars($_SESSION["fname"] ?? "Missing");

echo "\nSurname: ";
echo htmlspecialchars($_SESSION["sname"] ?? "Missing");

echo "\nUsername: ";
echo htmlspecialchars($_SESSION["username"] ?? "Missing");

echo "</pre>";
?>




<html>

<head>
    <title> Home Page </title>
    <link rel="stylesheet" href="Dashboard.css">

</head>

<body>
    

    <div class="container">

        
        <div class="sidebar">

            <div class="logo">
                <h2><img src="data:image/png;base64,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" alt="Logo" width="150">
</h2>
            </div>
            
            <?php if (($_SESSION["role"] ?? "") === "admin"): ?>
                    <a href="StudentManagement.php">Student Management</a>
                <?php endif; ?>
            <a href="Profile.html"><button>Student Profile</button></a><br>
            <a href="Course.html"><button>Student Course</button></a><br>
            <a href="Attendence.html"><button>Attendance Record</button></a><br>
            <a href="Fees.html"><button>Fees Payment</button></a><br>
            <a href="HallTicket.html"><button>Exam Hall Ticket</button></a><br>
            <a href="Scholarship.html"><button>Scholarship Application</button></a><br>
            <a href="Medical.html"><button>Medical Leave Application</button></a><br>
            <a href="Event.html"><button>Event Registration</button></a><br>
            <a href="Rule.html"><button>Rules and Regulation</button></a><br>

            <br><br>

            <div class="Logout">
                <a href="logout.php">Logout ></a>
            </div>

        </div>

        
        <div class="main">

            <div class="header">

                <div class="line"></div>

                <div class="Profile">

                    <div>
                        <h2><?php echo htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8'); ?></h2>
                        <h3><?php echo htmlspecialchars($studentUsername, ENT_QUOTES, 'UTF-8'); ?></h3>
                    </div>
                    
                </div>

            </div>

            <h2>Welcome to the Student Portal !</h2>

            <br>

            
            <div class="card">

                
               <div class="small-card">
                    <h3>Courses</h3>
                    <p>6 Registered Courses</p>
                </div>

                <div class="small-card">
                    <h3>Attendance</h3>
                    <p>92%</p>
                </div>

                <div class="small-card">
                    <h3> Credits</h3>
                    <p>42 / 66</p>
                </div>
                                <br>

                
                <div class="right-side">

                    <div class="medium-card">

                        <h3>Announcements</h3>

                        <ul>
                            <li>Mid-sem exam starts from 15 Aug.</li>
                            <li>Course registration closes on 10 Aug.</li>
                            <li>Hackathon registration is open now.</li>
                        </ul>

                    </div>

                    <br>

                    <div class="medium-card">

                        <h3>Reminders</h3>

                        <ul>
                            <li>Pay semester fee before 12 Aug.</li>
                            <li>Submit DBMS assignment by Friday.</li>
                            <li>Carry ID card during exams.</li>
                        </ul>

                    </div>

                </div>

            </div>

        </div>

    </div>


    <script src="Theme.js"></script>
        
</body>

</html>