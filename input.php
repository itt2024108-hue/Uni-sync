<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Data Entry — Uni-Sync</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Space+Grotesk:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>

<!-- NAV -->
<div class="nav-wrap">
  <div class="shell">
    <a href="index.php" class="brand"><span class="pulse"></span> Uni-Sync</a>
    <div class="nav-links">
      <a href="index.php">Home</a>
      <a href="planner.php">Study planner</a>
      <a href="dashboard.php">Dashboard</a>
      <a href="input.php" class="current">Add Course</a>
    </div>
    <div class="nav-cta">
      <button id="themeToggleBtn" class="btn-ghost btn me-2" title="Toggle Light/Dark Mode"><i class="bi bi-moon-stars"></i></button>
      <a href="dashboard.php" class="btn-ghost btn d-none d-sm-inline-flex">See the dashboard</a>

      <button class="nav-toggle" id="navToggle"><i class="bi bi-list"></i></button>
    </div>
  </div>
</div>

<div id="mobileMenu" class="shell d-none" style="padding-top:1rem; padding-bottom:1rem; border-bottom:1px solid var(--line-soft);">
  <a href="index.php">Home</a>
  <a href="planner.php">Study planner</a>
  <a href="dashboard.php">Dashboard</a>
  <a href="input.php" class="current">Add Course</a>
</div>

<!-- PAGE HEAD -->
<section class="page-head grid-bg">
  <div class="shell">
    <div class="kicker">Data Entry</div>
    <h1>Add a new syllabus to the engine.</h1>
    <p class="lead">Enter your course details and deadlines here to generate a new adaptive study plan.</p>
  </div>
</section>

<!-- INPUT FORM -->
<section style="padding-top:1rem;">
  <div class="shell">
    <div class="engine-panel" style="max-width: 600px; margin: 0 auto;">
      <form id="courseForm">
        <div class="mb-4">
          <div class="console-label mb-2">Course / Subject Name</div>
         <select id="courseName" class="form-control bg-dark text-light border-secondary" required>
    <option value="" disabled selected>Select a course...</option>
    <option value="ICT 1111 - Fundamentals of ICT">ICT 1111 - Fundamentals of ICT</option>
    <option value="ICT 1202 - Object Oriented Programming">ICT 1202 - Object Oriented Programming</option>
    <option value="ICT 1209 - Web Technologies">ICT 1209 - Web Technologies</option>
    <option value="ICT 1210 - Media Production">ICT 1210 - Media Production</option>
    <option value="CMT 1303 - Fundamentals of Mathematics">CMT 1303 - Fundamentals of Mathematics</option>
</select>
        </div>
        
        <div class="mb-4">
          <div class="console-label mb-2">Final Exam Date</div>
          <input type="date" id="examDate" class="form-control bg-dark text-light border-secondary" required>
        </div>
        <button type="submit" class="btn-signal btn w-100">Inject into Command Center</button>
      </form>
      <div id="formSuccess" class="mt-3 text-center d-none" style="color: var(--go);">
        <i class="bi bi-check-circle-fill me-2"></i> Course added to local storage!
      </div>
    </div>
  </div>
</section>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>