
<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Uni-Sync — Academic Command Center</title>

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
      <a href="index.php" class="current">Home</a>
      <a href="planner.php">Study planner</a>
      <a href="dashboard.php">Dashboard</a>
      <a href="input.php">Add Course</a>
    </div>
    <div class="nav-cta">
    <button id="themeToggleBtn" class="btn-ghost btn me-2" title="Toggle Light/Dark Mode"><i class="bi bi-moon-stars"></i></button>
    <a href="dashboard.php" class="btn-ghost btn d-none d-sm-inline-flex">See the dashboard</a>
    
    <div class="auth-buttons" style="display: flex; gap: 15px; align-items: center; margin-left: auto; padding-left: 20px; border-left: 1px solid rgba(255,255,255,0.1);">
        <?php if(isset($_SESSION['user_id'])): ?>
            
            <!-- PROFILE BUTTON -->
            <!-- PROFILE BUTTON -->
<a href="profile.php" style="display: flex; align-items: center; gap: 8px; color: var(--text-main); text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.2s; padding-right: 10px;">
    <div style="width: 32px; height: 32px; border-radius: 50%; background: rgba(255,255,255,0.05); display: flex; justify-content: center; align-items: center; border: 1px solid rgba(255,255,255,0.2); overflow: hidden;">
        <?php
        // Fetch profile pic if not already in session
        if (!isset($_SESSION['profile_pic']) && isset($_SESSION['user_id'])) {
            $db_conn = new mysqli("localhost", "root", "", "unisync_db");
            $res = $db_conn->query("SELECT profile_pic FROM users WHERE id = " . $_SESSION['user_id']);
            if ($res && $row = $res->fetch_assoc()) {
                $_SESSION['profile_pic'] = $row['profile_pic'];
            }
            $db_conn->close();
        }
        
        if (!empty($_SESSION['profile_pic'])): ?>
            <img src="<?php echo htmlspecialchars($_SESSION['profile_pic']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
        <?php else: ?>
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--muted);"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
        <?php endif; ?>
    </div>
    Profile
</a>

            <!-- LOG OUT BUTTON -->
            <a href="backend/logout.php" style="background: var(--signal); color: #000; padding: 6px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9rem; transition: 0.2s;">Log Out</a>
            
        <?php else: ?>
            
            <!-- LOGGED OUT STATE -->
            <a href="login.php" style="color: var(--text-main); text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: 0.2s;">Log In</a>
            <a href="signup.php" style="background: var(--signal); color: #000; padding: 6px 16px; border-radius: 6px; text-decoration: none; font-weight: bold; font-size: 0.9rem; transition: 0.2s;">Sign Up</a>
            
        <?php endif; ?>
    </div>
</div>
  </div>
</div>

<div id="mobileMenu" class="shell d-none" style="padding-top:1rem; padding-bottom:1rem; border-bottom:1px solid var(--line-soft);">
  <a href="index.php" class="current">Home</a>
  <a href="planner.php">Study planner</a>
  <a href="dashboard.php">Dashboard</a>
</div>

<!-- HERO -->
<section class="hero grid-bg">
  <div class="shell">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <div class="kicker">Built for exam season, not just calendars</div>
        <h1>Your syllabus, turned into a plan that survives a bad week.</h1>
        <p class="lead mt-3 mb-4">Uni-Sync tracks every exam and deadline, breaks each syllabus into daily study blocks, and rebuilds the plan the moment you fall behind no manual rescheduling, no guilt spiral.</p>
        <div class="d-flex gap-3">
          <a href="planner.php" class="btn-signal btn">Get started</a>
          <a href="dashboard.php" class="btn-ghost btn">See the dashboard</a>
        </div>
      </div>
      <div class="col-lg-6">
        <div class="console">
          <div class="console-head">
            <span>Live countdown</span>
            <span class="dots"><span></span><span></span><span></span></span>
          </div>
          <div class="console-label">Next up</div>
          <div class="console-course">Loading live deadline...</div>
          <div class="countdown mono" id="heroCountdown">
            <div class="unit"><div class="num" id="hd">00</div><div class="lbl">days</div></div>
            <div class="unit"><div class="num" id="hh">00</div><div class="lbl">hours</div></div>
            <div class="unit"><div class="num" id="hm">00</div><div class="lbl">min</div></div>
            <div class="unit"><div class="num" id="hs">00</div><div class="lbl">sec</div></div>
          </div>
          <div class="console-foot">
           <span class="go" id="homeSyllabusCoverage">Loading...</span>
            <span>Today's block: 45 min</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- STAT STRIP -->
<div class="strip">
  <div class="shell">
    <div class="item"><div class="num mono">1 formula</div><div class="cap">drives every recalculation no black-box AI</div></div>
    <div class="item"><div class="num mono">Web + mobile</div><div class="cap">one plan, synced across devices</div></div>
    <div class="item"><div class="num mono">Daily cap</div><div class="cap">rebalancing never overloads a single day</div></div>
    <div class="item"><div class="num mono">Zero setup</div><div class="cap">add a course, get a plan the same minute</div></div>
  </div>
</div>

<!-- OVERVIEW -->
<section>
  <div class="shell">
    <div class="kicker">What Uni-Sync actually does</div>
    <h2 class="mb-0" style="font-size:2.1rem; max-width:640px;">Three systems, one command center.</h2>

    <div class="feature-row">
      <div class="col">
        <h3>Centralized exam &amp; deadline input</h3>
        <p>Add a course once exam date, assignment due dates, and syllabus topics all live in one place instead of three different apps and a paper diary.</p>
        <div class="tag-list">
          <span>Course schedules</span><span>Exam dates</span><span>Assignment deadlines</span>
        </div>
      </div>
      <div class="col" style="padding-left: 100px;">
        <div class="engine-panel">
    <div id="homeCourseList">
        <div style="color: var(--muted); padding: 10px;">Loading upcoming exams...</div>
    </div>
    </div>
        </div>
      </div>
    </div>

    <div class="feature-row reverse">
      <div class="col" style="padding-right: 100px;">
        <h3>Adaptive study planner</h3>
        <p>Each syllabus is split into daily study blocks sized to what's actually left before the exam. Miss a session, and a deterministic formula redraws the rest of the week for you.</p>
        <a href="planner.php" class="btn-ghost btn mt-2">See the planner in action</a>
      </div>
      <div class="col" style="padding-left: 200px;">
        <div class="engine-panel">
         <!-- Dynamic Course Calendar -->
<div class="calendar-wrapper" style="background: var(--card-bg); border: 1px solid rgba(255,255,255,0.1); border-radius: 8px; padding: 15px; width: 100%; max-width: 300px; margin-top: 15px;">
    <div id="calendarHeader" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px; font-weight: bold; color: var(--text-main); font-size: 1rem;">
        <!-- Month and Year populate here -->
    </div>
    <div id="calendarGrid" style="display: grid; grid-template-columns: repeat(7, 1fr); gap: 8px; text-align: center; font-size: 0.85rem; color: var(--muted);">
        <!-- JavaScript populates days and highlights -->
    </div>
</div>
        </div>
      </div>
    </div>

    <div class="feature-row">
      <div class="col" style="padding-left: 100px;">
        <h3>One dashboard for everything</h3>
        <p>Countdowns, today's study blocks, and syllabus coverage in a single screen no switching between a calendar app, a to do list, and a notebook.</p>
        <a href="dashboard.php" class="btn-ghost btn mt-2">Open the dashboard preview</a>
      </div>

         
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
<section>
  <div class="shell">
    <div class="cta-block">
      <div class="kicker" style="justify-content:center;">Skill Development Project I — ICT 1108</div>
      <h2>Uni-Sync: an academic command center, built by students.</h2>
      <p>Developed by Group 06, Department of Information and Communication Technology, Faculty of Technology, Rajarata University of Sri Lanka.</p>
      <a href="planner.php" class="btn-signal btn">Explore the planner</a>
    </div>
  </div>
</section>

<footer>
  <div class="shell">
    <div class="brand"><span class="pulse"></span> Uni-Sync</div>
    <div class="meta">
      Group 06 — The Uni Sync<br>
      Supervisor: Mr. Nandika Tennakoon, ITT Department
    </div>
    <div class="meta">&copy; 2026 Uni-Sync. Built for Rajarata University of Sri Lanka.</div>
  </div>
</footer>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.3/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>

</body>
</html>